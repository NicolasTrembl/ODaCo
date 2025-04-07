<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_login();

$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $pdo->prepare("DELETE FROM recipes WHERE user_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
}

session_destroy();
header("Location: login.php?msg=account_deleted");
exit;
