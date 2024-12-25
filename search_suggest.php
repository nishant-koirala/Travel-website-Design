<?php
include 'database/db_connect.php';  // Include database connection

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $query = $_GET['q'];
    
    // Fetch packages that match the search query
    $sql = "SELECT title FROM packages WHERE title LIKE :query LIMIT 5"; // Adjust the query to match your database structure
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['query' => "%$query%"]);
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($packages) {
        foreach ($packages as $package) {
            echo '<div class="suggestion-item" onclick="selectSuggestion(\'' . htmlspecialchars($package['title']) . '\')">' . htmlspecialchars($package['title']) . '</div>';
        }
    } else {
        echo '<div>No results found.</div>';
    }
}
?>
