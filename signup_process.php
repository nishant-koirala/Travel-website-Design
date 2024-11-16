<?php
session_start(); // Start the session to store the error message

// Database connection
include 'database/db_connect.php'; // Ensure you have this file with database connection details

// Initialize error message variable
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Input sanitization
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $name = htmlspecialchars($_POST['name']);
    $address = htmlspecialchars($_POST['address']);
    $phone = htmlspecialchars($_POST['phone']);
    $dob = htmlspecialchars($_POST['dob']);

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }

    // Password validation: Minimum 8 characters, at least one letter and one number
    elseif (strlen($password) < 8 || !preg_match("/[a-z]/i", $password) || !preg_match("/[0-9]/", $password)) {
        $error = "Password must be at least 8 characters long and include at least one letter and one number.";
    }

    // Name validation: Only allow letters, spaces, hyphens, and apostrophes
    elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $name)) {
        $error = "Name must only contain letters, spaces, hyphens, and apostrophes.";
    }

    // Address validation: Ensure it's not empty
    elseif (empty($address)) {
        $error = "Address cannot be empty.";
    }

    // Phone validation: Check that it's a valid phone number (digits only, with optional country code)
    elseif (!preg_match("/^\+?[0-9]{10,15}$/", $phone)) {
        $error = "Phone number must be a valid number with 10 to 15 digits.";
    }

    // Date of Birth validation: Check if it's a valid date and user is at least 18 years old
    else {
        $date_now = new DateTime();
        $date_dob = DateTime::createFromFormat('Y-m-d', $dob);
        if (!$date_dob || $date_dob > $date_now || $date_now->diff($date_dob)->y < 18) {
            $error = "Invalid date of birth or you must be at least 18 years old.";
        }
    }

    // If no errors, attempt to register the user
    if (empty($error)) {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $error = "Email already registered.";
            } else {
                // Insert new user
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, address, phone, dob) VALUES (:name, :email, :password, :address, :phone, :dob)");
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'address' => $address,
                    'phone' => $phone,
                    'dob' => $dob
                ]);
                header('Location: login.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error = "An error occurred, please try again later.";
        }
    }

    // Store error message in session and redirect to signup page
    if (!empty($error)) {
        $_SESSION['error'] = $error;
        header('Location: signup.php'); // Redirect back to the signup form page
        exit;
    }
}
?>
