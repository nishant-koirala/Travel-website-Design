<?php
/**
 * Create Super Admin User Script
 * This script will create a super admin user directly in the database
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
    
    echo "<h2>Create Super Admin User</h2>";
    
    // Check if super admin already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'superadmin@travel.com'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "<div style='color: orange; font-weight: bold; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;'>";
        echo "⚠ Super admin already exists!";
        echo "</div>";
    } else {
        // Create super admin user
        $hashedPassword = password_hash('superadmin', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
        $stmt->execute([
            'name' => 'Super Admin',
            'email' => 'superadmin@travel.com',
            'password' => $hashedPassword,
            'role' => 'admin'
        ]);
        
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ Super admin user created successfully!";
        echo "</div>";
    }
    
    echo "<div style='padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 10px 0;'>";
    echo "<h3>Super Admin Credentials:</h3>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> superadmin@travel.com</li>";
    echo "<li><strong>Password:</strong> superadmin</li>";
    echo "<li><strong>Role:</strong> admin</li>";
    echo "</ul>";
    echo "</div>";
    
    // Display all admin users
    echo "<div style='padding: 10px; background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 4px; margin: 10px 0;'>";
    echo "<h3>All Admin Users:</h3>";
    
    $stmt = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE role = 'admin'");
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($admins) > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($admin['id']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['name']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['role']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No admin users found.</p>";
    }
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✗ Database Error: " . $e->getMessage();
    echo "</div>";
    
    echo "<div style='padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 10px 0;'>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Ensure database 'travel_website_db' exists</li>";
    echo "<li>Check database credentials</li>";
    echo "<li>Run setup_database.php first if database doesn't exist</li>";
    echo "</ul>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Super Admin - Travel Website</title>
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
        .btn-danger {
            background: #dc3545;
        }
        .btn-success {
            background: #28a745;
        }
        table {
            margin-top: 10px;
        }
        th {
            background-color: #f8f9fa;
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
