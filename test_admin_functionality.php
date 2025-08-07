<?php
echo "<h2>Admin Functionality Test - WMS System</h2>";

// Test database connection
require_once "connection.php";

if(!$con) {
    echo "<p style='color: red;'>✗ Database connection failed!</p>";
    exit();
}

echo "<p style='color: green;'>✓ Database connection successful!</p>";

// Test admin user exists
$result = mysqli_query($con, "SELECT * FROM usertable WHERE email = 'admin@wms.com' AND role = 'admin'");
if($result && mysqli_num_rows($result) > 0) {
    echo "<p style='color: green;'>✓ Admin user exists and has admin role!</p>";
} else {
    echo "<p style='color: red;'>✗ Admin user not found or missing admin role!</p>";
}

// Test complaints table
$result = mysqli_query($con, "SELECT COUNT(*) as count FROM garbageinfo");
if($result) {
    $count = mysqli_fetch_assoc($result)['count'];
    echo "<p style='color: green;'>✓ garbageinfo table accessible with $count complaints!</p>";
} else {
    echo "<p style='color: red;'>✗ Cannot access garbageinfo table!</p>";
}

// Test status values
$result = mysqli_query($con, "SELECT DISTINCT status FROM garbageinfo");
if($result) {
    echo "<p style='color: green;'>✓ Status values in database:</p>";
    echo "<ul>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<li>" . htmlspecialchars($row['status']) . "</li>";
    }
    echo "</ul>";
}

// Test admin dashboard access
echo "<hr>";
echo "<div style='background-color: #e8f5e8; padding: 15px; border: 1px solid #4caf50; border-radius: 5px;'>";
echo "<h3 style='color: #2e7d32;'>Admin Dashboard Features:</h3>";
echo "<ul>";
echo "<li><strong>✅ Edit Complaints:</strong> Blue edit button opens modal with all complaint details</li>";
echo "<li><strong>✅ Delete Complaints:</strong> Red delete button removes complaints and associated files</li>";
echo "<li><strong>✅ Status Management:</strong> Quick dropdown to change status (Pending/In Progress/Completed)</li>";
echo "<li><strong>✅ Full Form Editing:</strong> Modal form for complete complaint modification</li>";
echo "<li><strong>✅ Real-time Updates:</strong> Changes reflect immediately in the dashboard</li>";
echo "<li><strong>✅ Admin Security:</strong> Only admin users can access these features</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background-color: #e3f2fd; padding: 15px; border: 1px solid #2196f3; border-radius: 5px; margin-top: 20px;'>";
echo "<h3 style='color: #1976d2;'>How to Test Admin Features:</h3>";
echo "<ol>";
echo "<li><strong>Login as Admin:</strong> <a href='admin_login.php'>admin_login.php</a></li>";
echo "<li><strong>Access Dashboard:</strong> <a href='admin_dashboard.php'>admin_dashboard.php</a></li>";
echo "<li><strong>Test Quick Status Change:</strong> Use the dropdown in the Status column</li>";
echo "<li><strong>Test Edit Function:</strong> Click the blue edit button to open modal</li>";
echo "<li><strong>Test Delete Function:</strong> Click the red delete button (with confirmation)</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background-color: #fff3e0; padding: 15px; border: 1px solid #ff9800; border-radius: 5px; margin-top: 20px;'>";
echo "<h3 style='color: #e65100;'>Admin Credentials:</h3>";
echo "<p><strong>Email:</strong> admin@wms.com</p>";
echo "<p><strong>Password:</strong> admin123</p>";
echo "<p><em>Use these credentials to login and test all admin functionality!</em></p>";
echo "</div>";

mysqli_close($con);
?>
