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
                <?php echo nl2br(htmlspecialchars($article['contenu'])); ?>
            </div>

            <!-- Display Comments -->
            <?php
            require_once __DIR__ . '/../../models/Comment.php';
            require_once __DIR__ . '/../../controllers/comment_con.php';
            $commentController =new CommentCon('commentaire');
            $comments = $commentController->getCommentsByArticleId($article['auteur_id']);
            ?>
            <div class="comments-section" style="margin-top:40px;">
                <h3>Commentaires</h3>
                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment" style="border-bottom:1px solid #eee; margin-bottom:20px; padding-bottom:10px;">
                            <div style="color:#888; font-size:0.9rem; margin-bottom:5px;"><i class="fas fa-calendar-alt"></i> <?php echo isset($comment['date_creation']) ? date('d/m/Y', strtotime($comment['date_creation'])) : ''; ?></div>
                            <div><?php echo nl2br(htmlspecialchars($comment['contenu_comment'] ?? '')); ?></div>
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

            <!-- Add Comment Form -->
            <?php include 'add_comment.php'; ?>

            <a href="show.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour aux articles</a>
        </article>
    </main>
</body>
</html>
