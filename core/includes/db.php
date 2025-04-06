<?php
require_once BASE_PATH . '/config.php';

try {
    if (DB_DRIVER === 'sqlite') {
        $pdo = new PDO('sqlite:' . DB_FILE);
    } else {
        // In case I use the wrong driver name, I will throw an exception
        throw new Exception("Unsupported database driver: " . DB_DRIVER);
    }

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}
