<?php
function render_header($title = "ODaCo") {
    $username = $_SESSION['username'] ?? null;
    echo <<<HTML
            <!DOCTYPE html>
            <html lang="
            HTML
            . htmlspecialchars($_SESSION['lang'] ?? 'fr') . 
        <<<HTML
            "><head>
            <meta charset="UTF-8">
            <title>$title</title>
            <script src="https://cdn.tailwindcss.com"></script>
            </head>
            <body class="bg-gray-100 text-gray-800">
            <header class="bg-white shadow p-4 flex justify-between items-center">
                <h1 class="text-xl font-bold text-pink-600"><a href="index.php">ODaCo</a></h1>
                <nav class="space-x-4">
            HTML;

    if ($username) {
        echo <<<HTML
                    <a href="search.php" class="text-gray-700 hover:underline"> 
                HTML . 
                htmlspecialchars(t("Rechercher")) . 
            <<<HTML
                </a><a href="add.php" class="text-gray-700 hover:underline"> 
                HTML . 
                htmlspecialchars(t("Ajout")) . 
            <<<HTML
                </a><a href="todo.php" class="text-gray-700 hover:underline"> 
                HTML . 
                htmlspecialchars(t("Todo")) . 
            <<<HTML
                </a><a href="settings.php" class="text-gray-700 hover:underline"> 
                HTML . 
                htmlspecialchars(t("Réglages")) . 
            <<<HTML
                </a><a href="logout.php" class="text-red-700 hover:underline"> 
                HTML . 
                htmlspecialchars(t("Deconnexion")) . 
            <<<HTML
                </a>
                
                HTML;
    } else {
        echo '<a href="login.php" class="text-blue-600 hover:underline">Connexion</a>';
    }

    echo <<<HTML
                </nav>
            </header>
            <main class="p-6">
            HTML;
}

function render_footer() {

    $text = t("Texte_Footer");
    $text = str_replace("{year}", date("Y"), $text);
    
    echo <<<HTML
            </main>
            <footer class="text-center text-sm text-gray-500 py-4 mt-auto">
            HTML
            . htmlspecialchars($text) .
        <<<HTML
            </footer>
            </body>
            </html>
            HTML;
}
