<?php
require_once __DIR__ . '/../core/bootstrap.php';
render_header(t("Rechercher"));

$query = $_GET['q'] ?? '';
$ingredient = $_GET['ingredient'] ?? '';
$creator = $_GET['creator'] ?? '';
$order = $_GET['order'] ?? 'date_desc';

$sql = "
SELECT recipes.id, recipes.title, recipes.icon, recipes.cover, recipes.created_at, users.username, users.id as user_id
FROM recipes
LEFT JOIN users ON users.id = recipes.user_id
LEFT JOIN ingredients ON ingredients.recipe_id = recipes.id
WHERE 1 = 1
GROUP BY recipes.id
";

$params = [];

if ($query) {
    $sql .= " AND (recipes.title LIKE :q OR ingredients.name LIKE :q)";
    $params[':q'] = '%' . $query . '%';
}

if ($ingredient) {
    $sql .= " AND ingredients.name LIKE :ingredient";
    $params[':ingredient'] = '%' . $ingredient . '%';
}

if ($creator) {
    $sql .= " AND users.username LIKE :creator";
    $params[':creator'] = '%' . $creator . '%';
}

switch ($order) {
    case 'date_asc':
        $sql .= " ORDER BY recipes.created_at ASC";
        break;
    case 'alpha':
        $sql .= " ORDER BY recipes.title ASC";
        break;
    case 'relevance':
        $sql .= " ORDER BY CASE WHEN recipes.title LIKE :query_exact THEN 0 ELSE 1 END, recipes.created_at DESC";
        $params[':query_exact'] = '%' . $query . '%';
        break;
    default:
        $sql .= " ORDER BY recipes.created_at DESC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recipes = $stmt->fetchAll();
?>

<div class="max-w-4xl mx-auto p-4 space-y-6">
  <h2 class="text-3xl font-bold"><?= t("Chercher") ?></h2>

  <form method="GET" class="space-y-4">
    <input type="text" name="q" placeholder="<?= t("Mot_Cle") ?>" value="<?= htmlspecialchars($query) ?>" class="w-full p-2 border rounded">

    <div class="flex flex-col sm:flex-row gap-4">
      <input type="text" name="ingredient" placeholder="<?= t("Filtre_Ingrédients") ?>" value="<?= htmlspecialchars($ingredient) ?>" class="flex-1 p-2 border rounded">
      <input type="text" name="creator" placeholder="<?= t("Filtre_User") ?>" value="<?= htmlspecialchars($creator) ?>" class="flex-1 p-2 border rounded">
    </div>

    <div class="flex flex-col sm:flex-row gap-4 items-center">
      <select name="order" class="p-2 border rounded w-full sm:w-auto">
        <option value="date_desc" <?= $order === 'date_desc' ? 'selected' : '' ?>>📅 <?= t("Plus_recent") ?></option>
        <option value="date_asc" <?= $order === 'date_asc' ? 'selected' : '' ?>>📅 <?= t("Plus_ancien") ?></option>
        <option value="alpha" <?= $order === 'alpha' ? 'selected' : '' ?>>🔤 <?= t("AZ") ?></option>
        <option value="relevance" <?= $order === 'relevance' ? 'selected' : '' ?>>⭐ <?= t("Pertinance") ?></option>
        <option value="like" <?= $order === 'like' ? 'selected' : '' ?>>❤️ <?= t("Plus_aimes") ?></option>
      </select>

      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 w-full sm:w-auto"><?= t("Rechercher") ?></button>

      <a href="search.php?random=1" class="px-4 py-2 bg-yellow-400 text-black rounded hover:bg-yellow-500 w-full sm:w-auto text-center"><?= t("Random") ?></a>
    </div>
  </form>

  <?php
  if (isset($_GET['random'])) {
      $rand = $pdo->query("SELECT id FROM recipes ORDER BY RANDOM() LIMIT 1")->fetchColumn();
      if ($rand) {
          header("Location: recipe.php?id=" . $rand);
          exit;
      } else {
          echo "<p class='text-red-600'>" . t("Pas_Recette") . "</p>";
      }
  }
  ?>

  <?php if (count($recipes) === 0): ?>
    <p class="text-gray-500 italic"><?= t("Pas_Resultat") ?></p>
  <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <?php foreach ($recipes as $r): ?>
        <div class="min-w-[80%] sm:min-w-[300px] snap-center flex-shrink-0 bg rounded-2xl shadow-md overflow-hidden">
          <div class="block relative overflow-hidden rounded-lg shadow-md">
            <a href="recipe.php?id=<?= $r['id'] ?>"><img src="<?= $r['cover'] ?>" alt="<?= htmlspecialchars($r['title']) ?>" class="w-full h-48 object-cover"></a>
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-[var(--primary-color)] to-transparent tcolor p-4 flex justify-between items-end h-2/3 pointer-events-none">
              <div class="text-3xl"><?= htmlspecialchars($r['icon']) ?></div>
              <div class="text-right">
                <a href="recipe.php?id=<?= $r['id'] ?>">
                  <h4 class="text-lg font-bold"><?= htmlspecialchars($r['title']) ?></h4>
                </a>
                <p class="text-sm pointer-events-auto"><?php echo t("By") ?>
                  <a href="/user.php?id=<?= $r['user_id']?>"><?=htmlspecialchars($r['username'])?></a>
                </p>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php render_footer(); ?>
