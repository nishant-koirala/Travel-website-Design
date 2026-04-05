<?php
// Setup Chatbot Tables Script
include 'db_connect.php';

echo "<h2>Chatbot Tables Setup</h2>";

try {
    // Check if chatbot_messages table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'chatbot_messages'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ chatbot_messages table already exists";
        echo "</div>";
    } else {
        // Create chatbot_messages table
        $createTableSql = "CREATE TABLE chatbot_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            session_id VARCHAR(64) NOT NULL DEFAULT '',
            message TEXT NOT NULL,
            sender ENUM('user', 'ai') NOT NULL,
            message_type VARCHAR(32) NOT NULL DEFAULT 'general',
            intent VARCHAR(64) NOT NULL DEFAULT 'GENERAL',
            category VARCHAR(128) NOT NULL DEFAULT '',
            ai_used TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_session (session_id),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($createTableSql);
        
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ chatbot_messages table created successfully";
        echo "</div>";
    }
    
    // Check if packages table has category column
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM packages LIKE 'category'");
        $categoryExists = $stmt->rowCount() > 0;
        
        if ($categoryExists) {
            echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
            echo "✓ packages table already has category column";
            echo "</div>";
        } else {
            // Add category column to packages table
            $pdo->exec("ALTER TABLE packages ADD COLUMN category VARCHAR(128) NULL DEFAULT NULL AFTER description");
            
            echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
            echo "✓ category column added to packages table";
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "<div style='color: orange; font-weight: bold; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;'>";
        echo "⚠ Could not add category column to packages table: " . $e->getMessage();
        echo "</div>";
    }
    
    // Test inserting a sample message
    try {
        $stmt = $pdo->prepare("INSERT INTO chatbot_messages (session_id, message, sender, message_type, intent, category, ai_used) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['test_session', 'Test message for setup verification', 'user', 'test', 'TEST', 'test', 0]);
        
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ Test message inserted successfully";
        echo "</div>";
        
        // Clean up test message
        $pdo->exec("DELETE FROM chatbot_messages WHERE session_id = 'test_session'");
        
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✓ Test message cleaned up";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✗ Error inserting test message: " . $e->getMessage();
        echo "</div>";
    }
    
    echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✓ Chatbot tables setup completed successfully!";
    echo "</div>";
    
    echo "<div style='padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 10px 0;'>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Test the chatbot on the main website</li>";
    echo "<li>Check chat history in admin panel: <a href='../admin_setup/chatbot_history.php' target='_blank'>Chatbot History</a></li>";
    echo "<li>Messages will now be automatically saved to the database</li>";
    echo "</ol>";
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
    <title>Chatbot Tables Setup - Travel Website</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #333; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
        .btn-success { background: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: center; margin-top: 30px;">
            <a href="../admin_setup/chatbot_history.php" class="btn btn-success">View Chat History</a>
            <a href="../index.php" class="btn">Test Chatbot</a>
        </div>
    </div>
</body>
</html>
