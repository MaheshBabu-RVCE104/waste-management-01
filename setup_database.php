<?php
echo "<h2>Database Setup for Waste Management System</h2>";

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
    echo "<li>Check if MySQL is running on port 3306</li>";
    echo "<li>Try accessing phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
    echo "<li>If phpMyAdmin asks for password, note it down</li>";
    echo "<li>Update the connection.php files with the correct password</li>";
    echo "</ol>";
    echo "</div>";
    exit();
}

// Now create tables
echo "<h3>Creating Database Tables...</h3>";

// Create usertable
$create_usertable = "CREATE TABLE IF NOT EXISTS usertable (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    email varchar(255) NOT NULL,
    password varchar(255) NOT NULL,
    role varchar(20) DEFAULT 'user',
    PRIMARY KEY (id)
)";

if(mysqli_query($con, $create_usertable)) {
    echo "<p style='color: green;'>✓ usertable created/updated successfully!</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating usertable: " . mysqli_error($con) . "</p>";
}

// Create garbageinfo table
$create_garbageinfo = "CREATE TABLE IF NOT EXISTS garbageinfo (
    Id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    mobile varchar(15) DEFAULT NULL,
    email varchar(255) NOT NULL,
    wastetype varchar(255) NOT NULL,
    location varchar(255) NOT NULL,
    locationdescription varchar(255) NOT NULL,
    file varchar(255) NOT NULL,
    date varchar(255) DEFAULT NULL,
    status varchar(50) DEFAULT 'Pending',
    PRIMARY KEY (Id)
)";

if(mysqli_query($con, $create_garbageinfo)) {
    echo "<p style='color: green;'>✓ garbageinfo table created/updated successfully!</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating garbageinfo table: " . mysqli_error($con) . "</p>";
}

// Add role column if it doesn't exist
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

// Create admin user
$check_admin = "SELECT * FROM usertable WHERE email = 'admin@wms.com'";
$result = mysqli_query($con, $check_admin);

if(mysqli_num_rows($result) == 0) {
    $admin_name = "Admin User";
    $admin_email = "admin@wms.com";
    $admin_password = "admin123";
    $admin_role = "admin";
    
    $encpass = password_hash($admin_password, PASSWORD_BCRYPT);
    
    $insert_admin = "INSERT INTO usertable (name, email, password, role) VALUES ('$admin_name', '$admin_email', '$encpass', '$admin_role')";
    
    if(mysqli_query($con, $insert_admin)) {
        echo "<p style='color: green;'>✓ Admin user created successfully!</p>";
        echo "<div style='background-color: #e8f5e8; padding: 15px; border: 1px solid #4caf50; border-radius: 5px;'>";
        echo "<h4 style='color: #2e7d32;'>Admin Login Credentials:</h4>";
        echo "<p><strong>Email:</strong> admin@wms.com</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "<p><em>Please change this password after first login!</em></p>";
        echo "</div>";
    } else {
        echo "<p style='color: red;'>✗ Error creating admin user: " . mysqli_error($con) . "</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Admin user already exists!</p>";
}

// Show current tables
echo "<h3>Current Database Tables:</h3>";
$tables = mysqli_query($con, "SHOW TABLES");
if($tables) {
    echo "<ul>";
    while($row = mysqli_fetch_array($tables)) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
}

echo "<hr>";
echo "<div style='background-color: #e3f2fd; padding: 15px; border: 1px solid #2196f3; border-radius: 5px;'>";
echo "<h3 style='color: #1976d2;'>Setup Complete!</h3>";
echo "<p>Your database is now ready. You can:</p>";
echo "<ul>";
echo "<li><a href='phpGmailSMTP/trash.php'>Go to Waste Management System</a></li>";
echo "<li><a href='login-user.php'>Login as Regular User</a></li>";
echo "<li><a href='adminsignup/index.php'>Login as Admin</a></li>";
echo "</ul>";
echo "</div>";

mysqli_close($con);
?>
