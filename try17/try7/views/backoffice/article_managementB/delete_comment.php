<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../models/Comment.php';
require_once __DIR__ . '/../../../controllers/comment_con.php';

$commentController = new CommentCon('commentaire');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: show.php');
    exit;
}

$comment_id = intval($_GET['id']);
$comment = $commentController->getComment($comment_id);

if ($comment) {
    // Get the article ID before deleting the comment
    $article_id = $comment['article_id'];
    
    if ($commentController->deleteComment($comment_id)) {
        // Redirect back to the article page after successful deletion
        header('Location: show_article.php?auteur_id=' . $article_id);
        exit;
    }
}

// If something went wrong, redirect to the main page
header('Location: show.php');
exit;