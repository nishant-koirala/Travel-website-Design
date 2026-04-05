<?php
// Debug Chat History - No Authentication Required
echo "<h2>Debug Chat History</h2>";

try {
    include 'database/db_connect.php';
    
    echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✅ Database connection successful";
    echo "</div>";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'chatbot_messages'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✅ chatbot_messages table exists";
        echo "</div>";
        
        // Get message count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM chatbot_messages");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<div style='padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 10px 0;'>";
        echo "<p><strong>Total Messages:</strong> " . $result['count'] . "</p>";
        echo "</div>";
        
        // Get all messages
        $stmt = $pdo->query("SELECT * FROM chatbot_messages ORDER BY created_at DESC");
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($messages) {
            echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
            echo "✅ " . count($messages) . " messages found";
            echo "</div>";
            
            echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
            echo "<tr style='background: #f8f9fa;'>";
            echo "<th>ID</th><th>Session</th><th>Sender</th><th>Message</th><th>Type</th><th>Intent</th><th>Category</th><th>AI Used</th><th>Time</th>";
            echo "</tr>";
            
            foreach ($messages as $msg) {
                $rowColor = $msg['sender'] === 'user' ? '#e8f5e8' : '#f0f8ff';
                echo "<tr style='background: $rowColor;'>";
                echo "<td>" . $msg['id'] . "</td>";
                echo "<td><small>" . htmlspecialchars(substr($msg['session_id'], 0, 8)) . "...</small></td>";
                echo "<td><strong>" . htmlspecialchars($msg['sender']) . "</strong></td>";
                echo "<td>" . htmlspecialchars(substr($msg['message'], 0, 100)) . (strlen($msg['message']) > 100 ? '...' : '') . "</td>";
                echo "<td>" . htmlspecialchars($msg['message_type']) . "</td>";
                echo "<td>" . htmlspecialchars($msg['intent']) . "</td>";
                echo "<td>" . htmlspecialchars($msg['category']) . "</td>";
                echo "<td>" . ($msg['ai_used'] ? 'Yes' : 'No') . "</td>";
                echo "<td><small>" . htmlspecialchars($msg['created_at']) . "</small></td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Show latest message details
            $latest = $messages[0];
            echo "<div style='padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;'>";
            echo "<h4>Latest Message Details:</h4>";
            echo "<p><strong>Session:</strong> " . htmlspecialchars($latest['session_id']) . "</p>";
            echo "<p><strong>Sender:</strong> " . htmlspecialchars($latest['sender']) . "</p>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($latest['message']) . "</p>";
            echo "<p><strong>Type:</strong> " . htmlspecialchars($latest['message_type']) . "</p>";
            echo "<p><strong>Intent:</strong> " . htmlspecialchars($latest['intent']) . "</p>";
            echo "<p><strong>Category:</strong> " . htmlspecialchars($latest['category']) . "</p>";
            echo "<p><strong>AI Used:</strong> " . ($latest['ai_used'] ? 'Yes' : 'No') . "</p>";
            echo "<p><strong>Created:</strong> " . htmlspecialchars($latest['created_at']) . "</p>";
            echo "</div>";
            
        } else {
            echo "<div style='color: orange; font-weight: bold; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;'>";
            echo "⚠ No messages found in table";
            echo "</div>";
        }
        
    } else {
        echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "❌ chatbot_messages table does not exist";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
}

// Add a test message
echo "<div style='padding: 20px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 20px 0;'>";
echo "<h3>Add Test Message:</h3>";
echo "<form method='post'>";
echo "<input type='text' name='test_message' placeholder='Enter test message' style='padding: 5px; margin: 5px; width: 300px;'>";
echo "<input type='submit' value='Add Test Message' style='padding: 5px 15px; margin: 5px;'>";
echo "</form>";
echo "</div>";

if ($_POST['test_message']) {
    try {
        $sessionId = 'debug_' . date('YmdHis');
        $message = $_POST['test_message'];
        
        $stmt = $pdo->prepare("INSERT INTO chatbot_messages (session_id, message, sender, message_type, intent, category, ai_used) VALUES (?, ?, 'user', 'test', 'TEST', 'test', 0)");
        $stmt->execute([$sessionId, $message]);
        
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✅ Test message added successfully";
        echo "</div>";
        
        // Add AI response
        $aiResponse = "This is a test AI response for: " . $message;
        $stmt = $pdo->prepare("INSERT INTO chatbot_messages (session_id, message, sender, message_type, intent, category, ai_used) VALUES (?, ?, 'ai', 'test', 'TEST', 'test', 0)");
        $stmt->execute([$sessionId, $aiResponse]);
        
        echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "✅ Test AI response added";
        echo "</div>";
        
        echo "<script>setTimeout(() => window.location.reload(), 2000);</script>";
        
    } catch (Exception $e) {
        echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
        echo "❌ Error adding test message: " . $e->getMessage();
        echo "</div>";
    }
}

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='admin_setup/chatbot_history.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 10px;'>Try Admin History</a>";
echo "<a href='test_chatbot_api.php' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; margin: 10px;'>Test API Again</a>";
echo "</div>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug Chat History - Travel Website</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 50px auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #333; }
        table { font-size: 12px; }
        td, th { padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <p style="text-align: center; margin-top: 30px;">
            <small>This debug page shows all chat messages without authentication requirements.</small>
        </p>
    </div>
</body>
</html>
