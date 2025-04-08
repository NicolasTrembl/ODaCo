<?php
session_start();

$lang = $_POST['lang'] ?? 'fr';
$redirect = $_POST['redirect'] ?? '/';

if (in_array($lang, ['fr', 'en'])) {
    $_SESSION['lang'] = $lang;
}

header("Location: $redirect");
exit;
