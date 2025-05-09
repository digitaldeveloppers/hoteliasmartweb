<?php
header('Content-Type: application/json');

// Use PDO connection
require_once __DIR__ . '/../../config/db_pdo.php';

try {
    $db = getPDO();
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

if ($user_id <= 0) {
    die(json_encode(['success' => false, 'message' => 'Invalid user ID']));
}

// Start transaction
$db->beginTransaction();

try {
    // Delete user's comments
    $delete_comments = $db->prepare("DELETE FROM post_comments WHERE user_profile_id = ?");
    $delete_comments->execute([$user_id]);

    // Delete user's posts
    $delete_posts = $db->prepare("DELETE FROM forum_posts WHERE user_profile_id = ?");
    $delete_posts->execute([$user_id]);

    // Delete user profile
    $delete_user = $db->prepare("DELETE FROM user_profiles WHERE id = ?");
    $delete_user->execute([$user_id]);

    // Commit transaction
    $db->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback on error
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
}

// No need to close PDO connection
?>