<?php
// admin/config.php - Admin configuration
require_once '../includes/config.php';
require_once '../includes/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Admin language strings
$admin_lang = [];
if ($current_language === 'fr') {
    $admin_lang = [
        'dashboard' => 'Tableau de bord',
        'sections' => 'Sections',
        'chapters' => 'Chapitres',
        'pages' => 'Pages',
        'users' => 'Utilisateurs',
        'settings' => 'Paramètres',
        'logout' => 'Déconnexion',
        'welcome' => 'Bienvenue dans l\'administration',
        'total_sections' => 'Sections totales',
        'total_chapters' => 'Chapitres totaux',
        'total_pages' => 'Pages totales',
        'recent_activity' => 'Activité récente',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'add_new' => 'Ajouter nouveau',
        'title' => 'Titre',
        'description' => 'Description',
        'content' => 'Contenu',
        'status' => 'Statut',
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'created_at' => 'Créé le',
        'updated_at' => 'Mis à jour le',
        'actions' => 'Actions'
    ];
} else {
    $admin_lang = [
        'dashboard' => 'Dashboard',
        'sections' => 'Sections',
        'chapters' => 'Chapters',
        'pages' => 'Pages',
        'users' => 'Users',
        'settings' => 'Settings',
        'logout' => 'Logout',
        'welcome' => 'Welcome to Admin',
        'total_sections' => 'Total Sections',
        'total_chapters' => 'Total Chapters',
        'total_pages' => 'Total Pages',
        'recent_activity' => 'Recent Activity',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'add_new' => 'Add New',
        'title' => 'Title',
        'description' => 'Description',
        'content' => 'Content',
        'status' => 'Status',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'actions' => 'Actions'
    ];
}

// Get stats for dashboard
$db = new Database();
$conn = $db->getConnection();

$stats = [];
if ($conn) {
    // Total counts
    $stmt = $conn->query("SELECT COUNT(*) as count FROM sections WHERE is_active = TRUE");
    $stats['sections'] = $stmt->fetch()['count'];
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM chapters WHERE is_active = TRUE");
    $stats['chapters'] = $stmt->fetch()['count'];
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM pages WHERE is_active = TRUE");
    $stats['pages'] = $stmt->fetch()['count'];
    
    // Recent activity (last 10 updated pages)
    $stmt = $conn->query("
        SELECT p.title_en, p.title_fr, p.updated_at, 
               c.title_en as chapter_en, c.title_fr as chapter_fr
        FROM pages p
        JOIN chapters c ON p.chapter_id = c.id
        ORDER BY p.updated_at DESC
        LIMIT 10
    ");
    $stats['recent_activity'] = $stmt->fetchAll();
}
?>