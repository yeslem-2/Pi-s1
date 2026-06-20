<?php
// ============================================
// api/door_control.php - Validate user code then open/close/toggle door
// ============================================
header('Content-Type: application/json');
require_once '../config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST required']);
    exit();
}

$input   = json_decode(file_get_contents('php://input'), true);
$code    = $input['code']   ?? '';
$action  = $input['action'] ?? 'toggle'; // 'open' | 'close' | 'toggle'
$user_id = $_SESSION['user_id'];

$conn = getDBConnection();

// 1. Load user's door code
$stmt = $conn->prepare("SELECT door_code FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($row['door_code'] === null) {
    echo json_encode(['success' => false, 'message' => 'No door code set. Go to Profile → Settings to create one.']);
    $conn->close(); exit();
}

if (!password_verify($code, $row['door_code'])) {
    addNotification("Failed door access attempt by " . $_SESSION['username'], "warning");
    echo json_encode(['success' => false, 'message' => 'Incorrect code. Try again.']);
    $conn->close(); exit();
}

// 2. Determine new status
$result  = $conn->query("SELECT door_status FROM device_status WHERE id = 1");
$device  = $result->fetch_assoc();
$current = $device['door_status'] ?? 'CLOSED';

if ($action === 'open')       { $new_status = 'OPEN'; }
elseif ($action === 'close')  { $new_status = 'CLOSED'; }
else                          { $new_status = $current === 'OPEN' ? 'CLOSED' : 'OPEN'; }

// 3. Persist
$stmt = $conn->prepare("UPDATE device_status SET door_status = ? WHERE id = 1");
$stmt->bind_param("s", $new_status);
$stmt->execute();
$stmt->close();
$conn->close();

addNotification("Door " . strtolower($new_status) . " by " . $_SESSION['username'], "info");

echo json_encode([
    'success' => true,
    'status'  => $new_status,
    'message' => 'Door ' . strtolower($new_status)
]);
?>
