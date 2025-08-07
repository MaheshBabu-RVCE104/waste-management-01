<?php
session_start();
require_once "connection.php";

$errors = array();

// Check database connection
if(!$con) {
    $db_error = "Database connection failed. Please check your database configuration.";
}

// Handle admin login
if(isset($_POST['admin_login'])){
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    
    // Check if user exists and is admin
    $check_email = "SELECT * FROM usertable WHERE email = '$email' AND role = 'admin'";
    $res = mysqli_query($con, $check_email);
    
    if($res === false) {
        $errors['email'] = "Database error: " . mysqli_error($con);
    } elseif(mysqli_num_rows($res) > 0){
        $fetch = mysqli_fetch_assoc($res);
        $fetch_pass = $fetch['password'];
        
        if(password_verify($password, $fetch_pass)){
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $fetch['name'];
            $_SESSION['role'] = 'admin';
            
            // Redirect directly to complaints preview page
            header('location: admin_dashboard.php');
            exit();
        } else {
            $errors['email'] = "Incorrect email or password!";
        }
    } else {
        $errors['email'] = "Admin not found. Please check your credentials.";
    }
}

// Auto-create admin if not exists (only if no database errors)
if(!isset($db_error)) {
    $check_admin = "SELECT * FROM usertable WHERE email = 'admin@wms.com'";
    $result = mysqli_query($con, $check_admin);

    if($result === false) {
        // Database query failed, show error
        $db_error = "Database error: " . mysqli_error($con);
    } else {
        if(mysqli_num_rows($result) == 0) {
            $admin_name = "Admin User";
            $admin_email = "admin@wms.com";
            $admin_password = "admin123";
            $admin_role = "admin";
            
            $encpass = password_hash($admin_password, PASSWORD_BCRYPT);
            
            $insert_admin = "INSERT INTO usertable (name, email, password, role, code, status) VALUES ('$admin_name', '$admin_email', '$encpass', '$admin_role', 0, 'verified')";
            $insert_result = mysqli_query($con, $insert_admin);
            
            if($insert_result === false) {
                $db_error = "Failed to create admin user: " . mysqli_error($con);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Waste Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
        .admin-icon {
            font-size: 60px;
            color: #ff6b35;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-admin {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 25px;
            font-weight: bold;
            width: 100%;
        }
        .btn-admin:hover {
            background: linear-gradient(135deg, #e55a2b 0%, #e0851a 100%);
            color: white;
        }
        .credentials-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="admin-icon">
            <i class="fas fa-user-shield"></i>
        </div>
        
        <h3 class="text-center mb-4">Admin Login</h3>
        <p class="text-center text-muted mb-4">Access the complaints preview page</p>
        
        <?php if(count($errors) > 0): ?>
            <div class="alert alert-danger">
                <?php foreach($errors as $error): ?>
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($db_error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-database"></i> <?php echo $db_error; ?>
                <br><br>
                <strong>Please run the WMS database setup first:</strong>
                <a href="setup_wms_database.php" class="btn btn-warning btn-sm mt-2">
                    <i class="fas fa-cog"></i> Setup WMS Database
                </a>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" class="form-control" name="email" placeholder="Enter admin email" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter password" required>
            </div>
            
            <button type="submit" name="admin_login" class="btn btn-admin">
                <i class="fas fa-sign-in-alt"></i> Login as Admin
            </button>
        </form>
        
        <div class="credentials-box">
            <h6><i class="fas fa-info-circle"></i> Default Admin Credentials:</h6>
            <p class="mb-1"><strong>Email:</strong> admin@wms.com</p>
            <p class="mb-0"><strong>Password:</strong> admin123</p>
        </div>
        
        <div class="text-center mt-3">
            <a href="index.html" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-home"></i> Back to Home
            </a>
            <a href="login-user.php" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-user"></i> User Login
            </a>
        </div>
    </div>
</body>
</html>
