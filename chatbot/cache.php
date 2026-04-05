<?php
/**
 * File cache: keys are built in chat.php as q_ + md5(canonical message), TTL 1 hour.
 */

if (!defined('CHATBOT_ROOT')) {
    require_once __DIR__ . '/config.php';
}

function chatbot_cache_dir(): string
{
    $dir = CHATBOT_ROOT . DIRECTORY_SEPARATOR . 'cache_data';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir;
}

function chatbot_cache_get(string $key): ?string
{
    $path = chatbot_cache_dir() . DIRECTORY_SEPARATOR . $key . '.json';
    if (!is_file($path)) {
        return null;
    }
    $raw = @file_get_contents($path);
    if ($raw === false) {
        return null;
    }
    $data = json_decode($raw, true);
    if (!is_array($data) || !isset($data['exp'], $data['reply'])) {
        return null;
    }
    if (time() > (int) $data['exp']) {
        @unlink($path);
        return null;
    }
    return (string) $data['reply'];
}

function chatbot_cache_set(string $key, string $reply): void
{
    $path = chatbot_cache_dir() . DIRECTORY_SEPARATOR . $key . '.json';
    $payload = json_encode([
        'exp'   => time() + (int) CHATBOT_CACHE_TTL,
        'reply' => $reply,
    ], JSON_UNESCAPED_UNICODE);
    if ($payload !== false) {
        @file_put_contents($path, $payload, LOCK_EX);
    }
}
