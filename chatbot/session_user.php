<?php
/**
 * Read identity from an active PHP session (start session in chat.php before calling).
 */

function chatbot_session_identity(): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return [null, null];
    }
    $uid = null;
    if (isset($_SESSION['user_id'])) {
        $uid = (int) $_SESSION['user_id'];
        if ($uid <= 0) {
            $uid = null;
        }
    }
    $uname = isset($_SESSION['username']) ? trim((string) $_SESSION['username']) : '';
    $uname = $uname !== '' ? $uname : null;

    return [$uid, $uname];
}
