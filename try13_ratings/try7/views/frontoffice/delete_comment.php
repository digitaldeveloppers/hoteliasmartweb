<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../models/Comment.php';
require_once __DIR__ . '/../../controllers/comment_con.php';

$commentController = new CommentCon('commentaire');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: article.php');
    exit;
}

$comment_id = intval($_GET['id']);
$comment = $commentController->getComment($comment_id);

if (!$comment) {
    header('Location: article.php?error=comment_not_found');
    exit;
}

// Delete the associated image if it exists
if (!empty($comment['image_comment'])) {
    $imagePath = __DIR__ . '/../../uploads/' . $comment['image_comment'];
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

// Delete the comment
$commentController->deleteComment($comment_id);

// Redirect back to the article page
header('Location: article.php?id=' . $comment['auteur_id']);
exit;