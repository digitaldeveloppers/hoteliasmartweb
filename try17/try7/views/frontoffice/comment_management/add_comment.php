<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../models/Comment.php';
require_once __DIR__ . '/../../../controllers/comment_con.php';

$commentController = new CommentCon('commentaire');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : null;
    $content = trim($_POST['content'] ?? '');
    $image = $_FILES['image'] ?? null;

    if (!$article_id) {
        $errors[] = 'Invalid article.';
    }
    if (!$content) {
        $errors[] = 'Comment content is required.';
    }

    // Bad words filter
    $badWords = ["bitch", "hoe", "simp", "idiot", "stupid", "dumb", "fool", "moron", "loser", "jerk", "bastard", "asshole", "shit", "crap", "damn", "hell", "slut", "whore", "dick", "cock", "piss", "fuck", "fucking", "fucker", "motherfucker", "cunt", "twat", "prick", "wanker", "bollocks", "bugger", "arse", "arsehole", "jackass", "retard", "retarded", "suck", "sucks", "sucker", "pussy", "tit", "boob", "boobs", "nigger", "nigga", "spic", "chink", "gook", "kike", "fag", "faggot", "dyke", "tranny", "queer", "homo", "gay", "lesbo", "slutty", "bastards", "shithead", "shitface", "douche", "douchebag", "scumbag", "skank", "cum", "jizz", "spunk", "balls", "nuts", "testicle", "penis", "vagina", "anus", "butt", "butthole", "craphead", "shitbag", "shitass", "fuckface", "fuckhead", "asswipe", "asshat", "dipshit", "twit", "twithead", "twatwaffle", "dickhead", "dickweed", "pisshead", "pissface", "cockhead", "cockface", "cockbite", "cockmunch", "cocksmoker", "cumdumpster", "cumslut", "cumwhore", "cumface", "cumshot"];
    $contentLower = strtolower($content);
    foreach ($badWords as $word) {
        if (strpos($contentLower, $word) !== false) {
            $errors[] = 'Your comment contains inappropriate language.';
            break;
        }
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
        <div style="display:flex;align-items:center;width:100%;">
            <input type="text" name="content" id="content" style="flex:1;padding:8px;font-size:16px;box-sizing:border-box;">
            <button type="button" id="mic-btn" title="Speak your comment" style="margin-left:8px;background:#eee;border:none;padding:8px 12px;border-radius:50%;cursor:pointer;font-size:18px;">🎤</button>
        </div>
        <span id="mic-status" style="margin-left:8px;color:#1976d2;font-size:14px;display:none;">Listening...</span>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
  var micBtn = document.getElementById('mic-btn');
  var input = document.getElementById('content');
  var micStatus = document.getElementById('mic-status');
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
        if (micStatus) micStatus.style.display = 'none';
      } else {
        recognition.start();
        micBtn.classList.add('recording');
        micBtn.style.color = 'red';
        if (micStatus) micStatus.style.display = '';
      }
    });
    recognition.onresult = function(event) {
      var transcript = event.results[0][0].transcript;
      input.value += (input.value ? ' ' : '') + transcript;
    };
    recognition.onerror = function(event) {
      micBtn.classList.remove('recording');
      micBtn.style.color = '';
      if (micStatus) micStatus.style.display = 'none';
      alert('Speech recognition error: ' + event.error);
    };
    recognition.onend = function() {
      micBtn.classList.remove('recording');
      micBtn.style.color = '';
      if (micStatus) micStatus.style.display = 'none';
    };
  } else {
    micBtn.style.display = 'none';
    if (micStatus) micStatus.style.display = 'none';
  }
});
</script>