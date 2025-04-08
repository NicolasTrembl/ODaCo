<?php
require_once __DIR__ . '/../core/bootstrap.php';
render_header(t("Accueil"));

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "<p>" . t("Besoin_Co") . "</p>";
    render_footer();
    exit;
}

$sql_ingredients = "
SELECT DISTINCT r.id, r.title, r.icon, r.created_at, r.cover, r.user_id
FROM recipes r
JOIN ingredients i ON r.id = i.recipe_id
WHERE i.id IN (
    SELECT DISTINCT i.id 
    FROM recipe_likes rl
    JOIN recipes r2 ON rl.recipe_id = r2.id
    JOIN ingredients i ON r2.id = i.recipe_id
    WHERE rl.user_id = :user_id
)
AND r.id NOT IN (
    SELECT recipe_id FROM recipe_likes WHERE user_id = :user_id
)
ORDER BY r.created_at DESC LIMIT 5
";

$stmt = $pdo->prepare($sql_ingredients);
$stmt->execute([':user_id' => $user_id]);
$recipes_by_ingredients = $stmt->fetchAll();

$sql_users = "
SELECT DISTINCT r.id, r.title, r.icon, r.created_at, r.cover, r.user_id
FROM recipes r
WHERE r.user_id IN (SELECT liked_user_id FROM user_likes WHERE user_id = :user_id)
ORDER BY r.created_at DESC LIMIT 5
";
$stmt = $pdo->prepare($sql_users);
$stmt->execute([':user_id' => $user_id]);
$recipes_by_users = $stmt->fetchAll();

$sql_popular = "
SELECT DISTINCT r.id, r.title, r.icon, r.created_at, r.cover, r.user_id
FROM recipes r
JOIN recipe_likes rl ON r.id = rl.recipe_id
GROUP BY r.id
ORDER BY COUNT(rl.recipe_id) DESC LIMIT 5
";
$stmt = $pdo->prepare($sql_popular);
$stmt->execute();
$popular_recipes = $stmt->fetchAll();

$sql_liked_recipes = "
SELECT DISTINCT r.id, r.title, r.icon, r.created_at, r.cover, r.user_id
FROM recipes r
JOIN recipe_likes rl ON r.id = rl.recipe_id
WHERE rl.user_id = :user_id
ORDER BY r.created_at DESC LIMIT 5
";
$stmt = $pdo->prepare($sql_liked_recipes);
$stmt->execute([':user_id' => $user_id]);
$liked_recipes = $stmt->fetchAll();
?>

<h2 class="text-2xl font-semibold mb-4"><? echo t("Reco")?></h2>

<?php
function renderRecommendationSection($title, $recipes, $emptyText) {
  echo '<div class="mb-8">';
  echo '<h3 class="text-xl font-semibold mb-4" style="color: var(--text-color);">' . $title . '</h3>';
  
  if (count($recipes) > 0): ?>
    <div class="flex space-x-4 overflow-x-auto snap-x pb-2">
      <?php foreach ($recipes as $r): ?>
        <div class="min-w-[80%] sm:min-w-[300px] snap-center flex-shrink-0 bg-white rounded-2xl shadow-md overflow-hidden">
        <a href="recipe.php?id=<?= $r['id'] ?>" class="block relative overflow-hidden rounded-lg shadow-md">
          <img src="<?= $r['cover'] ?>" alt="<?= htmlspecialchars($r['title']) ?>" class="w-full h-48 object-cover">

          <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-[var(--primary-color)] to-transparent tcolor p-4 flex justify-between items-end h-2/3">

            <div class="text-3xl"><?= htmlspecialchars($r['icon']) ?></div>

            <div class="text-right">
              <h4 class="text-lg font-bold"><?= htmlspecialchars($r['title']) ?></h4>
              <p class="text-sm"><?php echo t("By") . " " . htmlspecialchars("User" . $r['user_id']); ?></p>
            </div>
          </div>
        </a>

        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-gray-600"><?= $emptyText ?></p>
  <?php endif;
  echo '</div>';
}
?>

<!-- Utilisation des 4 blocs -->
<?php
renderRecommendationSection(
  t("Reco_Ing"),
  $recipes_by_ingredients,
  t("No_Reco_Ing")
);

renderRecommendationSection(
  t("Reco_User"),
  $recipes_by_users,
  t("No_Reco_User")
);

renderRecommendationSection(
  t("Reco_Trending"),
  $popular_recipes,
  t("No_Reco_Trending")
);

renderRecommendationSection(
  t("Like_Reco"),
  $liked_recipes,
  t("No_Like_Reco")
);
?>


<?php render_footer(); ?>
