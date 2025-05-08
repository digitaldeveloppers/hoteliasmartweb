<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../models/Comment.php';
require_once __DIR__ . '/../../../controllers/comment_con.php';

$commentController = new CommentCon('commentaire');
$errors = [];
$comment = null;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: article.php');
    exit;
}
$comment_id = intval($_GET['id']);

// Fetch the comment to edit
$comment = $commentController->getComment($comment_id);
if (!$comment) {
    $errors[] = 'Comment not found.';
} else {
    // Pre-fill the form with existing comment data
    $content = $comment['contenu_comment'];
    $imageName = $comment['image_comment'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment'])) {
    $content = trim($_POST['content'] ?? '');
    $image = $_FILES['image'] ?? null;
    $imageName = $comment['image_comment']; // Use image_comment instead of image

    if (!$content) {
        $errors[] = 'Le contenu du commentaire est obligatoire.';
    }

    if ($image && $image['tmp_name']) {
        $targetDir = __DIR__ . '/../../uploads/';
        $imageName = uniqid('comment_') . '_' . basename($image['name']);
        $targetFile = $targetDir . $imageName;
        move_uploaded_file($image['tmp_name'], $targetFile);
    }

    if (empty($errors)) {
        $updatedComment = new Comment($content, $imageName, $comment['auteur_id'], $comment['date_creation']); // Use date_creation instead of date
        $updatedComment->set_id_comment($comment_id);
        $commentController->updateComment($updatedComment);
        header('Location: ../article.php?id=' . $comment['auteur_id']);
        exit;
    }
}
?>
<form action="edit_comment.php?id=<?php echo htmlspecialchars($comment_id); ?>" method="post" enctype="multipart/form-data" class="comment-form" style="margin-top:40px;" onsubmit="return validateForm();">
    <div>
        <label for="content" style="display:block;font-weight:bold;margin-bottom:4px;">Edit Comment:</label>
        <div style="display: flex; align-items: center;">
          <input type="text" name="content" id="content"  style="width:100%;padding:8px;font-size:16px;box-sizing:border-box;flex:1;" value="<?php echo htmlspecialchars(isset($_POST['content']) ? $_POST['content'] : ($content ?? '')); ?>">
          <button type="button" id="mic-content" title="Voice to text" style="margin-left:8px;background:none;border:none;cursor:pointer;font-size:22px;">
            <i class="fas fa-microphone"></i>
          </button>
        </div>
    </div>
    <div>
        <label for="image" style="display:block;font-weight:bold;margin-bottom:4px;">Image (optional):</label>
        <input type="file" name="image" id="image" accept="image/*" style="font-family:inherit;font-size:inherit;padding:0;margin:0;border:none;background:none;box-shadow:none;">
        <?php if (!empty($imageName)): ?>
            <div>Current image: <img src="../../uploads/<?php echo htmlspecialchars($imageName); ?>" alt="Comment Image" style="max-width:100px;"></div>
        <?php endif; ?>
    </div>
    <button type="submit" name="edit_comment" style="background-color:#1976d2;color:#fff;padding:10px 24px;border:none;border-radius:4px;cursor:pointer;font-size:16px;transition:background 0.2s;box-shadow:0 2px 4px rgba(0,0,0,0.08);margin-top:10px;">Update Comment</button>
    <a href="delete_comment.php?id=<?php echo htmlspecialchars($comment_id); ?>" onclick="return confirm('Are you sure you want to delete this comment?');" style="display:inline-block;background-color:#dc3545;color:#fff;padding:10px 24px;border:none;border-radius:4px;cursor:pointer;font-size:16px;transition:background 0.2s;box-shadow:0 2px 4px rgba(0,0,0,0.08);margin-top:10px;margin-left:10px;text-decoration:none;">Delete Comment</a>
    <?php if (!empty($errors)): ?>
        <div class="comment-errors" style="color:red;">
            <?php foreach ($errors as $error): ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</form>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var micBtn = document.getElementById('mic-content');
  var input = document.getElementById('content');
  var recognition;
  if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();
    recognition.lang = 'fr-FR';
    recognition.continuous = false;
    recognition.interimResults = false;
    micBtn.addEventListener('click', function() {
      if (micBtn.classList.contains('recording')) {
        recognition.stop();
        micBtn.classList.remove('recording');
        micBtn.style.color = '';
      } else {
        recognition.start();
        micBtn.classList.add('recording');
        micBtn.style.color = 'red';
      }
    });
    recognition.onresult = function(event) {
      var transcript = event.results[0][0].transcript;
      input.value += (input.value ? ' ' : '') + transcript;
    };
    recognition.onerror = function(event) {
      micBtn.classList.remove('recording');
      micBtn.style.color = '';
      alert('Speech recognition error: ' + event.error);
    };
    recognition.onend = function() {
      micBtn.classList.remove('recording');
      micBtn.style.color = '';
    };
  } else {
    micBtn.style.display = 'none';
  }
});
</script>
<script>
function validateForm() {
    var content = document.getElementById('content').value.trim();
    var errorDiv = document.querySelector('.comment-errors');
    
    if (!content) {
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'comment-errors';
            errorDiv.style.color = 'red';
            document.querySelector('.comment-form').appendChild(errorDiv);
        }
        errorDiv.innerHTML = '<div>Le contenu du commentaire est obligatoire.</div>';
        return false;
    }
    
    if (errorDiv) {
        errorDiv.innerHTML = '';
    }
    return true;
}
</script>
<script src="js/validation_edit_comment.js"></script>