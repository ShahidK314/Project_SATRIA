<?php
require_once __DIR__ . '/db.php';

function audit_log($user_id, $action, $entity_type = null, $entity_id = null, $comment = null) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action, entity_type, entity_id, comment, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    return $stmt->execute([$user_id, $action, $entity_type, $entity_id, $comment]);
}

?>
