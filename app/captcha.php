<?php
require_once __DIR__ . '/db.php';

function generate_captcha() {
    if (!isset($_SESSION)) session_start();
    
    // Generate random string
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $captcha_string = '';
    for ($i = 0; $i < 6; $i++) {
        $captcha_string .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    // Set cookie dan session untuk verifikasi
    $token = bin2hex(random_bytes(16));
    setcookie('captcha_token', $token, [
        'expires' => time() + 600,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    
    $_SESSION['captcha_' . $token] = [
        'code' => $captcha_string,
        'expires' => time() + 600
    ];
    
    return [
        'token' => $token,
        'code' => $captcha_string
    ];
}

function verify_captcha($token, $user_input) {
    if (!isset($_SESSION)) session_start();
    
    // Cek token cookie
    if (!isset($_COOKIE['captcha_token']) || $_COOKIE['captcha_token'] !== $token) {
        return false;
    }
    
    // Cek session
    $session_key = 'captcha_' . $token;
    if (!isset($_SESSION[$session_key])) {
        return false;
    }
    
    $captcha_data = $_SESSION[$session_key];
    
    // Cek expired
    if (time() > $captcha_data['expires']) {
        unset($_SESSION[$session_key]);
        setcookie('captcha_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        return false;
    }
    
    // Verifikasi input
    $result = strtoupper($user_input) === $captcha_data['code'];
    
    // Hapus session dan cookie setelah verifikasi
    unset($_SESSION[$session_key]);
    setcookie('captcha_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    
    return $result;
}

// Generate captcha image
function generate_captcha_image($code) {
    $width = 200;
    $height = 50;
    
    // Buat gambar
    $image = imagecreatetruecolor($width, $height);
    
    // Warna
    $bg = imagecolorallocate($image, 255, 255, 255);
    $text = imagecolorallocate($image, 0, 0, 0);
    
    // Isi background
    imagefilledrectangle($image, 0, 0, $width, $height, $bg);
    
    // Tambah noise/gangguan
    for ($i = 0; $i < 1000; $i++) {
        $x = rand(0, $width-1);
        $y = rand(0, $height-1);
        imagesetpixel($image, $x, $y, $text);
    }
    
    // Tambah garis random
    for ($i = 0; $i < 5; $i++) {
        imageline($image, 
            rand(0, $width/2), rand(0, $height), 
            rand($width/2, $width), rand(0, $height), 
            $text
        );
    }
    
    // Tulis text captcha
    $font_size = 20;
    $x = ($width - (strlen($code) * $font_size)) / 2;
    imagestring($image, 5, $x, ($height-20)/2, $code, $text);
    
    // Output gambar
    ob_start();
    imagepng($image);
    $image_data = ob_get_clean();
    imagedestroy($image);
    
    return base64_encode($image_data);
}