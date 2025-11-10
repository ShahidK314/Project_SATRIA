<?php
// Konfigurasi aplikasi (bahasa: Indonesia)
// Sesuaikan DB_USER dan DB_PASS sesuai instalasi XAMPP kamu

define('DB_HOST', 'localhost');
define('DB_NAME', 'db_satria');
define('DB_USER', 'root');
define('DB_PASS', '');

// BASE_URL disesuaikan dengan lokasi folder public pada XAMPP
// Contoh: jika folder ProyekPBL ada di htdocs, dan file index.php di folder public,
// BASE_URL bisa http://localhost/ProyekPBL/public
if (!defined('BASE_URL')) {
    // coba deteksi otomatis
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $base = str_replace('\\public\\index.php', '', $script);
    define('BASE_URL', $protocol . '://' . $host . $base);
}

    // Pengaturan file upload
    // File uploads disimpan di public/uploads agar mudah diakses dari browser
    if (!defined('UPLOAD_DIR')) {
        define('UPLOAD_DIR', __DIR__ . '/../public/uploads');
    }
    if (!defined('MAX_UPLOAD_SIZE')) {
        define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB per file
    }

// Allowed mime types / extensions
define('ALLOWED_EXT', ['pdf','jpg','jpeg','png','doc','docx','xls','xlsx']);
define('ALLOWED_MIME', [
    'application/pdf', 'image/jpeg', 'image/png',
    'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
]);

// Buat folder upload jika belum ada
if (!is_dir(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0777, true);
}

// SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_PORT', 587);
define('SMTP_FROM_EMAIL', 'noreply@satria.ac.id');
define('SMTP_FROM_NAME', 'SATRIA System');

// Google reCAPTCHA v2 (Google test keys - safe for development)
// These are public test keys provided by Google for local/dev testing:
// Site key: 6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI
// Secret key: 6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe
define('RECAPTCHA_SITE_KEY', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI');
define('RECAPTCHA_SECRET_KEY', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe');

// Email verification token expiry
define('EMAIL_TOKEN_EXPIRY', 24 * 60 * 60);
?>