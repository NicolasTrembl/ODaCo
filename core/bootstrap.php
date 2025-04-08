<?php
/* Usefull to write less require */

require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/core/includes/db.php';
require_once BASE_PATH . '/core/includes/functions.php';
require_once BASE_PATH . '/core/includes/auth.php';
require_once BASE_PATH . '/core/includes/layout.php';

session_start();

require_once BASE_PATH . '/core/includes/lang.php';


if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}



if (!isset($_SESSION['user_id']) && isset($_COOKIE['auth_token'])) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE remember_token = ?");
    $stmt->execute([$_COOKIE['auth_token']]);
    $userId = $stmt->fetchColumn();

    if ($userId) {
        $_SESSION['user_id'] = $userId;
    }
}
