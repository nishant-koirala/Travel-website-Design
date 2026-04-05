<?php
// Test script to check if packages were updated
include 'db_connect.php';

echo "<h2>Package Database Check</h2>";

try {
    $stmt = $pdo->query("SELECT id, title, duration_days, difficulty_level, accommodation_type FROM packages ORDER BY id");
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0; color: white;'>";
    echo "<th>ID</th><th>Title</th><th>Duration</th><th>Difficulty</th><th>Accommodation</th>";
    echo "</tr>";
    
    foreach ($packages as $package) {
        echo "<tr>";
        echo "<td>" . $package['id'] . "</td>";
        echo "<td>" . htmlspecialchars($package['title']) . "</td>";
        echo "<td>" . ($package['duration_days'] ?? 'N/A') . " days</td>";
        echo "<td>" . ucfirst($package['difficulty_level'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($package['accommodation_type'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Check itinerary details
    echo "<h3>Itinerary Check</h3>";
    $itineraryStmt = $pdo->query("SELECT package_id, COUNT(*) as day_count FROM itinerary_details GROUP BY package_id ORDER BY package_id");
    $itineraryCounts = $itineraryStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0; color: white;'>";
    echo "<th>Package ID</th><th>Days in Itinerary</th>";
    echo "</tr>";
    
    foreach ($itineraryCounts as $count) {
        echo "<tr>";
        echo "<td>" . $count['package_id'] . "</td>";
        echo "<td>" . $count['day_count'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Package Test - Travel Website</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { margin: 20px 0; }
        th { background: #007bff; color: white; padding: 10px; }
        td { padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div style="text-align: center; margin-top: 30px;">
        <a href="../package_details.php?id=1" style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">Test Package Details</a>
        <a href="../package_enhanced.php" style="padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">Package List</a>
    </div>
</body>
</html>
