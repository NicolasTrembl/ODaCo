<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_login();

render_header(t("Profil"));

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header("Location: index.php");
    exit;
}

// Récupération de l'utilisateur
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "<p>" . t("No_User_Found") . "</p>";
    exit;
}

// Récupération des recettes
$stmt = $pdo->prepare("SELECT id, title, cover, icon, user_id FROM recipes WHERE user_id = ?");
$stmt->execute([$user_id]);
$recipes = $stmt->fetchAll();

// Vérifie si l'utilisateur courant aime ce profil
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_likes WHERE user_id = ? AND liked_user_id = ?");
$stmt->execute([$_SESSION['user_id'], $user_id]);
$is_liked = $stmt->fetchColumn() > 0;
?>

<div class="max-w-4xl mx-auto p-4 space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-6 mb-6">
        <h1 class="text-3xl font-bold"><?= htmlspecialchars($user['username']) ?></h1>
        
        <form action="like_user.php" method="POST">
            <input type="hidden" name="liked_user_id" value="<?= $user_id ?>">
            <button type="submit" class="px-4 py-2 bg-<?= $is_liked ? 'red' : 'blue' ?>-600 text-white rounded hover:bg-<?= $is_liked ? 'red' : 'blue' ?>-700 transition">
                <?= $is_liked ? t('Dislike') : t('Like') ?>
            </button>
        </form>
    </div>

    <h2 class="text-2xl font-semibold mt-8 mb-4"><?= t("User_Recette") ?></h2>

    <?php if (count($recipes) === 0): ?>
        <p class="italic"><?php echo t("No_Recipes_Found") ?></p>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            <?php foreach ($recipes as $r): ?>
                <div class="snap-center flex-shrink-0 bg-white rounded-2xl shadow-md overflow-hidden">
                    <a href="recipe.php?id=<?= $r['id'] ?>" class="block relative overflow-hidden rounded-lg shadow-md">
                        <img src="<?= $r['cover'] ?>" alt="<?= htmlspecialchars($r['title']) ?>" class="w-full h-48 object-cover bg">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-[var(--primary-color)] to-transparent tcolor p-4 flex justify-between items-end h-2/3">
                            <div class="text-3xl"><?= htmlspecialchars($r['icon']) ?></div>
                            <div class="text-right">
                                <h4 class="text-lg font-bold"><?= htmlspecialchars($r['title']) ?></h4>
                                <p class="text-sm"><?php echo t("By") . " " . htmlspecialchars($user['username']); ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php render_footer(); ?>
