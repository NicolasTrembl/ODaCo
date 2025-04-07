<?php
require_once __DIR__ . '/../core/bootstrap.php';
session_destroy();

if (isset($_COOKIE['auth_token'])) {
    $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE remember_token = ?");
    $stmt->execute([$_COOKIE['auth_token']]);
    setcookie('auth_token', '', time() - 3600, '/', '', false, true);
}

header('Location: index.php');
exit;
