<?php
$page_title = 'System Settings';
require_once '../config.php';
requireAdmin();
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
$success = ''; $error = '';
$settings = getSettings();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $max_temp = floatval($_POST['max_temp']);
  $min_temp = floatval($_POST['min_temp']);
  $max_humidity = floatval($_POST['max_humidity']);
  $min_humidity = floatval($_POST['min_humidity']);
  $auto_mode = isset($_POST['auto_mode_enabled']) ? 1 : 0;
  $errors = [];
  if ($max_temp <= $min_temp) { $errors[] = "Max temperature must be greater than min temperature"; }
  if ($max_humidity <= $min_humidity) { $errors[] = "Max humidity must be greater than min humidity"; }
  if ($max_temp < -10 || $max_temp > 60) { $errors[] = "Max temperature must be between -10 and 60"; }
  if ($min_temp < -10 || $min_temp > 60) { $errors[] = "Min temperature must be between -10 and 60"; }
  if ($max_humidity < 10 || $max_humidity > 100) { $errors[] = "Max humidity must be between 10 and 100"; }
  if ($min_humidity < 10 || $min_humidity > 100) { $errors[] = "Min humidity must be between 10 and 100"; }
  if (empty($errors)) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE settings SET max_temp = ?, min_temp = ?, max_humidity = ?, min_humidity = ?, auto_mode_enabled = ? WHERE id = 1");
    $stmt->bind_param("ddddi", $max_temp, $min_temp, $max_humidity, $min_humidity, $auto_mode);
    if ($stmt->execute()) {
      $conn->query("UPDATE device_status SET auto_mode = $auto_mode WHERE id = 1");
      $success = "Settings updated successfully"; $settings = getSettings(); addNotification("System settings updated by admin", "info");
    } else { $error = "Failed to update settings"; }
    $stmt->close(); $conn->close();
  } else { $error = implode("<br>", $errors); }
}
?>
<?php include '../includes/navbar.php'; ?>
<main class="md:ml-64 pt-20 px-8 pb-12 min-h-screen bg-surface-container-low">
  <div class="mb-8">
    <h1 class="font-headline text-4xl font-bold text-on-surface tracking-tight">System Settings</h1>
    <p class="text-on-surface-variant mt-2 font-body text-sm">Configure temperature and humidity thresholds for the entire system.</p>
  </div>
  <?php if ($success): ?>
  <div class="mb-6 p-4 rounded-lg bg-tertiary-container/30 border border-tertiary/20 text-tertiary text-sm font-medium flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> <?php echo $success; ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
  <div class="mb-6 p-4 rounded-lg bg-error-container/20 border border-error/20 text-error text-sm font-medium flex items-center gap-2"><span class="material-symbols-outlined text-sm">error</span> <?php echo $error; ?></div>
  <?php endif; ?>
  <div class="max-w-2xl bg-surface-container-high rounded-xl p-8 border border-white/5">
    <form method="POST" action="">
      <div class="space-y-6">
        <div class="p-4 bg-surface-container rounded-lg border border-white/5">
          <h3 class="font-headline font-bold text-base text-white mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary">thermostat</span> Temperature Thresholds</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2" for="max_temp">Maximum</label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">thermostat</span>
                <input class="w-full pl-10 pr-8 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 text-sm" id="max_temp" name="max_temp" type="number" step="0.1" value="<?php echo $settings['max_temp']; ?>" required/>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">°C</span>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2" for="min_temp">Minimum</label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">ac_unit</span>
                <input class="w-full pl-10 pr-8 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 text-sm" id="min_temp" name="min_temp" type="number" step="0.1" value="<?php echo $settings['min_temp']; ?>" required/>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">°C</span>
              </div>
            </div>
          </div>
        </div>
        <div class="p-4 bg-surface-container rounded-lg border border-white/5">
          <h3 class="font-headline font-bold text-base text-white mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-secondary">humidity_mid</span> Humidity Thresholds</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2" for="max_humidity">Maximum</label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">water_drop</span>
                <input class="w-full pl-10 pr-8 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 text-sm" id="max_humidity" name="max_humidity" type="number" step="0.1" value="<?php echo $settings['max_humidity']; ?>" required/>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">%</span>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2" for="min_humidity">Minimum</label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">water_drop</span>
                <input class="w-full pl-10 pr-8 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 text-sm" id="min_humidity" name="min_humidity" type="number" step="0.1" value="<?php echo $settings['min_humidity']; ?>" required/>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">%</span>
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-between p-4 bg-surface-container rounded-lg border border-white/5">
          <div>
            <p class="text-sm font-bold text-white">Auto Mode System-Wide</p>
            <p class="text-[10px] text-slate-400">Enable AI-driven automatic control</p>
          </div>
          <label class="relative inline-flex cursor-pointer">
            <input type="checkbox" class="sr-only peer" name="auto_mode_enabled" <?php echo $settings['auto_mode_enabled'] == 1 ? 'checked' : ''; ?>>
            <div class="w-11 h-6 bg-outline peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tertiary-container"></div>
          </label>
        </div>
        <button type="submit" class="w-full py-4 bg-primary text-on-primary font-headline font-bold text-base rounded-lg hover:brightness-110 active:scale-[0.98] transition-all shadow-[0_4px_20px_rgba(53,125,241,0.2)] flex items-center justify-center gap-2">
          <span class="material-symbols-outlined">save</span> Save Settings
        </button>
      </div>
    </form>
  </div>
</main>
<?php require_once '../includes/footer.php'; ?>
