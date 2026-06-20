<?php
require_once '../config.php';
if (isLoggedIn()) { redirectByRole(); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = clean($_POST['username']);
  $password = $_POST['password'];
  if (empty($username) || empty($password)) {
    $error = "Please fill in all fields";
  } else {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();
      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        redirectByRole();
      } else { $error = "Invalid username or password"; }
    } else { $error = "Invalid username or password"; }
    $stmt->close(); $conn->close();
  }
}
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Login | SENTINEL_CONTROL</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "on-tertiary-container": "#009365", "surface-tint": "#adc6ff",
        "on-primary-container": "#357df1", "error": "#ffb4ab",
        "surface": "#0f131d", "surface-container-lowest": "#0a0e18",
        "surface-bright": "#353944", "secondary": "#b7c8e1",
        "outline": "#909097", "background": "#0f131d",
        "error-container": "#93000a", "surface-container": "#1c1f2a",
        "on-surface": "#dfe2f1", "on-error": "#690005",
        "outline-variant": "#45464d", "on-error-container": "#ffdad6",
        "primary": "#adc6ff", "surface-variant": "#313540",
        "primary-fixed-dim": "#adc6ff", "on-surface-variant": "#c6c6cd",
        "surface-container-highest": "#313540", "surface-dim": "#0f131d",
        "on-primary": "#002e6a", "surface-container-high": "#262a35",
        "surface-container-low": "#171b26", "tertiary": "#4edea3",
        "on-background": "#dfe2f1"
      },
      borderRadius: { DEFAULT: "0.125rem", lg: "0.25rem", xl: "0.5rem", full: "0.75rem" },
      fontFamily: { headline: ["Space Grotesk"], body: ["Inter"], label: ["Inter"] }
    }
  }
}
</script>
<style>
.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
.glass-panel { background: linear-gradient(135deg, rgba(28,31,42,0.8) 0%, rgba(15,19,29,0.9) 100%); backdrop-filter: blur(20px); }
.glow-effect { box-shadow: 0 0 40px -10px rgba(173,198,255,0.15); }
</style>
</head>
<body class="bg-surface font-body text-on-surface min-h-screen flex flex-col items-center justify-center overflow-hidden">
<div class="fixed inset-0 z-0 pointer-events-none">
  <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/5 rounded-full blur-[120px]"></div>
  <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-tertiary/5 rounded-full blur-[120px]"></div>
</div>
<main class="relative z-10 w-full max-w-md px-6">
  <div class="mb-10 text-center">
    <div class="inline-flex items-center justify-center w-16 h-16 bg-surface-container-high rounded-xl mb-6 shadow-xl border border-outline-variant/20">
      <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1;">shield_with_heart</span>
    </div>
    <h1 class="font-headline text-3xl font-bold tracking-tighter text-surface-tint">SENTINEL_CONTROL</h1>
    <p class="text-on-surface-variant text-sm mt-2 font-medium tracking-wide uppercase">Industrial Smart Monitoring System</p>
  </div>
  <div class="glass-panel border border-outline-variant/20 rounded-xl p-8 glow-effect">
    <?php if ($error): ?>
    <div class="mb-6 p-3 rounded-lg bg-error-container/20 border border-error/20 text-error text-sm font-medium"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="" class="space-y-6">
      <div class="space-y-2">
        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant" for="username">System Identity</label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-outline text-lg group-focus-within:text-primary transition-colors">account_circle</span>
          </div>
          <input class="block w-full pl-11 pr-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 focus:border-on-primary-container transition-all" id="username" name="username" placeholder="Operator ID or Username" required type="text"/>
        </div>
      </div>
      <div class="space-y-2">
        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant" for="password">Access Protocol</label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-outline text-lg group-focus-within:text-primary transition-colors">lock</span>
          </div>
          <input class="block w-full pl-11 pr-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 focus:border-on-primary-container transition-all" id="password" name="password" placeholder="••••••••" required type="password"/>
        </div>
      </div>
      <button type="submit" class="w-full flex items-center justify-center gap-2 py-4 px-6 bg-primary text-on-primary font-headline font-bold text-base rounded-lg hover:bg-primary-fixed-dim active:scale-[0.98] transition-all shadow-[0_4px_20px_rgba(53,125,241,0.2)]">
        INITIALIZE ACCESS
        <span class="material-symbols-outlined text-lg">arrow_forward_ios</span>
      </button>
    </form>
    <div class="mt-8 pt-8 border-t border-outline-variant/10 text-center">
      <p class="text-on-surface-variant text-sm">New Terminal? <a class="text-tertiary font-semibold hover:underline decoration-tertiary/30 underline-offset-4" href="register.php">Register Station</a></p>
    </div>
    <div class="mt-4 pt-4 border-t border-outline-variant/10 text-center">
      <p class="text-[10px] text-on-surface-variant opacity-60"><strong>Demo:</strong> admin / admin123 &nbsp;|&nbsp; user1 / user123</p>
    </div>
  </div>
  <div class="mt-12 text-center space-y-2 opacity-50">
    <div class="flex items-center justify-center gap-4">
      <div class="h-px w-8 bg-outline-variant"></div>
      <p class="text-[10px] font-headline font-bold uppercase tracking-[0.2em] text-on-surface-variant">Industrial Sentinel V2.0.4</p>
      <div class="h-px w-8 bg-outline-variant"></div>
    </div>
    <p class="text-[10px] text-on-surface-variant">Secure Node-to-Node Encryption Active</p>
  </div>
</main>
<div class="fixed bottom-8 left-8 hidden lg:block">
  <div class="flex items-center gap-3 glass-panel px-4 py-2 border border-outline-variant/10 rounded-full">
    <div class="w-2 h-2 rounded-full bg-tertiary animate-pulse shadow-[0_0_8px_rgba(78,222,163,0.6)]"></div>
    <span class="text-[10px] font-bold tracking-widest text-on-tertiary-container uppercase">System Operational</span>
  </div>
</div>
<div class="absolute inset-0 z-[-1] flex items-center justify-center overflow-hidden opacity-20 pointer-events-none">
  <div class="w-[800px] h-[800px] border border-outline-variant/10 rounded-full"></div>
  <div class="absolute w-[600px] h-[600px] border border-outline-variant/10 rounded-full"></div>
  <div class="absolute w-[400px] h-[400px] border border-outline-variant/10 rounded-full"></div>
</div>
</body>
</html>
