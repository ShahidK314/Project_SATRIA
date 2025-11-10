<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../verification.php';

// Create a test user without sending email
$name = 'Test User '.rand(1000,9999);
$username = 'testuser'.rand(1000,9999);
$password = 'Password123!';
$email = $username . '@example.com';
$department = 'Testing';

echo "Creating user: $username ($email)\n";
$created = create_user($name, $username, $password, 'pengusul', $department, $email);
if (!$created) {
    echo "Failed to create user. Possibly username exists.\n";
    exit(1);
}

// Get user id
$pdo = get_pdo();
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
$stmt->execute([$username]);
$user = $stmt->fetch();
if (!$user) {
    echo "User created but cannot find in DB.\n";
    exit(1);
}
$uid = $user['id'];

// Create profile
create_user_profile($uid, ['phone'=>'08123456789','department'=>$department,'bio'=>'Profil test']);

// Create verification token
$token = create_verification_token($uid, $email);

echo "User ID: $uid\n";
echo "Verification token: $token\n";

echo "Done. You can check DB entries in users, user_profiles, email_verifications tables.\n";
?>