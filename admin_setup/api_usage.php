<?php
require_once __DIR__ . '/auth_admin.php';
include __DIR__ . '/../database/db_connect.php';
require_once __DIR__ . '/../chatbot/quota.php';

$flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_demo_quota'])) {
    chatbot_quota_reset($pdo);
    header('Location: api_usage.php?reset=1');
    exit;
}

if (isset($_GET['reset']) && $_GET['reset'] === '1') {
    $flash = 'Demo quota counter reset.';
}

$quota = ['api_call_limit' => 500, 'period_calls' => 0];
$warnLevel = 0;
$totals = ['all_time_ai' => 0];
$bySession = [];
$dbError = '';

try {
    $quota = chatbot_quota_get($pdo);
    $warnLevel = chatbot_quota_warning_level($pdo);

    $totals['all_time_ai'] = (int) $pdo->query(
        "SELECT COUNT(*) FROM chatbot_messages WHERE sender = 'ai' AND ai_used = 1"
    )->fetchColumn();

    $sql = "SELECT session_id,
                   COUNT(*) AS total_calls,
                   MAX(created_at) AS last_used
            FROM chatbot_messages
            WHERE sender = 'ai' AND ai_used = 1
            GROUP BY session_id
            ORDER BY total_calls DESC, last_used DESC";
    $bySession = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('api_usage: ' . $e->getMessage());
    $dbError = 'Could not load stats (need chatbot_messages table).';
}

$limit = (int) $quota['api_call_limit'];
$used = (int) $quota['period_calls'];
$pct = ($limit > 0) ? min(100, (int) round(($used / $limit) * 100)) : 0;

include __DIR__ . '/component/nav_admin.php';
?>
<div class="main-content flex-grow-1 p-4">
    <div class="header mb-4">
        <h1 class="h3">Chatbot API usage</h1>
        <p class="text-muted mb-0">Counts from <code>chatbot_messages</code> where <code>sender = 'ai'</code> and <code>ai_used = 1</code>.</p>
    </div>

    <?php if ($flash !== ''): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($flash); ?></div>
    <?php endif; ?>
    <?php if ($dbError !== ''): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($dbError); ?></div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-<?php echo $warnLevel === 2 ? 'danger' : ($warnLevel === 1 ? 'warning' : 'secondary'); ?>">
                <div class="card-header <?php echo $warnLevel === 2 ? 'bg-danger text-white' : ($warnLevel === 1 ? 'bg-warning' : 'bg-light'); ?>">
                    System quota (chatbot_system_quota)
                </div>
                <div class="card-body">
                    <?php if ($limit <= 0): ?>
                        <p class="mb-2"><strong>Limit:</strong> Unlimited.</p>
                    <?php else: ?>
                        <p class="mb-2"><strong>Period:</strong> <?php echo (int) $used; ?> / <?php echo (int) $limit; ?> (<?php echo (int) $pct; ?>%)</p>
                        <div class="progress mb-3" style="height: 24px;">
                            <div class="progress-bar <?php echo $warnLevel === 2 ? 'bg-danger' : ($warnLevel === 1 ? 'bg-warning text-dark' : 'bg-success'); ?>"
                                 style="width: <?php echo (int) $pct; ?>%;"><?php echo (int) $pct; ?>%</div>
                        </div>
                    <?php endif; ?>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="reset_demo_quota" value="1">
                        <button type="submit" class="btn btn-primary btn-sm">Reset demo quota</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">Gemini rows (chatbot_messages)</div>
                <div class="card-body">
                    <p class="mb-0"><strong>Total AI responses with ai_used = 1:</strong> <?php echo (int) $totals['all_time_ai']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="h5 mb-3">By session</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>session_id</th>
                    <th>AI calls</th>
                    <th>Last</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bySession as $u): ?>
                    <tr>
                        <td><small><?php echo htmlspecialchars($u['session_id'] ?? ''); ?></small></td>
                        <td><?php echo (int) $u['total_calls']; ?></td>
                        <td><small><?php echo htmlspecialchars($u['last_used'] ?? ''); ?></small></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/admin.js" defer></script>
</body>
</html>
