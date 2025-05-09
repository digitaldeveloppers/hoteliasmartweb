<?php
header('Content-Type: application/json');

// Include database connection
require_once __DIR__ . '/../../config/db_connect.php';

try {
    // Validate input
    if (!isset($_POST['user_id'])) {
        throw new Exception('User ID is required');
    }

    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    if ($user_id === false || $user_id <= 0) {
        throw new Exception('Invalid user ID');
    }

    $db = getPDO();

    // Get user information
    $stmt = $db->prepare("SELECT * FROM user_profiles WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('User not found');
    }

    // Count user's posts
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM forum_posts WHERE user_profile_id = ?");
    $stmt->execute([$user_id]);
    $post_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Count user's comments
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM post_comments WHERE user_profile_id = ?");
    $stmt->execute([$user_id]);
    $comment_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Format date for better readability
    $user['created_at'] = date('M d, Y', strtotime($user['created_at']));

    // Return success response with user data and counts
    echo json_encode([
        'success' => true,
        'user' => $user,
        'post_count' => $post_count,
        'comment_count' => $comment_count
    ]);

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>