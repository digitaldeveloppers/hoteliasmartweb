<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/database/db_connect.php';

try {
    $db = getPDO();
} catch (Exception $e) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

// Check if user is logged in and has a profile
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'You must be logged in to vote']));
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$vote_type = isset($_POST['vote_type']) ? $_POST['vote_type'] : '';
$user_id = $_SESSION['user_id'];

if (!in_array($vote_type, ['upvote', 'downvote'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid vote type']));
}

// Start transaction
$db->beginTransaction();

try {
    // Check if user already voted
    $check_stmt = $db->prepare("SELECT vote_type FROM post_votes WHERE post_id = ? AND user_profile_id = ?");
    $check_stmt->execute([$post_id, $user_id]);
    $result = $check_stmt->fetch();
    
    if ($result) {
        // User already voted - update their vote
        $existing_vote = $result['vote_type'];
        
        if ($existing_vote === $vote_type) {
            // Remove vote if clicking the same button
            $stmt = $db->prepare("DELETE FROM post_votes WHERE post_id = ? AND user_profile_id = ?");
            $stmt->execute([$post_id, $user_id]);
        } else {
            // Change vote
            $stmt = $db->prepare("UPDATE post_votes SET vote_type = ? WHERE post_id = ? AND user_profile_id = ?");
            $stmt->execute([$vote_type, $post_id, $user_id]);
        }
    } else {
        // New vote
        $stmt = $db->prepare("INSERT INTO post_votes (post_id, user_profile_id, vote_type) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $user_id, $vote_type]);
    }
    
    // Get updated vote counts
    $count_stmt = $db->prepare("SELECT 
        SUM(CASE WHEN vote_type = 'upvote' THEN 1 ELSE 0 END) as upvotes,
        SUM(CASE WHEN vote_type = 'downvote' THEN 1 ELSE 0 END) as downvotes
        FROM post_votes WHERE post_id = ?");
    $count_stmt->execute([$post_id]);
    $counts = $count_stmt->fetch();
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'upvotes' => (int)$counts['upvotes'],
        'downvotes' => (int)$counts['downvotes']
    ]);
    
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error processing vote: ' . $e->getMessage()]);
}

// PDO connections close automatically when the script ends
?>