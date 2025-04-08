<?php
require_once __DIR__ . '/../core/bootstrap.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<p>" .t("Recette_non_trouvée") . "</p>";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->execute([$id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    echo "<p>" . t("Recette_manquante") . "</p>";
    exit;
}

// Ingrédients
$ingredients = $pdo->prepare("SELECT * FROM ingredients WHERE recipe_id = ?");
$ingredients->execute([$id]);
$ingredients = $ingredients->fetchAll();

// Étapes
$steps = $pdo->prepare("SELECT * FROM steps WHERE recipe_id = ? ORDER BY step_order ASC");
$steps->execute([$id]);
$steps = $steps->fetchAll();

render_header($recipe['title']);
?>

<?php
$is_liked = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM recipe_likes WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$_SESSION['user_id'], $id]);
    $is_liked = $stmt->fetchColumn() > 0;
}
?>

<form action="like.php" method="POST">
    <input type="hidden" name="recipe_id" value="<?= $id ?>">
    <button type="submit" name="like" class="px-4 py-2 bg-<?= $is_liked ? 'red' : 'blue' ?>-600 text-white rounded">
        <?= $is_liked ? t('Dislike') : t('Like') ?>
    </button>
</form>


<?php if (isset($recipe['id'])): ?>
  <button
    id="todo-toggle"
    class="bg-yellow-400 hover:bg-yellow-500 px-4 py-2 rounded text-white mt-4"
  >
    <?= t("Ajout_todo") ?>
  </button>

  <script>
    const recipeId = <?= $recipe['id'] ?>;
    const todoKey = 'todo_recipes';

    function getTodoList() {
      return JSON.parse(localStorage.getItem(todoKey) || '[]');
    }

    function setTodoList(list) {
      localStorage.setItem(todoKey, JSON.stringify(list));
    }

    function isInTodo(id) {
      return getTodoList().includes(id);
    }

    function toggleTodo() {
      const list = getTodoList();
      const index = list.indexOf(recipeId);

      if (index > -1) {
        list.splice(index, 1);
        document.getElementById('todo-toggle').innerText = "<?= t("Ajout_todo") ?>";
      } else {
        list.push(recipeId);
        document.getElementById('todo-toggle').innerText = "<?= t("Retirer_todo") ?>";
      }

      setTodoList(list);
    }

    document.addEventListener('DOMContentLoaded', () => {
      const btn = document.getElementById('todo-toggle');
      if (isInTodo(recipeId)) {
        btn.innerText = "<?= t("Retirer_todo") ?>";
      }

      btn.addEventListener('click', toggleTodo);
    });
  </script>
<?php endif; ?>

<h1 class="text-2xl font-semibold"><?= htmlspecialchars($recipe['title']) ?></h1>
<a href="user.php?id=<?= $recipe['user_id'] ?>" class="text-blue-500"><?= t("Voir_Createur") ?></a>


<?php if ($recipe['cover']): ?>
  <img src="<?= htmlspecialchars($recipe['cover']) ?>" class="mb-4 rounded max-h-72 object-cover w-full">
<?php endif; ?>

<?php if ($recipe['description']): ?>
  <p class="mb-4 text-gray-700"><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
<?php endif; ?>

<h3 class="text-xl font-semibold mt-6 mb-2"><?= t("Ingrédients") ?></h3>
<ul class="list-disc pl-6 mb-6">
  <?php foreach ($ingredients as $ing): ?>
    <li><?= htmlspecialchars($ing['quantity']) . ' / ' . htmlspecialchars($ing['unit']) . ' ' . htmlspecialchars($ing['name']) ?></li>
  <?php endforeach; ?>
</ul>

<h3 class="text-xl font-semibold mb-2"><?= t("Preparation") ?></h3>
<ol class="space-y-4 list-decimal pl-6 mb-6">
  <?php foreach ($steps as $step): ?>
    <li>
      <p><?= nl2br(htmlspecialchars($step['content'])) ?></p>
      <?php if ($step['image']): ?>
        <img src="<?= htmlspecialchars($step['image']) ?>" class="mt-2 rounded max-w-md">
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ol>

<?php if ($recipe['extra_info']): ?>
  <div class="mt-6">
    <h3 class="text-xl font-semibold mb-2"><?= t("Variantes_c") ?></h3>
    <p><?= nl2br(htmlspecialchars($recipe['extra_info'])) ?></p>
  </div>
<?php endif; ?>

<?php render_footer(); ?>
