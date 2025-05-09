<?php
// Database connection using PDO
require_once __DIR__ . '/../../config/db_pdo.php';

try {
    $db = getPDO();
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Check if we need to add upvotes/downvotes columns to forum_posts table
$check_columns = $db->query("SHOW COLUMNS FROM forum_posts LIKE 'upvotes'");
if ($check_columns->rowCount() == 0) {
    $db->exec("ALTER TABLE forum_posts ADD COLUMN upvotes INT DEFAULT 0, ADD COLUMN downvotes INT DEFAULT 0");
    echo "Added upvotes and downvotes columns to forum_posts table.<br>";
}

// Check if post_votes table exists (this might be where front office stores votes)
$check_table = $db->query("SHOW TABLES LIKE 'post_votes'");
if ($check_table->rowCount() > 0) {
    // Synchronize votes from post_votes table to forum_posts table
    $db->exec("
        UPDATE forum_posts p
        SET p.upvotes = (
            SELECT COUNT(*) FROM post_votes v 
            WHERE v.post_id = p.id AND v.vote_type = 'upvote'
        ),
        p.downvotes = (
            SELECT COUNT(*) FROM post_votes v 
            WHERE v.post_id = p.id AND v.vote_type = 'downvote'
        )
    ");
    echo "Synchronized votes from post_votes table to forum_posts table.<br>";
} else {
    echo "No post_votes table found. If your front office uses a different table for votes, please modify this script.<br>";
}

echo "Vote synchronization complete.";
?>