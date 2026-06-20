<?php
// ============================================
// api/get_chart_data.php - API: Get Chart Data
// ============================================
header('Content-Type: application/json');
require_once '../config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$conn = getDBConnection();

// Get last 24 readings for chart
$result = $conn->query("SELECT * FROM sensor_data ORDER BY recorded_at DESC LIMIT 24");

$temps = [];
$humids = [];
$labels = [];

$rows = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}

// Reverse to get chronological order
$rows = array_reverse($rows);

foreach ($rows as $row) {
    $temps[] = floatval($row['temperature']);
    $humids[] = floatval($row['humidity']);
    $labels[] = date('H:i', strtotime($row['recorded_at']));
}

$conn->close();

echo json_encode([
    'success' => true,
    'datasets' => [
        [
            'label' => 'Temperature (°C)',
            'data' => $temps,
            'color' => '#ff9800',
            'fill' => true,
            'fillColor' => 'rgba(255, 152, 0, 0.1)'
        ],
        [
            'label' => 'Humidity (%)',
            'data' => $humids,
            'color' => '#2196f3',
            'fill' => true,
            'fillColor' => 'rgba(33, 150, 243, 0.1)'
        ]
    ],
    'labels' => $labels
]);
?>
