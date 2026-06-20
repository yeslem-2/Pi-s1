/* ============================================
   dashboard.js - Dashboard-specific JavaScript
   ============================================ */

// ============================================
// Auto-refresh sensor data
// ============================================
let chart = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize chart if canvas exists
    const canvas = document.getElementById('sensorChart');
    if (canvas) {
        chart = new SimpleChart('sensorChart', {
            height: 300,
            datasets: [
                {
                    label: 'Temperature (°C)',
                    data: [],
                    color: '#ff9800',
                    fill: true,
                    fillColor: 'rgba(255, 152, 0, 0.1)'
                },
                {
                    label: 'Humidity (%)',
                    data: [],
                    color: '#2196f3',
                    fill: true,
                    fillColor: 'rgba(33, 150, 243, 0.1)'
                }
            ],
            labels: []
        });

        // Load chart data
        loadChartData();
    }

    // Auto-refresh data every 5 seconds
    setInterval(updateDashboard, 5000);
});

// ============================================
// Update Dashboard Data via AJAX
// ============================================
function updateDashboard() {
    // First generate new simulated data
    fetch('../api/simulate_data.php')
        .then(response => response.json())
        .then(() => {
            // Then fetch updated data
            return fetch('../api/get_data.php');
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const d = data.data;

                // Update temperature
                const tempEl = document.getElementById('temperature');
                if (tempEl) tempEl.textContent = d.temperature;

                // Update humidity
                const humEl = document.getElementById('humidity');
                if (humEl) humEl.textContent = d.humidity;

                // Update last updated time
                const timeEl = document.getElementById('last-updated');
                if (timeEl) timeEl.textContent = d.recorded_at;

                // Update device status
                const deviceStatusEl = document.getElementById('device-status');
                if (deviceStatusEl) deviceStatusEl.textContent = d.device_status;

                const acStatusEl = document.getElementById('ac-status');
                if (acStatusEl) acStatusEl.textContent = d.ac_status;

                // Check for alerts
                checkAlerts(d.temperature, d.humidity, d.max_temp, d.min_temp, d.max_humidity, d.min_humidity);

                // Reload chart data
                if (chart) {
                    loadChartData();
                }
            }
        })
        .catch(err => console.error('Error updating dashboard:', err));
}

// ============================================
// Load Chart Data
// ============================================
function loadChartData() {
    fetch('../api/get_chart_data.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && chart) {
                chart.update(data.datasets, data.labels);
            }
        })
        .catch(err => console.error('Error loading chart:', err));
}

// ============================================
// Check for Alerts
// ============================================
function checkAlerts(temp, humidity, maxTemp, minTemp, maxHumidity, minHumidity) {
    if (temp > maxTemp) {
        showTemporaryAlert('Warning: Temperature is above threshold!', 'warning');
    } else if (temp < minTemp) {
        showTemporaryAlert('Warning: Temperature is below threshold!', 'warning');
    }
    if (humidity > maxHumidity) {
        showTemporaryAlert('Warning: Humidity is above threshold!', 'warning');
    } else if (humidity < minHumidity) {
        showTemporaryAlert('Warning: Humidity is below threshold!', 'warning');
    }
}

// ============================================
// Show Temporary Alert
// ============================================
function showTemporaryAlert(message, type) {
    // Check if alert already exists
    if (document.getElementById('temp-alert')) return;

    const alert = document.createElement('div');
    alert.id = 'temp-alert';
    alert.className = `alert alert-${type}`;
    alert.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 3000; min-width: 300px; animation: slideIn 0.3s ease;';
    alert.textContent = message;
    document.body.appendChild(alert);

    // Remove after 5 seconds
    setTimeout(() => {
        alert.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}

// Add animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
`;
document.head.appendChild(style);
