<?php
require_once __DIR__ . '/../bootstrap.php';

function login_user($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        return true;
    }
    return false;
}

function register_user($username, $email, $password) {
    global $pdo;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return "Email invalide.";
    if (strlen($password) < 6) return "Mot de passe trop court.";

    $check = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $check->execute([$username, $email]);
    if ($check->fetch()) return "Nom d'utilisateur ou email déjà utilisé.";

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)")
        ->execute([$username, $email, $hash]);

    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['username'] = $username;
    return true;
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function is_login_blocked($ip) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }

    $attempts = $_SESSION['login_attempts'][$ip] ?? ['count' => 0, 'last' => time()];
    
    if ($attempts['count'] >= 5 && time() - $attempts['last'] < 60) {
        return true;
    }

    return false;
}

function record_login_attempt($ip, $success) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }

    if ($success) {
        unset($_SESSION['login_attempts'][$ip]);
    } else {
        $current = $_SESSION['login_attempts'][$ip] ?? ['count' => 0, 'last' => time()];
        $current['count']++;
        $current['last'] = time();
        $_SESSION['login_attempts'][$ip] = $current;
    }
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function current_user_id(): ?int {
    return $_SESSION['user_id'] ?? null;
}