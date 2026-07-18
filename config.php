<?php
// config/config.php
declare(strict_types=1);
if (!defined('MOBIL_SHOWROOM_CONFIG_LOADED')) {
    define('MOBIL_SHOWROOM_CONFIG_LOADED', true);
    session_start();

    // Database config - sesuaikan
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'mobil_showroom');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    // Base URL yang benar untuk XAMPP
    $baseUrl = '/pwd%20/Mobil_showroom/';
    define('BASE_URL', $baseUrl);

    // Upload config
    define('UPLOAD_DIR', __DIR__ . '/../uploads/');
    define('UPLOAD_URL', BASE_URL . 'uploads/');
    define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024);
    define('UPLOAD_ALLOWED', ['image/jpeg','image/png','image/webp']);

    // pastikan folder uploads ada
    if (!is_dir(UPLOAD_DIR)) {
        @mkdir(UPLOAD_DIR, 0755, true);
    }

    // PDO connection with automatic import for first run
    /** @var PDO|null $pdo */
    $pdo = null;
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        if (stripos($e->getMessage(), 'Unknown database') !== false) {
            $sqlFile = __DIR__ . '/../database/mobil_showroom.sql';
            if (!file_exists($sqlFile)) {
                die('File SQL database tidak ditemukan.');
            }

            $tmpConn = new mysqli(DB_HOST, DB_USER, DB_PASS);
            if ($tmpConn->connect_error) {
                die('Koneksi database gagal: ' . $tmpConn->connect_error);
            }
            $tmpConn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            $tmpConn->select_db(DB_NAME);

            $sql = file_get_contents($sqlFile);
            if ($sql === false) {
                $tmpConn->close();
                die('Gagal membaca file SQL.');
            }

            if (!$tmpConn->multi_query($sql)) {
                $tmpConn->close();
                die('Gagal mengimpor database: ' . $tmpConn->error);
            }
            do {
                if ($res = $tmpConn->store_result()) {
                    $res->free();
                }
            } while ($tmpConn->more_results() && $tmpConn->next_result());
            $tmpConn->close();

            try {
                $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
            } catch (PDOException $e2) {
                die('Koneksi database gagal setelah impor: ' . $e2->getMessage());
            }
        } else {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }

    // Auto-import if database exists but tables are missing
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='" . DB_NAME . "'");
        $count = $stmt ? (int) $stmt->fetchColumn() : 0;
        if ($count === 0) {
            $sqlFile = __DIR__ . '/../database/mobil_showroom.sql';
            if (file_exists($sqlFile)) {
                $tmpConn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                if ($tmpConn->connect_error) {
                    die('Koneksi database gagal: ' . $tmpConn->connect_error);
                }
                $sql = file_get_contents($sqlFile);
                if ($sql === false) {
                    $tmpConn->close();
                    die('Gagal membaca file SQL.');
                }
                if (!$tmpConn->multi_query($sql)) {
                    $tmpConn->close();
                    die('Gagal mengimpor database: ' . $tmpConn->error);
                }
                do {
                    if ($res = $tmpConn->store_result()) {
                        $res->free();
                    }
                } while ($tmpConn->more_results() && $tmpConn->next_result());
                $tmpConn->close();
            }
        }
    } catch (Exception $e) {
        // ignore missing information_schema access
    }

    // Helper
    if (!function_exists('is_logged')) {
        function is_logged(): bool {
            return !empty($_SESSION['user']);
        }
    }
    if (!function_exists('is_admin')) {
        function is_admin(): bool {
            return is_logged() && ($_SESSION['user']['role'] ?? '') === 'admin';
        }
    }
    if (!function_exists('require_login')) {
        function require_login() {
            if (!is_logged()) {
                header('Location: '.BASE_URL.'?page=login');
                exit;
            }
        }
    }
    if (!function_exists('require_admin')) {
        function require_admin() {
            if (!is_admin()) {
                header('Location: '.BASE_URL);
                exit;
            }
        }
    }

    /**
     * Resolve a stored gambar value to a usable image URL.
     * - empty -> placeholder
     * - absolute http(s) URL -> returned as-is
     * - already starts with UPLOAD_URL -> returned as-is
     * - leading slash (site-root relative) -> returned as-is
     * - filename stored in uploads folder -> return UPLOAD_URL . filename
     */
    if (!function_exists('get_image_url')) {
        function get_image_url(?string $val, string $placeholder = BASE_URL . 'uploads/placeholder.jpg'): string {
            $val = trim((string)($val ?? ''));
            if ($val === '') return $placeholder;
            if (preg_match('#^https?://#i', $val)) return $val;

            $normalized = str_replace('\\', '/', $val);
            $normalized = preg_replace('#^/+#', '', $normalized);
            $normalized = preg_replace('#^uploads/#i', '', $normalized);

            $candidatePaths = [];
            $candidatePaths[] = __DIR__ . '/../uploads/' . $normalized;
            $candidatePaths[] = '/Applications/XAMPP/xamppfiles/htdocs/pwd /Mobil_showroom/uploads/' . $normalized;

            foreach ($candidatePaths as $path) {
                if ($normalized !== '' && file_exists($path)) {
                    return BASE_URL . 'uploads/' . $normalized;
                }
            }

            if (preg_match('#^uploads/#i', $val)) {
                return BASE_URL . $val;
            }

            return $placeholder;
        }
    }

    // Create default admin if users table exists and empty
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        if ($stmt !== false) {
            $count = $stmt->fetchColumn();
            if ($count == 0) {
                $pass = password_hash('admin123', PASSWORD_DEFAULT);
                $ins = $pdo->prepare("INSERT INTO users (nama, email, password, role, created_at) VALUES (?,?,?,?,NOW())");
                $ins->execute(['Admin', 'admin@showroom.local', $pass, 'admin']);
            }
        }
    } catch (Exception $e) {
        // Tabel mungkin belum dibuat -> lewati
    }
}
?>