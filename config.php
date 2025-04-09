<?php

define('BASE_PATH', dirname(__FILE__));
define('CORE_PATH', BASE_PATH . '/core');
define('UPLOADS_PATH', BASE_PATH . '/uploads');

define('BASE_URL', '/');
define('PUBLIC_URL', BASE_URL . 'public');
define('UPLOADS_URL', BASE_URL . 'uploads');

define('DB_DRIVER', 'sqlite');
define('DB_FILE', BASE_PATH . '/sql/database.sqlite');

define('SITE_NAME', 'ODaCo');
define('SITE_VERSION', '1.0');

define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/avi', 'video/mkv']);

if (file_exists(DB_FILE)) {
    // echo "✅ La base SQLite existe déjà : " . DB_FILE;   
} else {

    if (!file_exists(dirname(DB_FILE))) {
        mkdir(dirname(DB_FILE), 0755, true);
    }
    
    $schemaFile = BASE_PATH . '/sql/schema.sql';
    if (!file_exists($schemaFile)) {
        die("❌ Le fichier de structure 'schema.sql' est introuvable." . PHP_EOL . $schemaFile);
    }
    
    $schemaSQL = file_get_contents($schemaFile);
    
    try {
        $pdo = new PDO('sqlite:' . DB_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec($schemaSQL);
        // echo "✅ Base de données SQLite créée avec succès : " . DB_FILE;
    } catch (Exception $e) {
        die("❌ Erreur lors de la création de la base : " . $e->getMessage());
    }
}


