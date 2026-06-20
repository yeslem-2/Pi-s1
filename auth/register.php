<?php
require_once '../config.php';
if (isLoggedIn()) { redirectByRole(); }
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = clean($_POST['username']);
  $email = clean($_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  if (empty($username) || empty($email) || empty($password)) {
    $error = "Please fill in all fields";
  } elseif (strlen($username) < 3) {
    $error = "Username must be at least 3 characters";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email format";
  } elseif (strlen($password) < 6) {
    $error = "Password must be at least 6 characters";
  } elseif ($password !== $confirm_password) {
    $error = "Passwords do not match";
  } else {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username); $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) { $error = "Username already exists"; }
    else {
      $stmt->close();
      $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
      $stmt->bind_param("s", $email); $stmt->execute();
      if ($stmt->get_result()->num_rows > 0) { $error = "Email already exists"; }
      else {
        $stmt->close();
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $username, $email, $hashed);
        if ($stmt->execute()) { $success = "Registration successful! You can now login."; }
        else { $error = "Registration failed. Please try again."; }
      }
    }
    $stmt->close(); $conn->close();
  }
}
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Register | SENTINEL_CONTROL</title>
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
        "secondary": "#b7c8e1", "outline": "#909097", "background": "#0f131d",
        "surface-container": "#1c1f2a", "on-surface": "#dfe2f1",
        "outline-variant": "#45464d", "primary": "#adc6ff",
        "on-surface-variant": "#c6c6cd", "surface-container-high": "#262a35",
        "on-primary": "#002e6a", "surface-container-low": "#171b26",
        "tertiary": "#4edea3", "on-background": "#dfe2f1"
      },
      borderRadius: { xl: "0.5rem" },
      fontFamily: { headline: ["Space Grotesk"], body: ["Inter"] }
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
  <div class="mb-8 text-center">
    <div class="inline-flex items-center justify-center w-16 h-16 bg-surface-container-high rounded-xl mb-6 shadow-xl border border-outline-variant/20">
      <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1;">shield_with_heart</span>
    </div>
    <h1 class="font-headline text-3xl font-bold tracking-tighter text-surface-tint">SENTINEL_CONTROL</h1>
    <p class="text-on-surface-variant text-sm mt-2 font-medium tracking-wide uppercase">Register New Station</p>
  </div>
  <div class="glass-panel border border-outline-variant/20 rounded-xl p-8 glow-effect">
    <?php if ($error): ?>
    <div class="mb-6 p-3 rounded-lg bg-error-container/20 border border-error/20 text-error text-sm font-medium"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="mb-6 p-3 rounded-lg bg-tertiary-container/30 border border-tertiary/20 text-tertiary text-sm font-medium"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="POST" action="" class="space-y-5">
      <div class="space-y-2">
        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant" for="username">Operator ID</label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-outline text-lg group-focus-within:text-primary transition-colors">badge</span>
          </div>
          <input class="block w-full pl-11 pr-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 focus:border-on-primary-container transition-all" id="username" name="username" placeholder="Choose a username" required type="text" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"/>
        </div>
      </div>
      <div class="space-y-2">
        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant" for="email">Comm Channel</label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-outline text-lg group-focus-within:text-primary transition-colors">mail</span>
          </div>
          <input class="block w-full pl-11 pr-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 focus:border-on-primary-container transition-all" id="email" name="email" placeholder="your@email.com" required type="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"/>
        </div>
      </div>
      <div class="space-y-2">
        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant" for="password">Access Code</label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-outline text-lg group-focus-within:text-primary transition-colors">lock</span>
          </div>
          <input class="block w-full pl-11 pr-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 focus:border-on-primary-container transition-all" id="password" name="password" placeholder="Min 6 characters" required type="password"/>
        </div>
      </div>
      <div class="space-y-2">
        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant" for="confirm_password">Confirm Access Code</label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-outline text-lg group-focus-within:text-primary transition-colors">verified_user</span>
          </div>
          <input class="block w-full pl-11 pr-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 focus:border-on-primary-container transition-all" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required type="password"/>
        </div>
      </div>
      <button type="submit" class="w-full flex items-center justify-center gap-2 py-4 px-6 bg-primary text-on-primary font-headline font-bold text-base rounded-lg hover:bg-primary-fixed-dim active:scale-[0.98] transition-all shadow-[0_4px_20px_rgba(53,125,241,0.2)]">
        REGISTER STATION
        <span class="material-symbols-outlined text-lg">terminal</span>
      </button>
    </form>
    <div class="mt-6 pt-6 border-t border-outline-variant/10 text-center">
      <p class="text-on-surface-variant text-sm">Already have access? <a class="text-tertiary font-semibold hover:underline decoration-tertiary/30 underline-offset-4" href="login.php">Sign in here</a></p>
    </div>
  </div>
  <div class="mt-10 text-center opacity-50">
    <p class="text-[10px] font-headline font-bold uppercase tracking-[0.2em] text-on-surface-variant">Industrial Sentinel V2.0.4</p>
  </div>
</main>
</body>
</html>
