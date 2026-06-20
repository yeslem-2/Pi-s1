<?php
$page_title = 'Notifications';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
$conn = getDBConnection();
$result = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 100");
$notifications = [];
if ($result) { while ($row = $result->fetch_assoc()) $notifications[] = $row; }
$conn->close();
$unread_count = count(array_filter($notifications, fn($n) => $n['is_read'] == 0));
?>
<?php include '../includes/navbar.php'; ?>
<main class="md:ml-64 pt-20 px-8 pb-12 min-h-screen bg-surface-container-low">
  <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
    <div>
      <h1 class="font-headline text-4xl font-bold text-on-surface tracking-tight">Notifications</h1>
      <p class="text-on-surface-variant mt-2 font-body text-sm">
        <?php if ($unread_count > 0): ?>
        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-error/10 text-error border border-error/20">
          <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
          <?php echo $unread_count; ?> unread
        </span>
        <?php endif; ?>
      </p>
    </div>
    <button onclick="markAllRead()" class="flex items-center gap-2 px-5 py-2 bg-primary text-on-primary text-xs font-bold uppercase tracking-widest rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-primary/10">
      <span class="material-symbols-outlined text-sm">done_all</span> Mark all read
    </button>
  </div>
  <div class="flex gap-2 flex-wrap mb-6" id="notif-filter-bar">
    <button class="px-4 py-2 bg-primary text-on-primary text-xs font-bold rounded-lg" onclick="filterNotifs('all',this)">All</button>
    <button class="px-4 py-2 bg-surface-container-high text-on-surface-variant text-xs font-medium rounded-lg hover:bg-surface-variant transition-colors" onclick="filterNotifs('unread',this)">Unread</button>
    <button class="px-4 py-2 bg-surface-container-high text-on-surface-variant text-xs font-medium rounded-lg hover:bg-surface-variant transition-colors" onclick="filterNotifs('warning',this)">Warning</button>
    <button class="px-4 py-2 bg-surface-container-high text-on-surface-variant text-xs font-medium rounded-lg hover:bg-surface-variant transition-colors" onclick="filterNotifs('info',this)">Info</button>
    <button class="px-4 py-2 bg-surface-container-high text-on-surface-variant text-xs font-medium rounded-lg hover:bg-surface-variant transition-colors" onclick="filterNotifs('success',this)">Success</button>
  </div>
  <div class="space-y-3" id="notif-cards">
    <?php if (empty($notifications)): ?>
    <div class="text-center py-16 bg-surface-container-high rounded-xl border border-white/5">
      <span class="material-symbols-outlined text-6xl block mb-4 opacity-30 text-on-surface-variant">notifications_off</span>
      <p class="text-on-surface-variant text-sm">No notifications yet.</p>
    </div>
    <?php else: ?>
    <?php foreach ($notifications as $n):
      $icon = $n['type'] === 'warning' ? 'warning' : ($n['type'] === 'success' ? 'check_circle' : 'info');
      $color = $n['type'] === 'warning' ? 'text-error' : ($n['type'] === 'success' ? 'text-tertiary' : 'text-primary');
      $bg = $n['type'] === 'warning' ? 'bg-error-container/20 border border-error/10' : 'bg-surface-container-high border border-white/5';
    ?>
    <div class="flex gap-4 p-5 rounded-xl <?php echo $bg; ?> <?php echo $n['is_read'] == 0 ? 'unread' : ''; ?> transition-colors hover:bg-surface-bright/50 notif-card" data-type="<?php echo $n['type']; ?>" data-read="<?php echo $n['is_read']; ?>" id="nc-<?php echo $n['id']; ?>">
      <span class="material-symbols-outlined <?php echo $color; ?> shrink-0 mt-0.5"><?php echo $icon; ?></span>
      <div class="flex-1">
        <p class="text-sm font-medium text-slate-200"><?php echo htmlspecialchars($n['message']); ?></p>
        <p class="text-[10px] text-slate-500 mt-1 uppercase font-medium"><?php echo $n['created_at']; ?></p>
      </div>
      <div class="flex items-start gap-2">
        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo $n['type'] === 'warning' ? 'bg-error/10 text-error border border-error/20' : ($n['type'] === 'success' ? 'bg-tertiary/10 text-tertiary border border-tertiary/20' : 'bg-primary/10 text-primary border border-primary/20'); ?>"><?php echo ucfirst($n['type']); ?></span>
        <?php if ($n['is_read'] == 0): ?>
        <button onclick="markOneRead(<?php echo $n['id']; ?>)" class="px-2 py-1 rounded text-[10px] font-medium bg-surface-container-lowest hover:bg-surface-variant text-on-surface-variant border border-white/5 transition-colors">Mark read</button>
        <?php else: ?>
        <span class="text-[10px] text-on-surface-variant flex items-center gap-1"><span class="material-symbols-outlined text-xs">check</span> Read</span>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>
<script>
function filterNotifs(type, btn) {
  document.querySelectorAll('#notif-filter-bar button').forEach(b => {
    b.className = 'px-4 py-2 text-xs font-medium rounded-lg transition-colors ' + (b === btn ? 'bg-primary text-on-primary font-bold' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-variant');
  });
  document.querySelectorAll('.notif-card').forEach(card => {
    const show = type === 'all' ? true : type === 'unread' ? card.dataset.read == 0 : card.dataset.type === type;
    card.style.display = show ? 'flex' : 'none';
  });
}
function markOneRead(id) {
  fetch('../api/mark_notification_read.php', {
    method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id })
  }).then(r => r.json()).then(data => {
    if (data.success) {
      const card = document.getElementById('nc-' + id);
      if (card) { card.classList.remove('unread'); card.dataset.read = 1; location.reload(); }
    }
  });
}
</script>
<?php require_once '../includes/footer.php'; ?>
