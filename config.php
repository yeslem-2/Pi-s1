<?php
// ============================================
// config.php - Database Configuration & Constants
// ============================================

define('DB_SQLITE_PATH', __DIR__ . '/smart_monitor.sqlite');

// Application settings
define('SITE_NAME', 'Smart Monitor');
define('SESSION_LIFETIME', 3600);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// mysqli Compatibility Layer (PDO/SQLite)
// ============================================

class MysqliResult {
    public $num_rows = 0;
    private $rows = [];
    private $index = 0;

    public function __construct(array $rows) {
        $this->rows    = $rows;
        $this->num_rows = count($rows);
    }

    public function fetch_assoc() {
        return $this->index < $this->num_rows ? $this->rows[$this->index++] : null;
    }

    public function free(): void {}
}

class MysqliStatement {
    private $pdo;
    private $sql;
    private $params  = [];
    private $pdoStmt = null;
    public  $affected_rows = 0;

    public function __construct(PDO $pdo, string $sql) {
        $this->pdo = $pdo;
        $this->sql = $sql;
    }

    public function bind_param(string $types, &...$vars): void {
        $this->params = [];
        foreach ($vars as $v) {
            $this->params[] = $v;
        }
    }

    public function execute(): bool {
        try {
            $this->pdoStmt = $this->pdo->prepare($this->sql);
            $ok = $this->pdoStmt->execute($this->params);
            $this->affected_rows = $this->pdoStmt->rowCount();
            return $ok;
        } catch (Exception $e) {
            error_log('DB execute error: ' . $e->getMessage() . ' | SQL: ' . $this->sql);
            return false;
        }
    }

    public function get_result(): MysqliResult {
        if ($this->pdoStmt === null) {
            return new MysqliResult([]);
        }
        try {
            $rows = $this->pdoStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $rows = [];
        }
        return new MysqliResult($rows ?: []);
    }

    public function close(): void {
        $this->pdoStmt = null;
        $this->params  = [];
    }
}

class MysqliWrapper {
    private $pdo = null;
    public  $connect_error = null;
    public  $affected_rows = 0;

    public function __get($name) {
        if ($name === 'insert_id') {
            return $this->pdo ? (int)$this->pdo->lastInsertId() : 0;
        }
        return null;
    }

    public function __construct() {
        try {
            $this->pdo = new PDO('sqlite:' . DB_SQLITE_PATH);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('PRAGMA journal_mode=WAL');
            $this->pdo->exec('PRAGMA foreign_keys=ON');
        } catch (Exception $e) {
            $this->connect_error = $e->getMessage();
        }
    }

    public function query(string $sql): MysqliResult|false {
        try {
            $stmt = $this->pdo->query($sql);
            try {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $rows = [];
            }
            $this->affected_rows = $stmt->rowCount();
            return new MysqliResult($rows ?: []);
        } catch (Exception $e) {
            error_log('DB query error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            return false;
        }
    }

    public function prepare(string $sql): MysqliStatement {
        return new MysqliStatement($this->pdo, $sql);
    }

    public function real_escape_string(string $str): string {
        return str_replace("'", "''", $str);
    }

    public function set_charset(string $charset): void {}

    public function close(): void {
        $this->pdo = null;
    }
}

// ============================================
// SQLite Database Initialisation
// ============================================

function initSQLiteDB(): void {
    $db = new PDO('sqlite:' . DB_SQLITE_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('PRAGMA journal_mode=WAL');

    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        username    TEXT    NOT NULL UNIQUE,
        email       TEXT    NOT NULL UNIQUE,
        password    TEXT    NOT NULL,
        role        TEXT    NOT NULL DEFAULT 'user',
        full_name   TEXT    NOT NULL DEFAULT '',
        door_code   TEXT             DEFAULT NULL,
        created_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS sensor_data (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        temperature REAL    NOT NULL,
        humidity    REAL    NOT NULL,
        recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS device_status (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        status      TEXT    NOT NULL DEFAULT 'OFF',
        ac_status   TEXT    NOT NULL DEFAULT 'OFF',
        auto_mode   INTEGER NOT NULL DEFAULT 0,
        door_status TEXT    NOT NULL DEFAULT 'CLOSED',
        updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS settings (
        id                 INTEGER PRIMARY KEY AUTOINCREMENT,
        max_temp           REAL    NOT NULL DEFAULT 35.00,
        min_temp           REAL    NOT NULL DEFAULT 15.00,
        max_humidity       REAL    NOT NULL DEFAULT 70.00,
        min_humidity       REAL    NOT NULL DEFAULT 30.00,
        auto_mode_enabled  INTEGER NOT NULL DEFAULT 0
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS notifications (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        message    TEXT    NOT NULL,
        type       TEXT    NOT NULL DEFAULT 'info',
        is_read    INTEGER NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Seed default data
    $userCount = (int) $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCount === 0) {
        $adminHash = '$2y$12$C4AeG4NaEqhb8Bb2v7AuZ.6p0sIMik1fOQGg1TzobR.IbHE04Z.CO';
        $userHash  = '$2y$12$NL3hyrBeAFFBltVkSvdEQOK90qcWsDMbJyupDxOFGKYYve/zMRj0O';
        $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@smartmonitor.com', $adminHash, 'admin']);
        $stmt->execute(['user1', 'user1@smartmonitor.com', $userHash,  'user']);

        $db->exec("INSERT INTO device_status (id, status, ac_status, auto_mode) VALUES (1,'OFF','OFF',0)");
        $db->exec("INSERT INTO settings (id, max_temp, min_temp, max_humidity, min_humidity, auto_mode_enabled) VALUES (1,35.00,15.00,70.00,30.00,0)");

        $db->exec("INSERT INTO sensor_data (temperature, humidity) VALUES
            (22.50,45.00),(23.10,44.50),(21.80,46.00),(24.00,43.20),(22.90,45.80)");

        $db->exec("INSERT INTO notifications (message, type, is_read) VALUES
            ('System initialized successfully','info',1),
            ('Welcome to Smart Monitor','success',1)");
    }
}

if (!file_exists(DB_SQLITE_PATH)) {
    initSQLiteDB();
} else {
    // Migration: add humidity columns to existing DB
    $db = new PDO('sqlite:' . DB_SQLITE_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $cols = $db->query("PRAGMA table_info(settings)")->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array('max_humidity', $cols)) {
        $db->exec("ALTER TABLE settings ADD COLUMN max_humidity REAL NOT NULL DEFAULT 70.00");
    }
    if (!in_array('min_humidity', $cols)) {
        $db->exec("ALTER TABLE settings ADD COLUMN min_humidity REAL NOT NULL DEFAULT 30.00");
    }
    $db = null;
}

// ============================================
// Base URL detection
// ============================================
$baseDocRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$baseAppDir = str_replace('\\', '/', __DIR__);
define('BASE_URL', rtrim(str_replace($baseDocRoot, '', $baseAppDir), '/'));

// ============================================
// Database Connection
// ============================================
function getDBConnection(): MysqliWrapper {
    $conn = new MysqliWrapper();
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// ============================================
// Helper Functions
// ============================================

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/auth/login.php");
        exit();
    }
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        header("Location: " . BASE_URL . "/user/dashboard.php");
        exit();
    }
}

function redirectByRole(): void {
    if (isAdmin()) {
        header("Location: " . BASE_URL . "/admin/dashboard.php");
    } else {
        header("Location: " . BASE_URL . "/user/dashboard.php");
    }
    exit();
}

function clean(string $data): string {
    return htmlspecialchars(stripslashes(trim($data)));
}

function getDeviceStatus(): array {
    $conn   = getDBConnection();
    $result = $conn->query("SELECT * FROM device_status WHERE id = 1");
    $status = ($result && $result->num_rows > 0)
        ? $result->fetch_assoc()
        : ['status' => 'OFF', 'ac_status' => 'OFF', 'auto_mode' => 0, 'door_status' => 'CLOSED'];
    $conn->close();
    return $status;
}

function getSettings(): array {
    $conn     = getDBConnection();
    $result   = $conn->query("SELECT * FROM settings WHERE id = 1");
    $settings = ($result && $result->num_rows > 0)
        ? $result->fetch_assoc()
        : ['max_temp' => 35.00, 'min_temp' => 15.00, 'max_humidity' => 70.00, 'min_humidity' => 30.00, 'auto_mode_enabled' => 0];
    $conn->close();
    return $settings;
}

function getLatestReading(): array {
    $conn    = getDBConnection();
    $result  = $conn->query("SELECT * FROM sensor_data ORDER BY recorded_at DESC LIMIT 1");
    $reading = ($result && $result->num_rows > 0)
        ? $result->fetch_assoc()
        : ['temperature' => 0, 'humidity' => 0, 'recorded_at' => '--'];
    $conn->close();
    return $reading;
}

function getUserById(int $id): ?array {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, username, full_name, email, role, door_code FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->num_rows > 0 ? $result->fetch_assoc() : null;
    $stmt->close();
    $conn->close();
    return $user;
}

function addNotification(string $message, string $type = 'info'): void {
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO notifications (message, type, is_read) VALUES (?, ?, 0)");
    $stmt->bind_param("ss", $message, $type);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>
