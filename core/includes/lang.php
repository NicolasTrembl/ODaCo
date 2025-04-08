<?php

if(!isset($_SESSION['lang'])){
    $lang = 'fr';
}

function load_translations($lang = 'fr') {
    static $translations = [];

    if (isset($translations[$lang])) {
        return $translations[$lang];
    }

    $translations[$lang] = [];
    $file = __DIR__ . '/../../lang.csv';

    if (!file_exists($file)) return [];


    if (($handle = fopen($file, 'r')) !== false) {
        
        $headers = fgetcsv($handle); 

        if ($headers === false) {
            fclose($handle);
            return [];
        }

        $lang_index = array_search($lang, $headers);

        if ($lang_index === false) {
            fclose($handle);
            return [];
        }

        while (($row = fgetcsv($handle)) !== false) {
            $key = $row[0];
            $translations[$lang][$key] = $row[$lang_index] ?? $key;
        }

        fclose($handle);
    }

    return $translations[$lang];
}

function t($key) {
    $lang = $_SESSION['lang'] ?? 'fr';
    $translations = load_translations($lang);
    return $translations[$key] ?? $key;
}
