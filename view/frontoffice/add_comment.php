<?php
session_start();
require_once __DIR__ . '/database/db_connect.php';
$db = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $comment_text = isset($_POST['comment_text']) ? trim($_POST['comment_text']) : '';

    // Toxic words filter
    $toxic_words = ['fuck', 'shit', 'trash', 'asshole', 'bitch'];
    $comment_text = preg_replace_callback('/\b(' . implode('|', $toxic_words) . ')\b/i', function($matches) {
        return str_repeat('*', strlen($matches[0]));
    }, $comment_text);
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    // Validation améliorée pour les commentaires vides
    if ($post_id <= 0) {
        echo "ID de publication invalide.";
        exit;
    }
    
    // Vérification stricte que le commentaire n'est pas vide après suppression des espaces
    if (empty($comment_text)) {
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
        // Rediriger avec un message d'erreur
        header("Location: $referer?error=empty_comment");
        exit;
    }

    // Verify the post exists
    $check_stmt = $db->prepare("SELECT id FROM forum_posts WHERE id = ? AND status = 'active'");
    $check_stmt->execute([$post_id]);
    $result = $check_stmt->rowCount();

    if ($result === 0) {
        echo "Post not found.";
        exit;
    }

    // Insert the comment
    $stmt = $db->prepare("INSERT INTO post_comments (post_id, user_profile_id, comment_text, created_at) VALUES (?, ?, ?, NOW())");
    
    if ($stmt->execute([$post_id, $user_id, $comment_text])) {
        // Redirect back to the page where the comment was added
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
        header("Location: $referer");
        exit;
    } else {
        echo "Error adding comment: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request method.";
}

// PDO connections close automatically when the script ends
?>