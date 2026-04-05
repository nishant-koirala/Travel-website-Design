<?php

if (!defined('GEMINI_API_KEY')) {
    require_once __DIR__ . '/config.php';
}

function gemini_http_post_json(string $url, string $jsonBody): array
{
    if (function_exists('curl_init')) {
        foreach ([true, false] as $verifySsl) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json; charset=utf-8'],
                CURLOPT_POSTFIELDS     => $jsonBody,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 45,
                CURLOPT_SSL_VERIFYPEER => $verifySsl,
                CURLOPT_SSL_VERIFYHOST => $verifySsl ? 2 : 0,
            ]);
            $body = curl_exec($ch);
            $errno = curl_errno($ch);
            $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($body !== false && $errno === 0) {
                return ['http' => $http, 'body' => (string) $body, 'ok' => true];
            }
        }
    }
    if (!filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
        error_log("Gemini API Error: allow_url_fopen is disabled");
        return ['http' => 0, 'body' => '', 'ok' => false];
    }
    $ctx = stream_context_create([
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json\r\n",
            'content'       => $jsonBody,
            'timeout'       => 45,
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false,
        ],
    ]);
    $body = @file_get_contents($url, false, $ctx);
    $http = 0;
    if (isset($http_response_header[0]) && preg_match('/HTTP\/\S*\s+(\d{3})/', $http_response_header[0], $m)) {
        $http = (int) $m[1];
    }
    return [
        'http' => $http,
        'body' => $body === false ? '' : (string) $body,
        'ok'   => $body !== false,
    ];
}

function gemini_extract_text($json): ?string
{
    if (!is_array($json)) {
        return null;
    }
    $candidates = $json['candidates'] ?? null;
    if (!is_array($candidates) || !isset($candidates[0])) {
        return null;
    }
    $parts = $candidates[0]['content']['parts'] ?? null;
    if (!is_array($parts)) {
        return null;
    }
    $chunks = [];
    foreach ($parts as $part) {
        if (is_array($part) && isset($part['text'])) {
            $chunks[] = (string) $part['text'];
        }
    }
    return $chunks === [] ? null : implode('', $chunks);
}

function gemini_travel_assistant(string $userMessage, string $packagesJson, ?bool &$ok = null): string
{
    if ($ok !== null) {
        $ok = false;
    }
    $key = trim((string) GEMINI_API_KEY);
    if ($key === '') {
        return '';
    }
    $prompt = "You are a travel assistant.\n\nUser query: {$userMessage}\n\nAvailable packages:\n{$packagesJson}\n\nRules:\n- Recommend actual packages from list\n- Mention name, location, price\n- Explain why it fits\n- Be natural and helpful\n- Do NOT say 'no data found'";
    $payload = [
        'contents' => [['parts' => [['text' => $prompt]]]],
        'generationConfig' => [
            'maxOutputTokens' => 512,
            'temperature'     => 0.35,
        ],
    ];
    $jsonBody = json_encode($payload, JSON_UNESCAPED_UNICODE);
    if ($jsonBody === false) {
        return '';
    }
    
    // Smart model failover system
    $models = [
        'gemini-3.1-flash-lite',   // highest daily limit
        'gemini-2.5-flash',        // current main model
        'gemini-3-flash',          // backup
        'gemini-2.5-flash-lite',   // lightweight fallback
        'gemini-1.5-flash'         // final fallback
    ];
    
    foreach ($models as $model) {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $key;
        
        $res = gemini_http_post_json($url, $jsonBody);
        
        if (!$res['ok']) {
            continue; // try next model
        }
        
        if ($res['http'] === 429) {
            // quota exceeded → try next model
            continue;
        }
        
        if ($res['http'] >= 400) {
            continue;
        }
        
        $json = json_decode($res['body'], true);
        
        if (!is_array($json) || !empty($json['error'])) {
            continue;
        }
        
        $text = gemini_extract_text($json);
        
        if ($text !== null && trim($text) !== '') {
            // Log which model was used (optional but useful)
            error_log("Gemini success with model: " . $model);
            
            if ($ok !== null) {
                $ok = true;
            }
            return trim($text);
        }
    }
    
    // If all models failed, log and return empty
    error_log("Gemini API Error: All models failed");
    return '';
}
