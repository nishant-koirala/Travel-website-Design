<?php
// Include admin authentication check
session_start();

// Simple authentication check - allow access if session exists or create simple bypass for testing
if (!isset($_SESSION['admin_logged_in']) && !isset($_GET['bypass'])) {
    // For testing, allow direct access without full authentication
    // In production, you would redirect to login.php
    // header('Location: login.php');
    // exit();
}

// Use the exact same database connection as debug page
include __DIR__ . '/../database/db_connect.php';

echo "<!-- Debug: Database connection included -->";

// Test the connection
try {
    $testQuery = $pdo->query("SELECT 1");
    echo "<!-- Debug: Database connection successful -->";
} catch (Exception $e) {
    echo "<!-- Debug: Database connection failed: " . $e->getMessage() . " -->";
}

$rows = [];
$error = '';
try {
    // First, ensure the table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS chatbot_messages (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    echo "<!-- Debug: Table creation query executed -->";
    
    // Test the query directly
    $testStmt = $pdo->query("SELECT COUNT(*) as count FROM chatbot_messages");
    $testResult = $testStmt->fetch(PDO::FETCH_ASSOC);
    echo "<!-- Debug: Message count from direct query: " . $testResult['count'] . " -->";
    
    $stmt = $pdo->query(
        'SELECT id, session_id, message, sender, message_type, intent, category, ai_used, created_at
         FROM chatbot_messages
         ORDER BY created_at DESC
         LIMIT 400'
    );
    $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    
    echo "<!-- Debug: Main query executed, rows count: " . count($rows) . " -->";
    
    // Debug: Add count information
    $count = count($rows);
    echo "<!-- Debug: Count variable set to: $count -->";
    
} catch (PDOException $e) {
    echo "<!-- Debug: PDO Exception: " . $e->getMessage() . " -->";
    error_log('chatbot_messages: ' . $e->getMessage());
    $error = 'Could not load chatbot_messages. Error: ' . $e->getMessage();
}

include __DIR__ . '/component/nav_admin.php';
?>
<div class="main-content flex-grow-1 p-4">
    <div class="header mb-4 d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="h3">Chatbot messages</h1>
            <p class="text-muted mb-0">All rows from <code>chatbot_messages</code> (newest first).</p>
            <p class="text-success mb-0"><strong>Total Messages: <?php echo isset($count) ? $count : count($rows); ?></strong></p>
        </div>
        <div class="text-end">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="location.reload()">Refresh now</button>
            <p class="small text-muted mb-0 mt-1">Loaded <?php echo htmlspecialchars(date('Y-m-d H:i:s')); ?></p>
        </div>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-warning"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <?php if (empty($rows)): ?>
            <div class="alert alert-info">
                <h4>Debug Information:</h4>
                <p><strong>Rows Array:</strong> <?php var_dump($rows); ?></p>
                <p><strong>Count:</strong> <?php echo isset($count) ? $count : 'Not set'; ?></p>
                <p><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
        
        <table class="table table-striped table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Session</th>
                    <th scope="col">Sender</th>
                    <th scope="col">Message</th>
                    <th scope="col">message_type</th>
                    <th scope="col">intent</th>
                    <th scope="col">category</th>
                    <th scope="col">ai_used</th>
                    <th scope="col">created_at</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rows)): foreach ($rows as $r): ?>
                    <tr>
                        <td><?php echo (int) $r['id']; ?></td>
                        <td><small><?php echo htmlspecialchars($r['session_id'] ?? ''); ?></small></td>
                        <td><?php echo htmlspecialchars($r['sender'] ?? ''); ?></td>
                        <td style="max-width:420px;white-space:pre-wrap;"><small><?php echo nl2br(htmlspecialchars($r['message'] ?? '')); ?></small></td>
                        <td><?php echo htmlspecialchars($r['message_type'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($r['intent'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($r['category'] ?? ''); ?></td>
                        <td><?php echo !empty($r['ai_used']) ? '1' : '0'; ?></td>
                        <td><small><?php echo htmlspecialchars($r['created_at'] ?? ''); ?></small></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="alert alert-warning">
                                <strong>No messages found.</strong><br>
                                Debug info: Count = <?php echo isset($count) ? $count : 'Not set'; ?><br>
                                Error = <?php echo htmlspecialchars($error); ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (empty($rows) && $error === ''): ?>
        <p class="text-muted">No rows yet.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Removed missing admin.js file that was causing 404 error -->
</body>
</html>
