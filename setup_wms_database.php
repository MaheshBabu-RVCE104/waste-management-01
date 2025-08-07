<?php
echo "<h2>WMS Database Setup and Admin Configuration</h2>";

// Try different connection methods
$connection_success = false;
$con = null;

$configs = [
    ['localhost', 'root', '', 'wms'],
    ['localhost', 'root', 'root', 'wms'],
    ['127.0.0.1', 'root', '', 'wms'],
    ['127.0.0.1', 'root', 'root', 'wms'],
];

foreach($configs as $config) {
    list($host, $user, $password, $database) = $config;
    
    echo "<p>Testing connection: $host, $user, " . ($password ? '***' : 'empty') . "</p>";
    
    // First try to connect without database
    $con = @mysqli_connect($host, $user, $password);
    
    if($con) {
        echo "<p style='color: green;'>✓ Connected to MySQL server successfully!</p>";
        
        // Check if database exists
        $result = mysqli_query($con, "SHOW DATABASES LIKE '$database'");
        if(mysqli_num_rows($result) > 0) {
            echo "<p style='color: green;'>✓ Database '$database' exists!</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Database '$database' does not exist. Creating...</p>";
            
            $create_db = "CREATE DATABASE IF NOT EXISTS $database";
            if(mysqli_query($con, $create_db)) {
                echo "<p style='color: green;'>✓ Database '$database' created successfully!</p>";
            } else {
                echo "<p style='color: red;'>✗ Failed to create database: " . mysqli_error($con) . "</p>";
                continue;
            }
        }
        
        // Select the database
        if(mysqli_select_db($con, $database)) {
            echo "<p style='color: green;'>✓ Database '$database' selected successfully!</p>";
            $connection_success = true;
            break;
        } else {
            echo "<p style='color: red;'>✗ Failed to select database: " . mysqli_error($con) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Connection failed: " . mysqli_connect_error() . "</p>";
    }
}

if(!$connection_success) {
    echo "<div style='background-color: #ffebee; padding: 15px; border: 1px solid #f44336; border-radius: 5px;'>";
    echo "<h3 style='color: #d32f2f;'>Connection Failed!</h3>";
    echo "<p>Please follow these steps:</p>";
    echo "<ol>";
    echo "<li>Make sure XAMPP is running</li>";
    echo "<li>Start MySQL service in XAMPP Control Panel</li>";
    echo "<li>Import the wms.sql file into phpMyAdmin</li>";
    echo "<li>Check if MySQL is running on port 3306</li>";
    echo "</ol>";
    echo "</div>";
    exit();
}

// Now update the database structure
echo "<h3>Updating Database Structure...</h3>";

// Add role column to usertable if it doesn't exist
$check_role = "SHOW COLUMNS FROM usertable LIKE 'role'";
$result = mysqli_query($con, $check_role);

if(mysqli_num_rows($result) == 0) {
    $add_role = "ALTER TABLE usertable ADD COLUMN role varchar(20) DEFAULT 'user'";
    if(mysqli_query($con, $add_role)) {
        echo "<p style='color: green;'>✓ Role column added to usertable!</p>";
    } else {
        echo "<p style='color: red;'>✗ Error adding role column: " . mysqli_error($con) . "</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Role column already exists in usertable!</p>";
}

// Create admin user in usertable
$check_admin = "SELECT * FROM usertable WHERE email = 'admin@wms.com'";
$result = mysqli_query($con, $check_admin);

if(mysqli_num_rows($result) == 0) {
    $admin_name = "Admin User";
    $admin_email = "admin@wms.com";
    $admin_password = "admin123";
    $admin_role = "admin";
    
    $encpass = password_hash($admin_password, PASSWORD_BCRYPT);
    
    $insert_admin = "INSERT INTO usertable (name, email, password, role, code, status) VALUES ('$admin_name', '$admin_email', '$encpass', '$admin_role', 0, 'verified')";
    
    if(mysqli_query($con, $insert_admin)) {
        echo "<p style='color: green;'>✓ Admin user created successfully in usertable!</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating admin user: " . mysqli_error($con) . "</p>";
    }
} else {
    // Update existing admin user to have admin role
    $update_admin = "UPDATE usertable SET role = 'admin' WHERE email = 'admin@wms.com'";
    if(mysqli_query($con, $update_admin)) {
        echo "<p style='color: green;'>✓ Admin user role updated!</p>";
    }
}

// Also create admin in adminlogin table for compatibility
$check_adminlogin = "SELECT * FROM adminlogin WHERE username = 'admin@wms.com'";
$result = mysqli_query($con, $check_adminlogin);

if(mysqli_num_rows($result) == 0) {
    $insert_adminlogin = "INSERT INTO adminlogin (username, password) VALUES ('admin@wms.com', 'admin123')";
    if(mysqli_query($con, $insert_adminlogin)) {
        echo "<p style='color: green;'>✓ Admin user created in adminlogin table for compatibility!</p>";
    }
}

// Show current tables and their structure
echo "<h3>Current Database Tables:</h3>";
$tables = mysqli_query($con, "SHOW TABLES");
if($tables) {
    echo "<ul>";
    while($row = mysqli_fetch_array($tables)) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
}

// Show admin users
echo "<h3>Admin Users:</h3>";
$admin_users = mysqli_query($con, "SELECT name, email, role FROM usertable WHERE role = 'admin'");
if($admin_users && mysqli_num_rows($admin_users) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Name</th><th>Email</th><th>Role</th></tr>";
    while($row = mysqli_fetch_assoc($admin_users)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No admin users found.</p>";
}

echo "<hr>";
echo "<div style='background-color: #e8f5e8; padding: 15px; border: 1px solid #4caf50; border-radius: 5px;'>";
echo "<h3 style='color: #2e7d32;'>Setup Complete!</h3>";
echo "<p><strong>Admin Login Credentials:</strong></p>";
echo "<p><strong>Email:</strong> admin@wms.com</p>";
echo "<p><strong>Password:</strong> admin123</p>";
echo "<p><em>You can now login as admin and access the complaints preview page!</em></p>";
echo "</div>";

echo "<div style='background-color: #e3f2fd; padding: 15px; border: 1px solid #2196f3; border-radius: 5px; margin-top: 20px;'>";
echo "<h3 style='color: #1976d2;'>Next Steps:</h3>";
echo "<ul>";
echo "<li><a href='admin_login.php'>Go to Admin Login</a></li>";
echo "<li><a href='index.html'>Go to Homepage</a></li>";
echo "<li><a href='phpGmailSMTP/trash.php'>Go to Complaints Page</a></li>";
echo "</ul>";
echo "</div>";

mysqli_close($con);
?>
