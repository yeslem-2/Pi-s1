<?php
require_once __DIR__ . '/../config.php';
requireLogin();
$page_title = $page_title ?? 'Dashboard';
$user_role = $_SESSION['role'] ?? 'user';
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?php echo $page_title; ?> | SENTINEL_CONTROL</title>
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
        "surface-bright": "#353944", "secondary-fixed-dim": "#b7c8e1",
        "secondary": "#b7c8e1", "outline": "#909097",
        "on-primary-fixed-variant": "#004395", "on-secondary-fixed": "#0b1c30",
        "primary-fixed": "#d8e2ff", "background": "#0f131d",
        "inverse-on-surface": "#2c303b", "inverse-primary": "#005ac2",
        "error-container": "#93000a", "secondary-fixed": "#d3e4fe",
        "surface-container": "#1c1f2a", "on-secondary": "#213145",
        "on-tertiary-fixed": "#002113", "tertiary-fixed": "#6ffbbe",
        "on-secondary-fixed-variant": "#38485d", "on-tertiary": "#003824",
        "on-surface": "#dfe2f1", "on-error": "#690005",
        "on-secondary-container": "#a9bad3", "outline-variant": "#45464d",
        "on-error-container": "#ffdad6", "primary": "#adc6ff",
        "inverse-surface": "#dfe2f1", "surface-variant": "#313540",
        "primary-fixed-dim": "#adc6ff", "tertiary-container": "#001c10",
        "on-primary-fixed": "#001a42", "primary-container": "#00163a",
        "on-surface-variant": "#c6c6cd", "surface-container-highest": "#313540",
        "surface-dim": "#0f131d", "secondary-container": "#3a4a5f",
        "on-primary": "#002e6a", "surface-container-high": "#262a35",
        "surface-container-low": "#171b26", "tertiary-fixed-dim": "#4edea3",
        "tertiary": "#4edea3", "on-tertiary-fixed-variant": "#005236",
        "on-background": "#dfe2f1"
      },
      borderRadius: { DEFAULT: "0.125rem", lg: "0.25rem", xl: "0.5rem", full: "0.75rem" },
      fontFamily: { headline: ["Space Grotesk"], body: ["Inter"], label: ["Inter"] }
    }
  }
}
</script>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body class="bg-surface text-on-surface font-body selection:bg-primary/30">
