<?php
// =============================================================================
<<<<<<< HEAD
// APP CONFIGURATION - Nen Tang Game Doc Lap Vi (itch.io clone)
// =============================================================================

define('APP_NAME', 'Nền Tảng Game Độc Lập Việt');
=======
// APP CONFIGURATION
// =============================================================================

define('APP_NAME', 'Nền Tảng Phân Phối Trò Chơi Kỹ Thuật Số');
>>>>>>> origin/main
$scriptDirectory = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
define('APP_BASE_URL', $scriptDirectory === '.' ? '' : $scriptDirectory);
define('APP_TIMEZONE', 'Asia/Ho_Chi_Minh');

<<<<<<< HEAD
// XAMPP defaults; import test.sql into this database before opening the app.
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'nen_tang_game_doc_lap_vi_v2');
=======
// XAMPP defaults; import *.sql into this database before opening the app.
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'game_distribution_platform_db');
>>>>>>> origin/main
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
