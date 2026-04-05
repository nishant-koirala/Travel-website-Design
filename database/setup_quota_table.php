<?php
// Setup Chatbot Quota Table
include 'db_connect.php';

echo "<h2>Chatbot Quota Table Setup</h2>";

try {
    // Create quota table
    $createTableSql = "CREATE TABLE IF NOT EXISTS chatbot_system_quota (
        id INT PRIMARY KEY,
        api_call_limit INT NOT NULL DEFAULT 100,
        period_calls INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($createTableSql);
    
    echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✅ chatbot_system_quota table created successfully";
    echo "</div>";
    
    // Insert default quota record
    $insertSql = "INSERT IGNORE INTO chatbot_system_quota (id, api_call_limit, period_calls) VALUES (1, 100, 0)";
    $pdo->exec($insertSql);
    
    echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✅ Default quota record inserted (100 calls limit, 0 used)";
    echo "</div>";
    
    // Check current quota
    $stmt = $pdo->query("SELECT api_call_limit, period_calls FROM chatbot_system_quota WHERE id = 1");
    $quota = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($quota) {
        echo "<div style='padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 10px 0;'>";
        echo "<p><strong>Current Quota:</strong></p>";
        echo "<ul>";
        echo "<li>API Call Limit: " . $quota['api_call_limit'] . "</li>";
        echo "<li>Period Calls Used: " . $quota['period_calls'] . "</li>";
        echo "<li>Remaining Calls: " . ($quota['api_call_limit'] - $quota['period_calls']) . "</li>";
        echo "</ul>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='../admin_setup/api_dashboard.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px;'>View API Dashboard</a>";
echo "<a href='../admin_setup/admin.php' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px;'>Admin Panel</a>";
echo "</div>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chatbot Quota Setup - Travel Website</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
    </style>
</head>
<body>
    <div class="container">
        <p style="text-align: center; margin-top: 30px;">
            <small>Quota table has been set up successfully!</small>
        </p>
    </div>
</body>
</html>
