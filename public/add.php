<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_login();
render_header(t("Ajouter_Recette"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $icon = trim($_POST['icon']);
    $description = trim($_POST['description'] ?? '');
    $extra = trim($_POST['extra_info'] ?? '');
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id || empty($title)) {
        echo "<p class='text-red-500'>" . t("Titre_manquant") . "</p>";
        return;
    }

    $cover_path = null;
    if (!empty($_FILES['cover']['tmp_name'])) {
        $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $cover_path = 'uploads/cover/' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['cover']['tmp_name'], __DIR__ . '/' . $cover_path);
    }

    $stmt = $pdo->prepare("INSERT INTO recipes (user_id, title, icon, cover, description, extra_info) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $icon, $cover_path, $description, $extra]);
    $recipe_id = $pdo->lastInsertId();

    foreach ($_POST['ingredient_name'] as $i => $name) {
        $name = trim($name);
        if ($name !== '') {
            $quantity = $_POST['ingredient_quantity'][$i] ?? '';
            $unit = $_POST['ingredient_unit'][$i] ?? '';
            $stmt = $pdo->prepare("INSERT INTO ingredients (recipe_id, name, quantity, unit) VALUES (?, ?, ?, ?)");
            $stmt->execute([$recipe_id, $name, $quantity, $unit]);
        }
    }

    $steps_upload_dir = __DIR__ . '/uploads/steps';
    if (!is_dir($steps_upload_dir)) {
        mkdir($steps_upload_dir, 0755, true);
    }

    foreach ($_POST['step_content'] as $i => $content) {
        $content = trim($content);
        $image_path = null;

        if (!empty($_FILES['step_image']['tmp_name'][$i])) {
            $ext = pathinfo($_FILES['step_image']['name'][$i], PATHINFO_EXTENSION);
            $image_path = 'uploads/steps/' . uniqid() . '.' . $ext;
            $full_path = __DIR__ . '/' . $image_path;
            
            if (!move_uploaded_file($_FILES['step_image']['tmp_name'][$i], $full_path)) {
                // If move fails, set image_path to null
                error_log("Failed to move uploaded step image: " . $_FILES['step_image']['name'][$i]);
                $image_path = null;
            }
        }

        if ($content !== '' || $image_path) {
            $stmt = $pdo->prepare("INSERT INTO steps (recipe_id, step_order, content, image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$recipe_id, $i, $content, $image_path]);
        }
    }

    echo "<p class='text-green-600 font-bold'>" . t("Ajout_Succes") . "</p>";
}


?>
<h2 class="text-2xl font-semibold mb-4"><?php echo t("Ajout") ?></h2>

<form action="add.php" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-2xl mx-auto p-4">

  <div>
    <label class="block mb-1 font-medium"><?php echo t("Nom_Cocktail") ?></label>
    <input type="text" name="title" class="w-full p-2 border rounded" required>
  </div>

  <div>
    <label class="block mb-1 font-medium"><?php echo t("Icone_Cocktail") ?></label>
    <select name="icon" class="w-full p-2 border rounded">
      <option value="🍸">🍸 Martini</option>
      <option value="🍹">🍹 Tropical</option>
      <option value="🍺">🍺 Bière</option>
      <option value="🥃">🥃 Whisky</option>
      <option value="🍷">🍷 Vin</option>
      <option value="🧉">🧉 Maté</option>
      <option value="🥂">🥂 Champagne</option>
    </select>
  </div>

  <div>
    <label class="block mb-1 font-medium"><?php echo t("Photo_Principale") ?></label>
    <input type="file" name="cover" accept="image/*" class="block">
  </div>

  <div>
    <label class="block mb-1 font-medium"><?php echo t("Description") ?></label>
    <textarea name="description" rows="4" class="w-full p-2 border rounded"></textarea>
  </div>

  <div>
    <label class="block mb-1 font-medium"><?php echo t("Ingrédients") ?></label>
    <div id="ingredients" class="space-y-2">
      <div class="flex flex-col sm:flex-row gap-2">
        <input type="text" name="ingredient_name[]" placeholder="<?php echo t("Nom") ?>" class="flex-1 p-2 border rounded">
        <input type="text" name="ingredient_quantity[]" placeholder="<?php echo t("Quantité") ?>" class="sm:w-24 p-2 border rounded">
        <input type="text" name="ingredient_unit[]" placeholder="<?php echo t("Unité") ?>" class="sm:w-24 p-2 border rounded">
      </div>
    </div>
    <button type="button" id="add-ingredient" class="mt-2 text-blue-600 hover:underline"><?php echo t("Ajout_Ingrédients") ?></button>
  </div>

  <div>
    <label class="block mb-1 font-medium"><?php echo t("Etapes") ?></label>
    <div id="steps" class="space-y-4">
      <div>
        <textarea name="step_content[]" placeholder="<?= t("Texte_Etape") ?>" rows="2" class="w-full p-2 border rounded"></textarea>
        <input type="file" name="step_image[]" accept="image/*" class="mt-1">
      </div>
    </div>
    <button type="button" id="add-step" class="mt-2 text-blue-600 hover:underline"><?php echo t("Ajout_Etapes") ?></button>
  </div>

  <div>
    <label class="block mb-1 font-medium"><?php echo t("Variantes") ?></label>
    <textarea name="extra_info" rows="3" class="w-full p-2 border rounded"></textarea>
  </div>

  <div>
    <button type="submit" class="scolor px-6 py-2 rounded"><?php echo t("Ajout_Recette") ?></button>
  </div>
</form>


<script>

  const MAX_FILE_SIZE = 2 * 1024 * 1024;
  
  function validateFileSize(fileInput) {
    if (fileInput.files.length > 0) {
      const fileSize = fileInput.files[0].size;
      if (fileSize > MAX_FILE_SIZE) {
        alert('File size exceeds 2MB limit. Please choose a smaller image.');
        fileInput.value = '';
        return false;
      }
    }
    return true;
  }
  
  $('input[name="cover"]').change(function() {
    validateFileSize(this);
  });
  
  $(document).on('change', 'input[name="step_image[]"]', function() {
    validateFileSize(this);
  });
  
  $('form').submit(function(e) {
    let isValid = true;
    
    if ($('input[name="cover"]')[0].files.length > 0) {
      isValid = validateFileSize($('input[name="cover"]')[0]);
    }
    
    $('input[name="step_image[]"]').each(function() {
      if (this.files.length > 0) {
        if (!validateFileSize(this)) {
          isValid = false;
        }
      }
    });
    
    if (!isValid) {
      e.preventDefault();
    }
  });
  

  $(function () {
    $('#add-ingredient').click(function () {
      const newInput = `
        <div class="flex flex-col sm:flex-row gap-2">
          <input type="text" name="ingredient_name[]" placeholder="Nom" class="flex-1 p-2 border rounded">
          <input type="text" name="ingredient_quantity[]" placeholder="Quantité" class="sm:w-24 p-2 border rounded">
          <input type="text" name="ingredient_unit[]" placeholder="Unité" class="sm:w-24 p-2 border rounded">
        </div>`;
      $('#ingredients').append(newInput);
    });

    $('#add-step').click(function () {
      const newStep = `
        <div>
          <textarea name="step_content[]" placeholder="<?= t("Texte_Etape") ?>" rows="2" class="w-full p-2 border rounded"></textarea>
          <input type="file" name="step_image[]" accept="image/*" class="mt-1">
        </div>`;
      $('#steps').append(newStep);
    });
  });
</script>

<?= render_footer() ?>