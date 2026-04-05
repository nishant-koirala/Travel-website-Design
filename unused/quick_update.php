<?php
/**
 * Quick Database Update Script
 * Add missing fields to packages table
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
    
    echo "<h2>Quick Database Update</h2>";
    
    // Check if columns exist and add them if they don't
    $columns = [
        'duration_days' => "INT DEFAULT 1",
        'itinerary' => "TEXT NULL",
        'includes' => "TEXT NULL",
        'excludes' => "TEXT NULL",
        'difficulty_level' => "ENUM('easy', 'moderate', 'challenging') DEFAULT 'easy'",
        'accommodation_type' => "VARCHAR(255) NULL",
        'transportation' => "VARCHAR(255) NULL"
    ];
    
    foreach ($columns as $column => $definition) {
        try {
            $checkSql = "SHOW COLUMNS FROM packages LIKE '$column'";
            $stmt = $pdo->query($checkSql);
            
            if ($stmt->rowCount() == 0) {
                $alterSql = "ALTER TABLE packages ADD COLUMN $column $definition";
                $pdo->exec($alterSql);
                echo "<div style='color: green;'>✓ Added column: $column</div>";
            } else {
                echo "<div style='color: blue;'>ℹ Column $column already exists</div>";
            }
        } catch (Exception $e) {
            echo "<div style='color: orange;'>⚠ Error with $column: " . $e->getMessage() . "</div>";
        }
    }
    
    // Update existing packages with default values
    $updates = [
        'Beach Paradise' => [
            'duration_days' => 7,
            'includes' => 'Airport transfers, Hotel accommodation, Daily breakfast, Guided tours, Entrance fees',
            'excludes' => 'Lunch and dinner, Personal expenses, Travel insurance, Tips',
            'difficulty_level' => 'moderate',
            'accommodation_type' => '3-Star Hotels',
            'transportation' => 'Private vehicle with driver'
        ],
        'Mountain Adventure' => [
            'duration_days' => 5,
            'includes' => 'Mountain guide, Climbing equipment, Accommodation, All meals, Insurance',
            'excludes' => 'Personal gear, Alcohol, Tips, Extra activities',
            'difficulty_level' => 'challenging',
            'accommodation_type' => 'Mountain Lodges',
            'transportation' => '4x4 Vehicle'
        ],
        'City Explorer' => [
            'duration_days' => 3,
            'includes' => 'City guide, Museum entries, Hotel accommodation, Daily breakfast',
            'excludes' => 'Lunch and dinner, Shopping, Personal expenses',
            'difficulty_level' => 'easy',
            'accommodation_type' => '4-Star Hotels',
            'transportation' => 'Public transport + Walking'
        ],
        'Safari Experience' => [
            'duration_days' => 6,
            'includes' => 'Safari drives, Park fees, Accommodation, All meals, Guide services',
            'excludes' => 'International flights, Travel insurance, Tips, Alcoholic beverages',
            'difficulty_level' => 'moderate',
            'accommodation_type' => 'Safari Lodges',
            'transportation' => 'Safari vehicles'
        ],
        'Island Getaway' => [
            'duration_days' => 4,
            'includes' => 'Island transfers, Beach resort, Daily breakfast, Water sports equipment',
            'excludes' => 'Lunch and dinner, Personal expenses, Travel insurance',
            'difficulty_level' => 'easy',
            'accommodation_type' => 'Beach Resort',
            'transportation' => 'Speed boat + Private vehicle'
        ]
    ];
    
    foreach ($updates as $packageName => $data) {
        try {
            $setClause = [];
            $params = [];
            
            foreach ($data as $field => $value) {
                $setClause[] = "$field = ?";
                $params[] = $value;
            }
            
            $params[] = $packageName;
            
            $updateSql = "UPDATE packages SET " . implode(', ', $setClause) . " WHERE title = ?";
            $stmt = $pdo->prepare($updateSql);
            $stmt->execute($params);
            
            echo "<div style='color: green;'>✓ Updated package: $packageName</div>";
        } catch (Exception $e) {
            echo "<div style='color: orange;'>⚠ Error updating $packageName: " . $e->getMessage() . "</div>";
        }
    }
    
    echo "<div style='color: green; font-weight: bold; margin-top: 20px;'>✓ Database update completed!</div>";
    
} catch (PDOException $e) {
    echo "<div style='color: red;'>✗ Database Error: " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quick Update - Travel Website</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
    </style>
</head>
<body>
    <div style="text-align: center; margin-top: 30px;">
        <a href="../book_enhanced.php?package=Mountain%20Adventure&price=799.99" class="btn">Test Booking Page</a>
        <a href="../package_enhanced.php" class="btn">View Packages</a>
    </div>
</body>
</html>
