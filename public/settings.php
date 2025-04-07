<?php
require_once __DIR__ . '/../core/bootstrap.php';

require_login();
render_header("Réglages");
?>

<h2 class="text-2xl font-bold mb-4">⚙️ Réglages du compte</h2>

<!-- Langue -->
<div class="mb-6">
  <h3 class="text-xl font-semibold mb-2">Langue</h3>
  <div class="flex gap-4">
    <button disabled class="bg-gray-200 px-4 py-2 rounded cursor-not-allowed">🇫🇷 Français</button>
    <button disabled class="bg-gray-200 px-4 py-2 rounded cursor-not-allowed">🇬🇧 English</button>
  </div>
  <p class="text-sm text-gray-500 mt-1">Le changement de langue sera disponible plus tard.</p>
</div>

<!-- Thème -->
<div class="mb-6">
  <h3 class="text-xl font-semibold mb-2">Thème</h3>
  <div class="flex gap-4">
    <button disabled class="bg-gray-200 px-4 py-2 rounded cursor-not-allowed">🌞 Clair</button>
    <button disabled class="bg-gray-200 px-4 py-2 rounded cursor-not-allowed">🌙 Sombre</button>
  </div>
  <p class="text-sm text-gray-500 mt-1">La personnalisation du thème arrivera bientôt.</p>
</div>

<!-- Déconnexion -->
<div class="mb-6">
  <h3 class="text-xl font-semibold mb-2">Déconnexion</h3>
  <form action="logout.php" method="post">
    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">🚪 Se déconnecter</button>
  </form>
</div>

<!-- Suppression de compte -->
<div class="mb-6">
  <h3 class="text-xl font-semibold mb-2">Supprimer le compte</h3>
  <form action="delete_account.php" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">
    <button type="submit" class="bg-black text-white px-4 py-2 rounded hover:bg-red-700">🗑️ Supprimer mon compte</button>
  </form>
</div>

<?php render_footer(); ?>
