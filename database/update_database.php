<?php
/**
 * Update Database Schema Script
 * This script will add missing fields to existing tables
 * Run this if you already have the database but need to add missing fields
 */

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_website_db";

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Update Database Schema</h2>";
    
    // Check if messages table needs submitted_at field
    $stmt = $pdo->prepare("SHOW COLUMNS FROM messages LIKE 'submitted_at'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Add submitted_at column to messages table
        $pdo->exec("ALTER TABLE messages ADD COLUMN submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER seen");
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ Added submitted_at column to messages table";
        echo "</div>";
    } else {
        echo "<div style='color: blue; font-weight: bold; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0;'>";
        echo "ℹ submitted_at column already exists in messages table";
        echo "</div>";
    }
    
    // Check if bookings table needs status field
    $stmt = $pdo->prepare("SHOW COLUMNS FROM bookings LIKE 'status'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Add status column to bookings table
        $pdo->exec("ALTER TABLE bookings ADD COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending' AFTER price");
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ Added status column to bookings table";
        echo "</div>";
    } else {
        echo "<div style='color: blue; font-weight: bold; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0;'>";
        echo "ℹ status column already exists in bookings table";
        echo "</div>";
    }
    
    // Add indexes for better performance
    try {
        $pdo->exec("ALTER TABLE messages ADD INDEX idx_submitted_at (submitted_at)");
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ Added submitted_at index to messages table";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='color: orange; font-weight: bold; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;'>";
        echo "⚠ Index already exists or could not be added: " . $e->getMessage();
        echo "</div>";
    }
    
    try {
        $pdo->exec("ALTER TABLE bookings ADD INDEX idx_status (status)");
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ Added status index to bookings table";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='color: orange; font-weight: bold; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;'>";
        echo "⚠ Index already exists or could not be added: " . $e->getMessage();
        echo "</div>";
    }
    
    echo "<div style='padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 10px 0;'>";
    echo "<h3>Database Update Complete!</h3>";
    echo "<p>The database schema has been updated with all required fields for:</p>";
    echo "<ul>";
    echo "<li>✓ Messages table with submitted_at field</li>";
    echo "<li>✓ Bookings table with status field</li>";
    echo "<li>✓ Performance indexes for reports</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✗ Database Error: " . $e->getMessage();
    echo "</div>";
    
    echo "<div style='padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 10px 0;'>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Ensure database 'travel_website_db' exists</li>";
    echo "<li>Check database credentials</li>";
    echo "<li>Run setup_database.php first if database doesn't exist</li>";
    echo "</ul>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Database - Travel Website</title>
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
        .btn-success {
            background: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: center; margin-top: 30px;">
            <a href="../admin_setup/admin.php" class="btn btn-success">Go to Admin Panel</a>
            <a href="../index.php" class="btn">Go to Website</a>
        </div>
    </div>
</body>
</html>
