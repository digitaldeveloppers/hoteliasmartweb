<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../models/Comment.php';
require_once __DIR__ . '/../../controllers/comment_con.php';

$commentController = new CommentCon('commentaire');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : null;
    $author = trim($_POST['author'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $image = $_FILES['image'] ?? null;
    $errors = [];

    
    /*if (!$content) {
        $errors[] = 'Comment content is required.';
    }*/
    if (!$article_id) {
        $errors[] = 'Invalid article.';
    }

    // Fetch the article's auteur_id from the database
    $auteur_id = null;
    if ($article_id) {
        require_once __DIR__ . '/../../controllers/ArticleController.php';
        $articleController = new ArticleController();
        $article = $articleController->getArticleByAuteurId($article_id);
        if ($article && isset($article['auteur_id'])) {
            $auteur_id = $article['auteur_id'];
        } else {
            $errors[] = 'Could not find article author.';
        }
    }

    $imageName = null;
    if ($image && $image['tmp_name']) {
        $targetDir = __DIR__ . '/../../uploads/';
        $imageName = uniqid('comment_') . '_' . basename($image['name']);
        $targetFile = $targetDir . $imageName;
        move_uploaded_file($image['tmp_name'], $targetFile);
    }

    if (empty($errors)) {
        $comment = new Comment($content, $imageName, $auteur_id, date('Y-m-d H:i:s'));
        $commentController->addComment($comment);
        header('Location: article.php?id=' . $article_id);
        exit;
    }
}
?>
<form action="add_comment.php" method="post" enctype="multipart/form-data" class="comment-form" style="margin-top:40px;">
    <input type="hidden" name="article_id" value="<?php echo htmlspecialchars($_GET['id'] ?? $_POST['article_id'] ?? ''); ?>">
    
    <div>
        <label for="content" style="display:block;font-weight:bold;margin-bottom:4px;">Comment:</label>
        <input type="text" name="content" id="content"  style="width:100%;padding:8px;font-size:16px;box-sizing:border-box;">
    </div>
    <div>
        <label for="image" style="display:block;font-weight:bold;margin-bottom:4px;">Image (optional):</label>
        <input type="file" name="image" id="image" accept="image/png,image/jpeg,image/jpg" style="font-family:inherit;font-size:inherit;padding:0;margin:0;border:none;background:none;box-shadow:none;" >
    </div>
    <button type="submit" name="add_comment" style="background-color:#1976d2;color:#fff;padding:10px 24px;border:none;border-radius:4px;cursor:pointer;font-size:16px;transition:background 0.2s;box-shadow:0 2px 4px rgba(0,0,0,0.08);margin-top:10px;">Add Comment</button>
    <?php if (!empty($errors)): ?>
        <div class="comment-errors" style="color:red;">
            <?php foreach ($errors as $error): ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</form>
<script src="js/validation_comment.js"></script>