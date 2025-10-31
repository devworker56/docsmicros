<?php
// config.php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'u834808878_microdocs_db');
define('DB_USER', 'u834808878_docsAdmin');
define('DB_PASS', 'Ossouka@1968');

// Site configuration
define('SITE_NAME', 'GestionMicro-Dons Docs');
define('BASE_URL', 'https://docs.gestionmicrodons.com');

// Default language
$current_language = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en');
$_SESSION['lang'] = $current_language;

// Language strings
$lang = [];
if ($current_language === 'fr') {
    $lang = [
        'home' => 'Accueil',
        'search' => 'Rechercher',
        'for_donors' => 'Pour les Donateurs',
        'for_organizations' => 'Pour les Organismes', 
        'for_businesses' => 'Pour les Entreprises',
        'admin' => 'Administration',
        'previous' => 'Précédent',
        'next' => 'Suivant',
        'search_placeholder' => 'Rechercher dans la documentation...',
        'view_all' => 'Voir tout',
        'edit_page' => 'Modifier la page'
    ];
} else {
    $lang = [
        'home' => 'Home',
        'search' => 'Search', 
        'for_donors' => 'For Donors',
        'for_organizations' => 'For Organizations',
        'for_businesses' => 'For Businesses',
        'admin' => 'Admin',
        'previous' => 'Previous',
        'next' => 'Next',
        'search_placeholder' => 'Search documentation...',
        'view_all' => 'View all',
        'edit_page' => 'Edit Page'
    ];
}
?>