<?php
/**
 * Package queries — reads columns dynamically; no schema changes.
 */

if (!isset($pdo)) {
    require_once __DIR__ . '/config.php';
}

/**
 * @return string[]
 */
function chatbot_package_columns(PDO $pdo): array
{
    static $cols = null;
    if ($cols !== null) {
        return $cols;
    }
    $cols = [];
    try {
        $stmt = $pdo->query('SHOW COLUMNS FROM packages');
        if ($stmt) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!empty($row['Field'])) {
                    $cols[] = $row['Field'];
                }
            }
        }
    } catch (Throwable $e) {
        $cols = ['id', 'title', 'description', 'price', 'image'];
    }
    return $cols;
}

function chatbot_has_column(array $cols, string $name): bool
{
    return in_array($name, $cols, true);
}

function chatbot_location_column(array $cols): ?string
{
    foreach (['location', 'destination', 'region', 'place'] as $c) {
        if (chatbot_has_column($cols, $c)) {
            return $c;
        }
    }
    return null;
}

function chatbot_duration_column(array $cols): ?string
{
    foreach (['duration_days', 'duration', 'days', 'trip_duration'] as $c) {
        if (chatbot_has_column($cols, $c)) {
            return $c;
        }
    }
    return null;
}

function chatbot_select_list(PDO $pdo): string
{
    $cols = chatbot_package_columns($pdo);
    $want = ['id', 'title', 'description', 'price'];
    foreach (['image', 'location', 'destination', 'region', 'place', 'duration_days', 'duration', 'days', 'trip_duration'] as $c) {
        if (chatbot_has_column($cols, $c) && !in_array($c, $want, true)) {
            $want[] = $c;
        }
    }
    $want = array_values(array_intersect($want, $cols));
    if (!in_array('price', $want, true)) {
        $want[] = 'price';
    }
    return implode(', ', array_map(static function ($c) {
        return '`' . str_replace('`', '``', $c) . '`';
    }, $want));
}

/**
 * @return array<string, mixed>|null
 */
function getCheapestPackage(PDO $pdo): ?array
{
    $sel = chatbot_select_list($pdo);
    $sql = "SELECT {$sel} FROM packages ORDER BY CAST(price AS DECIMAL(12,2)) ASC LIMIT 1";
    $stmt = $pdo->query($sql);
    $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    return $row ?: null;
}

/**
 * @return array<string, mixed>|null
 */
function getMostExpensivePackage(PDO $pdo): ?array
{
    $sel = chatbot_select_list($pdo);
    $sql = "SELECT {$sel} FROM packages ORDER BY CAST(price AS DECIMAL(12,2)) DESC LIMIT 1";
    $stmt = $pdo->query($sql);
    $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    return $row ?: null;
}

/**
 * @return array<int, array<string, mixed>>
 */
function filterByLocation(PDO $pdo, string $location): array
{
    $location = trim($location);
    if ($location === '') {
        return [];
    }
    $cols = chatbot_package_columns($pdo);
    $sel = chatbot_select_list($pdo);
    $locCol = chatbot_location_column($cols);
    $like = '%' . $location . '%';

    if ($locCol !== null) {
        $sql = "SELECT {$sel} FROM packages WHERE `{$locCol}` LIKE :loc ORDER BY CAST(price AS DECIMAL(12,2)) ASC LIMIT 20";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':loc' => $like]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    $sql = "SELECT {$sel} FROM packages WHERE title LIKE :q OR description LIKE :q ORDER BY CAST(price AS DECIMAL(12,2)) ASC LIMIT 20";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':q' => $like]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/**
 * @return array<int, array<string, mixed>>
 */
function searchPackages(PDO $pdo, string $keyword): array
{
    $keyword = trim($keyword);
    if ($keyword === '') {
        return [];
    }
    $sel = chatbot_select_list($pdo);
    $like = '%' . $keyword . '%';
    $sql = "SELECT {$sel} FROM packages WHERE title LIKE :q OR description LIKE :q ORDER BY CAST(price AS DECIMAL(12,2)) ASC LIMIT 20";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':q' => $like]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/**
 * @return array<string, mixed>|null
 */
function findPackageByLooseName(PDO $pdo, string $name): ?array
{
    $name = trim($name);
    if ($name === '') {
        return null;
    }
    $sel = chatbot_select_list($pdo);
    $sql = "SELECT {$sel} FROM packages WHERE title LIKE :q ORDER BY LENGTH(title) ASC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':q' => '%' . $name . '%']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * @return string Plain-text comparison or error message
 */
function comparePackages(PDO $pdo, string $name1, string $name2): string
{
    $a = findPackageByLooseName($pdo, $name1);
    $b = findPackageByLooseName($pdo, $name2);
    if (!$a) {
        return 'I could not find a package matching "' . $name1 . '". Try the exact name from our list.';
    }
    if (!$b) {
        return 'I could not find a package matching "' . $name2 . '". Try the exact name from our list.';
    }
    if ((string) $a['id'] === (string) $b['id']) {
        return 'Both searches matched the same package. Try more distinct names.';
    }
    return format_package_compare_structured($a, $b);
}

/**
 * @return array<int, array<string, mixed>>
 */
function getTopPackages(PDO $pdo, int $limit = 5): array
{
    $limit = max(1, min(10, $limit));
    $sel = chatbot_select_list($pdo);
    $sql = "SELECT {$sel} FROM packages ORDER BY CAST(price AS DECIMAL(12,2)) ASC LIMIT " . (int) $limit;
    $stmt = $pdo->query($sql);
    return $stmt ? ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];
}

/**
 * @param array<int, array<string, mixed>> $packages
 * @return array<int, array<string, mixed>>
 */
function packages_for_ai_context(PDO $pdo, array $packages, string $userQuery): array
{
    $cap = 5;
    if (!empty($packages)) {
        return chatbot_slice_ai_packages($packages, $cap);
    }
    $kw = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $userQuery);
    $kw = trim(preg_replace('/\s+/', ' ', $kw));
    if (strlen($kw) >= 2) {
        $words = preg_split('/\s+/', $kw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $words = array_values(array_filter($words, static function ($w) {
            return strlen($w) > 2;
        }));
        $found = [];
        foreach (array_slice($words, 0, 3) as $w) {
            foreach (searchPackages($pdo, $w) as $row) {
                $found[(string) $row['id']] = $row;
            }
        }
        if (!empty($found)) {
            return chatbot_slice_ai_packages(array_values($found), $cap);
        }
    }
    return chatbot_slice_ai_packages(getTopPackages($pdo, $cap), $cap);
}

/**
 * Send 3–5 rows to AI when enough exist; never more than $max.
 *
 * @param array<int, array<string, mixed>> $rows
 * @return array<int, array<string, mixed>>
 */
function chatbot_slice_ai_packages(array $rows, int $max = 5): array
{
    $max = max(3, min(5, $max));
    $n = count($rows);
    if ($n === 0) {
        return [];
    }
    if ($n >= 3) {
        return array_slice($rows, 0, min($max, $n));
    }
    return array_slice($rows, 0, $n);
}

/**
 * @param array<string, mixed> $row
 */
function format_package_line(array $row): string
{
    $parts = [];
    if (!empty($row['title'])) {
        $parts[] = (string) $row['title'];
    }
    if (isset($row['price'])) {
        $parts[] = 'Price: ' . $row['price'];
    }
    foreach (['location', 'destination', 'region', 'place'] as $k) {
        if (!empty($row[$k])) {
            $parts[] = 'Location: ' . $row[$k];
            break;
        }
    }
    foreach (['duration_days', 'duration', 'days', 'trip_duration'] as $k) {
        if (isset($row[$k]) && $row[$k] !== '' && $row[$k] !== null) {
            $parts[] = 'Duration: ' . $row[$k];
            break;
        }
    }
    if (!empty($row['description'])) {
        $desc = preg_replace('/\s+/', ' ', strip_tags((string) $row['description']));
        if (strlen($desc) > 160) {
            $desc = substr($desc, 0, 157) . '...';
        }
        $parts[] = $desc;
    }
    return implode(' — ', $parts);
}

/**
 * @param array<string, mixed> $row
 */
function package_row_price_label(array $row): string
{
    return isset($row['price']) ? (string) $row['price'] : 'Not listed';
}

/**
 * @param array<string, mixed> $row
 */
function package_row_duration_label(array $row): string
{
    foreach (['duration_days', 'duration', 'days', 'trip_duration'] as $k) {
        if (isset($row[$k]) && $row[$k] !== '' && $row[$k] !== null) {
            return (string) $row[$k];
        }
    }
    return 'Not listed';
}

/**
 * @param array<string, mixed> $row
 */
function package_row_location_label(array $row): string
{
    foreach (['location', 'destination', 'region', 'place'] as $k) {
        if (!empty($row[$k])) {
            return (string) $row[$k];
        }
    }
    return 'Not listed';
}

/**
 * @param array<string, mixed> $a
 * @param array<string, mixed> $b
 */
function format_package_compare_structured(array $a, array $b): string
{
    $ta = $a['title'] ?? 'Package A';
    $tb = $b['title'] ?? 'Package B';

    $lines = [
        'Compare (from our catalog):',
        '',
        '1) ' . $ta,
        '   Price: ' . package_row_price_label($a),
        '   Duration: ' . package_row_duration_label($a),
        '   Location: ' . package_row_location_label($a),
        '',
        '2) ' . $tb,
        '   Price: ' . package_row_price_label($b),
        '   Duration: ' . package_row_duration_label($b),
        '   Location: ' . package_row_location_label($b),
        '',
        'All values above are from our database only.',
    ];
    return implode("\n", $lines);
}
