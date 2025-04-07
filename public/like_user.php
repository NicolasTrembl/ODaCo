<?php
require_once __DIR__ . '/../core/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header(header: "Location: login.php");
    exit;
}

if (!isset($_POST['liked_user_id']) || !is_numeric($_POST['liked_user_id'])) {
    header("Location: user.php?id=" . $liked_user_id);
    exit;
}

$liked_user_id = (int) $_POST['liked_user_id'];
$user_id = $_SESSION['user_id'];

if ($user_id === $liked_user_id) {
    header("Location: user.php?id=" . $liked_user_id);
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_likes WHERE user_id = ? AND liked_user_id = ?");
$stmt->execute([$user_id, $liked_user_id]);
$already_liked = $stmt->fetchColumn() > 0;

if ($already_liked) {
    $stmt = $pdo->prepare("DELETE FROM user_likes WHERE user_id = ? AND liked_user_id = ?");
    $stmt->execute([$user_id, $liked_user_id]);
} else {
    $stmt = $pdo->prepare("INSERT INTO user_likes (user_id, liked_user_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $liked_user_id]);
}

header("Location: user.php?id=" . $liked_user_id);
exit;
