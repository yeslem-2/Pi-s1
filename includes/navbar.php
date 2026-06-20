<?php
$_current_user_display = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User';
?>
<header class="fixed top-0 w-full z-50 bg-[#0f131d]/60 backdrop-blur-xl border-b border-white/10 shadow-[0_8px_32px_rgba(15,23,29,0.5)] flex justify-between items-center px-6 h-16">
  <div class="flex items-center gap-8">
    <span class="text-xl font-bold tracking-tighter text-[#adc6ff] font-headline">SENTINEL_CONTROL</span>
    <div class="hidden md:flex items-center bg-surface-container-low px-3 py-1.5 rounded-lg border border-white/5">
      <span class="material-symbols-outlined text-slate-400 text-sm">search</span>
      <input class="bg-transparent border-none focus:ring-0 text-sm text-on-surface placeholder:text-slate-500 w-64" placeholder="Search systems..." type="text"/>
    </div>
  </div>
  <div class="flex items-center gap-4">
    <div style="position:relative;">
      <a href="<?php echo $user_role === 'admin' ? BASE_URL . '/admin/notifications.php' : BASE_URL . '/user/notifications.php'; ?>" class="text-slate-400 hover:text-white transition-colors active:scale-95 duration-200 notification-bell">
        <span class="material-symbols-outlined">notifications</span>
        <span class="notification-badge absolute -top-1 -right-1 bg-error text-on-error text-[9px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center" id="notif-badge" style="display:none;">0</span>
      </a>
      <button class="text-slate-500 hover:text-white text-xs ml-0.5" onclick="event.preventDefault(); toggleNotifications();"><span class="material-symbols-outlined text-sm">arrow_drop_down</span></button>
      <div class="notifications-dropdown absolute top-full right-0 w-80 bg-surface-container-high border border-white/5 rounded-xl shadow-2xl hidden z-50" id="notif-dropdown">
        <div class="p-4 border-b border-white/5 flex justify-between items-center">
          <span class="text-sm font-bold text-white">Notifications</span>
          <a href="javascript:void(0)" onclick="markAllRead()" class="text-[10px] text-primary hover:underline">Mark all read</a>
        </div>
        <div id="notif-list" class="max-h-64 overflow-y-auto">
          <div class="p-6 text-center text-xs text-on-surface-variant">Loading...</div>
        </div>
        <div class="p-3 border-t border-white/5 text-center">
          <a href="<?php echo $user_role === 'admin' ? BASE_URL . '/admin/notifications.php' : BASE_URL . '/user/notifications.php'; ?>" class="text-[10px] text-primary hover:underline">View all notifications</a>
        </div>
      </div>
    </div>
    <button onclick="toggleTheme()" class="text-slate-400 hover:text-white transition-colors active:scale-95 duration-200" id="theme-toggle-btn">
      <span class="material-symbols-outlined" id="theme-icon">dark_mode</span>
    </button>
    <a href="<?php echo $user_role === 'admin' ? BASE_URL . '/admin/settings.php' : BASE_URL . '/user/settings.php'; ?>" class="text-slate-400 hover:text-white transition-colors active:scale-95 duration-200">
      <span class="material-symbols-outlined">settings</span>
    </a>
    <a href="<?php echo $user_role === 'admin' ? BASE_URL . '/admin/settings.php' : BASE_URL . '/user/profile.php'; ?>" class="h-8 w-8 rounded-full overflow-hidden border border-primary/20 bg-primary-container flex items-center justify-center text-primary font-headline font-bold text-sm hover:brightness-110 transition-all">
      <?php echo strtoupper(substr($_current_user_display, 0, 1)); ?>
    </a>
  </div>
</header>
