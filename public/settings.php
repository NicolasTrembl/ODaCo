<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_login();
render_header(t("Réglages"));
?>

<h2 class="text-2xl font-bold mb-4"><?= t("Tout_Réglages") ?></h2>

<!-- Langue -->
<div class="mb-6">
  <h3 class="text-xl font-semibold mb-2"><?= t("Langue") ?></h3>
  <form method="POST" action="/set_lang.php" class="flex flex-wrap gap-4">
    <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
    <?php
    $languages = [];
    $langFile = __DIR__ . '/../lang.csv';
    
    if (file_exists($langFile)) {
      $csv = array_map('str_getcsv', file($langFile));
      $headers = array_shift($csv);
      
      foreach ($csv as $row) {
        if ($row[0] === 'LANGNAME') {
          for ($i = 1; $i < count($headers); $i++) {
            $languages[$headers[$i]] = $row[$i];
          }
          break;
        }
      }
    }
    
    $currentLang = $_SESSION['lang'] ?? 'fr';
    
    foreach ($languages as $langKey => $langName):
    ?>
      <button type="submit" name="lang" value="<?= htmlspecialchars($langKey) ?>"
      class="px-4 py-2 rounded <?= $currentLang === $langKey ? 'bg-blue-600 text-white' : 'bg-gray-500' ?>">
      <?= htmlspecialchars($langName) ?>
      </button>
    <?php endforeach; ?>
  </form>
</div>

<div class="mb-6">
  <h3 class="text-xl font-semibold mb-2"><?= t("Theme") ?></h3>
  <div class="flex flex-wrap gap-4 mb-4">
    <button id="theme-light" class="bg-gray-100 text-black px-4 py-2 rounded"><?= t("Theme_Clair") ?></button>
    <button id="theme-dark" class="bg-gray-800 text-white px-4 py-2 rounded"><?= t("Theme_Sombre") ?></button>
    <button id="theme-custom" class="bg-gradient-to-r from-pink-400 to-yellow-400 text-white px-4 py-2 rounded"><?= t("Theme_Personnalisé") ?></button>
  </div>

  <div id="custom-theme-form" class="hidden space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="flex items-center justify-between p-4 rounded-xl shadow-color border">
        <label for="color-text" class="font-medium"><?= t("Texte_C") ?></label>
        <input type="color" id="color-text" class="w-12 h-12 rounded-full border">
      </div>

      <div class="flex items-center justify-between p-4 rounded-xl shadow-color border">
        <label for="color-bg" class="font-medium"><?= t("Fond") ?></label>
        <input type="color" id="color-bg" class="w-12 h-12 rounded-full border">
      </div>

      <div class="flex items-center justify-between p-4 rounded-xl shadow-color border">
        <label for="color-primary" class="font-medium"><?= t("Primaire") ?></label>
        <input type="color" id="color-primary" class="w-12 h-12 rounded-full border">
      </div>

      <div class="flex items-center justify-between p-4 rounded-xl shadow-color border">
        <label for="color-secondary" class="font-medium"><?= t("Secondaire") ?></label>
        <input type="color" id="color-secondary" class="w-12 h-12 rounded-full border">
      </div>
    </div>

    <div class="text-right">
      <button id="apply-custom-theme" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl shadow-color transition">
        🎨 <?= t("Appliquer") ?>
      </button>
    </div>
  </div>

</div>

<div class="mb-6">
  <h3 class="text-xl font-semibold mb-2"><?= t("Deconnexion") ?></h3>
  <form action="logout.php" method="post">
    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"><?= t("Se_Deconnecter") ?></button>
  </form>
</div>

<div class="mb-6">
  <h3 class="text-xl font-semibold mb-2"><?= t("Suppr_Compte") ?></h3>
  <form action="delete_account.php" method="post" onsubmit="return confirm('<?= t('Confirmer_Suppr_Compte') . t('Confirmer_Suppr_Compte_2') ?>')">
    <button type="submit" class="bg-black text-white px-4 py-2 rounded hover:bg-red-700"><?= t("Suppr_Compte") ?></button>
  </form>
</div>

<script>
  function applyTheme(theme) {
    localStorage.setItem("theme", theme);
    document.documentElement.setAttribute("data-theme", theme);

    if (theme === "custom") {
      const custom = JSON.parse(localStorage.getItem("custom_theme") || "{}");
      if (custom.text)     document.documentElement.style.setProperty('--text-color', custom.text);
      if (custom.bg)       document.documentElement.style.setProperty('--background-color', custom.bg);
      if (custom.primary)  document.documentElement.style.setProperty('--primary-color', custom.primary);
      if (custom.secondary)document.documentElement.style.setProperty('--secondary-color', custom.secondary);
    }
  }

  $(document).ready(function () {
    const current = localStorage.getItem("theme") || "light";
    applyTheme(current);

    if (current === "custom") {
      $("#custom-theme-form").removeClass("hidden");
    }

    $("#theme-light").click(() => {
      applyTheme("light");
      $("#custom-theme-form").addClass("hidden");
    });

    $("#theme-dark").click(() => {
      applyTheme("dark");
      $("#custom-theme-form").addClass("hidden");
    });

    $("#theme-custom").click(() => {
      $("#custom-theme-form").removeClass("hidden");
    });

    $("#apply-custom-theme").click(() => {
      const custom = {
        text: $("#color-text").val(),
        bg: $("#color-bg").val(),
        primary: $("#color-primary").val(),
        secondary: $("#color-secondary").val()
      };
      localStorage.setItem("custom_theme", JSON.stringify(custom));
      applyTheme("custom");
    });
  });
</script>

<?php render_footer(); ?>
