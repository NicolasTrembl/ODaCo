<?php
require_once '../core/bootstrap.php';

$mode = $_POST['mode'] ?? 'login';
$error = null;

$ip = $_SERVER['REMOTE_ADDR'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  
  if (!csrf_check($_POST['csrf'] ?? '')) {
    $error = t("Erreur_token_csrf");
  } elseif (is_login_blocked($ip)) {
    $error = t("Erreur_trop_tentatives");
  } elseif ($mode === 'register') {
    $result = register_user($_POST['username'], $_POST['email'], $_POST['password']);
    if (!empty($_POST['remember_me'])) {
      $token = bin2hex(random_bytes(32));
      setcookie('auth_token', $token, time() + 86400 * 30, '/', '', false, true);
      $user_id = current_user_id();
      $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
      $stmt->execute([$token, $user_id]);
    }
    if ($result === true) header('Location: index.php');
    else $error = $result;
  } elseif ($mode === 'login') {
    $ok = login_user($_POST['username'], $_POST['password']);
    record_login_attempt($ip, $ok);
    if (!empty(($_POST['remember_me']))) {
      $token = bin2hex(random_bytes(32));
      setcookie('auth_token', $token, time() + (86400 * 30), '/', '', false, true);
      $user_id = current_user_id();
      $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
      $stmt->execute([$token, $user_id]);
    }
    if ($ok) header('Location: index.php');
    else $error = t("Erreur_Identifiant");
  }
} 
  
render_header(t("Connexion"));
?>

<main class="flex items-center justify-center h-screen">
  
    <div class="scolor p-6 rounded shadow-md w-full max-w-sm">
    <h2 id="form-title" class="text-xl font-semibold mb-4"><?php echo t("Connexion") ?></h2>

    <div class="flex justify-center mb-4">
      <button onclick="toggleMode('login')" class="px-3 py-1 pcolor border rounded-l text-sm"><?php echo t("Connexion") ?></button>
      <button onclick="toggleMode('register')" class="px-3 py-1 pcolor border rounded-r text-sm"><?php echo t("Inscription") ?></button>
    </div>

    <?php if ($error): ?>
      <p class=" mb-3"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-3">
      <input type="hidden" name="mode" id="mode" value="<?= htmlspecialchars($mode) ?>">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <div>
        <input type="text" name="username" placeholder="<?php echo t("Nom_Utilisateur") ?>" required
                class="w-full bg px-3 py-2 border rounded">
      </div>
      <div id="email-field" style="<?= $mode === 'register' ? '' : 'display:none;' ?>">
        <input type="email" name="email" placeholder="<?php echo t("Email") ?>"
                class="w-full bg px-3 py-2 border rounded">
      </div>
      <div>
        <input type="password" name="password" placeholder="<?php echo t("MDP") ?>" required
                class="w-full bg px-3 py-2 border rounded">
      </div>
      <label class="flex items-center space-x-2">
        <input type="checkbox" name="remember_me" class="form-checkbox">
        <span><?php echo t("Souvenir_MDP") ?></span>
      </label>
      <button type="submit"
              class="w-full bg px-4 py-2 rounded">
            <?php echo t("Valider") ?>
      </button>
    </form>
  </div>
</main>

<script>
    function toggleMode(mode) {
      document.getElementById('mode').value = mode;
      document.getElementById('form-title').textContent = mode === 'login' ? '<?php echo t("Connexion") ?>' : '<?php echo t("Inscription") ?>';
      document.getElementById('email-field').style.display = mode === 'register' ? 'block' : 'none';
    }
  </script>

<?php render_footer(); ?>