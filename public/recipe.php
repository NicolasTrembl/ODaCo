<?php
require_once __DIR__ . '/../core/bootstrap.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='container mx-auto p-4'><p class='text-red-500'>" . t("Recette_non_trouvée") . "</p></div>";
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->execute([$id]);
$recipe = $stmt->fetch();

if (!$recipe) {
  echo "<div class='container mx-auto p-4'><p class='text-red-500'>" . t("Recette_manquante") . "</p></div>";
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

$is_liked = false;
if (isset($_SESSION['user_id'])) {
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM recipe_likes WHERE user_id = ? AND recipe_id = ?");
  $stmt->execute([$_SESSION['user_id'], $id]);
  $is_liked = $stmt->fetchColumn() > 0;
}
?>

<div class="container mx-auto px-4 py-6 max-w-4xl">
  <div class="pcolor rounded-lg shadow-lg overflow-hidden">
    <?php if ($recipe['cover']): ?>
      <div class="w-full h-64 sm:h-80 md:h-96 relative">
        <img src="<?= htmlspecialchars($recipe['cover']) ?>" 
           class="w-full h-full object-cover" 
           alt="<?= htmlspecialchars($recipe['title']) ?>">
      </div>
    <?php endif; ?>

    <div class="p-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <h1 class="text-3xl font-bold mb-2 sm:mb-0 truncate max-w-xl" title="<?= htmlspecialchars($recipe['title']) ?>"><?= htmlspecialchars($recipe['title']) ?></h1>

        
        <div class="flex flex-wrap gap-2">
          <form action="like.php" method="POST">
            <input type="hidden" name="recipe_id" value="<?= $id ?>">
            <button type="submit" name="like" class="px-4 py-2 bg-<?= $is_liked ? 'red' : 'blue' ?>-600 hover:bg-<?= $is_liked ? 'red' : 'blue' ?>-700 text-white rounded-md transition duration-200 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="<?= $is_liked ? 'currentColor' : 'none' ?>" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
              <?= $is_liked ? t('Dislike') : t('Like') ?>
            </button>
          </form>

          <?php if (isset($recipe['id'])): ?>
            <button
              id="todo-toggle"
              class="bg-yellow-500 hover:bg-yellow-600 px-4 py-2 rounded-md text-white transition duration-200 flex items-center"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              <?= t("Ajout_todo") ?>
            </button>
          <?php endif; ?>
        </div>
      </div>
      <div class="my-2 flex flex-wrap justify-evenly gap-2">
        <a href="user.php?id=<?= $recipe['user_id'] ?>" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md transition-colors duration-200 shadow-sm">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <?= t("Voir_Createur") ?>
        </a>
        
      <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $recipe['user_id']): ?>
        <form action="delete.php" method="POST" onsubmit="return confirm('<?= t('Confirmer_suppression') ?>');">
          <input type="hidden" name="recipe_id" value="<?= $id ?>">
          <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md text-white transition duration-200 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <?= t("Supprimer") ?>
          </button>
        </form>
      <?php endif; ?>
      </div>


      <?php if ($recipe['description']): ?>
        <div class="bg p-4 rounded-md mb-8">
          <p class="leading-relaxed"><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
        </div>
      <?php endif; ?>

      <div class="md:flex md:gap-8">
        <div class="md:w-1/3 mb-8 md:mb-0">
          <h3 class="text-2xl font-semibold mb-4 borderb pb-2"><?= t("Ingrédients") ?></h3>
          <ul class="space-y-2">
            <?php foreach ($ingredients as $ing): ?>
              <li class="flex items-center">
                <span class="h-2 w-2 bg rounded-full mr-2"></span>
                <span><?= htmlspecialchars($ing['quantity']) . ' ' . htmlspecialchars($ing['unit']) . ' ' . htmlspecialchars($ing['name']) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="md:w-2/3">
          <h3 class="text-2xl font-semibold mb-4 borderb pb-2"><?= t("Preparation") ?></h3>
          <ol class="space-y-6">
          <?php foreach ($steps as $i => $step): ?>
            <li class="bg p-4 rounded-lg shadow-sm border flex flex-col sm:flex-row gap-4 items-start">
              <?php if (!empty($step['image'])): ?>
                <img src="<?= htmlspecialchars($step['image']) ?>" 
                    alt="<?= t("Image_etape") . ' ' . ($i + 1) ?>" 
                    class="rounded-md w-full sm:w-48 h-auto object-cover shadow" />
              <?php endif; ?>              
              <div class="flex-1">
                <p class="leading-relaxed"><?= nl2br(htmlspecialchars($step['content'])) ?></p>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>

        </div>
      </div>

      <?php if ($recipe['extra_info']): ?>
        <div class="mt-8 bg  p-5 rounded-md">
          <h3 class="text-xl font-semibold mb-2"><?= t("Variantes_c") ?></h3>
          <p class=" leading-relaxed"><?= nl2br(htmlspecialchars($recipe['extra_info'])) ?></p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

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
    const $btn = $('#todo-toggle');

    if (index > -1) {
      list.splice(index, 1);
      $btn.html(`<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg><?= t("Ajout_todo") ?>`);
    } else {
      list.push(recipeId);
      $btn.html(`<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg><?= t("Retirer_todo") ?>`);
    }

    setTodoList(list);
  }

  $(document).ready(function() {
    const $btn = $('#todo-toggle');
    if (isInTodo(recipeId)) {
      $btn.html(`<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg><?= t("Retirer_todo") ?>`);
    }

    $btn.on('click', toggleTodo);
  });
</script>

<?php render_footer(); ?>

