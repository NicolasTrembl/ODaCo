<?php
require_once __DIR__ . '/../core/bootstrap.php';

require_login();

render_header('Profil utilisateur');

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "<p>Utilisateur non trouvé.</p>";
    exit;
}

$stmt = $pdo->prepare("SELECT id, title FROM recipes WHERE user_id = ?");
$stmt->execute([$user_id]);
$recipes = $stmt->fetchAll();
?>

<h1 class="text-2xl font-semibold"><?= htmlspecialchars($user['username']) ?></h1>

<h2 class="text-xl">Recettes de cet utilisateur :</h2>
<ul>
    <?php foreach ($recipes as $recipe): ?>
        <li><a href="recipe.php?id=<?= $recipe['id'] ?>"><?= htmlspecialchars($recipe['title']) ?></a></li>
    <?php endforeach; ?>
</ul>

<?php
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_likes WHERE user_id = ? AND liked_user_id = ?");
$stmt->execute([$_SESSION['user_id'], $user_id]);
$is_liked = $stmt->fetchColumn() > 0;
?>

<form action="like_user.php" method="POST">
    <input type="hidden" name="liked_user_id" value="<?= $user_id ?>">
    <button type="submit" class="px-4 py-2 bg-<?= $is_liked ? 'red' : 'blue' ?>-600 text-white rounded">
        <?= $is_liked ? 'Dislike' : 'Like' ?>
    </button>
</form>

<?php render_footer(); ?>
