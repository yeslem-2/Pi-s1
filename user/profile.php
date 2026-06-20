<?php
$page_title = 'My Profile';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if (isset($_GET['lock'])) {
    unset($_SESSION['profile_unlocked']);
    header('Location: ' . BASE_URL . '/user/profile.php');
    exit();
}

$user      = getUserById($_SESSION['user_id']);
$has_code  = $user['door_code'] !== null;
$is_locked = $has_code && !isset($_SESSION['profile_unlocked']);
?>
<?php include '../includes/navbar.php'; ?>
<main class="md:ml-64 pt-24 pb-12 px-6 lg:px-10 min-h-screen bg-surface-container-low">
  <?php if ($is_locked): ?>
  <div class="max-w-md mx-auto mt-12">
    <div class="glass-panel border border-outline-variant/20 rounded-xl p-8 glow-effect text-center">
      <div class="w-16 h-16 mx-auto mb-6 bg-surface-container-high rounded-2xl flex items-center justify-center border border-outline-variant/20">
        <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1;">lock</span>
      </div>
      <h2 class="font-headline text-2xl font-bold text-white mb-2">Profile Locked</h2>
      <p class="text-on-surface-variant text-sm mb-6">Enter your door code to access profile settings</p>
      <div class="pin-display" id="gate-pin-display">
        <span class="pin-dot" id="gdot-1"></span>
        <span class="pin-dot" id="gdot-2"></span>
        <span class="pin-dot" id="gdot-3"></span>
        <span class="pin-dot" id="gdot-4"></span>
      </div>
      <div id="gate-error" class="hidden p-3 mb-4 rounded-lg bg-error-container/20 border border-error/20 text-error text-sm"></div>
      <div class="pin-pad">
        <?php for ($i = 1; $i <= 9; $i++): ?>
        <button class="pin-btn" onclick="gatePinInput(<?php echo $i; ?>)"><?php echo $i; ?></button>
        <?php endfor; ?>
        <button class="pin-btn pin-clear" onclick="gatePinClear()"><span class="material-symbols-outlined">backspace</span></button>
        <button class="pin-btn" onclick="gatePinInput(0)">0</button>
        <button class="pin-btn pin-submit" onclick="gatePinSubmit()"><span class="material-symbols-outlined">check</span></button>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
    <div>
      <span class="text-tertiary text-xs font-bold tracking-widest uppercase mb-2 block">
        <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-tertiary pulse-active inline-block"></span> Access Granted</span>
      </span>
      <h1 class="text-4xl font-headline font-bold text-white tracking-tight">My Profile</h1>
    </div>
    <?php if ($has_code): ?>
    <a href="<?php echo BASE_URL; ?>/user/profile.php?lock=1" class="px-4 py-2 bg-surface-container-high hover:bg-surface-variant text-on-surface text-sm font-medium rounded-lg border border-white/5 transition-colors flex items-center gap-2">
      <span class="material-symbols-outlined text-sm">lock</span> Lock Profile
    </a>
    <?php endif; ?>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-surface-container-high rounded-xl p-8 border border-white/5">
      <div class="flex items-center gap-3 mb-8">
        <div class="w-14 h-14 rounded-xl bg-primary-container flex items-center justify-center text-primary font-headline font-bold text-xl">
          <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
        </div>
        <div>
          <h3 class="font-headline font-bold text-lg text-white"><?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?></h3>
          <p class="text-on-surface-variant text-sm">@<?php echo htmlspecialchars($user['username']); ?> &bull; <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        <span class="ml-auto inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo $user['role'] === 'admin' ? 'bg-on-primary-container/10 text-on-primary-container border border-on-primary-container/20' : 'bg-secondary-container/30 text-secondary border border-secondary/20'; ?>"><?php echo ucfirst($user['role']); ?></span>
      </div>
      <div id="info-feedback"></div>
      <div class="space-y-5">
        <div>
          <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2" for="full_name">Display Name</label>
          <input type="text" id="full_name" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 text-sm" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" placeholder="Your full name">
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Username</label>
          <input type="text" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface/50 text-sm" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2" for="email">Email Address</label>
          <input type="email" id="email" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 text-sm" value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>
        <button onclick="saveProfile()" class="w-full py-3 bg-primary text-on-primary font-headline font-bold text-sm rounded-lg hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
          <span class="btn-text">Save Changes</span>
          <span class="btn-spinner hidden"><span class="material-symbols-outlined text-sm animate-spin">refresh</span></span>
        </button>
      </div>
    </div>
    <div class="bg-surface-container-high rounded-xl p-8 border border-white/5">
      <div class="flex items-center gap-3 mb-8">
        <span class="material-symbols-outlined text-primary text-3xl">lock</span>
        <div>
          <h3 class="font-headline font-bold text-lg text-white">Door Security Code</h3>
          <p class="text-on-surface-variant text-xs"><?php echo $has_code ? 'Your door code is set. It protects both this page and the door button.' : 'Set a 4 – 6 digit code. It will lock this page and the door button.'; ?></p>
        </div>
      </div>
      <div id="code-feedback"></div>
      <div class="space-y-5">
        <?php if ($has_code): ?>
        <div>
          <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2" for="current_code">Current Code</label>
          <input type="password" id="current_code" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 text-sm" maxlength="6" placeholder="Current 4 – 6 digit code">
        </div>
        <?php endif; ?>
        <div>
          <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2" for="new_code"><?php echo $has_code ? 'New Code' : 'Set Code'; ?></label>
          <input type="password" id="new_code" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 text-sm" maxlength="6" placeholder="4 – 6 digits">
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2" for="confirm_code">Confirm Code</label>
          <input type="password" id="confirm_code" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 text-sm" maxlength="6" placeholder="Repeat the code">
        </div>
        <button onclick="saveDoorCode()" class="w-full py-3 bg-tertiary/20 text-tertiary font-headline font-bold text-sm rounded-lg hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-2 border border-tertiary/20">
          <span class="btn-text"><?php echo $has_code ? 'Update Code' : 'Set Code'; ?></span>
          <span class="btn-spinner hidden"><span class="material-symbols-outlined text-sm animate-spin">refresh</span></span>
        </button>
      </div>
    </div>
  </div>
  <?php endif; ?>
</main>
<?php $extra_js = ['../js/profile.js']; require_once '../includes/footer.php'; ?>
