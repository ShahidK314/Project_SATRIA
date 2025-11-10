<?php
require_once __DIR__ . '/config.php';

function get_pdo() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            // Jika database belum dibuat, beri pesan yang jelas
            die('Koneksi database gagal: ' . $e->getMessage() . '\nPastikan database `db_satria` sudah di-import dari sql/db_satria.sql');
        }
    }
    return $pdo;
}

?>