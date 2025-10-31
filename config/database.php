<?php
define('DB_HOST', 'localhost'); 
define('DB_USER', 'root');      
define('DB_PASS', '');          
define('DB_NAME', 'db_satria'); 

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($db->connect_errno) {
    die("ERROR: Gagal terhubung ke database. " . $db->connect_error);
}

if (!$db->set_charset("utf8mb4")) {
    printf("Error loading character set utf8mb4: %s\n", $db->error);
    exit();
}
?>