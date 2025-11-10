<?php
require_once __DIR__ . '/../captcha.php';

// Generate new CAPTCHA
$captcha = generate_captcha();
$image = generate_captcha_image($captcha['code']);

// Return image
header('Content-Type: application/json');
echo json_encode([
    'token' => $captcha['token'],
    'image' => 'data:image/png;base64,' . $image
]);