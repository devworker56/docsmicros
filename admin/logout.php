<?php
// admin/logout.php - Admin logout
require_once '../includes/config.php';

// Destroy admin session
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_user_id']);
unset($_SESSION['admin_role']);

// Redirect to login page
header('Location: login.php');
exit;
?>