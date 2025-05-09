<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db_connect.php';
$db = getPDO();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    
    try {
        // First, check if the user owns this post
        $check_stmt = $db->prepare("SELECT user_profile_id FROM forum_posts WHERE id = ?");
        $check_stmt->execute([$post_id]);
        $post = $check_stmt->fetch();

        if (!$post || $post['user_profile_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Delete associated notifications first
        $delete_notifications = $db->prepare("DELETE FROM post_notifications WHERE post_id = ?");
        $delete_notifications->execute([$post_id]);

        // Delete the post
        $delete_stmt = $db->prepare("DELETE FROM forum_posts WHERE id = ? AND user_profile_id = ?");
        $success = $delete_stmt->execute([$post_id, $_SESSION['user_id']]);

        echo json_encode(['success' => $success]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}