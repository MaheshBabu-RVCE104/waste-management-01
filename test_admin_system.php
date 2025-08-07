<?php
echo "<h2>Admin System Test - WMS Database</h2>";

// Test database connection
require_once "connection.php";

if(!$con) {
    echo "<p style='color: red;'>✗ Database connection failed!</p>";
    exit();
}

echo "<p style='color: green;'>✓ Database connection successful!</p>";

// Test usertable structure
$result = mysqli_query($con, "SHOW COLUMNS FROM usertable");
if($result) {
    echo "<p style='color: green;'>✓ usertable exists!</p>";
    $has_role = false;
    while($row = mysqli_fetch_assoc($result)) {
        if($row['Field'] == 'role') {
            $has_role = true;
            break;
        }
    }
    if($has_role) {
        echo "<p style='color: green;'>✓ Role column exists in usertable!</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Role column missing in usertable!</p>";
    }
} else {
    echo "<p style='color: red;'>✗ usertable does not exist!</p>";
}

// Test admin user
$result = mysqli_query($con, "SELECT * FROM usertable WHERE email = 'admin@wms.com' AND role = 'admin'");
if($result && mysqli_num_rows($result) > 0) {
    echo "<p style='color: green;'>✓ Admin user exists!</p>";
} else {
    echo "<p style='color: orange;'>⚠ Admin user not found!</p>";
}

// Test garbageinfo table
$result = mysqli_query($con, "SELECT COUNT(*) as count FROM garbageinfo");
if($result) {
    $count = mysqli_fetch_assoc($result)['count'];
    echo "<p style='color: green;'>✓ garbageinfo table exists with $count complaints!</p>";
} else {
    echo "<p style='color: red;'>✗ garbageinfo table does not exist!</p>";
}

// Test adminlogin table (for compatibility)
$result = mysqli_query($con, "SELECT COUNT(*) as count FROM adminlogin");
if($result) {
    $count = mysqli_fetch_assoc($result)['count'];
    echo "<p style='color: green;'>✓ adminlogin table exists with $count records!</p>";
} else {
    echo "<p style='color: orange;'>⚠ adminlogin table does not exist!</p>";
}

echo "<hr>";
echo "<div style='background-color: #e8f5e8; padding: 15px; border: 1px solid #4caf50; border-radius: 5px;'>";
echo "<h3>Test Results Summary:</h3>";
echo "<p><strong>Admin Login URL:</strong> <a href='admin_login.php'>admin_login.php</a></p>";
echo "<p><strong>Admin Dashboard URL:</strong> <a href='admin_dashboard.php'>admin_dashboard.php</a></p>";
echo "<p><strong>Database Setup URL:</strong> <a href='setup_wms_database.php'>setup_wms_database.php</a></p>";
echo "<p><strong>Default Admin Credentials:</strong></p>";
echo "<ul>";
echo "<li><strong>Email:</strong> admin@wms.com</li>";
echo "<li><strong>Password:</strong> admin123</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background-color: #e3f2fd; padding: 15px; border: 1px solid #2196f3; border-radius: 5px; margin-top: 20px;'>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If any tests failed, run <a href='setup_wms_database.php'>setup_wms_database.php</a></li>";
echo "<li>Go to <a href='admin_login.php'>admin_login.php</a> to login as admin</li>";
echo "<li>After successful login, you'll be redirected to the complaints preview page</li>";
echo "<li>You can edit and delete complaints from the admin dashboard</li>";
echo "</ol>";
echo "</div>";

mysqli_close($con);
?>
