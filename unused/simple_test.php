<?php
// Simple test to show package data
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

echo "<h1>Package Update Test</h1>";

try {
    $stmt = $pdo->query("SELECT id, title, duration_days FROM packages ORDER BY id LIMIT 3");
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Current Packages:</h2>";
    foreach ($packages as $package) {
        echo "<p><strong>ID " . $package['id'] . ":</strong> " . htmlspecialchars($package['title']) . " (" . ($package['duration_days'] ?? 'N/A') . " days)</p>";
    }
    
    // Test itinerary
    $itineraryStmt = $pdo->query("SELECT COUNT(*) as count FROM itinerary_details WHERE package_id = 1");
    $count = $itineraryStmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h2>Everest Itinerary Count: " . $count['count'] . " days</h2>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error: " . $e->getMessage() . "</h2>";
}
?>
