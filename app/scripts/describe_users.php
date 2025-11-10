<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();
$stmt = $pdo->query("SHOW COLUMNS FROM users");
$cols = $stmt->fetchAll();
foreach ($cols as $c) {
    echo $c['Field'] . "\t" . $c['Type'] . "\n";
}
