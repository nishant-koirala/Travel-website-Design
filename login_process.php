<?php
// login_process.php

session_start();

// Database connection
include 'database/db_connect.php'; // Ensure this file contains the correct database connection

// Dummy admin credentials (for demonstration purposes)
$admin_email = "nishantkoirala16@gmail.com";
$admin_password = "admin123"; // Change this to your desired password

// Get form data
$email = $_POST['email'];
$password = $_POST['password'];

try {
    // Check if the email is for the admin
    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['user'] = 'admin';
        $_SESSION['username'] = 'Admin';
        $_SESSION['user_id'] = null;
        header('Location: admin_setup/admin.php');
        exit();
    } else {
        // Check customer credentials in the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Verify the user's password (assuming it's hashed in the database)
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['role'] === 'admin' ? 'admin' : 'customer';
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            if ($user['role'] === 'admin') {
                header('Location: admin_setup/admin.php');
            } else {
                header('Location: package.php');
            }
            exit();
        } else {
            // Invalid credentials, redirect back to login with error
            $_SESSION['error_message'] = "Invalid email or password. Please try again.";
            header('Location: login.php');
            exit();
        }
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error_message'] = "An error occurred. Please try again later.";
    header('Location: login.php');
    exit();
}
?>
