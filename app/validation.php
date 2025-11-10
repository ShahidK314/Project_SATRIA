<?php
// Fungsi validasi password
function validate_password($password) {
    $score = 0;
    $feedback = [];
    
    // Minimal 8 karakter
    if (strlen($password) >= 8) {
        $score += 25;
    } else {
        $feedback[] = "Password minimal 8 karakter";
    }
    
    // Harus ada huruf kecil
    if (preg_match('/[a-z]/', $password)) {
        $score += 25;
    } else {
        $feedback[] = "Tambahkan huruf kecil";
    }
    
    // Harus ada huruf besar
    if (preg_match('/[A-Z]/', $password)) {
        $score += 25;
    } else {
        $feedback[] = "Tambahkan huruf besar";
    }
    
    // Harus ada angka atau simbol
    if (preg_match('/[0-9\W]/', $password)) {
        $score += 25;
    } else {
        $feedback[] = "Tambahkan angka atau simbol";
    }
    
    return [
        'score' => $score,
        'feedback' => $feedback
    ];
}

// Validasi email dengan format dan MX record
function validate_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    $domain = substr(strrchr($email, "@"), 1);
    return checkdnsrr($domain, 'MX');
}

// Sanitasi input
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Log aktivitas pendaftaran
function log_registration($userId, $success, $details = null) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO activity_logs (
        user_id, action, entity_type, status, details
    ) VALUES (?, ?, ?, ?, ?)');
    
    return $stmt->execute([
        $userId,
        'REGISTER',
        'users',
        $success ? 'success' : 'failed',
        $details ? json_encode($details) : null
    ]);
}

// Verifikasi reCAPTCHA
function verify_recaptcha($response) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $response
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
}