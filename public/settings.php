<?php
require_once __DIR__ . '/../core/bootstrap.php';

require_login();
render_header(t("Réglages"));
?>

<h2 class="text-2xl font-bold mb-4"><?= t("Tout_Réglages") ?></h2>

<!-- Langue -->
<div class="mb-6">
  <h3 class="text-xl font-semibold mb-2"><?= t("Langue") ?></h3>
  <form method="POST" action="/set_lang.php" class="flex gap-4">
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
      class="px-4 py-2 rounded <?= $currentLang === $langKey ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">
      <?= htmlspecialchars($langName) ?>
      </button>
    <?php endforeach; ?>
  </form>
</div>


<!-- Thème -->
<div class="mb-6">
  <h3 class="text-xl font-semibold mb-2"><?= t("Theme") ?></h3>
  <div class="flex gap-4">
    <button disabled class="bg-gray-200 px-4 py-2 rounded cursor-not-allowed"><?= t("Theme_Clair") ?></button>
    <button disabled class="bg-gray-200 px-4 py-2 rounded cursor-not-allowed"><?= t("Theme_Sombre") ?></button>
  </div>
</div>

<!-- Déconnexion -->
<div class="mb-6">
  <h3 class="text-xl font-semibold mb-2"><?= t("Deconnexion") ?></h3>
  <form action="logout.php" method="post">
    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"><?= t("Se_Deconnecter") ?></button>
  </form>
</div>

<!-- Suppression de compte -->
<div class="mb-6">
  <h3 class="text-xl font-semibold mb-2"><?= t("Suppr_Compte") ?></h3>
  <form action="delete_account.php" method="post" onsubmit="return confirm('<?= t('Confirmer_Suppr_Compte') . t('Confirmer_Suppr_Compte_2') ?>')">
    <button type="submit" class="bg-black text-white px-4 py-2 rounded hover:bg-red-700"><?= t("Suppr_Compte") ?></button>
  </form>
</div>

<?php render_footer(); ?>
