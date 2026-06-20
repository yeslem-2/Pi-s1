<?php
// ============================================
// api/mark_notifications_read.php - API: Mark Notifications as Read
// ============================================
header('Content-Type: application/json');
require_once '../config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$conn = getDBConnection();
$conn->query("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
$conn->close();

echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
?>
