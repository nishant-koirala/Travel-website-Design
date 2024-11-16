<?php



try {
   // Database configuration
$servername = "localhost";  // Your server name
$username = "root";         // Your database username
$password = "";             // Your database password
$dbname = "traveldb";  // Your database name

    // Data Source Name (DSN) for MySQL
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8";

    // Create a new PDO instance
    $pdo = new PDO($dsn, $username, $password);

    // Set PDO error mode to throw exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   
} catch (PDOException $e) {
    // Log the error (optional, for debugging)
    error_log("Connection failed: " . $e->getMessage());

    // Display a user-friendly message, without exposing sensitive details
    die("Could not connect to the database. Please try again later.");
}
?>
