<?php
// Include the database connection
include 'database/db_connect.php'; // Adjust the path as needed

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input data
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
    $message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';

    // Prepare SQL query
    $sql = "INSERT INTO messages (name, email, phone, message) VALUES (:name, :email, :phone, :message)";
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':message', $message);

    // Execute query and check if it was successful
    try {
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['status' => 'error', 'message' => 'Failed to submit your message: ' . $errorInfo[2]]);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
