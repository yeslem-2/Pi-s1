<?php
// ============================================
// api/get_data.php - API: Get Latest Sensor Data
// ============================================
header('Content-Type: application/json');
require_once '../config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Get latest sensor reading
$latest = getLatestReading();

// Get device status
$device = getDeviceStatus();

// Get settings
$settings = getSettings();

$response = [
    'success' => true,
    'data' => [
        'temperature' => $latest ? floatval($latest['temperature']) : null,
        'humidity' => $latest ? floatval($latest['humidity']) : null,
        'recorded_at' => $latest ? $latest['recorded_at'] : null,
        'device_status' => $device['status'],
        'ac_status' => $device['ac_status'],
        'auto_mode' => intval($device['auto_mode']),
        'max_temp' => floatval($settings['max_temp']),
        'min_temp' => floatval($settings['min_temp']),
        'max_humidity' => floatval($settings['max_humidity']),
        'min_humidity' => floatval($settings['min_humidity'])
    ]
];

echo json_encode($response);
?>
