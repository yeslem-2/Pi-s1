<?php
$page_title = 'Dashboard';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
$device   = getDeviceStatus();
$settings = getSettings();
$latest   = getLatestReading();
$temperature  = $latest ? $latest['temperature'] : '--';
$humidity     = $latest ? $latest['humidity']    : '--';
$door_status  = $device['door_status'] ?? 'CLOSED';
?>
<?php include '../includes/navbar.php'; ?>
<main class="md:ml-64 pt-24 pb-12 px-6 lg:px-10 min-h-screen bg-surface-container-low">
  <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
    <div>
      <span class="text-tertiary text-xs font-bold tracking-widest uppercase mb-2 block">
        <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-tertiary pulse-active inline-block"></span> System Operational</span>
      </span>
      <h1 class="text-4xl font-headline font-bold text-white tracking-tight">Main Controller Area A-1</h1>
    </div>
    <div class="flex items-center gap-3 bg-surface-container-high p-1 rounded-xl">
      <button class="px-4 py-2 bg-primary text-on-primary text-sm font-semibold rounded-lg shadow-lg shadow-primary/10">Real-time</button>
      <button class="px-4 py-2 text-on-surface-variant text-sm font-medium hover:bg-surface-variant rounded-lg transition-colors">Historical</button>
    </div>
  </div>
  <div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-surface-container-high rounded-xl p-8 relative overflow-hidden group hover:bg-surface-bright transition-colors duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full -mr-16 -mt-16 blur-3xl group-hover:bg-primary/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-12">
          <div>
            <p class="text-on-surface-variant text-sm font-medium mb-1">Current Temperature</p>
            <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-tertiary pulse-active"></span><span class="text-tertiary text-[10px] font-bold tracking-widest uppercase">Live Feed</span></div>
          </div>
          <span class="material-symbols-outlined text-primary-fixed-dim bg-primary-container p-2 rounded-lg">thermostat</span>
        </div>
        <div class="flex items-baseline gap-2">
          <span class="text-7xl font-headline font-bold text-white tracking-tighter" id="temperature"><?php echo $temperature; ?></span>
          <span class="text-2xl font-headline font-medium text-on-surface-variant">°C</span>
        </div>
        <div class="mt-8 flex items-center justify-between">
          <div class="flex gap-4">
            <div><p class="text-[10px] text-slate-500 uppercase tracking-tighter">Min</p><p class="text-sm font-bold text-slate-300"><?php echo $settings['min_temp']; ?>°C</p></div>
            <div><p class="text-[10px] text-slate-500 uppercase tracking-tighter">Max</p><p class="text-sm font-bold text-slate-300"><?php echo $settings['max_temp']; ?>°C</p></div>
          </div>
          <div class="h-12 w-24 bg-gradient-to-t from-primary/10 to-transparent rounded-t-lg flex items-end justify-between px-1 gap-0.5">
            <div class="w-full bg-primary/40 h-[40%] rounded-t-sm"></div>
            <div class="w-full bg-primary/40 h-[60%] rounded-t-sm"></div>
            <div class="w-full bg-primary/40 h-[55%] rounded-t-sm"></div>
            <div class="w-full bg-primary/40 h-[75%] rounded-t-sm"></div>
            <div class="w-full bg-primary/60 h-[90%] rounded-t-sm"></div>
          </div>
        </div>
      </div>
      <div class="bg-surface-container-high rounded-xl p-8 relative overflow-hidden group hover:bg-surface-bright transition-colors duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-secondary/5 rounded-full -mr-16 -mt-16 blur-3xl"></div>
        <div class="flex justify-between items-start mb-12">
          <div>
            <p class="text-on-surface-variant text-sm font-medium mb-1">Air Humidity</p>
            <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-tertiary pulse-active"></span><span class="text-tertiary text-[10px] font-bold tracking-widest uppercase">Live Feed</span></div>
          </div>
          <span class="material-symbols-outlined text-secondary bg-secondary-container p-2 rounded-lg">humidity_mid</span>
        </div>
        <div class="flex items-baseline gap-2">
          <span class="text-7xl font-headline font-bold text-white tracking-tighter" id="humidity"><?php echo $humidity; ?></span>
          <span class="text-2xl font-headline font-medium text-on-surface-variant">%</span>
        </div>
        <div class="mt-8 flex items-center justify-between">
          <div class="flex gap-4">
            <div><p class="text-[10px] text-slate-500 uppercase tracking-tighter">Min</p><p class="text-sm font-bold text-slate-300"><?php echo $settings['min_humidity']; ?>%</p></div>
            <div><p class="text-[10px] text-slate-500 uppercase tracking-tighter">Max</p><p class="text-sm font-bold text-slate-300"><?php echo $settings['max_humidity']; ?>%</p></div>
          </div>
          <div class="h-12 w-24 bg-gradient-to-t from-secondary/10 to-transparent rounded-t-lg flex items-end justify-between px-1 gap-0.5">
            <div class="w-full bg-secondary/40 h-[70%] rounded-t-sm"></div>
            <div class="w-full bg-secondary/40 h-[65%] rounded-t-sm"></div>
            <div class="w-full bg-secondary/40 h-[60%] rounded-t-sm"></div>
            <div class="w-full bg-secondary/40 h-[68%] rounded-t-sm"></div>
            <div class="w-full bg-secondary/60 h-[62%] rounded-t-sm"></div>
          </div>
        </div>
      </div>
      <div class="col-span-1 md:col-span-2 bg-surface-container-high rounded-xl p-8">
        <div class="flex justify-between items-center mb-8">
          <div>
            <h3 class="font-headline font-bold text-lg text-white">Temperature Trend</h3>
            <p class="text-xs text-on-surface-variant">Sensor data - last 24 readings</p>
          </div>
        </div>
        <div class="h-64 relative">
          <canvas id="sensorChart" class="w-full h-full"></canvas>
        </div>
      </div>
    </div>
    <div class="col-span-12 lg:col-span-4 space-y-6">
      <div class="bg-surface-container-high rounded-xl p-8 border border-white/5">
        <h3 class="font-headline font-bold text-lg text-white mb-6">Device Controls</h3>
        <div class="space-y-6">
          <div class="flex items-center justify-between p-4 bg-surface-container rounded-lg group hover:bg-surface-variant transition-colors">
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-primary">power_settings_new</span>
              <span class="text-sm font-medium text-slate-200">System Power</span>
            </div>
            <label class="relative inline-flex cursor-pointer">
              <input type="checkbox" class="sr-only peer" id="toggle-device" <?php echo $device['status'] === 'ON' ? 'checked' : ''; ?> onchange="toggleDevice('device', this.checked, this)">
              <div class="w-11 h-6 bg-outline peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tertiary-container"></div>
            </label>
          </div>
          <div class="flex items-center justify-between p-4 bg-surface-container rounded-lg group hover:bg-surface-variant transition-colors">
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-secondary">ac_unit</span>
              <span class="text-sm font-medium text-slate-200">AC Unit</span>
            </div>
            <label class="relative inline-flex cursor-pointer">
              <input type="checkbox" class="sr-only peer" id="toggle-ac" <?php echo $device['ac_status'] === 'ON' ? 'checked' : ''; ?> onchange="toggleDevice('ac', this.checked, this)">
              <div class="w-11 h-6 bg-outline peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tertiary-container"></div>
            </label>
          </div>
          <div class="pt-6 border-t border-white/5">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-bold text-white">Automatic Mode</p>
                <p class="text-[10px] text-slate-400">AI-driven optimization</p>
              </div>
              <label class="relative inline-flex cursor-pointer">
                <input type="checkbox" class="sr-only peer" id="toggle-auto" <?php echo $device['auto_mode'] == 1 ? 'checked' : ''; ?> onchange="toggleDevice('auto', this.checked, this)">
                <div class="w-11 h-6 bg-outline peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tertiary-container"></div>
              </label>
            </div>
          </div>
          <div class="pt-6 border-t border-white/5">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-bold text-white">Door</p>
                <p class="text-[10px] text-slate-400" id="door-status-text"><?php echo $door_status; ?></p>
              </div>
              <button onclick="openDoorModal('toggle')" class="px-4 py-2 bg-surface-container-lowest hover:bg-surface-variant text-xs font-medium rounded-lg border border-white/5 transition-colors" id="door-toggle-btn"><?php echo $door_status === 'OPEN' ? 'Close' : 'Open'; ?></button>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-surface-container-high rounded-xl p-8 border border-white/5">
        <div class="flex justify-between items-center mb-6">
          <h3 class="font-headline font-bold text-lg text-white">Recent Alerts</h3>
          <a href="notifications.php" class="text-[10px] font-bold text-primary uppercase tracking-widest cursor-pointer hover:underline">View All</a>
        </div>
        <div class="space-y-4" id="notif-cards-sidebar">
          <?php
          $conn_n = getDBConnection();
          $res_n = $conn_n->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 3");
          if ($res_n) while ($n = $res_n->fetch_assoc()):
            $icon = $n['type'] === 'warning' ? 'warning' : ($n['type'] === 'success' ? 'check_circle' : 'info');
            $bg = $n['type'] === 'warning' ? 'bg-error-container/20 border border-error/10' : 'bg-surface-container hover:bg-surface-variant border border-white/5';
            $color = $n['type'] === 'warning' ? 'text-error' : ($n['type'] === 'success' ? 'text-tertiary' : 'text-primary-fixed-dim');
          ?>
          <div class="flex gap-4 p-4 rounded-lg <?php echo $bg; ?> transition-colors">
            <span class="material-symbols-outlined <?php echo $color; ?> shrink-0"><?php echo $icon; ?></span>
            <div>
              <p class="text-xs font-bold text-slate-200"><?php echo htmlspecialchars($n['message']); ?></p>
              <p class="text-[9px] text-slate-500 mt-1 uppercase font-medium"><?php echo $n['created_at']; ?></p>
            </div>
          </div>
          <?php endwhile; $conn_n->close(); ?>
          <?php if (!$res_n || $res_n->num_rows == 0): ?>
          <div class="text-center py-6 text-xs text-on-surface-variant">
            <span class="material-symbols-outlined text-3xl block mb-2 opacity-50">notifications_off</span>
            No alerts
          </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="relative rounded-xl overflow-hidden h-40 group cursor-pointer shadow-2xl bg-gradient-to-br from-primary-container to-surface-container">
        <div class="absolute inset-0 bg-gradient-to-t from-surface to-transparent opacity-90"></div>
        <div class="absolute bottom-4 left-4">
          <p class="text-white font-headline font-bold text-sm">Hardware Status</p>
          <p class="text-tertiary text-[10px] font-medium">
            <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-tertiary pulse-active"></span> All nodes green</span>
          </p>
        </div>
        <div class="absolute top-4 right-4 bg-tertiary/20 backdrop-blur-md p-1.5 rounded-lg border border-tertiary/30">
          <span class="material-symbols-outlined text-tertiary text-lg">memory</span>
        </div>
      </div>
    </div>
  </div>
</main>
<div class="modal fixed inset-0 bg-black/50 z-50 hidden items-center justify-center" id="door-modal">
  <div class="modal-content bg-surface-container-high rounded-xl p-8 w-full max-w-sm border border-white/5 glow-effect" style="text-align:center;">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-headline font-bold text-lg text-white"><span class="material-symbols-outlined">lock</span> Door Control</h3>
      <button onclick="closeDoorModal()" class="text-slate-400 hover:text-white"><span class="material-symbols-outlined">close</span></button>
    </div>
    <p class="text-on-surface-variant text-sm mb-4">Enter your code to <strong id="door-action-label" class="text-white">toggle</strong> the door</p>
    <div class="pin-display">
      <span class="pin-dot" id="ddot-1"></span>
      <span class="pin-dot" id="ddot-2"></span>
      <span class="pin-dot" id="ddot-3"></span>
      <span class="pin-dot" id="ddot-4"></span>
    </div>
    <div id="door-modal-error" class="hidden p-3 mb-4 rounded-lg bg-error-container/20 border border-error/20 text-error text-sm"></div>
    <div class="pin-pad">
      <?php for ($i = 1; $i <= 9; $i++): ?>
      <button class="pin-btn" onclick="doorPinInput(<?php echo $i; ?>)"><?php echo $i; ?></button>
      <?php endfor; ?>
      <button class="pin-btn pin-clear" onclick="doorPinClear()"><span class="material-symbols-outlined">backspace</span></button>
      <button class="pin-btn" onclick="doorPinInput(0)">0</button>
      <button class="pin-btn pin-submit" onclick="doorPinSubmit()"><span class="material-symbols-outlined">check</span></button>
    </div>
    <p class="mt-4 text-[10px] text-on-surface-variant">No code set? <a href="<?php echo BASE_URL; ?>/user/profile.php" class="text-primary">Go to Profile</a> to create one.</p>
  </div>
</div>
<footer class="md:hidden fixed bottom-0 left-0 w-full bg-[#0f131d]/90 backdrop-blur-xl border-t border-white/10 flex justify-around items-center h-16 z-50">
  <a class="text-[#adc6ff] flex flex-col items-center" href="#">
    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
    <span class="text-[10px] mt-1 font-medium">Home</span>
  </a>
  <a class="text-slate-400 flex flex-col items-center" href="profile.php">
    <span class="material-symbols-outlined">account_circle</span>
    <span class="text-[10px] mt-1 font-medium">Profile</span>
  </a>
  <a class="text-slate-400 flex flex-col items-center" href="history.php">
    <span class="material-symbols-outlined">history</span>
    <span class="text-[10px] mt-1 font-medium">History</span>
  </a>
  <a class="text-slate-400 flex flex-col items-center" href="notifications.php">
    <span class="material-symbols-outlined">notifications</span>
    <span class="text-[10px] mt-1 font-medium">Alerts</span>
  </a>
</footer>
<?php $extra_js = ['../js/chart.js', '../js/dashboard.js']; require_once '../includes/footer.php'; ?>
