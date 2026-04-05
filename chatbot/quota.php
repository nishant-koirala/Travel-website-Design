<?php
/**
 * Demo system-wide AI quota (not per-user). period_calls increments on each successful Gemini response.
 */

if (!isset($pdo)) {
    require_once __DIR__ . '/config.php';
}

function chatbot_quota_get(PDO $pdo): array
{
    try {
        $pdo->exec('INSERT IGNORE INTO chatbot_system_quota (id, api_call_limit, period_calls) VALUES (1, 500, 0)');
        $stmt = $pdo->query('SELECT api_call_limit, period_calls FROM chatbot_system_quota WHERE id = 1 LIMIT 1');
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        if (!$row) {
            return ['api_call_limit' => 500, 'period_calls' => 0];
        }
        return [
            'api_call_limit' => max(0, (int) $row['api_call_limit']),
            'period_calls'   => max(0, (int) $row['period_calls']),
        ];
    } catch (Throwable $e) {
        // Tables not installed yet — do not block AI
        return ['api_call_limit' => 0, 'period_calls' => 0];
    }
}

/** True if a new AI call is allowed (under global limit). */
function chatbot_ai_quota_allows(PDO $pdo): bool
{
    $q = chatbot_quota_get($pdo);
    if ($q['api_call_limit'] <= 0) {
        return true;
    }
    return $q['period_calls'] < $q['api_call_limit'];
}

/** Call after a successful Gemini response only. */
function chatbot_quota_increment(PDO $pdo): void
{
    try {
        $pdo->exec('UPDATE chatbot_system_quota SET period_calls = period_calls + 1 WHERE id = 1');
    } catch (Throwable $e) {
        error_log('[chatbot] quota increment failed: ' . $e->getMessage());
    }
}

/** Demo “payment”: reset system period counter. */
function chatbot_quota_reset(PDO $pdo): void
{
    try {
        $pdo->exec('UPDATE chatbot_system_quota SET period_calls = 0 WHERE id = 1');
    } catch (Throwable $e) {
        error_log('[chatbot] quota reset failed: ' . $e->getMessage());
    }
}

function chatbot_quota_set_limit(PDO $pdo, int $limit): void
{
    $limit = max(0, $limit);
    try {
        $stmt = $pdo->prepare('UPDATE chatbot_system_quota SET api_call_limit = ? WHERE id = 1');
        $stmt->execute([$limit]);
    } catch (Throwable $e) {
        error_log('[chatbot] quota set_limit failed: ' . $e->getMessage());
    }
}

/** 0 = ok, 1 = near (>=80%), 2 = at/over limit */
function chatbot_quota_warning_level(PDO $pdo): int
{
    $q = chatbot_quota_get($pdo);
    $lim = $q['api_call_limit'];
    $used = $q['period_calls'];
    if ($lim <= 0) {
        return 0;
    }
    if ($used >= $lim) {
        return 2;
    }
    if ($used >= (int) floor($lim * 0.8)) {
        return 1;
    }
    return 0;
}
