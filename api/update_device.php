<?php
// ============================================
// api/update_device.php - API: Update Device Status
// ============================================
header('Content-Type: application/json');
require_once '../config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$type = $input['type'] ?? '';
$status = $input['status'] ?? '';

$conn = getDBConnection();

if ($type === 'device') {
    $status_val = $status === true || $status === 'true' ? 'ON' : 'OFF';
    $stmt = $conn->prepare("UPDATE device_status SET status = ? WHERE id = 1");
    $stmt->bind_param("s", $status_val);
    $stmt->execute();
    $stmt->close();
    addNotification("Device turned " . $status_val . " by " . $_SESSION['username'], "info");
    echo json_encode(['success' => true, 'message' => 'Device updated', 'status' => $status_val]);

} elseif ($type === 'ac') {
    $status_val = $status === true || $status === 'true' ? 'ON' : 'OFF';
    $stmt = $conn->prepare("UPDATE device_status SET ac_status = ? WHERE id = 1");
    $stmt->bind_param("s", $status_val);
    $stmt->execute();
    $stmt->close();
    addNotification("AC turned " . $status_val . " by " . $_SESSION['username'], "info");
    echo json_encode(['success' => true, 'message' => 'AC updated', 'status' => $status_val]);

} elseif ($type === 'auto') {
    $mode = $status === true || $status === 'true' ? 1 : 0;
    $stmt = $conn->prepare("UPDATE device_status SET auto_mode = ? WHERE id = 1");
    $stmt->bind_param("i", $mode);
    $stmt->execute();
    $stmt->close();
    $conn->query("UPDATE settings SET auto_mode_enabled = $mode WHERE id = 1");
    addNotification("Auto mode " . ($mode ? "enabled" : "disabled") . " by " . $_SESSION['username'], "info");
    echo json_encode(['success' => true, 'message' => 'Auto mode updated', 'status' => $mode]);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid type']);
}

$conn->close();
?>
