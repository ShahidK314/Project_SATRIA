<?php
require_once __DIR__ . '/db.php';

function create_notification($user_id, $message, $link = null) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO notifications (user_id, message, link, created_at, is_read) VALUES (?, ?, ?, NOW(), 0)');
    return $stmt->execute([$user_id, $message, $link]);
}

function get_notifications($user_id, $only_unread = true) {
    $pdo = get_pdo();
    if ($only_unread) {
        $stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC');
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$user_id]);
    }
    return $stmt->fetchAll();
}

function mark_notifications_read($user_id) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
    return $stmt->execute([$user_id]);
}

?>
