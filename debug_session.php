<?php
require_once 'includes/config.php';

echo "<h2>Session Debug Info</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n\n";

echo "All Session Variables:\n";
print_r($_SESSION);

echo "\nSpecific Admin Variables:\n";
echo "admin_logged_in: " . (isset($_SESSION['admin_logged_in']) ? $_SESSION['admin_logged_in'] : 'NOT SET') . "\n";
echo "admin_username: " . (isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'NOT SET') . "\n";
echo "admin_user_id: " . (isset($_SESSION['admin_user_id']) ? $_SESSION['admin_user_id'] : 'NOT SET') . "\n";
echo "admin_role: " . (isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'NOT SET') . "\n";
echo "</pre>";

echo "<br><a href='admin/logout.php'>Force Logout</a>";
?>