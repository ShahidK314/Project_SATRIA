<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();
// Check if column exists
$stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'email'");
$stmt->execute();
if ($stmt->fetch()) {
    echo "Column 'email' already exists.\n";
    exit;
}
$sql = "ALTER TABLE users ADD COLUMN email VARCHAR(255) NULL AFTER username, ADD UNIQUE (email)";
$pdo->exec($sql);
echo "Added 'email' column to users table.\n";
