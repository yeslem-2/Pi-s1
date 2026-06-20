# Smart Temperature & Humidity Monitoring System

A university-level web application for monitoring temperature and humidity with simulated sensor data and device control. Features an industrial control room design ("Stitch" / Industrial Sentinel template) with dark glassmorphism UI.

## Project Structure

```
smart-monitor/
├── index.php                 # Landing page
├── config.php                # Database config & helper functions
├── database.sql              # MySQL database setup script
├── css/
│   └── style.css             # Custom CSS (glass-panel, pin-pad, toast, animations)
├── js/
│   ├── main.js               # Core JavaScript (sidebar, door PIN, toasts, notifications)
│   ├── chart.js              # Simple canvas chart (no external libraries)
│   ├── dashboard.js          # Dashboard auto-refresh & alerts
│   └── profile.js            # Profile gate PIN management
├── images/                   # Static images
├── includes/
│   ├── header.php            # HTML head, Tailwind CDN, Google Fonts, Material Symbols
│   ├── footer.php            # Scripts, toast container, closing tags
│   ├── sidebar.php           # Glassmorphic nav sidebar (role-based)
│   └── navbar.php            # Fixed top bar with search, notifications, settings
├── auth/
│   ├── login.php             # Login page (standalone, Stitch design)
│   ├── register.php          # Registration page (standalone)
│   └── logout.php            # Logout handler
├── user/
│   ├── dashboard.php         # User dashboard (temp/humidity gauges, door PIN, device toggles)
│   ├── profile.php           # User profile with gate PIN setup
│   ├── history.php           # Sensor history with pagination
│   ├── notifications.php     # Notifications list
│   └── settings.php          # User settings (read-only thresholds)
├── admin/
│   ├── dashboard.php         # Admin dashboard (stats, widgets, sensor table)
│   ├── users.php             # User management (add/delete/role)
│   ├── settings.php          # System temperature thresholds
│   ├── device.php            # Global device/AC/door control
│   ├── logs.php              # System logs with pagination
│   └── notifications.php     # Notification management
└── api/
    ├── get_data.php          # Get latest sensor data (AJAX)
    ├── get_chart_data.php    # Get chart history (AJAX)
    ├── update_device.php     # Update device status (AJAX)
    ├── door_control.php      # Door PIN-based control (AJAX)
    ├── get_notifications.php # Get notifications (AJAX)
    └── mark_read.php         # Mark notifications read (AJAX)
```

## Quick Start (PHP Built-in Server)

```bash
php -S localhost:8000
```

Open http://localhost:8000 in your browser.

## Setup Instructions (XAMPP)

### Step 1: Install XAMPP
Download and install XAMPP from https://www.apachefriends.org/

### Step 2: Start Services
1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL**

### Step 3: Create Database
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click **Import** tab
3. Select `database.sql` from the project folder
4. Click **Go**

### Step 4: Copy Project
Copy the `smart-monitor` folder to XAMPP's htdocs directory:
- **Windows:** `C:\xampp\htdocs\smart-monitor`
- **Mac:** `/Applications/XAMPP/htdocs/smart-monitor`
- **Linux:** `/opt/lampp/htdocs/smart-monitor`

### Step 5: Access Application
```
http://localhost/smart-monitor
```

## Demo Accounts

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | admin123 |
| User | user1 | user123 |

## Database Tables

- **users** - User accounts with roles (admin/user)
- **sensor_data** - Temperature & humidity readings
- **device_status** - Device ON/OFF state and auto mode
- **settings** - Temperature thresholds and system config
- **notifications** - Alert messages

## Features

- Authentication with password hashing
- User dashboard with real-time data (temperature, humidity, door control)
- Admin dashboard with user management and system overview
- Auto-refresh every 5 seconds (AJAX)
- Notifications for threshold alerts
- Door control with PIN pad modal
- Responsive design with mobile bottom navigation
- Canvas-based charts (no libraries)

## Design System

- **Theme**: Dark only, industrial slate + deep cobalt palette
- **Framework**: Tailwind CSS (CDN), no build step
- **Typography**: Space Grotesk (headings) + Inter (body) via Google Fonts
- **Icons**: Material Symbols Outlined
- **Surface colors**: `#0f131d` (surface), `#adc6ff` (primary), `#4edea3` (tertiary), `#ffb4ab` (error), `#357df1` (on-primary-container)
- **Borders eliminated** — structure via tonal surface nesting
- **Glassmorphism** — backdrop blur + semi-transparent panels for floating elements

## Technologies

- HTML5, CSS3, JavaScript (Vanilla)
- PHP (Procedural)
- MySQL / SQLite
- AJAX (Fetch API)
- Tailwind CSS (CDN)
- Google Fonts + Material Symbols

## Simulated Data

The system simulates sensor data. To generate test data:
1. Log in to the admin panel
2. Go to System Logs to see existing readings
3. Data updates automatically every 5 seconds

## Notes

- This is a **simulation** - no real hardware is connected
- Built as a university project
- Can run with PHP's built-in server (no Apache/MySQL required if using SQLite)
