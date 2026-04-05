<?php
/**
 * Chatbot configuration — DB uses existing project connection.
 * API key: optional chatbot/.env line GEMINI_API_KEY=... else constant below.
 */

if (!defined('CHATBOT_ROOT')) {
    define('CHATBOT_ROOT', __DIR__);
}

if (!defined('GEMINI_API_KEY')) {
    $geminiKey = '';
    $envPath = __DIR__ . DIRECTORY_SEPARATOR . '.env';
    if (is_readable($envPath)) {
        $lines = @file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (is_array($lines)) {
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#') {
                    continue;
                }
                if (preg_match('/^\s*GEMINI_API_KEY\s*=\s*(.+)\s*$/', $line, $m)) {
                    $geminiKey = trim($m[1], " \t\"'");
                    break;
                }
            }
        }
    }
    if ($geminiKey === '') {
        $env = getenv('GEMINI_API_KEY');
        $geminiKey = ($env !== false && $env !== '') ? trim($env) : '';
    }
    if ($geminiKey === '') {
        $geminiKey = 'AIzaSyA2BDiAg07sJOxejuFcxSxMbIYc7pPc2zA';
    }
    define('GEMINI_API_KEY', $geminiKey);
}

if (!defined('GEMINI_MODEL')) {
    define('GEMINI_MODEL', 'gemini-1.5-flash');
}

if (!defined('CHATBOT_CACHE_TTL')) {
    define('CHATBOT_CACHE_TTL', 3600); // 1 hour
}

if (!defined('CHATBOT_LOG_PREFIX')) {
    define('CHATBOT_LOG_PREFIX', '[chatbot] ');
}

require_once dirname(__DIR__) . '/database/db_connect.php';