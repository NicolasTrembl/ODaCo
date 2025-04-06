<?php
require_once __DIR__ . '/../core/bootstrap.php';

render_header("Accueil");

echo "<h2 class='text-2xl font-semibold mb-4'>Bienvenue " . ($_SESSION['username'] ?? "invité") . " !</h2>";


render_footer();
