<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    // Validate input
    if (!isset($_POST['post_id'])) {
        throw new Exception('Post ID is required');
    }

    $post_id = filter_var($_POST['post_id'], FILTER_VALIDATE_INT);
    if ($post_id === false || $post_id <= 0) {
        throw new Exception('Invalid post ID');
    }

    $db = getPDO();

    // Check if post exists
    $stmt = $db->prepare("SELECT id FROM forum_posts WHERE id = ?");
    $stmt->execute([$post_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Post not found');
    }

    // Start transaction
    $db->beginTransaction();

    // Delete notifications first
    $stmt = $db->prepare("DELETE FROM post_notifications WHERE post_id = ?");
    $stmt->execute([$post_id]);

    // Delete comments
    $stmt = $db->prepare("DELETE FROM post_comments WHERE post_id = ?");
    $stmt->execute([$post_id]);

    // Delete votes
    $stmt = $db->prepare("DELETE FROM post_votes WHERE post_id = ?");
    $stmt->execute([$post_id]);

    // Delete reports
    $stmt = $db->prepare("DELETE FROM post_reports WHERE post_id = ?");
    $stmt->execute([$post_id]);

    // Finally delete the post
    $stmt = $db->prepare("DELETE FROM forum_posts WHERE id = ?");
    $stmt->execute([$post_id]);

    // Commit transaction
    $db->commit();
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}