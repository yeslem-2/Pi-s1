<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user_role = $_SESSION['role'] ?? 'user';
$username = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User';
function navItem($href, $icon, $label, $current, $fill = false) {
  $active = basename($href) === $current ? true : false;
  $fillAttr = $fill ? "style=\"font-variation-settings: 'FILL' 1;\"" : '';
  $activeClasses = $active ? 'text-[#adc6ff] border-l-4 border-[#357df1] bg-gradient-to-r from-[#357df1]/10 to-transparent' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5';
  echo "<a href=\"{$href}\" class=\"flex items-center gap-3 px-4 py-3 {$activeClasses} font-medium text-sm transition-all duration-300 rounded-r-none rounded-l-none\">";
  echo "<span class=\"material-symbols-outlined\" {$fillAttr}>{$icon}</span>";
  echo "<span>{$label}</span></a>";
}
?>
<div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden" onclick="toggleSidebar()"></div>
<aside class="fixed left-0 top-0 h-full w-64 z-40 bg-[#0f131d]/80 backdrop-blur-md border-r border-white/5 flex flex-col pt-20 pb-6 px-4 transition-transform duration-300 -translate-x-full md:translate-x-0" id="sidebar">
  <div class="mb-8 px-4">
    <h2 class="font-headline font-bold text-lg text-white">Industrial Sentinel</h2>
    <p class="text-slate-500 text-[10px] uppercase tracking-widest mt-1">V2.0.4 Active</p>
  </div>
  <nav class="flex-1 space-y-1">
    <?php if ($user_role === 'user'): ?>
      <?php navItem('dashboard.php', 'dashboard', 'Dashboard', $current_page, true); ?>
      <?php navItem('history.php', 'history', 'History', $current_page); ?>
      <?php navItem('notifications.php', 'notifications', 'Notifications', $current_page); ?>
      <?php navItem('profile.php', 'account_circle', 'My Profile', $current_page); ?>
      <?php navItem('settings.php', 'tune', 'Settings', $current_page); ?>
    <?php else: ?>
      <?php navItem('dashboard.php', 'dashboard', 'Dashboard', $current_page, true); ?>
      <?php navItem('users.php', 'group', 'Manage Users', $current_page); ?>
      <?php navItem('device.php', 'settings_remote', 'Device Control', $current_page); ?>
      <?php navItem('notifications.php', 'notifications', 'Notifications', $current_page); ?>
      <?php navItem('logs.php', 'history', 'System Logs', $current_page); ?>
      <?php navItem('settings.php', 'tune', 'Settings', $current_page); ?>
      <?php navItem('../user/profile.php', 'account_circle', 'My Profile', $current_page); ?>
    <?php endif; ?>
  </nav>
  <div class="mt-auto pt-6 border-t border-white/5 space-y-1">
    <a href="../auth/logout.php" class="flex items-center gap-3 px-4 py-3 text-error/80 hover:text-error hover:bg-error/5 font-medium text-sm transition-all duration-300">
      <span class="material-symbols-outlined">logout</span>
      <span>Logout</span>
    </a>
  </div>
</aside>
