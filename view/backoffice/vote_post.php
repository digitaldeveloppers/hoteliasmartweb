<?php
header('Content-Type: application/json');
// Use PDO connection
require_once __DIR__ . '/../../config/db_pdo.php';

try {
    $db = getPDO();
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$vote_type = isset($_POST['vote_type']) ? $_POST['vote_type'] : '';

if ($post_id <= 0) {
    die(json_encode(['success' => false, 'message' => 'Invalid post ID']));
}

if ($vote_type !== 'upvote' && $vote_type !== 'downvote') {
    die(json_encode(['success' => false, 'message' => 'Invalid vote type']));
}

// Get current vote counts
$stmt = $db->prepare("SELECT upvotes, downvotes FROM forum_posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    die(json_encode(['success' => false, 'message' => 'Post not found']));
}

// Update vote count
if ($vote_type === 'upvote') {
    $upvotes = ($post['upvotes'] ?? 0) + 1;
    $update = $db->prepare("UPDATE forum_posts SET upvotes = ? WHERE id = ?");
    $result = $update->execute([$upvotes, $post_id]);
} else {
    $downvotes = ($post['downvotes'] ?? 0) + 1;
    $update = $db->prepare("UPDATE forum_posts SET downvotes = ? WHERE id = ?");
    $result = $update->execute([$downvotes, $post_id]);
}

if ($result) {
    echo json_encode([
        'success' => true, 
        'upvotes' => $vote_type === 'upvote' ? $upvotes : $post['upvotes'],
        'downvotes' => $vote_type === 'downvote' ? $downvotes : $post['downvotes']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update vote']);
}

// No need to close PDO connection
?>