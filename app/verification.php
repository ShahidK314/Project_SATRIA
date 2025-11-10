<?php
require_once __DIR__ . '/db.php';

// Buat tabel email_verifications jika belum ada
function create_verification_table() {
    $pdo = get_pdo();
    $sql = "CREATE TABLE IF NOT EXISTS email_verifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        email VARCHAR(255) NOT NULL,
        token VARCHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX (token),
        INDEX (expires_at)
    )";
    $pdo->exec($sql);
}

// Buat tabel user_profiles jika belum ada
function create_profile_table() {
    $pdo = get_pdo();
    $sql = "CREATE TABLE IF NOT EXISTS user_profiles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL UNIQUE,
        bio TEXT,
        avatar VARCHAR(255),
        phone VARCHAR(20),
        department VARCHAR(100),
        position VARCHAR(100),
        last_login DATETIME,
        email_verified BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
}

// Generate random token untuk verifikasi email
function generate_verification_token() {
    return bin2hex(random_bytes(32));
}

// Simpan token verifikasi ke database
function create_verification_token($userId, $email) {
    $pdo = get_pdo();
    $token = generate_verification_token();
    $expires = date('Y-m-d H:i:s', time() + EMAIL_TOKEN_EXPIRY);
    
    // Hapus token lama jika ada
    $stmt = $pdo->prepare('DELETE FROM email_verifications WHERE user_id = ?');
    $stmt->execute([$userId]);
    
    // Buat token baru
    $stmt = $pdo->prepare('INSERT INTO email_verifications (user_id, email, token, expires_at) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $email, $token, $expires]);
    
    return $token;
}

// Verifikasi token
function verify_email_token($token) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT user_id, email FROM email_verifications 
        WHERE token = ? AND expires_at > NOW() 
        LIMIT 1');
    $stmt->execute([$token]);
    $verification = $stmt->fetch();
    
    if ($verification) {
        // Update user profile
        $stmt = $pdo->prepare('UPDATE user_profiles SET email_verified = TRUE WHERE user_id = ?');
        $stmt->execute([$verification['user_id']]);
        
        // Hapus token yang sudah digunakan
        $stmt = $pdo->prepare('DELETE FROM email_verifications WHERE token = ?');
        $stmt->execute([$token]);
        
        return true;
    }
    return false;
}

// Inisialisasi profil pengguna
function create_user_profile($userId, $data = []) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO user_profiles (
        user_id, phone, department, position, bio
    ) VALUES (?, ?, ?, ?, ?)');
    
    return $stmt->execute([
        $userId,
        $data['phone'] ?? null,
        $data['department'] ?? null,
        $data['position'] ?? null,
        $data['bio'] ?? null
    ]);
}

// Buat tabel saat file di-include
create_verification_table();
create_profile_table();