<?php
/**
 * Database Setup Script
 * This script will create the database and all required tables
 * Run this file once to set up the database
 */

// Database configuration for setup (without specifying database initially)
$servername = "localhost";
$username = "root";
$password = "";

try {
    // Connect to MySQL server without selecting database
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Travel Website Database Setup</h2>";
    
    // Read and execute the SQL file
    $sqlFile = __DIR__ . '/create_database.sql';
    
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        
        // Execute the SQL script
        $pdo->exec($sql);
        
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ Database and tables created successfully!";
        echo "</div>";
        
        echo "<div style='padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 10px 0;'>";
        echo "<h3>Database Information:</h3>";
        echo "<ul>";
        echo "<li><strong>Database Name:</strong> travel_website_db</li>";
        echo "<li><strong>Tables Created:</strong> users, packages, bookings, messages</li>";
        echo "<li><strong>Default Admin:</strong> admin@travel.com / admin123</li>";
        echo "<li><strong>Sample Data:</strong> 5 sample packages inserted</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<div style='padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;'>";
        echo "<h3>Next Steps:</h3>";
        echo "<ol>";
        echo "<li>Delete this setup file for security</li>";
        echo "<li>Update the database connection in db_connect.php if needed</li>";
        echo "<li>Test the website functionality</li>";
        echo "</ol>";
        echo "</div>";
        
    } else {
        throw new Exception("SQL file not found: " . $sqlFile);
    }
    
} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✗ Database Error: " . $e->getMessage();
    echo "</div>";
    
    echo "<div style='padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 10px 0;'>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Ensure MySQL server is running</li>";
    echo "<li>Check database credentials (username: root, password: empty)</li>";
    echo "<li>Make sure user has CREATE DATABASE privileges</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✗ Error: " . $e->getMessage();
    echo "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Setup - Travel Website</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2, h3 {
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 5px;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-success {
            background: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: center; margin-top: 30px;">
            <a href="../index.php" class="btn btn-success">Go to Website</a>
            <a href="../admin_setup/admin.php" class="btn">Admin Panel</a>
        </div>
    </div>
</body>
</html>
