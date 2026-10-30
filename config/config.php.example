<?php
// =============================================================================
// APP CONFIGURATION
// =============================================================================

define('APP_NAME', 'Nền Tảng Phân Phối Trò Chơi Kỹ Thuật Số');
$scriptDirectory = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
define('APP_BASE_URL', $scriptDirectory === '.' ? '' : $scriptDirectory);
define('APP_TIMEZONE', 'Asia/Ho_Chi_Minh');

// XAMPP defaults; import *.sql into this database before opening the app.
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'game_distribution_platform_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', 3306);

// Paths
define('APP_DIR', dirname(__DIR__));
define('VIEWS_DIR', APP_DIR . '/templates');
define('PUBLIC_DIR', APP_DIR . '/public');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set(APP_TIMEZONE);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function app_url(string $path = ''): string {
    $base = rtrim(APP_BASE_URL, '/');
    $path = '/' . ltrim($path, '/');
    if ($base === '' || $path === $base || strpos($path, $base . '/') === 0) {
        return $path;
    }
    return $base . $path;
}
