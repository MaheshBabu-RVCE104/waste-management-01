<?php
echo "<h2>MySQL Connection Test</h2>";

// Test different connection configurations
$configs = [
    ['localhost', 'root', '', 'wms'],
    ['localhost', 'root', 'root', 'wms'],
    ['localhost', 'root', 'Rootroot', 'wms'],
    ['127.0.0.1', 'root', '', 'wms'],
    ['127.0.0.1', 'root', 'root', 'wms'],
];

foreach($configs as $config) {
    list($host, $user, $password, $database) = $config;
    
    echo "<p>Testing: $host, $user, " . ($password ? '***' : 'empty') . ", $database</p>";
    
    $con = @mysqli_connect($host, $user, $password, $database);
    
    if($con) {
        echo "<p style='color: green;'>✓ SUCCESS! Connection works with this configuration.</p>";
        echo "<p><strong>Working Configuration:</strong></p>";
        echo "<p>Host: $host</p>";
        echo "<p>User: $user</p>";
        echo "<p>Password: " . ($password ? '***' : 'empty') . "</p>";
        echo "<p>Database: $database</p>";
        
        // Test if database exists
        $result = mysqli_query($con, "SHOW TABLES");
        if($result) {
            echo "<p style='color: green;'>✓ Database '$database' exists and is accessible.</p>";
            
            // Show tables
            echo "<p><strong>Tables in database:</strong></p>";
            echo "<ul>";
            while($row = mysqli_fetch_array($result)) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: orange;'>⚠ Database '$database' might not exist.</p>";
        }
        
        mysqli_close($con);
        break;
    } else {
        echo "<p style='color: red;'>✗ Failed: " . mysqli_connect_error() . "</p>";
    }
}

echo "<hr>";
echo "<h3>XAMPP MySQL Setup Instructions:</h3>";
echo "<ol>";
echo "<li>Open XAMPP Control Panel</li>";
echo "<li>Click 'Admin' next to MySQL</li>";
echo "<li>This will open phpMyAdmin</li>";
echo "<li>Check if database 'wms' exists</li>";
echo "<li>If not, create it by clicking 'New' and entering 'wms'</li>";
echo "<li>Check the root user password in phpMyAdmin</li>";
echo "</ol>";

echo "<p><a href='test_db.php'>Test Database Structure</a></p>";
?>
