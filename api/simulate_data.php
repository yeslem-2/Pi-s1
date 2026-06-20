<?php
// ============================================
// api/simulate_data.php - API: Generate Simulated Sensor Data
// ============================================
header('Content-Type: application/json');
require_once '../config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Generate simulated temperature and humidity
// Temperature: random between 18°C and 38°C (with some variation from last reading)
// Humidity: random between 30% and 70%

$latest = getLatestReading();
$settings = getSettings();

if ($latest) {
    // Small variation from last reading (realistic simulation)
    $temp_change = (rand(-20, 20) / 10); // -2.0 to +2.0
    $humid_change = (rand(-15, 15) / 10); // -1.5 to +1.5

    $temperature = round(floatval($latest['temperature']) + $temp_change, 2);
    $humidity = round(floatval($latest['humidity']) + $humid_change, 2);

    // Keep within realistic bounds
    $temperature = max(15.0, min(45.0, $temperature));
    $humidity = max(20.0, min(80.0, $humidity));
} else {
    // First reading - start with reasonable defaults
    $temperature = round(rand(200, 280) / 10, 2); // 20.0 to 28.0
    $humidity = round(rand(350, 550) / 10, 2); // 35.0 to 55.0
}

// Insert into database
$conn = getDBConnection();
$stmt = $conn->prepare("INSERT INTO sensor_data (temperature, humidity) VALUES (?, ?)");
$stmt->bind_param("dd", $temperature, $humidity);
$stmt->execute();
$new_id = $conn->insert_id;
$stmt->close();

// Check thresholds and generate notifications
$max_temp = floatval($settings['max_temp']);
$min_temp = floatval($settings['min_temp']);
$max_humidity = floatval($settings['max_humidity']);
$min_humidity = floatval($settings['min_humidity']);

if ($temperature > $max_temp) {
    addNotification("Temperature alert: {$temperature}°C exceeds maximum threshold of {$max_temp}°C", "warning");
} elseif ($temperature < $min_temp) {
    addNotification("Temperature alert: {$temperature}°C is below minimum threshold of {$min_temp}°C", "warning");
}
if ($humidity > $max_humidity) {
    addNotification("Humidity alert: {$humidity}% exceeds maximum threshold of {$max_humidity}%", "warning");
} elseif ($humidity < $min_humidity) {
    addNotification("Humidity alert: {$humidity}% is below minimum threshold of {$min_humidity}%", "warning");
}

// Auto mode logic: if auto mode is enabled and temperature/humidity is out of range, turn on AC
$device = getDeviceStatus();
if ($device['auto_mode'] == 1) {
    $should_ac_on = ($temperature > $max_temp) || ($humidity > $max_humidity);
    $should_ac_off = ($temperature <= $max_temp && $humidity <= $max_humidity);
    if ($should_ac_on && $device['ac_status'] === 'OFF') {
        $conn->query("UPDATE device_status SET ac_status = 'ON' WHERE id = 1");
        addNotification("Auto mode: AC turned ON due to high temperature or humidity", "info");
    } elseif ($should_ac_off && $device['ac_status'] === 'ON') {
        $conn->query("UPDATE device_status SET ac_status = 'OFF' WHERE id = 1");
        addNotification("Auto mode: AC turned OFF - conditions normalized", "info");
    }
}

$conn->close();

echo json_encode([
    'success' => true,
    'data' => [
        'id' => $new_id,
        'temperature' => $temperature,
        'humidity' => $humidity,
        'recorded_at' => date('Y-m-d H:i:s')
    ]
]);
?>
