<?php
function render_header($title = "ODaCo") {
    $username = $_SESSION['username'] ?? null;
    $lang = htmlspecialchars($_SESSION['lang'] ?? 'fr');
    echo <<<HTML
        <!DOCTYPE html>
        <html lang="$lang">
        <head>

            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1" />

            <title>$title</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
            <link rel="stylesheet" href="../assets/css/theme.css"></link>
            <!-- AVOID THEME FLASHING -->
            <script>
              (function() {
                const theme = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', theme);

                if (theme === 'custom') {
                  try {
                    const custom = JSON.parse(localStorage.getItem('custom_theme') || '{}');
                    if (custom.text)     document.documentElement.style.setProperty('--text-color', custom.text);
                    if (custom.bg)       document.documentElement.style.setProperty('--background-color', custom.bg);
                    if (custom.primary)  document.documentElement.style.setProperty('--primary-color', custom.primary);
                    if (custom.secondary)document.documentElement.style.setProperty('--secondary-color', custom.secondary);
                  } catch (e) {
                    console.error("Thème personnalisé invalide", e);
                  }
                }
              })();
            </script>

        </head>
        <body class="bg tcolor">
            <header class="pcolor shadow p-4 flex justify-between items-center relative sticky top-0 z-30">

                <h1 class="text-xl font-bold"><a href="index.php">ODaCo</a></h1>

                <!-- Desktop nav -->
                <nav class="hidden md:flex space-x-4">
    HTML;

    if ($username) {
        echo '<a href="search.php" class="hover:underline">' . htmlspecialchars(t("Rechercher")) . '</a>';
        echo '<a href="users.php" class="hover:underline">' . htmlspecialchars(t("Cherche_User")) . '</a>';
        echo '<a href="add.php" class="hover:underline">' . htmlspecialchars(t("Ajout")) . '</a>';
        echo '<a href="todo.php" class="hover:underline">' . htmlspecialchars(t("Todo")) . '</a>';
        echo '<a href="settings.php" class="hover:underline">' . htmlspecialchars(t("Réglages")) . '</a>';
        echo '<a href="logout.php" class="text-red-700 hover:underline">' . htmlspecialchars(t("Deconnexion")) . '</a>';
    } else {
        echo '<a href="login.php" class="text-blue-600 hover:underline">Connexion</a>';
    }

    echo <<<HTML
        </nav>

        <!-- Burger -->
        <button id="burger" class="md:hidden focus:outline-none">
        <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        </button>

        <!-- Mobile menu -->
        <div id="mobileMenu" class="fixed top-0 right-0 h-full w-64 bg shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50 p-6 space-y-4">
    HTML;

    if ($username) {
        echo '<a href="search.php" class="block hover:underline">' . htmlspecialchars(t("Rechercher")) . '</a>';
        echo '<a href="users.php" class="block hover:underline">' . htmlspecialchars(t("Cherche_User")) . '</a>';
        echo '<a href="add.php" class="block hover:underline">' . htmlspecialchars(t("Ajout")) . '</a>';
        echo '<a href="todo.php" class="block hover:underline">' . htmlspecialchars(t("Todo")) . '</a>';
        echo '<a href="settings.php" class="block hover:underline">' . htmlspecialchars(t("Réglages")) . '</a>';
        echo '<a href="logout.php" class="block text-red-700 hover:underline">' . htmlspecialchars(t("Deconnexion")) . '</a>';
    } else {
        echo '<a href="login.php" class="block text-blue-600 hover:underline">Connexion</a>';
    }

    echo <<<HTML
    </div>

    <!-- Overlay (optional) -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40"></div>
  </header>

  <main class="p-6">

  <script>
    \$(function () {
      \$("#burger").click(function () {
        \$("#mobileMenu").removeClass("translate-x-full");
        \$("#overlay").show();
      });

      \$("#overlay").click(function () {
        \$("#mobileMenu").addClass("translate-x-full");
        \$(this).hide();
      });
    });
  </script>
HTML;
}

function render_footer() {
    $text = t("Texte_Footer");
    $text = str_replace("{year}", date("Y"), $text);
    echo <<<HTML
    </main>
    <footer class="text-center text-sm py-4 mt-auto">
      {$text}
    </footer>
  </body>
</html>
HTML;
}
