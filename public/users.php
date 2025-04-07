<?php
require_once __DIR__ . '/../core/bootstrap.php';
render_header('Utilisateurs');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ?");
$stmt->execute([$user_id]);
$users = $stmt->fetchAll();
?>

<h2 class="text-2xl font-semibold mb-4">🔍 Utilisateurs</h2>

<ul class="space-y-3">
  <?php foreach ($users as $user): ?>
    <li class="border p-3 rounded hover:bg-gray-100 transition">
      <div class="flex items-center justify-between">
        <p class="text-lg font-semibold"><?= htmlspecialchars($user['username']) ?></p>
        
        <?php
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_likes WHERE user_id = ? AND liked_user_id = ?");
        $stmt->execute([$user_id, $user['id']]);
        $is_liked = $stmt->fetchColumn() > 0;
        ?>

        <form action="like_user.php" method="POST">
          <input type="hidden" name="liked_user_id" value="<?= $user['id'] ?>">
          <button type="submit" name="like_user" class="px-4 py-2 bg-<?= $is_liked ? 'red' : 'blue' ?>-600 text-white rounded">
            <?= $is_liked ? 'Dislike' : 'Like' ?>
          </button>
        </form>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php render_footer(); ?>
