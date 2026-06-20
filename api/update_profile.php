<?php
// ============================================
// api/update_profile.php - Update user profile info, door code, and session lock
// ============================================
header('Content-Type: application/json');
require_once '../config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$user_id = $_SESSION['user_id'];

// ---- Lock profile (clear unlock flag) ----
if ($action === 'lock') {
    unset($_SESSION['profile_unlocked']);
    echo json_encode(['success' => true]);
    exit();
}

// ---- Verify door code to unlock profile ----
if ($action === 'verify_code') {
    $code = $input['code'] ?? '';
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT door_code FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();

    if ($row['door_code'] !== null && password_verify($code, $row['door_code'])) {
        $_SESSION['profile_unlocked'] = true;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect code']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit();
}

$conn = getDBConnection();

// ---- Update name & email ----
if ($action === 'update_info') {
    $full_name = trim($input['full_name'] ?? '');
    $email    = trim($input['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Enter a valid email address']);
        $conn->close(); exit();
    }

    // Ensure email is not taken by another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already in use by another account']);
        $stmt->close(); $conn->close(); exit();
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $full_name, $email, $user_id);

    if ($stmt->execute()) {
        $_SESSION['email']     = $email;
        $_SESSION['full_name'] = $full_name;
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error – update failed']);
    }
    $stmt->close();

// ---- Set / update door security code ----
} elseif ($action === 'update_door_code') {
    $current_code = $input['current_code'] ?? '';
    $new_code     = $input['new_code'] ?? '';

    if (!preg_match('/^\d{4,6}$/', $new_code)) {
        echo json_encode(['success' => false, 'message' => 'Code must be 4 – 6 digits']);
        $conn->close(); exit();
    }

    // If a code already exists, verify it first
    $stmt = $conn->prepare("SELECT door_code FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row['door_code'] !== null && !password_verify($current_code, $row['door_code'])) {
        echo json_encode(['success' => false, 'message' => 'Current code is incorrect']);
        $conn->close(); exit();
    }

    $hashed = password_hash($new_code, PASSWORD_BCRYPT);
    $stmt   = $conn->prepare("UPDATE users SET door_code = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed, $user_id);

    if ($stmt->execute()) {
        $_SESSION['profile_unlocked'] = true;
        echo json_encode(['success' => true, 'message' => 'Door code updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error – update failed']);
    }
    $stmt->close();

} else {
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
}

$conn->close();
?>
