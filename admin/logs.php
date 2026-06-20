<?php
$page_title = 'System Logs';
require_once '../config.php';
requireAdmin();
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
$conn = getDBConnection();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 30;
$offset = ($page - 1) * $per_page;
$total_result = $conn->query("SELECT COUNT(*) as total FROM sensor_data");
$total = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);
$stmt = $conn->prepare("SELECT * FROM sensor_data ORDER BY recorded_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<?php include '../includes/navbar.php'; ?>
<main class="md:ml-64 pt-20 px-8 pb-12 min-h-screen bg-surface-container-low">
  <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
    <div>
      <h1 class="font-headline text-4xl font-bold text-on-surface tracking-tight">System Logs</h1>
      <p class="text-on-surface-variant mt-2 font-body text-sm">Detailed telemetry analysis - sensor readings</p>
    </div>
    <div class="flex items-center gap-2">
      <span class="text-xs text-on-surface-variant font-medium"><?php echo $total; ?> total records</span>
    </div>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface-container-high rounded-xl p-6 border-l-4 border-primary">
      <p class="text-[0.6875rem] font-medium uppercase tracking-widest text-on-surface-variant mb-2">Total Readings</p>
      <div class="flex items-baseline gap-2">
        <span class="font-headline text-3xl font-bold text-on-surface"><?php echo $total; ?></span>
      </div>
    </div>
    <div class="bg-surface-container-high rounded-xl p-6 border-l-4 border-tertiary">
      <p class="text-[0.6875rem] font-medium uppercase tracking-widest text-on-surface-variant mb-2">Page Size</p>
      <div class="flex items-baseline gap-2">
        <span class="font-headline text-3xl font-bold text-on-surface"><?php echo $per_page; ?></span>
        <span class="text-sm text-on-surface-variant">records</span>
      </div>
    </div>
    <div class="bg-surface-container-high rounded-xl p-6 border-l-4 border-secondary">
      <p class="text-[0.6875rem] font-medium uppercase tracking-widest text-on-surface-variant mb-2">Current Page</p>
      <div class="flex items-baseline gap-2">
        <span class="font-headline text-3xl font-bold text-on-surface"><?php echo $page; ?></span>
        <span class="text-sm text-on-surface-variant">of <?php echo $total_pages; ?></span>
      </div>
    </div>
    <div class="bg-surface-container-high rounded-xl p-6 border-l-4 border-tertiary">
      <p class="text-[0.6875rem] font-medium uppercase tracking-widest text-on-surface-variant mb-2">System Status</p>
      <div class="flex items-center gap-2">
        <span class="w-3 h-3 bg-tertiary rounded-full pulse-active"></span>
        <span class="font-headline text-2xl font-bold text-tertiary">Nominal</span>
      </div>
    </div>
  </div>
  <div class="bg-surface-container rounded-xl overflow-hidden shadow-2xl">
    <div class="p-6 border-b border-outline-variant/10 flex justify-between items-center">
      <div>
        <h3 class="text-xl font-headline font-bold text-on-surface">Historical Logs</h3>
        <p class="text-[10px] text-on-surface-variant font-label uppercase tracking-widest mt-1">Granular Sensor Readouts</p>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left font-body text-sm">
        <thead class="bg-surface-container-low text-on-surface-variant uppercase text-[10px] tracking-widest font-bold">
          <tr><th class="px-6 py-4">ID</th><th class="px-6 py-4">Temp (°C)</th><th class="px-6 py-4">Humidity (%)</th><th class="px-6 py-4">Recorded At</th></tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
          <?php if ($result) while ($row = $result->fetch_assoc()): ?>
          <tr class="hover:bg-white/5 transition-colors cursor-pointer group">
            <td class="px-6 py-4 font-headline font-bold text-primary">#<?php echo $row['id']; ?></td>
            <td class="px-6 py-4 font-headline font-bold text-on-surface"><?php echo $row['temperature']; ?>°C</td>
            <td class="px-6 py-4 font-headline font-bold text-secondary"><?php echo $row['humidity']; ?>%</td>
            <td class="px-6 py-4 font-mono text-xs text-on-surface-variant"><?php echo $row['recorded_at']; ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <div class="p-6 bg-surface-container-low flex justify-between items-center text-xs text-on-surface-variant font-medium">
      <p>Showing <?php echo $total > 0 ? $offset + 1 : 0; ?> to <?php echo min($offset + $per_page, $total); ?> of <?php echo $total; ?> entries</p>
      <div class="flex items-center gap-4">
        <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="flex items-center gap-1 hover:text-primary transition-colors">
          <span class="material-symbols-outlined text-sm">chevron_left</span> Previous
        </a>
        <?php endif; ?>
        <div class="flex items-center gap-1">
          <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
          <a href="?page=<?php echo $i; ?>" class="w-8 h-8 flex items-center justify-center <?php echo $i === $page ? 'bg-primary text-on-primary rounded' : 'hover:bg-surface-container-highest rounded transition-colors'; ?> text-xs font-bold"><?php echo $i; ?></a>
          <?php endfor; ?>
        </div>
        <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="flex items-center gap-1 hover:text-primary transition-colors">
          Next <span class="material-symbols-outlined text-sm">chevron_right</span>
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>
<?php $conn->close(); require_once '../includes/footer.php'; ?>
