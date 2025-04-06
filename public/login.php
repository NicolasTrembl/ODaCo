<?php
session_start();
require_once '../core/bootstrap.php';

$mode = $_POST['mode'] ?? 'login';
$error = null;

$ip = $_SERVER['REMOTE_ADDR'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  
  if (!csrf_check($_POST['csrf'] ?? '')) {
    $error = "Token CSRF invalide.";
  } elseif (is_login_blocked($ip)) {
    $error = "Trop de tentatives. Réessaie dans 1 minute.";
  } elseif ($mode === 'register') {
    $result = register_user($_POST['username'], $_POST['email'], $_POST['password']);
    if ($result === true) header('Location: index.php');
    else $error = $result;
  } elseif ($mode === 'login') {
    $ok = login_user($_POST['username'], $_POST['password']);
    record_login_attempt($ip, $ok);
    if ($ok) header('Location: index.php');
    else $error = "Mauvais identifiants.";
  }
} 
  

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion / Inscription</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function toggleMode(mode) {
      document.getElementById('mode').value = mode;
      document.getElementById('form-title').textContent = mode === 'login' ? 'Connexion' : 'Créer un compte';
      document.getElementById('email-field').style.display = mode === 'register' ? 'block' : 'none';
    }
  </script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <div class="bg-white p-6 rounded shadow-md w-full max-w-sm">
    <h2 id="form-title" class="text-xl font-semibold mb-4">Connexion</h2>

    <div class="flex justify-center mb-4">
      <button onclick="toggleMode('login')" class="px-3 py-1 border rounded-l text-sm">Connexion</button>
      <button onclick="toggleMode('register')" class="px-3 py-1 border rounded-r text-sm">Inscription</button>
    </div>

    <?php if ($error): ?>
      <p class="text-red-500 mb-3"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-3">
      <input type="hidden" name="mode" id="mode" value="<?= htmlspecialchars($mode) ?>">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <div>
        <input type="text" name="username" placeholder="Nom d'utilisateur" required
               class="w-full px-3 py-2 border rounded">
      </div>
      <div id="email-field" style="<?= $mode === 'register' ? '' : 'display:none;' ?>">
        <input type="email" name="email" placeholder="Adresse email"
               class="w-full px-3 py-2 border rounded">
      </div>
      <div>
        <input type="password" name="password" placeholder="Mot de passe" required
               class="w-full px-3 py-2 border rounded">
      </div>
      <button type="submit"
              class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Valider
      </button>
    </form>
  </div>
</body>
</html>
