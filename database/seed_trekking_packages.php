<?php
/**
 * Seed Nepal trekking packages (skip if title already exists).
 * Run once: php database/seed_trekking_packages.php
 * Or open in browser: /database/seed_trekking_packages.php?run=1
 */
require_once __DIR__ . '/db_connect.php';

$isCli = (PHP_SAPI === 'cli');
if (!$isCli && (!isset($_GET['run']) || $_GET['run'] !== '1')) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<p>Add <code>?run=1</code> to this URL to seed trekking packages.</p>';
    exit;
}

if (!$isCli) {
    header('Content-Type: text/html; charset=utf-8');
}

$packages = [
    ['Everest Base Camp Trek — 14 Days', 'Classic Himalayan trek from Lukla to Everest Base Camp (5364m). Teahouse lodges, acclimatization days, stunning Khumbu views. Best Mar–May & Sep–Nov. Guided group departures.', 1899.00, 'everest-trek.jpg'],
    ['Annapurna Circuit Trek — 16 Days', 'Full circuit crossing Thorong La Pass (5416m). Diverse landscapes from subtropical valleys to high desert. Tea houses and cultural villages. Iconic Nepal trekking experience.', 1650.00, 'annapurna-circuit.jpg'],
    ['Langtang Valley Trek — 10 Days', 'Closer to Kathmandu; lush forests, Tamang culture, views of Langtang Lirung. Moderate difficulty — great first high-altitude trek in Nepal.', 980.00, 'langtang.jpg'],
    ['Mardi Himal Trek — 9 Days', 'Shorter trek with close-up views of Machhapuchhre and Annapurna. Less crowded trails, ridge-line camps, romantic sunsets — ideal for couples.', 890.00, 'mardi-himal.jpg'],
    ['Ghorepani Poon Hill Trek — 7 Days', 'Gentle trek; sunrise over Annapurna and Dhaulagiri from Poon Hill. Perfect for beginners and families; rhododendron forests in spring.', 650.00, 'poon-hill.jpg'],
    ['Manaslu Circuit Trek — 18 Days', 'Remote circuit around the eighth-highest peak; restricted area permit trek. Dramatic gorges, Tibetan-influenced villages, Larkya La pass.', 2199.00, 'manaslu.jpg'],
    ['Upper Mustang Trek — 14 Days', 'Forbidden Kingdom; arid Tibetan plateau landscapes, ancient monasteries, unique culture. Special permit required — premium adventure.', 2450.00, 'mustang.jpg'],
    ['Helambu Trek — 8 Days', 'Easy–moderate trek near Kathmandu; Sherpa villages, Buddhist stupas, great short itinerary for limited time.', 720.00, 'helambu.jpg'],
];

try {
    $check = $pdo->prepare('SELECT id FROM packages WHERE title = ? LIMIT 1');
    $ins = $pdo->prepare('INSERT INTO packages (title, description, price, image) VALUES (?, ?, ?, ?)');
    $added = 0;
    $skipped = 0;
    foreach ($packages as $p) {
        $check->execute([$p[0]]);
        if ($check->fetch()) {
            $skipped++;
            continue;
        }
        $ins->execute([$p[0], $p[1], $p[2], $p[3]]);
        $added++;
    }
    $msg = "Done. Added $added packages, skipped $skipped (already existed).";
    echo $isCli ? ($msg . PHP_EOL) : '<p>' . htmlspecialchars($msg) . '</p><p><a href="../admin_setup/chatbot_history.php">Chatbot history</a></p>';
} catch (Throwable $e) {
    $err = 'Error: ' . $e->getMessage();
    echo $isCli ? ($err . PHP_EOL) : '<p style="color:red">' . htmlspecialchars($err) . '</p>';
}
