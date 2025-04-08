<?php
require_once __DIR__ . '/../core/bootstrap.php';
render_header( t("Rechercher") );

$query = $_GET['q'] ?? '';
$ingredient = $_GET['ingredient'] ?? '';
$creator = $_GET['creator'] ?? '';
$order = $_GET['order'] ?? 'date_desc';

$sql = "
SELECT recipes.id, recipes.title, recipes.icon, recipes.created_at, users.username, users.id as user_id
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

<h2 class="text-2xl font-semibold mb-4"><?= t("Chercher") ?></h2>

<form method="GET" class="space-y-4 mb-8">

  <input type="text" name="q" placeholder="<?= t("Mot_Cle") ?>" value="<?= htmlspecialchars($query) ?>" class="w-full p-2 border rounded">

  <div class="flex gap-4">
    <input type="text" name="ingredient" placeholder="<?= t("Filtre_Ingrédients") ?>" value="<?= htmlspecialchars($ingredient) ?>" class="flex-1 p-2 border rounded">
    <input type="text" name="creator" placeholder="<?= t("Filtre_User") ?>" value="<?= htmlspecialchars($creator) ?>" class="flex-1 p-2 border rounded">
  </div>

  <div class="flex gap-4">
    <select name="order" class="p-2 border rounded">
      <option value="date_desc" <?= $order === 'date_desc' ? 'selected' : '' ?>>📅 <?= t("Plus_recent") ?></option>
      <option value="date_asc" <?= $order === 'date_asc' ? 'selected' : '' ?>>📅 <?= t("Plus_ancien") ?></option>
      <option value="alpha" <?= $order === 'alpha' ? 'selected' : '' ?>>🔤 <?= t("AZ") ?></option>
      <option value="relevance" <?= $order === 'relevance' ? 'selected' : '' ?>>⭐ <?= t("Pertinance") ?></option>
      <option value="like" <?= $order === 'like' ? 'selected' : '' ?>>⭐ <?= t("Plus_aimes") ?></option>
    </select>

    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"><?= t(key: "Rechercher") ?></button>

    <a href="search.php?random=1" class="px-4 py-2 bg-yellow-400 text-black rounded hover:bg-yellow-500 ml-auto"><?= t("Random") ?></a>
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

<ul class="space-y-3">
  <?php if (count($recipes) === 0): ?>
    <li class="text-gray-500"><?= t("Pas_Resultat") ?></li>
  <?php endif; ?>
  <?php foreach ($recipes as $r): ?>
    <li class="border p-3 rounded hover:bg-gray-100 transition">
      <a href="recipe.php?id=<?= $r['id'] ?>" class="flex items-center gap-3">
        <span class="text-2xl"><?= htmlspecialchars($r['icon']) ?></span>
        <div>
          <p class="text-lg font-semibold"><?= htmlspecialchars($r['title']) ?></p>
          <p class="text-sm text-gray-500 flex flex-direction-row gap-2 items-center">
            <?= t("Par") ?> 
            <a href="user.php?id=<?=$r['user_id']?>" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">
              <?= htmlspecialchars($r['username']) ?>
            </a> 
            • <?= date('d/m/Y', strtotime($r['created_at'])) ?>
          </p>
        </div>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<?php render_footer(); ?>
