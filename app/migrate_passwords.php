<?php
// Jalankan dari CLI: php app/migrate_passwords.php
require_once __DIR__ . '/auth.php';
echo "Memulai migrasi password ke hash...\n";
migrate_hash_passwords();
echo "Selesai. Semua password plaintext di DB telah di-hash jika ditemukan.\n";
