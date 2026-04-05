<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/aiService.php';

function chatbot_ensure_messages_table(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS chatbot_messages (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
}

function chatbot_packages_has_category(PDO $pdo): bool
{
    static $has = null;
    if ($has !== null) {
        return $has;
    }
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM packages LIKE 'category'");
        $has = $stmt && $stmt->rowCount() > 0;
    } catch (Throwable $e) {
        $has = false;
    }
    return $has;
}

function chatbot_detect_intent_category(string $text): array
{
    $t = mb_strtolower($text, 'UTF-8');
    $intent = 'GENERAL';
    $category = 'general';

    if (preg_match('/\b(cheap|cheapest|budget|lowest|affordable)\b/u', $t)) {
        $intent = 'CHEAPEST';
        $category = 'budget';
    } elseif (preg_match('/\b(expensive|luxury|premium|priciest|highest)\b/u', $t)) {
        $intent = 'LUXURY';
        $category = 'luxury';
    } elseif (preg_match('/\b(trek|trekking|himalaya|everest|annapurna|langtang|nepal\s+trek)\b/u', $t)) {
        $intent = 'TREK';
        $category = 'trek';
    } elseif (preg_match('/\b(beach|coast|ocean|sea)\b/u', $t)) {
        $intent = 'BEACH';
        $category = 'beach';
    } elseif (preg_match('/\b(city|urban|museum|sightseeing)\b/u', $t)) {
        $intent = 'CITY';
        $category = 'city';
    } elseif (preg_match('/\b(safari|wildlife)\b/u', $t)) {
        $intent = 'SAFARI';
        $category = 'safari';
    } elseif (preg_match('/\b(island|tropical)\b/u', $t)) {
        $intent = 'ISLAND';
        $category = 'island';
    }

    return [$intent, $category];
}

function chatbot_fetch_packages(PDO $pdo, string $category): array
{
    $rows = [];
    if (chatbot_packages_has_category($pdo) && $category !== '' && $category !== 'general') {
        $stmt = $pdo->prepare('SELECT * FROM packages WHERE category LIKE :c LIMIT 5');
        $stmt->execute([':c' => '%' . $category . '%']);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    if ($rows === []) {
        $stmt = $pdo->query('SELECT * FROM packages LIMIT 5');
        $rows = $stmt ? ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];
    }
    return $rows;
}

function chatbot_fallback_from_packages(array $packages): string
{
    if ($packages === []) {
        return 'We could not load packages right now. Please try again later.';
    }
    $lines = ['Here are some options from our catalog:'];
    foreach ($packages as $p) {
        if (!is_array($p)) {
            continue;
        }
        $loc = $p['location'] ?? $p['destination'] ?? $p['region'] ?? $p['place'] ?? '—';
        $lines[] = ($p['title'] ?? 'Package') . ' — ' . $loc . ' — ' . ($p['price'] ?? '');
    }
    return implode("\n", $lines);
}

chatbot_ensure_messages_table($pdo);

$showContactForm = false;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['reply' => 'Method not allowed.', 'show_contact_form' => false]);
    exit;
}

// Check API quota before processing
$quotaStmt = $pdo->query("SELECT api_call_limit, period_calls FROM chatbot_system_quota WHERE id = 1");
$quota = $quotaStmt->fetch(PDO::FETCH_ASSOC);

$apiLimit = $quota['api_call_limit'] ?? 100;
$periodCalls = $quota['period_calls'] ?? 0;

if ($periodCalls >= $apiLimit) {
    echo json_encode([
        'reply' => 'API limit reached. Please purchase more credits to continue using the chatbot.',
        'show_contact_form' => false,
        'limit_reached' => true
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$message = is_array($data) && isset($data['message']) ? trim((string) $data['message']) : '';

$sessionId = session_id();
if ($sessionId === '') {
    $sessionId = 'anon_' . bin2hex(random_bytes(8));
}

if ($message === '') {
    $ins = $pdo->prepare(
        'INSERT INTO chatbot_messages (session_id, message, sender, message_type, intent, category, ai_used)
         VALUES (?, ?, \'user\', \'user\', \'EMPTY\', \'\', 0)'
    );
    $ins->execute([$sessionId, '[empty request]']);
    echo json_encode(['reply' => 'Please type a message.', 'show_contact_form' => false], JSON_UNESCAPED_UNICODE);
    exit;
}

[$intent, $category] = chatbot_detect_intent_category($message);

$showContactForm = (bool) preg_match(
    '/\b(human|agent|contact\s+form|representative)\b/i',
    $message
);

$insUser = $pdo->prepare(
    'INSERT INTO chatbot_messages (session_id, message, sender, message_type, intent, category, ai_used)
     VALUES (?, ?, \'user\', \'user\', ?, ?, 0)'
);
$insUser->execute([$sessionId, $message, $intent, $category]);

$packages = chatbot_fetch_packages($pdo, $category);
$packagesJson = json_encode($packages, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
if ($packagesJson === false) {
    $packagesJson = '[]';
}

$geminiOk = false;
$aiText = gemini_travel_assistant($message, $packagesJson, $geminiOk);

if ($geminiOk && $aiText !== '') {
    $reply = $aiText;
    $msgType = 'recommendation';
    $aiUsed = 1;
} else {
    // Force test mode - comment out fallback to test if Gemini is working
    // $reply = chatbot_fallback_from_packages($packages);
    // $msgType = 'fallback';
    // $aiUsed = 0;
    
    // Test mode: Force AI response even if geminiOk is false
    if ($aiText !== '') {
        $reply = $aiText;
        $msgType = 'recommendation';
        $aiUsed = 1;
        $geminiOk = true; // Force to true for testing
    } else {
        $reply = chatbot_fallback_from_packages($packages);
        $msgType = 'fallback';
        $aiUsed = 0;
    }
}

$insAi = $pdo->prepare(
    'INSERT INTO chatbot_messages (session_id, message, sender, message_type, intent, category, ai_used)
     VALUES (?, ?, \'ai\', ?, ?, ?, ?)'
);
$insAi->execute([$sessionId, $reply, $msgType, $intent, $category, $aiUsed]);

echo json_encode([
    'reply'             => $reply,
    'show_contact_form' => $showContactForm,
], JSON_UNESCAPED_UNICODE);
