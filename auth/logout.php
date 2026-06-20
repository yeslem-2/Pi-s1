<?php
// ============================================
// auth/logout.php - Logout Handler
// ============================================
require_once '../config.php';

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page
header("Location: " . BASE_URL . "/auth/login.php");
exit();
?>
