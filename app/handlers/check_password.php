<?php
require_once __DIR__ . '/validation.php';

function handle_password_check() {
    header('Content-Type: application/json');
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $password = $input['password'] ?? '';
    
    // Validate password
    $result = validate_password($password);
    
    echo json_encode($result);
    exit;
}