<?php
require_once __DIR__ . '/../core/bootstrap.php';
render_header("Ma liste à faire");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['ids'] ?? [];

    if (!is_array($ids) || empty($ids)) {
        echo "<p>Aucune recette dans votre liste.</p>";
    } else {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $recipes = $stmt->fetchAll();

        if (!$recipes) {
            echo "<p>Aucune recette trouvée.</p>";
        } else {
            echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
            foreach ($recipes as $recipe) {
                echo '<a href="recipe.php?id=' . $recipe['id'] . '" class="p-4 border rounded hover:bg-gray-50">';
                echo '<h3 class="text-xl font-semibold">' . htmlspecialchars($recipe['title']) . '</h3>';
                echo '<p class="text-gray-600">' . htmlspecialchars(substr($recipe['description'], 0, 100)) . '...</p>';
                echo '</a>';
            }
            echo '</div>';
        }
    }

    render_footer();
    exit;
}
?>

<h2 class="text-2xl font-bold mb-4">📋 Ma liste de recettes à faire</h2>
<p>Chargement en cours...</p>

<form id="todo-form" method="POST" style="display: none;">
  <input type="hidden" name="ids[]">
</form>

<script>
  const todoIds = JSON.parse(localStorage.getItem('todo_recipes') || '[]');
  if (todoIds.length === 0) {
    document.querySelector('p').innerText = 'Liste vide';
  } else {
    const form = document.getElementById('todo-form');
    todoIds.forEach(id => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'ids[]';
      input.value = id;
      form.appendChild(input);
    });
    form.submit();
  }
</script>

<?php render_footer(); ?>
