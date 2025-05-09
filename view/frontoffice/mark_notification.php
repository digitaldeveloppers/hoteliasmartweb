<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';
$db = getPDO();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notification_id = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : 0;
    
    if ($notification_id > 0) {
        $stmt = $db->prepare("
            UPDATE post_notifications 
            SET is_read = TRUE 
            WHERE id = ? AND user_profile_id = ?
        ");
        
        if ($stmt->execute([$notification_id, $_SESSION['user_id']])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to mark notification as read']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
    }
}