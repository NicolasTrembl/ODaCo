<?php
require_once '../core/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$recipe_id = isset($_POST['recipe_id']) ? (int)$_POST['recipe_id'] : 0;

if ($recipe_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid recipe ID']);
    exit;
}

$stmt = $pdo->prepare('SELECT user_id, cover FROM recipes WHERE id = ?');
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) {
    http_response_code(404);
    echo json_encode(['error' => 'Recipe not found']);
    exit;
}

if ($recipe['user_id'] != $_SESSION['user_id']) {
    http_response_code(403);
    echo json_encode(['error' => 'You do not have permission to delete this recipe']);
    exit;
}

$pdo->beginTransaction();

try {
    $image_filename = $recipe['cover'];

    $stmt = $pdo->prepare('DELETE FROM recipe_likes WHERE recipe_id = ?');
    $stmt->execute([$recipe_id]);

    $stmt = $pdo->prepare('DELETE FROM ingredients WHERE recipe_id = ?');
    $stmt->execute([$recipe_id]);

    $stmt = $pdo->prepare('SELECT image FROM steps WHERE recipe_id = ? AND image IS NOT NULL AND image != ""');
    $stmt->execute([$recipe_id]);
    $step_images = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($step_images as $image) {
        $image_path = '../uploads/steps/' . $image;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    $stmt = $pdo->prepare('DELETE FROM steps WHERE recipe_id = ?');
    $stmt->execute([$recipe_id]);

    $stmt = $pdo->prepare('DELETE FROM recipes WHERE id = ?');
    $stmt->execute([$recipe_id]);

    $pdo->commit();
    
    if (!empty($image_filename)) {
        $image_path = '../uploads/cover/' . $image_filename;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    header('Location: index.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete recipe', 'message' => $e->getMessage()]);
}