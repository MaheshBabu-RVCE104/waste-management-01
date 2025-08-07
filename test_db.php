<?php
require_once "connection.php";

echo "<h2>Database Structure Check</h2>";

// Check if role column exists
$check_role = "SHOW COLUMNS FROM usertable LIKE 'role'";
$result = mysqli_query($con, $check_role);

if(mysqli_num_rows($result) == 0) {
    echo "<p>Role column does not exist. Adding it...</p>";
    
    $add_role = "ALTER TABLE usertable ADD COLUMN role varchar(20) DEFAULT 'user'";
    if(mysqli_query($con, $add_role)) {
        echo "<p style='color: green;'>✓ Role column added successfully!</p>";
    } else {
        echo "<p style='color: red;'>✗ Error adding role column: " . mysqli_error($con) . "</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Role column already exists!</p>";
}

// Check if admin user exists
$check_admin = "SELECT * FROM usertable WHERE email = 'admin@wms.com'";
$result = mysqli_query($con, $check_admin);

if(mysqli_num_rows($result) == 0) {
    echo "<p>Admin user does not exist. Creating...</p>";
    
    $admin_name = "Admin User";
    $admin_email = "admin@wms.com";
    $admin_password = "admin123";
    $admin_role = "admin";
    
    $encpass = password_hash($admin_password, PASSWORD_BCRYPT);
    
    $insert_admin = "INSERT INTO usertable (name, email, password, role) VALUES ('$admin_name', '$admin_email', '$encpass', '$admin_role')";
    
    if(mysqli_query($con, $insert_admin)) {
        echo "<p style='color: green;'>✓ Admin user created successfully!</p>";
        echo "<p><strong>Admin Credentials:</strong></p>";
        echo "<p>Email: admin@wms.com</p>";
        echo "<p>Password: admin123</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating admin user: " . mysqli_error($con) . "</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Admin user already exists!</p>";
}

// Show current users
echo "<h3>Current Users:</h3>";
$users = "SELECT name, email, role FROM usertable";
$result = mysqli_query($con, $users);

if(mysqli_num_rows($result) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Name</th><th>Email</th><th>Role</th></tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No users found.</p>";
}

echo "<p><a href='phpGmailSMTP/trash.php'>Go to Waste Management System</a></p>";
?>
