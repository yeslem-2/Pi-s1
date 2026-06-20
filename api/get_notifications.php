<?php
// ============================================
// api/get_notifications.php - API: Get Notifications
// ============================================
header('Content-Type: application/json');
require_once '../config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$conn = getDBConnection();

// Get unread count
$count_result = $conn->query("SELECT COUNT(*) as count FROM notifications WHERE is_read = 0");
$unread_count = $count_result->fetch_assoc()['count'];

// Get recent notifications (last 10)
$result = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10");
$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

$conn->close();

echo json_encode([
    'success' => true,
    'unread_count' => intval($unread_count),
    'notifications' => $notifications
]);
?>
