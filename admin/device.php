<?php
$page_title = 'Device Control';
require_once '../config.php';
requireAdmin();
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
$device = getDeviceStatus();
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $conn = getDBConnection();
  if ($action === 'device') {
    $status = $_POST['status'] === 'true' ? 'ON' : 'OFF';
    $stmt = $conn->prepare("UPDATE device_status SET status = ? WHERE id = 1");
    $stmt->bind_param("s", $status); $stmt->execute(); $stmt->close();
    addNotification("Admin turned device " . $status, "info");
    $success = "Device turned " . $status;
  } elseif ($action === 'ac') {
    $status = $_POST['status'] === 'true' ? 'ON' : 'OFF';
    $stmt = $conn->prepare("UPDATE device_status SET ac_status = ? WHERE id = 1");
    $stmt->bind_param("s", $status); $stmt->execute(); $stmt->close();
    addNotification("Admin turned AC " . $status, "info");
    $success = "AC turned " . $status;
  } elseif ($action === 'auto') {
    $mode = $_POST['status'] === 'true' ? 1 : 0;
    $stmt = $conn->prepare("UPDATE device_status SET auto_mode = ? WHERE id = 1");
    $stmt->bind_param("i", $mode); $stmt->execute(); $stmt->close();
    $conn->query("UPDATE settings SET auto_mode_enabled = $mode WHERE id = 1");
    addNotification("Admin " . ($mode ? "enabled" : "disabled") . " auto mode", "info");
    $success = "Auto mode " . ($mode ? "enabled" : "disabled");
  } elseif ($action === 'door') {
    $door = strtoupper($_POST['door_status'] ?? 'CLOSED');
    if ($door !== 'OPEN' && $door !== 'CLOSED') $door = 'CLOSED';
    $stmt = $conn->prepare("UPDATE device_status SET door_status = ? WHERE id = 1");
    $stmt->bind_param("s", $door); $stmt->execute(); $stmt->close();
    addNotification("Admin set door to " . $door, "info");
    $success = "Door set to " . $door;
  }
  $conn->close();
  $device = getDeviceStatus();
}
?>
<?php include '../includes/navbar.php'; ?>
<main class="md:ml-64 pt-20 px-8 pb-12 min-h-screen bg-surface-container-low">
  <div class="mb-8">
    <h1 class="font-headline text-4xl font-bold text-on-surface tracking-tight">Device Control</h1>
    <p class="text-on-surface-variant mt-2 font-body text-sm">Global device management - changes affect all users.</p>
  </div>
  <?php if ($success): ?>
  <div class="mb-6 p-4 rounded-lg bg-tertiary-container/30 border border-tertiary/20 text-tertiary text-sm font-medium flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> <?php echo $success; ?></div>
  <?php endif; ?>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-high rounded-xl p-8 border border-white/5">
      <div class="flex items-center justify-between mb-8">
        <div>
          <p class="text-sm font-medium text-on-surface-variant">Main Device</p>
          <p class="text-[10px] text-slate-500 uppercase tracking-tighter">System Power</p>
        </div>
        <span class="material-symbols-outlined text-primary text-3xl">power_settings_new</span>
      </div>
      <form method="POST" action="">
        <input type="hidden" name="action" value="device">
        <input type="hidden" name="status" id="device-input" value="<?php echo $device['status'] === 'ON' ? 'true' : 'false'; ?>">
        <label class="relative inline-flex cursor-pointer items-center gap-3">
          <input type="checkbox" class="sr-only peer" id="device-toggle" <?php echo $device['status'] === 'ON' ? 'checked' : ''; ?> onchange="document.getElementById('device-input').value = this.checked; this.form.submit();">
          <div class="w-11 h-6 bg-outline peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tertiary-container"></div>
          <span class="text-sm font-bold <?php echo $device['status'] === 'ON' ? 'text-tertiary' : 'text-on-surface-variant'; ?>"><?php echo $device['status']; ?></span>
        </label>
      </form>
    </div>
    <div class="bg-surface-container-high rounded-xl p-8 border border-white/5">
      <div class="flex items-center justify-between mb-8">
        <div>
          <p class="text-sm font-medium text-on-surface-variant">Air Conditioner</p>
          <p class="text-[10px] text-slate-500 uppercase tracking-tighter">Cooling System</p>
        </div>
        <span class="material-symbols-outlined text-secondary text-3xl">ac_unit</span>
      </div>
      <form method="POST" action="">
        <input type="hidden" name="action" value="ac">
        <input type="hidden" name="status" id="ac-input" value="<?php echo $device['ac_status'] === 'ON' ? 'true' : 'false'; ?>">
        <label class="relative inline-flex cursor-pointer items-center gap-3">
          <input type="checkbox" class="sr-only peer" id="ac-toggle" <?php echo $device['ac_status'] === 'ON' ? 'checked' : ''; ?> onchange="document.getElementById('ac-input').value = this.checked; this.form.submit();">
          <div class="w-11 h-6 bg-outline peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tertiary-container"></div>
          <span class="text-sm font-bold <?php echo $device['ac_status'] === 'ON' ? 'text-tertiary' : 'text-on-surface-variant'; ?>"><?php echo $device['ac_status']; ?></span>
        </label>
      </form>
    </div>
    <div class="bg-surface-container-high rounded-xl p-8 border border-white/5">
      <div class="flex items-center justify-between mb-8">
        <div>
          <p class="text-sm font-medium text-on-surface-variant">Auto Mode</p>
          <p class="text-[10px] text-slate-500 uppercase tracking-tighter">AI-Driven Control</p>
        </div>
        <span class="material-symbols-outlined text-tertiary text-3xl">smart_toy</span>
      </div>
      <form method="POST" action="">
        <input type="hidden" name="action" value="auto">
        <input type="hidden" name="status" id="auto-input" value="<?php echo $device['auto_mode'] == 1 ? 'true' : 'false'; ?>">
        <label class="relative inline-flex cursor-pointer items-center gap-3">
          <input type="checkbox" class="sr-only peer" id="auto-toggle" <?php echo $device['auto_mode'] == 1 ? 'checked' : ''; ?> onchange="document.getElementById('auto-input').value = this.checked; this.form.submit();">
          <div class="w-11 h-6 bg-outline peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tertiary-container"></div>
          <span class="text-sm font-bold <?php echo $device['auto_mode'] == 1 ? 'text-tertiary' : 'text-on-surface-variant'; ?>"><?php echo $device['auto_mode'] == 1 ? 'ENABLED' : 'DISABLED'; ?></span>
        </label>
      </form>
    </div>
  </div>
  <div class="bg-surface-container-high rounded-xl p-8 border border-white/5">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 class="font-headline font-bold text-lg text-white">Door Control</h3>
        <p class="text-[10px] text-slate-400">Physical Access Management</p>
      </div>
      <span class="material-symbols-outlined text-3xl" style="color:<?php echo ($device['door_status'] ?? 'CLOSED') === 'OPEN' ? '#4edea3' : '#ffb4ab'; ?>">door_front</span>
    </div>
    <div class="flex items-center justify-between p-4 bg-surface-container rounded-lg">
      <div>
        <p class="text-sm font-medium text-slate-200">Door Status</p>
        <p class="text-[10px] text-slate-500">Physical entry point</p>
      </div>
      <div class="flex items-center gap-4">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded text-[10px] font-bold uppercase <?php echo ($device['door_status'] ?? 'CLOSED') === 'OPEN' ? 'bg-tertiary/10 text-tertiary' : 'bg-error/10 text-error'; ?>">
          <span class="w-1.5 h-1.5 rounded-full <?php echo ($device['door_status'] ?? 'CLOSED') === 'OPEN' ? 'bg-tertiary' : 'bg-error'; ?>"></span>
          <?php echo $device['door_status'] ?? 'CLOSED'; ?>
        </span>
        <form method="POST" action="">
          <input type="hidden" name="action" value="door">
          <input type="hidden" name="door_status" value="<?php echo ($device['door_status'] ?? 'CLOSED') === 'OPEN' ? 'CLOSED' : 'OPEN'; ?>">
          <button type="submit" class="px-4 py-2 bg-surface-container-lowest hover:bg-surface-variant text-xs font-medium rounded-lg border border-white/5 transition-colors">
            <?php echo ($device['door_status'] ?? 'CLOSED') === 'OPEN' ? 'Close' : 'Open'; ?>
          </button>
        </form>
      </div>
    </div>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <div class="bg-surface-container-high p-6 rounded-xl border border-white/5">
      <p class="text-[10px] uppercase tracking-widest text-on-surface-variant font-medium">Current Temperature</p>
      <div class="mt-2">
        <span class="text-4xl font-headline font-bold text-white tracking-tighter">
          <?php $latest = getLatestReading(); echo $latest ? $latest['temperature'] : '--'; ?>
        </span>
        <span class="text-lg text-on-surface-variant ml-1">°C</span>
      </div>
    </div>
    <div class="bg-surface-container-high p-6 rounded-xl border border-white/5">
      <p class="text-[10px] uppercase tracking-widest text-on-surface-variant font-medium">Current Humidity</p>
      <div class="mt-2">
        <span class="text-4xl font-headline font-bold text-white tracking-tighter">
          <?php echo $latest ? $latest['humidity'] : '--'; ?>
        </span>
        <span class="text-lg text-on-surface-variant ml-1">%</span>
      </div>
    </div>
  </div>
</main>
<?php require_once '../includes/footer.php'; ?>
