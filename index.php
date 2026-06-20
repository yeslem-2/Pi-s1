<?php
require_once 'config.php';
if (isLoggedIn()) { redirectByRole(); }
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>SENTINEL_CONTROL - Smart Temperature & Humidity Monitoring</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "surface": "#0f131d", "surface-container-high": "#262a35",
        "on-primary-container": "#357df1", "error": "#ffb4ab",
        "outline-variant": "#45464d", "on-surface": "#dfe2f1",
        "primary": "#adc6ff", "on-surface-variant": "#c6c6cd",
        "on-primary": "#002e6a", "surface-container-low": "#171b26",
        "tertiary": "#4edea3", "surface-tint": "#adc6ff",
        "outline": "#909097", "on-background": "#dfe2f1",
        "surface-container": "#1c1f2a", "surface-container-lowest": "#0a0e18"
      },
      fontFamily: { headline: ["Space Grotesk"], body: ["Inter"] }
    }
  }
}
</script>
<style>
.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
.glass-panel { background: linear-gradient(135deg, rgba(28,31,42,0.8) 0%, rgba(15,19,29,0.9) 100%); backdrop-filter: blur(20px); }
</style>
</head>
<body class="bg-surface font-body text-on-surface min-h-screen overflow-x-hidden">
<div class="fixed inset-0 z-0 pointer-events-none">
  <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/5 rounded-full blur-[120px]"></div>
  <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-tertiary/5 rounded-full blur-[120px]"></div>
</div>
<header class="fixed top-0 w-full z-50 bg-[#0f131d]/60 backdrop-blur-xl border-b border-white/10">
  <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
    <span class="text-xl font-bold tracking-tighter text-[#adc6ff] font-headline">SENTINEL_CONTROL</span>
    <div class="flex items-center gap-4">
      <a href="auth/login.php" class="px-5 py-2 bg-primary text-on-primary text-sm font-bold rounded-lg hover:brightness-110 active:scale-95 transition-all">Login</a>
      <a href="auth/register.php" class="px-5 py-2 border border-white/20 text-on-surface text-sm font-medium rounded-lg hover:bg-white/5 transition-all">Register</a>
    </div>
  </div>
</header>
<main class="relative z-10">
  <section class="min-h-screen flex flex-col items-center justify-center text-center px-6 pt-20">
    <div class="inline-flex items-center justify-center w-20 h-20 bg-surface-container-high rounded-2xl mb-8 shadow-xl border border-outline-variant/20">
      <span class="material-symbols-outlined text-primary text-5xl" style="font-variation-settings: 'FILL' 1;">shield_with_heart</span>
    </div>
    <h1 class="font-headline text-5xl md:text-7xl font-bold tracking-tighter text-surface-tint mb-6">SENTINEL_CONTROL</h1>
    <p class="text-on-surface-variant text-lg md:text-xl max-w-2xl mb-12 font-medium">Real-time Temperature & Humidity Monitoring System with Intelligent Device Control</p>
    <div class="flex gap-4 flex-wrap justify-center">
      <a href="auth/login.php" class="px-8 py-4 bg-primary text-on-primary font-headline font-bold text-base rounded-xl hover:bg-primary-fixed-dim active:scale-[0.98] transition-all shadow-[0_4px_20px_rgba(53,125,241,0.2)] flex items-center gap-2">
        INITIALIZE ACCESS <span class="material-symbols-outlined">arrow_forward_ios</span>
      </a>
      <a href="auth/register.php" class="px-8 py-4 border border-white/20 text-on-surface font-headline font-bold text-base rounded-xl hover:bg-white/5 active:scale-[0.98] transition-all flex items-center gap-2">
        REGISTER STATION <span class="material-symbols-outlined">terminal</span>
      </a>
    </div>
  </section>
  <section class="max-w-6xl mx-auto px-6 pb-24">
    <h2 class="font-headline text-3xl font-bold text-center text-white mb-16">System Capabilities</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div class="glass-panel border border-outline-variant/20 rounded-xl p-8 text-center group hover:border-primary/20 transition-all duration-300">
        <div class="w-14 h-14 mx-auto mb-5 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary/20 transition-colors">
          <span class="material-symbols-outlined text-primary text-3xl">thermostat</span>
        </div>
        <h3 class="font-headline font-bold text-lg text-white mb-3">Real-time Monitoring</h3>
        <p class="text-on-surface-variant text-sm leading-relaxed">Monitor temperature and humidity levels in real-time with automatic data updates every 5 seconds.</p>
      </div>
      <div class="glass-panel border border-outline-variant/20 rounded-xl p-8 text-center group hover:border-primary/20 transition-all duration-300">
        <div class="w-14 h-14 mx-auto mb-5 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary/20 transition-colors">
          <span class="material-symbols-outlined text-primary text-3xl">settings_remote</span>
        </div>
        <h3 class="font-headline font-bold text-lg text-white mb-3">Device Control</h3>
        <p class="text-on-surface-variant text-sm leading-relaxed">Turn devices ON/OFF remotely. Control air conditioning and enable auto mode for smart management.</p>
      </div>
      <div class="glass-panel border border-outline-variant/20 rounded-xl p-8 text-center group hover:border-primary/20 transition-all duration-300">
        <div class="w-14 h-14 mx-auto mb-5 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary/20 transition-colors">
          <span class="material-symbols-outlined text-primary text-3xl">warning</span>
        </div>
        <h3 class="font-headline font-bold text-lg text-white mb-3">Smart Alerts</h3>
        <p class="text-on-surface-variant text-sm leading-relaxed">Get instant notifications when temperature or humidity exceeds your configured thresholds.</p>
      </div>
      <div class="glass-panel border border-outline-variant/20 rounded-xl p-8 text-center group hover:border-primary/20 transition-all duration-300">
        <div class="w-14 h-14 mx-auto mb-5 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary/20 transition-colors">
          <span class="material-symbols-outlined text-primary text-3xl">query_stats</span>
        </div>
        <h3 class="font-headline font-bold text-lg text-white mb-3">History & Analytics</h3>
        <p class="text-on-surface-variant text-sm leading-relaxed">View detailed history of all sensor readings with visual charts and trend analysis.</p>
      </div>
      <div class="glass-panel border border-outline-variant/20 rounded-xl p-8 text-center group hover:border-primary/20 transition-all duration-300">
        <div class="w-14 h-14 mx-auto mb-5 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary/20 transition-colors">
          <span class="material-symbols-outlined text-primary text-3xl">group</span>
        </div>
        <h3 class="font-headline font-bold text-lg text-white mb-3">Role-based Access</h3>
        <p class="text-on-surface-variant text-sm leading-relaxed">Separate admin and user dashboards with different permissions and control levels.</p>
      </div>
      <div class="glass-panel border border-outline-variant/20 rounded-xl p-8 text-center group hover:border-primary/20 transition-all duration-300">
        <div class="w-14 h-14 mx-auto mb-5 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary/20 transition-colors">
          <span class="material-symbols-outlined text-primary text-3xl">dark_mode</span>
        </div>
        <h3 class="font-headline font-bold text-lg text-white mb-3">Industrial Design</h3>
        <p class="text-on-surface-variant text-sm leading-relaxed">Premium industrial control room aesthetic with glassmorphism, tonal depth, and ambient glow.</p>
      </div>
    </div>
  </section>
  <footer class="text-center py-12 border-t border-white/5">
    <div class="flex items-center justify-center gap-4 mb-4">
      <div class="h-px w-8 bg-outline-variant"></div>
      <p class="text-[10px] font-headline font-bold uppercase tracking-[0.2em] text-on-surface-variant">Industrial Sentinel V2.0.4</p>
      <div class="h-px w-8 bg-outline-variant"></div>
    </div>
    <p class="text-[10px] text-on-surface-variant opacity-60">&copy; <?php echo date('Y'); ?> SENTINEL_CONTROL - Smart Monitoring System</p>
  </footer>
</main>
</body>
</html>
