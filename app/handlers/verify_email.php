<?php
require_once __DIR__ . '/../app/verification.php';
require_once __DIR__ . '/../app/mail.php';

function handle_verify_email() {
    $token = $_GET['token'] ?? '';
    
    if (verify_email_token($token)) {
        flash_set('success', 'Email berhasil diverifikasi. Silakan login.');
        header('Location: ' . BASE_URL . '/?page=login');
        exit;
    }
    
    flash_set('error', 'Token verifikasi tidak valid atau sudah kadaluarsa.');
    header('Location: ' . BASE_URL . '/?page=login');
    exit;
}