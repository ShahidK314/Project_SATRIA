<?php
require_once __DIR__ . '/db.php';
session_start();

function login($username, $password) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if (!$user) return false;

    $stored = $user['password'];
    // Jika tersimpan hash (bcrypt/argon) gunakan password_verify
    if (strlen($stored) > 20 && (strpos($stored, '$2y$') === 0 || strpos($stored, '$argon2') === 0)) {
        if (password_verify($password, $stored)) {
            // set session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            return true;
        }
        return false;
    }

    // Backward compatibility: jika masih plaintext di DB (demo), cocokkan langsung
    if ($stored === $password) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
        return true;
    }
    return false;
}

function logout() {
    session_unset();
    session_destroy();
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!current_user()) {
        header('Location: ' . BASE_URL . '/?page=login');
        exit;
    }
}

function has_role($roles) {
    $user = current_user();
    if (!$user) return false;
    if (is_array($roles)) return in_array($user['role'], $roles);
    return $user['role'] === $roles;
}

// Utility: buat user baru (hash password otomatis)
function create_user($name, $username, $password, $role = 'pengusul', $unit = null, $email = null) {
    $pdo = get_pdo();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    // Try to insert with email column if it exists
    try {
        $stmt = $pdo->prepare('INSERT INTO users (name, username, password, role, unit, email) VALUES (?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$name, $username, $hash, $role, $unit, $email]);
    } catch (PDOException $e) {
        // Fallback to older schema without email
        $stmt = $pdo->prepare('INSERT INTO users (name, username, password, role, unit) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$name, $username, $hash, $role, $unit]);
    }
}

// Skrip migrasi sederhana: hash password plaintext di DB (jalankan sekali dari CLI)
function migrate_hash_passwords() {
    $pdo = get_pdo();
    $stmt = $pdo->query("SELECT id, password FROM users");
    $rows = $stmt->fetchAll();
    foreach ($rows as $r) {
        $pw = $r['password'];
        if (strlen($pw) < 60 || (strpos($pw, '$2y$') !== 0 && strpos($pw, '$argon2') !== 0)) {
            $hash = password_hash($pw, PASSWORD_DEFAULT);
            $u = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $u->execute([$hash, $r['id']]);
        }
    }
}

?>