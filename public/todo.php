<?php
require_once __DIR__ . '/../core/bootstrap.php';
render_header(title: t("Todo"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['ids'] ?? [];

    if (!is_array($ids) || empty($ids)) {
        echo "<p>" . t("Pas_Todo") . "</p>";
    } else {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $recipes = $stmt->fetchAll();

        if (!$recipes) {
            echo "<p>" . t("Pas_Todo_Trouvé") . "</p>";
        } else {
            echo '<h2 class="text-2xl font-bold mb-4">' . t("Ma_Todo") . '</h2>';
            echo '<div id="todo-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">';
            foreach ($recipes as $recipe) {
                $id = $recipe['id'];
                echo '<div class="todo-item p-4 border rounded transition-all" data-id="' . $id . '">';
                echo '<label class="flex items-start gap-3 cursor-pointer">';
                echo '<input type="checkbox" class="mt-1 done-checkbox">';
                echo '<div class="flex-1">';
                echo '<h3 class="text-xl font-semibold title">' . htmlspecialchars($recipe['title']) . '</h3>';
                echo '<p class="text-gray-600 description">' . htmlspecialchars(substr($recipe['description'], 0, 100)) . '...</p>';
                echo '</div>';
                echo '</label>';
                echo '<a href="recipe.php?id=' . $id . '" class="block text-sm text-blue-600 mt-2 hover:underline">' . t("Voir_Recette") . '</a>';
                echo '</div>';
            }
            echo '</div>';
        }
    }

    render_footer();
    exit;
}
?>

<h2 class="text-2xl font-bold mb-4"><?= t("Ma_Todo") ?></h2>
<p><?= t("Chargement") ?></p>

<form id="todo-form" method="POST" style="display: none;">
  <input type="hidden" name="ids[]">
</form>

<script>
  const todoIds = JSON.parse(localStorage.getItem('todo_recipes') || '[]');
  const doneIds = JSON.parse(localStorage.getItem('todo_recipes_done') || '[]');

  if (todoIds.length === 0) {
    $('p').text('<?= t("Liste_Vide") ?>');
  } else {
    const $form = $('#todo-form');
    todoIds.forEach(id => {
      $form.append(`<input type="hidden" name="ids[]" value="${id}">`);
    });
    $form.submit();
  }

  $(document).ready(function () {
    const done = new Set(JSON.parse(localStorage.getItem('todo_recipes_done') || '[]'));

    $('.todo-item').each(function () {
      const $item = $(this);
      const id = $item.data('id');

      const $checkbox = $item.find('.done-checkbox');
      const $title = $item.find('.title');
      const $desc = $item.find('.description');

      if (done.has(id)) {
        $checkbox.prop('checked', true);
        $item.addClass('opacity-50 line-through');
      }

      $checkbox.on('change', function () {
        if (this.checked) {
          done.add(id);
          $item.addClass('opacity-50 line-through');
        } else {
          done.delete(id);
          $item.removeClass('opacity-50 line-through');
        }

        localStorage.setItem('todo_recipes_done', JSON.stringify([...done]));
      });
    });
  });
</script>

<?php render_footer(); ?>
