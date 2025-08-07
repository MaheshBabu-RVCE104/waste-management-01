<?php
session_start();
require_once "connection.php";

// Auto-login as admin
$admin_email = "admin@wms.com";
$admin_password = "admin123";

// Check if admin user exists
$check_admin = "SELECT * FROM usertable WHERE email = '$admin_email'";
$result = mysqli_query($con, $check_admin);

if(mysqli_num_rows($result) == 0) {
    // Create admin user if it doesn't exist
    $admin_name = "Admin User";
    $admin_role = "admin";
    $encpass = password_hash($admin_password, PASSWORD_BCRYPT);
    
    $insert_admin = "INSERT INTO usertable (name, email, password, role) VALUES ('$admin_name', '$admin_email', '$encpass', '$admin_role')";
    mysqli_query($con, $insert_admin);
}

// Set admin session
$_SESSION['email'] = $admin_email;
$_SESSION['name'] = "Admin User";
$_SESSION['role'] = "admin";

// Redirect to admin dashboard (complaints preview page)
header('Location: admin_dashboard.php');
exit();
?>
