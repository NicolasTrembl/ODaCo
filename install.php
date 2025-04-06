<?php
/* Transform the /sql/schema.sql file into a SQLite database */
require_once __DIR__ . '/config.php';

if (file_exists(DB_FILE)) {
    echo "✅ La base SQLite existe déjà : " . DB_FILE;
    exit;
}

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
    echo "✅ Base de données SQLite créée avec succès : " . DB_FILE;
} catch (Exception $e) {
    die("❌ Erreur lors de la création de la base : " . $e->getMessage());
}
