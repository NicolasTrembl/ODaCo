<?php
/* Usefull to write less require */

require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/core/includes/db.php';
require_once BASE_PATH . '/core/includes/functions.php';
require_once BASE_PATH . '/core/includes/auth.php';
require_once BASE_PATH . '/core/includes/layout.php';

session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}