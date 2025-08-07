<?php
require_once "connection.php";

// Check if admin already exists
$check_admin = "SELECT * FROM usertable WHERE email = 'admin@wms.com'";
$result = mysqli_query($con, $check_admin);

if(mysqli_num_rows($result) == 0) {
    // Create admin user
    $admin_name = "Admin User";
    $admin_email = "admin@wms.com";
    $admin_password = "admin123"; // You should change this password
    $admin_role = "admin";
    
    $encpass = password_hash($admin_password, PASSWORD_BCRYPT);
    
    $insert_admin = "INSERT INTO usertable (name, email, password, role) VALUES ('$admin_name', '$admin_email', '$encpass', '$admin_role')";
    
    if(mysqli_query($con, $insert_admin)) {
        echo "Admin user created successfully!<br>";
        echo "Email: admin@wms.com<br>";
        echo "Password: admin123<br>";
        echo "<strong>Please change this password after first login!</strong>";
    } else {
        echo "Error creating admin user: " . mysqli_error($con);
    }
} else {
    echo "Admin user already exists!";
}
?>
