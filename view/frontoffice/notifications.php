<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';
$db = getPDO();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Not logged in']));
}

$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'];

switch ($action) {
    case 'get':
        $stmt = $db->prepare("
            SELECT n.*, f.title as post_title 
            FROM post_notifications n
            JOIN forum_posts f ON n.post_id = f.id
            WHERE n.user_profile_id = ? AND n.is_read = FALSE
            ORDER BY n.created_at DESC
            LIMIT 10
        ");
        $stmt->execute([$user_id]);
        echo json_encode(['notifications' => $stmt->fetchAll()]);
        break;

    case 'mark-read':
        $notification_id = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : 0;
        if ($notification_id > 0) {
            $stmt = $db->prepare("
                UPDATE post_notifications 
                SET is_read = TRUE 
                WHERE id = ? AND user_profile_id = ?
            ");
            $success = $stmt->execute([$notification_id, $user_id]);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}