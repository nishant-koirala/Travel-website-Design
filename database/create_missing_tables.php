<?php
/**
 * Create Missing Tables Script
 * This script will create the missing itinerary and related tables
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
    
    echo "<h2>Create Missing Tables</h2>";
    
    // Create itinerary_details table
    $itineraryTableSql = "
    CREATE TABLE IF NOT EXISTS itinerary_details (
        id INT AUTO_INCREMENT PRIMARY KEY,
        package_id INT NOT NULL,
        day_number INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        meals VARCHAR(255),
        activities TEXT,
        accommodation VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_package_id (package_id),
        INDEX idx_day_number (day_number),
        UNIQUE KEY unique_package_day (package_id, day_number)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    try {
        $pdo->exec($itineraryTableSql);
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ itinerary_details table created/verified";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='color: orange; font-weight: bold; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;'>";
        echo "⚠ itinerary_details table: " . $e->getMessage();
        echo "</div>";
    }
    
    // Create package_inclusions table
    $inclusionsTableSql = "
    CREATE TABLE IF NOT EXISTS package_inclusions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        package_id INT NOT NULL,
        inclusion_type ENUM('inclusion', 'exclusion') NOT NULL,
        item VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_package_id (package_id),
        INDEX idx_inclusion_type (inclusion_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    try {
        $pdo->exec($inclusionsTableSql);
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ package_inclusions table created/verified";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='color: orange; font-weight: bold; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;'>";
        echo "⚠ package_inclusions table: " . $e->getMessage();
        echo "</div>";
    }
    
    // Create package_images table
    $imagesTableSql = "
    CREATE TABLE IF NOT EXISTS package_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        package_id INT NOT NULL,
        image_name VARCHAR(255) NOT NULL,
        image_caption VARCHAR(255),
        is_primary BOOLEAN DEFAULT FALSE,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_package_id (package_id),
        INDEX idx_is_primary (is_primary)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    try {
        $pdo->exec($imagesTableSql);
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ package_images table created/verified";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='color: orange; font-weight: bold; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;'>";
        echo "⚠ package_images table: " . $e->getMessage();
        echo "</div>";
    }
    
    // Add foreign key constraints if they don't exist
    try {
        $pdo->exec("ALTER TABLE itinerary_details ADD CONSTRAINT fk_itinerary_package FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE");
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ itinerary_details foreign key added";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='color: blue; font-weight: bold; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0;'>";
        echo "ℹ itinerary_details foreign key: " . $e->getMessage();
        echo "</div>";
    }
    
    try {
        $pdo->exec("ALTER TABLE package_inclusions ADD CONSTRAINT fk_inclusions_package FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE");
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ package_inclusions foreign key added";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='color: blue; font-weight: bold; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0;'>";
        echo "ℹ package_inclusions foreign key: " . $e->getMessage();
        echo "</div>";
    }
    
    try {
        $pdo->exec("ALTER TABLE package_images ADD CONSTRAINT fk_images_package FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE");
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ package_images foreign key added";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='color: blue; font-weight: bold; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0;'>";
        echo "ℹ package_images foreign key: " . $e->getMessage();
        echo "</div>";
    }
    
    // Insert sample itinerary data if tables are empty
    $checkItinerary = $pdo->query("SELECT COUNT(*) as count FROM itinerary_details")->fetch(PDO::FETCH_ASSOC)['count'];
    if ($checkItinerary == 0) {
        echo "<div style='color: blue; font-weight: bold; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0;'>";
        echo "ℹ Adding sample itinerary data...";
        echo "</div>";
        
        // Sample data for Beach Paradise (ID 1)
        $sampleItinerary = [
            [1, 1, 'Arrival and Beach Welcome', 'Arrive at the airport, transfer to beach resort, welcome drink and orientation', 'Dinner', 'Airport transfer, Check-in, Beach walk', 'Ocean View Resort'],
            [1, 2, 'Beach Exploration Day', 'Full day beach activities with water sports', 'Breakfast, Lunch', 'Swimming, Snorkeling, Beach volleyball', 'Ocean View Resort'],
            [1, 3, 'Island Hopping Tour', 'Visit nearby islands and pristine beaches', 'Breakfast, Lunch', 'Boat tour, Island hopping, Snorkeling', 'Ocean View Resort'],
            [1, 4, 'Cultural Experience', 'Visit local villages and experience local culture', 'Breakfast, Lunch', 'Village tour, Local market visit, Cultural show', 'Ocean View Resort'],
            [1, 5, 'Water Sports Adventure', 'Adventure water sports activities', 'Breakfast, Lunch', 'Jet skiing, Parasailing, Banana boat', 'Ocean View Resort'],
            [1, 6, 'Relaxation and Spa', 'Free day for relaxation and spa treatments', 'Breakfast', 'Spa treatment, Beach relaxation', 'Ocean View Resort'],
            [1, 7, 'Departure', 'Final breakfast and airport transfer', 'Breakfast', 'Check-out, Airport transfer', '']
        ];
        
        foreach ($sampleItinerary as $data) {
            $stmt = $pdo->prepare("INSERT INTO itinerary_details (package_id, day_number, title, description, meals, activities, accommodation) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute($data);
        }
        
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ Sample itinerary data added";
        echo "</div>";
    }
    
    echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✓ Missing tables created successfully!";
    echo "</div>";
    
    echo "<div style='padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 10px 0;'>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='../package_details.php?id=1' style='color: #667eea; text-decoration: none;'>Test Package Details Page</a></li>";
    echo "<li><a href='../package_enhanced.php' style='color: #667eea; text-decoration: none;'>View Enhanced Package List</a></li>";
    echo "<li><a href='../book_enhanced.php?package=Beach%20Paradise&price=599.99' style='color: #667eea; text-decoration: none;'>Test Enhanced Booking</a></li>";
    echo "</ol>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✗ Database Error: " . $e->getMessage();
    echo "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Missing Tables - Travel Website</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #333; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
        .btn-success { background: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: center; margin-top: 30px;">
            <a href="../package_details.php?id=1" class="btn btn-success">Test Package Details</a>
            <a href="../package_enhanced.php" class="btn">Package List</a>
        </div>
    </div>
</body>
</html>
