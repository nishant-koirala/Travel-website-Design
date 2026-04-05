<?php
/**
 * Setup Itinerary Tables Script
 * This script will add itinerary functionality to the database
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
    
    echo "<h2>Setup Itinerary Functionality</h2>";
    
    // Read and execute the SQL file
    $sqlFile = __DIR__ . '/add_itinerary_tables.sql';
    
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement) && !preg_match('/^USE /', $statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignore errors for IF NOT EXISTS and similar statements
                    if (strpos($e->getMessage(), 'already exists') === false && 
                        strpos($e->getMessage(), 'Duplicate column') === false) {
                        echo "<div style='color: orange; font-weight: bold; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;'>";
                        echo "⚠ Warning: " . $e->getMessage();
                        echo "</div>";
                    }
                }
            }
        }
        
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ Itinerary functionality added successfully!";
        echo "</div>";
        
        echo "<div style='padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 10px 0;'>";
        echo "<h3>Features Added:</h3>";
        echo "<ul>";
        echo "<li>✓ Enhanced packages table with itinerary fields</li>";
        echo "<li>✓ Detailed day-by-day itinerary system</li>";
        echo "<li>✓ Structured inclusions/exclusions management</li>";
        echo "<li>✓ Multiple package images support</li>";
        echo "<li>✓ Sample data for all existing packages</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<div style='padding: 10px; background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 4px; margin: 10px 0;'>";
        echo "<h3>New Tables Created:</h3>";
        echo "<ul>";
        echo "<li><strong>itinerary_details</strong> - Day-by-day itinerary information</li>";
        echo "<li><strong>package_inclusions</strong> - Structured inclusions/exclusions</li>";
        echo "<li><strong>package_images</strong> - Multiple images per package</li>";
        echo "</ul>";
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
    echo "<li>Ensure database 'travel_website_db' exists</li>";
    echo "<li>Run setup_database.php first if main tables don't exist</li>";
    echo "<li>Check database credentials</li>";
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
    <title>Setup Itinerary - Travel Website</title>
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
            <a href="../package.php" class="btn btn-success">View Packages</a>
            <a href="../admin_setup/admin.php" class="btn">Admin Panel</a>
        </div>
    </div>
</body>
</html>
