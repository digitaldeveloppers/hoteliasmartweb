<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../models/Article.php';
require_once __DIR__ . '/../../controllers/ArticleController.php';

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
require_once __DIR__ . '/../../models/Comment.php';
require_once __DIR__ . '/../../controllers/comment_con.php';
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
        require_once __DIR__ . '/../../controllers/ArticleController.php';
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
        $targetDir = __DIR__ . '/../../uploads/';
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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Hotelia Smart - Articles</title>
    <link rel="shortcut icon" type="image/icon" href="assets/HS.png"/>
    
    <!--font-awesome.min.css-->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">

    <!--linear icon css-->
    <link rel="stylesheet" href="assets/css/linearicons.css">

    <!--animate.css-->
    <link rel="stylesheet" href="assets/css/animate.css">

    <!--flaticon.css-->
    <link rel="stylesheet" href="assets/css/flaticon.css">

    <!--slick.css-->
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/slick-theme.css">
    
    <!--bootstrap.min.css-->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    
    <!-- bootsnav -->
    <link rel="stylesheet" href="assets/css/bootsnav.css" >	
    
    <!--style.css-->
    <link rel="stylesheet" href="assets/css/stylefront3.css">
    
    <!--responsive.css-->
    <link rel="stylesheet" href="assets/css/responsive.css">

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
			                    <li><a href="index.php">home</a></li>
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
                <img src="../../uploads/<?php echo htmlspecialchars($article['imageArticle']); ?>" alt="Image de l'article">
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
                            <a href="edit_comment.php?id=<?php echo $comment['id_comment']; ?>" style="color:#1976d2; text-decoration:underline; margin-top:8px; display:inline-block;">Edit</a>
                            <a href="delete_comment.php?id=<?php echo $comment['id_comment']; ?>" onclick="return confirm('Are you sure you want to delete this comment?');" style="color:#dc3545; text-decoration:underline; margin-top:8px; display:inline-block; margin-left:10px;">Delete</a>
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
            </div>

            <a href="show.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour aux articles</a>
        </article>
        <!-- Chatbot UI for Article Summary/Explanation -->
        <div id="chatbot-container" style="max-width:800px;margin:40px auto 0 auto;padding:20px;background:#f5f7fa;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.07);">
            <h3 style="margin-bottom:16px;color:#1a237e;">AI Article Assistant</h3>
            <div id="chat-messages" style="height:220px;overflow-y:auto;background:#fff;border-radius:6px;padding:12px 10px 12px 10px;margin-bottom:12px;border:1px solid #e0e0e0;font-size:15px;"></div>
            <form id="chat-form" style="display:flex;gap:8px;align-items:center;">
                <input type="text" id="chat-input" placeholder="Ask for a summary, explanation, or more..." style="flex:1;padding:8px 10px;border-radius:4px;border:1px solid #bdbdbd;font-size:15px;" autocomplete="off" />
                <button type="submit" style="background:#1976d2;color:#fff;padding:8px 18px;border:none;border-radius:4px;cursor:pointer;font-size:15px;">Send</button>
            </form>
            <div id="chatbot-status" style="margin-top:8px;color:#888;font-size:13px;display:none;">AI is thinking...</div>
        </div>
        <script>
        const apiKey = "AIzaSyCPQN3ESONgktYladA0GBS5mMynIQ7j7MQ";
        const articleContent = `<?php echo str_replace('`', '\`', html_entity_decode($article['contenu'])); ?>`;
        const articleTitle = `<?php echo str_replace('`', '\`', html_entity_decode($article['titre'])); ?>`;
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatMessages = document.getElementById('chat-messages');
        const chatbotStatus = document.getElementById('chatbot-status');
        function appendMessage(sender, text) {
            const msgDiv = document.createElement('div');
            msgDiv.style.marginBottom = '10px';
            msgDiv.innerHTML = `<strong>${sender}:</strong> ${text}`;
            chatMessages.appendChild(msgDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const userMsg = chatInput.value.trim();
            if (!userMsg) return;
            appendMessage('You', userMsg);
            chatInput.value = '';
            chatbotStatus.style.display = 'block';
            chatbotStatus.textContent = 'AI is thinking...';
            // Compose prompt
            const prompt = `Article Title: ${articleTitle}\nArticle Content: ${articleContent}\n\nUser: ${userMsg}\nAI:`;
            try {
                const response = await fetch('ai_proxy.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ prompt })
                });
                const data = await response.json();
                let aiText = 'Sorry, I could not generate a response.';
                if (data && data.candidates && data.candidates[0] && data.candidates[0].content && data.candidates[0].content.parts && data.candidates[0].content.parts[0] && data.candidates[0].content.parts[0].text) {
                    aiText = data.candidates[0].content.parts[0].text;
                } else if (data && data.error) {
                    aiText = 'AI Error: ' + (data.error.message || JSON.stringify(data.error));
                }
                appendMessage('AI', aiText);
            } catch (err) {
                appendMessage('AI', 'There was an error contacting the AI service: ' + err.message);
                console.error('Fetch error:', err);
            }
            chatbotStatus.style.display = 'none';
        });
        </script>
    </main>
</body>
</html>
