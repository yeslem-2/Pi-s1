<?php
$page_title = 'System Overview';
require_once '../config.php';
requireAdmin();
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
$conn = getDBConnection();
$total_users = 0; $total_readings = 0; $total_notifications = 0;
$r = $conn->query("SELECT COUNT(*) as count FROM users");
if ($r) $total_users = $r->fetch_assoc()['count'];
$r = $conn->query("SELECT COUNT(*) as count FROM sensor_data");
if ($r) $total_readings = $r->fetch_assoc()['count'];
$r = $conn->query("SELECT COUNT(*) as count FROM notifications WHERE is_read = 0");
if ($r) $total_notifications = $r->fetch_assoc()['count'];
$device = getDeviceStatus();
$latest = getLatestReading();
$conn->close();
?>
<?php include '../includes/navbar.php'; ?>
<main class="md:ml-64 pt-20 px-8 pb-12 min-h-screen bg-surface-container-low">
  <div class="flex justify-between items-end mb-8">
    <div>
      <h1 class="text-3xl font-bold font-headline tracking-tight text-white">System Overview</h1>
      <p class="text-on-surface-variant mt-1">Real-time telemetry and network orchestration.</p>
    </div>
    <div class="flex gap-3">
      <a href="users.php" class="flex items-center gap-2 px-4 py-2 bg-surface-container-high hover:bg-surface-bright text-on-surface text-sm font-medium rounded-lg transition-colors border border-white/5">
        <span class="material-symbols-outlined text-sm">manage_accounts</span> Manage Permissions
      </a>
      <a href="users.php" class="flex items-center gap-2 px-4 py-2 bg-primary text-on-primary text-sm font-bold rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-primary/10">
        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">person_add</span> Add User
      </a>
    </div>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface-container-high p-6 rounded-xl border border-white/5 relative overflow-hidden group">
      <div class="flex justify-between items-start relative z-10">
        <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-medium">Temperature</span>
        <span class="material-symbols-outlined text-primary">thermostat</span>
      </div>
      <div class="mt-4 relative z-10">
        <span class="text-4xl font-headline font-bold text-white tracking-tighter" id="temperature"><?php echo $latest ? $latest['temperature'] : '--'; ?></span>
        <span class="text-lg text-on-surface-variant ml-1">°C</span>
        <div class="text-on-surface-variant text-xs mt-2">Latest reading</div>
      </div>
      <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
        <span class="material-symbols-outlined text-8xl">thermostat</span>
      </div>
    </div>
    <div class="bg-surface-container-high p-6 rounded-xl border border-white/5 relative overflow-hidden group">
      <div class="flex justify-between items-start relative z-10">
        <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-medium">Humidity</span>
        <span class="material-symbols-outlined text-secondary">water_drop</span>
      </div>
      <div class="mt-4 relative z-10">
        <span class="text-4xl font-headline font-bold text-white tracking-tighter" id="humidity"><?php echo $latest ? $latest['humidity'] : '--'; ?></span>
        <span class="text-lg text-on-surface-variant ml-1">%</span>
        <div class="text-on-surface-variant text-xs mt-2">Latest reading</div>
      </div>
      <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
        <span class="material-symbols-outlined text-8xl">water_drop</span>
      </div>
    </div>
    <div class="bg-surface-container-high p-6 rounded-xl border border-white/5 relative overflow-hidden group">
      <div class="flex justify-between items-start relative z-10">
        <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-medium">Total Users</span>
        <span class="material-symbols-outlined text-primary">groups</span>
      </div>
      <div class="mt-4 relative z-10">
        <span class="text-4xl font-headline font-bold text-white tracking-tighter"><?php echo $total_users; ?></span>
        <div class="text-on-surface-variant text-xs mt-2">Registered operators</div>
      </div>
      <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
        <span class="material-symbols-outlined text-8xl">groups</span>
      </div>
    </div>
    <div class="bg-surface-container-high p-6 rounded-xl border border-white/5 relative overflow-hidden group border-error/20">
      <div class="flex justify-between items-start relative z-10">
        <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-medium">Unread Alerts</span>
        <span class="material-symbols-outlined text-error">warning</span>
      </div>
      <div class="mt-4 relative z-10">
        <span class="text-4xl font-headline font-bold text-error tracking-tighter"><?php echo $total_notifications; ?></span>
        <div class="text-on-surface-variant text-xs mt-2">Pending notifications</div>
      </div>
      <div class="absolute -right-4 -bottom-4 opacity-10">
        <span class="material-symbols-outlined text-8xl text-error">notifications</span>
      </div>
    </div>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-gradient-to-br from-surface-container-high to-surface-container p-6 rounded-xl border border-white/5 shadow-xl">
        <div class="flex justify-between items-center mb-6">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-primary/10 rounded-lg"><span class="material-symbols-outlined text-primary">thermostat</span></div>
            <div><h3 class="text-white font-bold">Node_24A Temp</h3><p class="text-[10px] text-on-surface-variant uppercase tracking-tighter">Server Room 01</p></div>
          </div>
          <span class="text-xs bg-tertiary/10 text-tertiary px-2 py-0.5 rounded border border-tertiary/20">Live</span>
        </div>
        <div class="flex items-end justify-between">
          <div><span class="text-5xl font-headline font-bold text-white tracking-tighter"><?php echo $latest ? $latest['temperature'] : '--'; ?></span><span class="text-lg text-on-surface-variant ml-1">°C</span></div>
          <div class="flex items-end gap-1 h-12">
            <div class="w-1 bg-primary/20 h-4 rounded-full"></div>
            <div class="w-1 bg-primary/20 h-6 rounded-full"></div>
            <div class="w-1 bg-primary/40 h-5 rounded-full"></div>
            <div class="w-1 bg-primary/60 h-8 rounded-full"></div>
            <div class="w-1 bg-primary/80 h-10 rounded-full"></div>
            <div class="w-1 bg-primary h-12 rounded-full shadow-[0_0_8px_rgba(173,198,255,0.5)]"></div>
          </div>
        </div>
      </div>
      <div class="bg-gradient-to-br from-surface-container-high to-surface-container p-6 rounded-xl border border-white/5 shadow-xl">
        <div class="flex justify-between items-center mb-6">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-secondary/10 rounded-lg"><span class="material-symbols-outlined text-secondary">water_drop</span></div>
            <div><h3 class="text-white font-bold">Node_24A Hum</h3><p class="text-[10px] text-on-surface-variant uppercase tracking-tighter">Server Room 01</p></div>
          </div>
          <span class="text-xs bg-tertiary/10 text-tertiary px-2 py-0.5 rounded border border-tertiary/20">Live</span>
        </div>
        <div class="flex items-end justify-between">
          <div><span class="text-5xl font-headline font-bold text-white tracking-tighter"><?php echo $latest ? $latest['humidity'] : '--'; ?></span><span class="text-lg text-on-surface-variant ml-1">%</span></div>
          <div class="flex items-end gap-1 h-12">
            <div class="w-1 bg-secondary/20 h-8 rounded-full"></div>
            <div class="w-1 bg-secondary/40 h-6 rounded-full"></div>
            <div class="w-1 bg-secondary/20 h-7 rounded-full"></div>
            <div class="w-1 bg-secondary/60 h-5 rounded-full"></div>
            <div class="w-1 bg-secondary/80 h-9 rounded-full"></div>
            <div class="w-1 bg-secondary h-6 rounded-full shadow-[0_0_8px_rgba(183,200,225,0.3)]"></div>
          </div>
        </div>
      </div>
      <div class="md:col-span-2 bg-surface-container p-6 rounded-xl border border-white/5">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-white font-bold">Sensor Readings</h3>
          <span class="text-xs text-on-surface-variant">Last 10 entries</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead><tr class="bg-surface-container-low/50">
              <th class="px-4 py-3 text-[10px] uppercase tracking-widest text-on-surface-variant font-semibold">ID</th>
              <th class="px-4 py-3 text-[10px] uppercase tracking-widest text-on-surface-variant font-semibold">Temperature</th>
              <th class="px-4 py-3 text-[10px] uppercase tracking-widest text-on-surface-variant font-semibold">Humidity</th>
              <th class="px-4 py-3 text-[10px] uppercase tracking-widest text-on-surface-variant font-semibold">Recorded</th>
            </tr></thead>
            <tbody class="divide-y divide-white/5">
              <?php
              $conn2 = getDBConnection();
              $result = $conn2->query("SELECT * FROM sensor_data ORDER BY recorded_at DESC LIMIT 10");
              if ($result) while ($row = $result->fetch_assoc()):
              ?>
              <tr class="hover:bg-white/5 transition-colors group">
                <td class="px-4 py-3 text-sm font-headline text-primary">#<?php echo $row['id']; ?></td>
                <td class="px-4 py-3 text-sm font-headline font-bold text-white"><?php echo $row['temperature']; ?>°C</td>
                <td class="px-4 py-3 text-sm font-headline font-bold text-secondary"><?php echo $row['humidity']; ?>%</td>
                <td class="px-4 py-3 text-sm text-on-surface-variant"><?php echo $row['recorded_at']; ?></td>
              </tr>
              <?php endwhile; $conn2->close(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="bg-surface-container-high rounded-xl border border-white/5 overflow-hidden flex flex-col">
      <div class="p-6 border-b border-white/5">
        <h3 class="text-white font-bold">Health Summary</h3>
        <p class="text-xs text-on-surface-variant mt-1">Infrastructure vitals</p>
      </div>
      <div class="flex-1 p-6 space-y-6">
        <div><div class="flex justify-between text-xs mb-2"><span class="text-on-surface-variant">System</span><span class="text-white font-medium"><?php echo $device['status']; ?></span></div>
          <div class="h-1.5 w-full bg-surface-container-low rounded-full overflow-hidden">
            <div class="h-full bg-primary w-[<?php echo $device['status'] === 'ON' ? '100' : '30'; ?>%] rounded-full"></div>
          </div>
        </div>
        <div><div class="flex justify-between text-xs mb-2"><span class="text-on-surface-variant">AC Unit</span><span class="text-white font-medium"><?php echo $device['ac_status']; ?></span></div>
          <div class="h-1.5 w-full bg-surface-container-low rounded-full overflow-hidden">
            <div class="h-full bg-secondary w-[<?php echo $device['ac_status'] === 'ON' ? '100' : '20'; ?>%] rounded-full"></div>
          </div>
        </div>
        <div><div class="flex justify-between text-xs mb-2"><span class="text-on-surface-variant">Auto Mode</span><span class="text-white font-medium"><?php echo $device['auto_mode'] ? 'ENABLED' : 'DISABLED'; ?></span></div>
          <div class="h-1.5 w-full bg-surface-container-low rounded-full overflow-hidden">
            <div class="h-full bg-tertiary w-[<?php echo $device['auto_mode'] ? '90' : '10'; ?>%] rounded-full"></div>
          </div>
        </div>
      </div>
      <div class="p-4 bg-surface-container-lowest/50 m-4 rounded-lg border border-white/5">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded bg-tertiary-container flex items-center justify-center">
            <span class="material-symbols-outlined text-tertiary text-xl">cloud_done</span>
          </div>
          <div><p class="text-xs font-bold text-white">Data Feed Active</p><p class="text-[10px] text-on-surface-variant">Auto-refresh every 5s</p></div>
        </div>
      </div>
    </div>
  </div>
  <div class="bg-surface-container rounded-xl border border-white/5 overflow-hidden">
    <div class="p-6 flex justify-between items-center border-b border-white/5">
      <h3 class="text-white font-bold">Recent Users</h3>
      <a href="users.php" class="text-primary text-xs font-medium hover:underline">View All</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead><tr class="bg-surface-container-low/50">
          <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-on-surface-variant font-semibold">ID</th>
          <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-on-surface-variant font-semibold">Username</th>
          <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-on-surface-variant font-semibold">Email</th>
          <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-on-surface-variant font-semibold">Role</th>
          <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-on-surface-variant font-semibold">Created</th>
        </tr></thead>
        <tbody class="divide-y divide-white/5">
          <?php
          $conn3 = getDBConnection();
          $result2 = $conn3->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 10");
          if ($result2) while ($row = $result2->fetch_assoc()):
          ?>
          <tr class="hover:bg-white/5 transition-colors group">
            <td class="px-6 py-4 text-sm"><?php echo $row['id']; ?></td>
            <td class="px-6 py-4 text-sm font-medium text-white"><?php echo htmlspecialchars($row['username']); ?></td>
            <td class="px-6 py-4 text-sm text-on-surface-variant"><?php echo htmlspecialchars($row['email']); ?></td>
            <td class="px-6 py-4">
              <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo $row['role'] === 'admin' ? 'bg-on-primary-container/10 text-on-primary-container border border-on-primary-container/20' : 'bg-secondary-container/30 text-secondary border border-secondary/20'; ?>"><?php echo ucfirst($row['role']); ?></span>
            </td>
            <td class="px-6 py-4 text-sm text-on-surface-variant"><?php echo $row['created_at']; ?></td>
          </tr>
          <?php endwhile; $conn3->close(); ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<?php $extra_js = ['../js/chart.js', '../js/dashboard.js']; require_once '../includes/footer.php'; ?>
