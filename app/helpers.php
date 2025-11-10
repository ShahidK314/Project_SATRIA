<?php
// Flash messages sederhana
function flash_set($key, $msg) {
    if (!isset($_SESSION)) session_start();
    $_SESSION['flash'][$key] = $msg;
}

function flash_get($key) {
    if (!isset($_SESSION)) session_start();
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// CSRF helpers
function csrf_token() {
    if (!isset($_SESSION)) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    $t = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . e($t) . '">';
}

function verify_csrf() {
    if (!isset($_SESSION)) session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            die('CSRF token tidak valid.');
        }
    }
}

// Upload helper: validasi mime/size/extension, simpan file ke UPLOAD_DIR dan kembalikan nama file atau false
function save_upload($tmpPath, $origName) {
    // ukuran
    if (filesize($tmpPath) > MAX_UPLOAD_SIZE) return false;
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXT)) return false;
    // mime check
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);
    if (!in_array($mime, ALLOWED_MIME)) return false;
    $safe = uniqid('up_') . '.' . $ext;
    if (move_uploaded_file($tmpPath, UPLOAD_DIR . '/' . $safe)) return $safe;
    return false;
}

?>