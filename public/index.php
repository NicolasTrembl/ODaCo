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
SELECT DISTINCT r.id, r.title, r.icon, r.created_at
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
SELECT DISTINCT r.id, r.title, r.icon, r.created_at
FROM recipes r
WHERE r.user_id IN (SELECT liked_user_id FROM user_likes WHERE user_id = :user_id)
ORDER BY r.created_at DESC LIMIT 5
";
$stmt = $pdo->prepare($sql_users);
$stmt->execute([':user_id' => $user_id]);
$recipes_by_users = $stmt->fetchAll();

$sql_popular = "
SELECT DISTINCT r.id, r.title, r.icon, r.created_at
FROM recipes r
JOIN recipe_likes rl ON r.id = rl.recipe_id
GROUP BY r.id
ORDER BY COUNT(rl.recipe_id) DESC LIMIT 5
";
$stmt = $pdo->prepare($sql_popular);
$stmt->execute();
$popular_recipes = $stmt->fetchAll();

$sql_liked_recipes = "
SELECT DISTINCT r.id, r.title, r.icon, r.created_at
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

<div class="recommendation-category">
  <h3><?php echo t("Reco_Ing") ?></h3>
  <ul>
    <?php if (count($recipes_by_ingredients) > 0): ?>
      <?php foreach ($recipes_by_ingredients as $r): ?>
        <li>
          <a href="recipe.php?id=<?= $r['id'] ?>">
            <img src="<?= $r['icon'] ?>" alt="<?= $r['title'] ?>" class="w-12 h-12">
            <span><?= htmlspecialchars($r['title']) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <p><?php echo t("No_Reco_Ing") ?></p>
    <?php endif; ?>
  </ul>
</div>

<div class="recommendation-category">
  <h3><?php echo t("Reco_User"); ?></h3>
  <ul>
    <?php if (count($recipes_by_users) > 0): ?>
      <?php foreach ($recipes_by_users as $r): ?>
        <li>
          <a href="recipe.php?id=<?= $r['id'] ?>">
            <img src="<?= $r['icon'] ?>" alt="<?= $r['title'] ?>" class="w-12 h-12">
            <span><?= htmlspecialchars($r['title']) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <p><?php echo t("No_Reco_User") ?></p>
    <?php endif; ?>
  </ul>
</div>

<!-- Recettes populaires -->
<div class="recommendation-category">
  <h3><?php echo t("Reco_Trending")?></h3>
  <ul>
    <?php if (count($popular_recipes) > 0): ?>
      <?php foreach ($popular_recipes as $r): ?>
        <li>
          <a href="recipe.php?id=<?= $r['id'] ?>">
          <span><?= $r['icon'] ?></span>
          <span><?= htmlspecialchars($r['title']) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <p> <?php echo t("No_Reco_Trending") ?> </p>
    <?php endif; ?>
  </ul>
</div>

<div class="recommendation-category">
  <h3> <?php echo t("Like_Reco") ?> </h3>
  <ul>
    <?php if (count($liked_recipes) > 0): ?>
      <?php foreach ($liked_recipes as $r): ?>
        <li>
          <a href="recipe.php?id=<?= $r['id'] ?>">
            <span><?= $r['icon'] ?></span>
            <span><?= htmlspecialchars($r['title']) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <p> <?php echo t("No_Like_Reco") ?> </p>
    <?php endif; ?>
  </ul>
</div>

<?php render_footer(); ?>
