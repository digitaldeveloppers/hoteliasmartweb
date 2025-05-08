<?php
//putenv('OPENAI_API_KEY=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');
// --- Chatbot AJAX handler ---
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['action']) && $input['action'] === 'chatbot') {
        $question = $input['question'] ?? '';
        $article = $input['article'] ?? '';
        $api_key = getenv('OPENAI_API_KEY');
        if (!$api_key) {
            header('Content-Type: application/json');
            echo json_encode(['answer' => 'OpenAI API key not set. Please set OPENAI_API_KEY in your environment.']);
            exit;
        }
        $messages = [
            ['role' => 'system', 'content' => 'You are an assistant that explains articles and answers questions about them.'],
            ['role' => 'user', 'content' => "Article:\n$article\n\nUser question: $question\n\nAnswer as helpfully as possible, based only on the article above."]
        ];
        $payload = json_encode([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'max_tokens' => 512,
            'temperature' => 0.7
        ]);
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        $answer = '';
        $debug = '';
        if ($result && !$error) {
            $data = json_decode($result, true);
            if (isset($data['choices'][0]['message']['content'])) {
                $answer = $data['choices'][0]['message']['content'];
            } else {
                $debug = $result;
                $answer = 'API error: ' . htmlspecialchars($result);
            }
        } else {
            $answer = 'cURL error: ' . htmlspecialchars($error) . '\nResponse: ' . htmlspecialchars($result);
        }
        header('Content-Type: application/json');
        echo json_encode(['answer' => $answer, 'debug' => $debug]);
        exit;
    }
}
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../models/Article.php';
require_once __DIR__ . '/../../../controllers/ArticleController.php';

$controller = new ArticleController();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: show.php');
    exit;
}

$article = $controller->getArticleByAuteurId($_GET['id']);

if (!$article) {
    header('Location: show.php');
    exit;
}

// --- Comment form processing logic ---
require_once __DIR__ . '/../../../models/Comment.php';
require_once __DIR__ . '/../../../controllers/comment_con.php';
$commentController = new CommentCon('commentaire');
$errors = [];
$success_message = '';
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
        if (preg_match('/\\b' . preg_quote($word, '/') . '\\b/', $contentLower)) {
            $errors[] = 'Your comment contains inappropriate language.';
            break;
        }
    }
    // Fetch the article's auteur_id from the database
    $auteur_id = null;
    if ($article_id) {
        require_once __DIR__ . '/../../../controllers/ArticleController.php';
        $articleController = new ArticleController();
        $articleCheck = $articleController->getArticleByAuteurId($article_id);
        if ($articleCheck && isset($articleCheck['auteur_id'])) {
            $auteur_id = $articleCheck['auteur_id'];
        } else {
            $errors[] = 'Could not find article author.';
        }
    }
    $imageName = null;
    if ($image && $image['tmp_name']) {
        $targetDir = __DIR__ . '/../../../uploads/';
        $imageName = uniqid('comment_') . '_' . basename($image['name']);
        $targetFile = $targetDir . $imageName;
        move_uploaded_file($image['tmp_name'], $targetFile);
    }
    if (empty($errors)) {
        $comment = new Comment($content, $imageName, $auteur_id, date('Y-m-d H:i:s'));
        $commentController->addComment($comment);
        $success_message = 'Comment added successfully!';
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;
    if ($rating && $rating >= 1 && $rating <= 5) {
        $controller->addRating($article['auteur_id'], $rating);
        $success_message = 'Rating submitted successfully!';
    } else {
        $errors[] = 'Please select a valid rating.';
    }
}
?>
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Hotelia Smart - Articles</title>
    <link rel="shortcut icon" type="image/icon" href="../assets/HS.png"/>
    
    <!--font-awesome.min.css-->
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">

    <!--linear icon css-->
    <link rel="stylesheet" href="../assets/css/linearicons.css">

    <!--animate.css-->
    <link rel="stylesheet" href="../assets/css/animate.css">

    <!--flaticon.css-->
    <link rel="stylesheet" href="../assets/css/flaticon.css">

    <!--slick.css-->
    <link rel="stylesheet" href="../assets/css/slick.css">
    <link rel="stylesheet" href="../assets/css/slick-theme.css">
    
    <!--bootstrap.min.css-->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    
    <!-- bootsnav -->
    <link rel="stylesheet" href="../assets/css/bootsnav.css" >	
    
    <!--style.css-->
    <link rel="stylesheet" href="../assets/css/stylefront3.css">
    
    <!--responsive.css-->
    <link rel="stylesheet" href="../assets/css/responsive.css">

    <style>
        .article-container {
            max-width: 800px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .article-header {
            margin-bottom: 30px;
        }
        .article-title {
            font-size: 2rem;
            color: #1a237e;
            margin-bottom: 15px;
        }
        .article-meta {
            display: flex;
            gap: 20px;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .article-image {
            width: 100%;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        .article-image img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 8px;
        }
        .article-content {
            line-height: 1.8;
            color: #333;
        }
        .back-link {
            display: inline-block;
            margin-top: 30px;
            padding: 8px 15px;
            background: #1a237e;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .back-link:hover {
            background: #3949ab;
        }
    </style>
</head>
<body>
    <header>
       
    </header>


    
         <!-- top-area Start -->
		<section class="top-area">
			<div class="header-area">
				<!-- Start Navigation -->
			    <nav class="navbar navbar-default bootsnav  navbar-sticky navbar-scrollspy"  data-minus-value-desktop="70" data-minus-value-mobile="55" data-speed="1000">

			        <div class="container">

			            <!-- Start Header Navigation -->
			            <div class="navbar-header">
			                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
			                    <i class="fa fa-bars"></i>
			                </button>
			                <a class="navbar-brand" href="home.php">Hotelia<span>Smart</span></a>

			            </div><!--/.navbar-header-->
			            <!-- End Header Navigation -->

			            <!-- Collect the nav links, forms, and other content for toggling -->
			            <div class="collapse navbar-collapse menu-ui-design" id="navbar-menu">
			                <ul class="nav navbar-nav navbar-right" data-in="fadeInDown" data-out="fadeOutUp">
			                    <li><a href="../index.php">home</a></li>
                                <li><a href="#">New Article</a></li>
                                
			                   
                                <li><a href="./show.php">Articles</a></li>								
			                    
			                </ul><!--/.nav -->
			            </div><!-- /.navbar-collapse -->
			        </div><!--/.container-->
			    </nav><!--/nav-->
			    <!-- End Navigation -->
			</div><!--/.header-area-->
		    <div class="clearfix"></div>

		</section><!-- /.top-area-->
		<!-- top-area End -->

    <main>
        <article class="article-container">
            <div class="article-header">
                <h1 class="article-title"><?php echo htmlspecialchars($article['titre']); ?></h1>
                <div class="article-meta">
                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($article['categorie']); ?></span>
                    <span><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($article['date_article'])); ?></span>
                </div>
            </div>

            <?php if (!empty($article['imageArticle'])): ?>
            <div class="article-image">
                <img src="../../../uploads/<?php echo htmlspecialchars($article['imageArticle']); ?>" alt="Image de l'article">
            </div>
            <?php endif; ?>

            <div class="article-content">
                <?php echo nl2br(html_entity_decode($article['contenu'])); ?>
            </div>

            <!-- Display Comments -->
            <?php
            $comments = $commentController->getCommentsByArticleId($article['auteur_id']);
            ?>
            <div class="comments-section" style="margin-top:40px;">
                <h3>Commentaires</h3>
                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment" style="border-bottom:1px solid #eee; margin-bottom:20px; padding-bottom:10px;">
                            <div style="color:#888; font-size:0.9rem; margin-bottom:5px;"><i class="fas fa-calendar-alt"></i> <?php echo isset($comment['date_creation']) ? date('d/m/Y', strtotime($comment['date_creation'])) : ''; ?></div>
                            <div><?php echo nl2br(html_entity_decode($comment['contenu_comment'] ?? '')); ?></div>
                            <?php if (!empty($comment['image_comment'])): ?>
                            <div><img src="../../uploads/<?php echo htmlspecialchars($comment['image_comment']); ?>" alt="Comment image" style="max-width:150px; margin-top:10px;"></div>
                            <?php endif; ?>
                            <a href="./comment_management/edit_comment.php?id=<?php echo $comment['id_comment']; ?>" style="color:#1976d2; text-decoration:underline; margin-top:8px; display:inline-block;">Edit</a>
                            <a href="./comment_management/delete_comment.php?id=<?php echo $comment['id_comment']; ?>" onclick="return confirm('Are you sure you want to delete this comment?');" style="color:#dc3545; text-decoration:underline; margin-top:8px; display:inline-block; margin-left:10px;">Delete</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div>Aucun commentaire pour cet article.</div>
                <?php endif; ?>
            </div>

            <!-- Add Comment Form (inline, with message display) -->
            <div class="add-comment-section" style="margin-top:40px;">
                <?php if (!empty($success_message)): ?>
                    <div class="comment-success" style="color:green; margin-bottom:10px;"> <?php echo $success_message; ?> </div>
                <?php endif; ?>
                <?php if (!empty($errors)): ?>
                    <div class="comment-errors" style="color:red; margin-bottom:10px;">
                        <?php foreach ($errors as $error): ?>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form action="" method="post" enctype="multipart/form-data" class="comment-form">
                    <input type="hidden" name="article_id" value="<?php echo htmlspecialchars($_GET['id'] ?? $_POST['article_id'] ?? ''); ?>">
                    <div>
                    <label for="rating" style="display:block;font-weight:bold;margin-bottom:4px;">Rate this article:</label>
                        <div id="star-rating" style="display:flex;gap:5px;">
                            <i class="fas fa-star" data-value="1" style="font-size:24px;cursor:pointer;color:#ccc;"></i>
                            <i class="fas fa-star" data-value="2" style="font-size:24px;cursor:pointer;color:#ccc;"></i>
                            <i class="fas fa-star" data-value="3" style="font-size:24px;cursor:pointer;color:#ccc;"></i>
                            <i class="fas fa-star" data-value="4" style="font-size:24px;cursor:pointer;color:#ccc;"></i>
                            <i class="fas fa-star" data-value="5" style="font-size:24px;cursor:pointer;color:#ccc;"></i>
                        </div>
                        <input type="hidden" name="rating" id="rating" value="">
                    </div>
                    <script>
                        const stars = document.querySelectorAll('#star-rating .fa-star');
                        stars.forEach(star => {
                            star.addEventListener('click', function() {
                                const ratingValue = this.getAttribute('data-value');
                                document.getElementById('rating').value = ratingValue;
                                stars.forEach(s => s.style.color = '#ccc');
                                for (let i = 0; i < ratingValue; i++) {
                                    stars[i].style.color = '#ffcc00';
                                }
                            });
                        });
                    </script>
                    <button type="submit" name="submit_rating" style="background-color:#1976d2;color:#fff;padding:10px 24px;border:none;border-radius:4px;cursor:pointer;font-size:16px;transition:background 0.2s;box-shadow:0 2px 4px rgba(0,0,0,0.08);margin-top:10px;">Submit Rating</button>
                </div>
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
                </form>
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
                        micStatus.style.display = 'none';
                      } else {
                        recognition.start();
                        micBtn.classList.add('recording');
                        micBtn.style.color = 'red';
                        micStatus.style.display = 'inline';
                      }
                    });
                    recognition.onresult = function(event) {
                      var transcript = event.results[0][0].transcript;
                      input.value += (input.value ? ' ' : '') + transcript;
                    };
                    recognition.onerror = function(event) {
                      micBtn.classList.remove('recording');
                      micBtn.style.color = '';
                      micStatus.style.display = 'none';
                      alert('Speech recognition error: ' + event.error);
                    };
                    recognition.onend = function() {
                      micBtn.classList.remove('recording');
                      micBtn.style.color = '';
                      micStatus.style.display = 'none';
                    };
                  } else {
                    micBtn.style.display = 'none';
                    micStatus.style.display = 'none';
                  }
                });
                </script>
            </div>

            <a href="show.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour aux articles</a>
        </article>
       <!-- Chatbot UI -->
       <div id="chatbot-container" style="max-width:400px;margin:40px auto 0;padding:20px;background:#f9f9f9;border-radius:10px;box-shadow:0 0 10px #ddd;">
            <h3 style="text-align:center;">Ask about this article</h3>
            <div id="chat-messages" style="height:200px;overflow-y:auto;background:#fff;padding:10px;border-radius:6px;border:1px solid #eee;margin-bottom:10px;"></div>
            <form id="chat-form" style="display:flex;gap:10px;">
                <input type="text" id="chat-input" placeholder="Type your question..." style="flex:1;padding:7px;border-radius:4px;border:1px solid #ccc;">
                <button type="submit" style="padding:7px 15px;border:none;background:#007bff;color:#fff;border-radius:4px;">Send</button>
            </form>
        </div>
        <script>
        // Get article content (as plain text)
        function getArticleText() {
            // You may need to adjust this selector to match your article's main content
            var article = document.querySelector('article');
            return article ? article.innerText : '';
        }

        function appendMessage(sender, text) {
            const chat = document.getElementById('chat-messages');
            const msgDiv = document.createElement('div');
            msgDiv.style.margin = '8px 0';
            msgDiv.innerHTML = `<b>${sender}:</b> ${text}`;
            chat.appendChild(msgDiv);
            chat.scrollTop = chat.scrollHeight;
        }

        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const input = document.getElementById('chat-input');
            const question = input.value.trim();
            if (!question) return;
            appendMessage('You', question);
            input.value = '';
            appendMessage('Bot', '<span style="color:gray;">Thinking...</span>');

            // Prepare messages array for Grok API
            const articleText = getArticleText();
            const messages = [
                { role: 'system', content: 'You are an assistant that explains articles and answers questions about them.' },
                { role: 'user', content: `Article:\n${articleText}\n\nUser question: ${question}\n\nAnswer as helpfully as possible, based only on the article above.` }
            ];

            fetch('grok_proxy.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ messages })
            })
            .then(res => res.json())
            .then(data => {
                const chat = document.getElementById('chat-messages');
                chat.lastChild.remove();
                if (data.choices && data.choices[0] && data.choices[0].message && data.choices[0].message.content) {
                    appendMessage('Bot', data.choices[0].message.content);
                } else if (data.error) {
                    appendMessage('Bot', 'API error: ' + (data.details || data.error));
                } else {
                    appendMessage('Bot', 'Sorry, I could not get an answer.');
                }
            })
            .catch((err) => {
                const chat = document.getElementById('chat-messages');
                chat.lastChild.remove();
                appendMessage('Bot', 'Error contacting the AI service.');
            });
        });
        </script>
    </main>
</body>
</html>
