<?php
require_once __DIR__ . '/../core/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['recipe_id'])) {
    header("Location: index.php");
    exit;
}

$recipe_id = (int) $_POST['recipe_id'];
$user_id = $_SESSION['user_id'];

if (isset($_POST['like'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM recipe_likes WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$user_id, $recipe_id]);
    $already_liked = $stmt->fetchColumn() > 0;

    if ($already_liked) {
        $stmt = $pdo->prepare("DELETE FROM recipe_likes WHERE user_id = ? AND recipe_id = ?");
        $stmt->execute([$user_id, $recipe_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO recipe_likes (user_id, recipe_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $recipe_id]);
    }
}

header("Location: recipe.php?id=" . $recipe_id);
exit;
