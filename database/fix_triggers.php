<?php
/**
 * Fix Triggers Script
 * This script will properly create the triggers for MariaDB compatibility
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
    
    echo "<h2>Fix Database Triggers</h2>";
    
    // Drop existing triggers
    echo "<div style='color: blue; font-weight: bold; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0;'>";
    echo "ℹ Dropping existing triggers...";
    echo "</div>";
    
    try {
        $pdo->exec("DROP TRIGGER IF EXISTS after_booking_insert");
        $pdo->exec("DROP TRIGGER IF EXISTS before_booking_update");
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ Existing triggers dropped";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='color: orange; font-weight: bold; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;'>";
        echo "⚠ No existing triggers to drop";
        echo "</div>";
    }
    
    // Create after_booking_insert trigger
    echo "<div style='color: blue; font-weight: bold; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0;'>";
    echo "ℹ Creating after_booking_insert trigger...";
    echo "</div>";
    
    $trigger1 = "
    CREATE TRIGGER after_booking_insert
    AFTER INSERT ON bookings
    FOR EACH ROW
    BEGIN
        INSERT INTO notifications (type, title, message, booking_id)
        VALUES (
            'new_booking',
            CONCAT('New Booking: ', NEW.booking_number),
            CONCAT('New booking received from ', NEW.name, ' for ', NEW.package),
            NEW.id
        );
    END";
    
    try {
        $pdo->exec($trigger1);
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ after_booking_insert trigger created";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✗ Error creating after_booking_insert: " . $e->getMessage();
        echo "</div>";
    }
    
    // Create before_booking_update trigger
    echo "<div style='color: blue; font-weight: bold; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0;'>";
    echo "ℹ Creating before_booking_update trigger...";
    echo "</div>";
    
    $trigger2 = "
    CREATE TRIGGER before_booking_update
    BEFORE UPDATE ON bookings
    FOR EACH ROW
    BEGIN
        IF OLD.status != NEW.status THEN
            INSERT INTO booking_status_history (booking_id, old_status, new_status, change_reason)
            VALUES (OLD.id, OLD.status, NEW.status, CONCAT('Status changed from ', OLD.status, ' to ', NEW.status));
        END IF;
    END";
    
    try {
        $pdo->exec($trigger2);
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ before_booking_update trigger created";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✗ Error creating before_booking_update: " . $e->getMessage();
        echo "</div>";
    }
    
    // Test triggers by checking if they exist
    echo "<div style='color: blue; font-weight: bold; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0;'>";
    echo "ℹ Verifying triggers...";
    echo "</div>";
    
    $stmt = $pdo->query("SHOW TRIGGERS");
    $triggers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div style='padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 10px 0;'>";
    echo "<h3>Active Triggers:</h3>";
    if (count($triggers) > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Trigger</th><th>Event</th><th>Table</th><th>Timing</th></tr>";
        foreach ($triggers as $trigger) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($trigger['Trigger']) . "</td>";
            echo "<td>" . htmlspecialchars($trigger['Event']) . "</td>";
            echo "<td>" . htmlspecialchars($trigger['Table']) . "</td>";
            echo "<td>" . htmlspecialchars($trigger['Timing']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No triggers found.</p>";
    }
    echo "</div>";
    
    echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✓ Trigger setup completed!";
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
    <title>Fix Triggers - Travel Website</title>
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
