<?php
require_once __DIR__ . '/../../../controllers/ArticleController.php';

// Create an instance of the ArticleController
$articleController = new ArticleController();

// Get all articles
$articles = $articleController->getAllArticles();

// Get average rating for each article
foreach ($articles as &$article) {
    $ratings = $articleController->getAverageRating($article['auteur_id']);
    $article['average_rating'] = $ratings['average'];
    $article['rating_count'] = $ratings['count'];
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
    <link rel="shortcut icon" type="image/icon" href="./assets/HS.png"/>
    
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
        .articles-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 10px;
            max-width: 1400px;
            margin: 100px;
        }
        .article-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            width: 33.33%;
            min-width: 300px;
            box-sizing: border-box;
        }
        .article-card:hover {
            transform: translateY(-5px);
        }
        .article-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            border-radius: 8px 8px 0 0;
            position: relative;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .article-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease, filter 0.3s ease;
        }
        .article-image:hover img {
            transform: scale(1.05);
            filter: brightness(1.05);
        }
        .article-content {
            padding: 20px;
        }
        .article-title {
            font-size: 1.25rem;
            margin-bottom: 10px;
            color: #1a237e;
        }
        .article-meta {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
        }
        .article-excerpt {
            color: #444;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .read-more {
            display: inline-block;
            padding: 8px 15px;
            background: #1a237e;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .read-more:hover {
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
        <section class="articles-section">
            <div class="container">
                <h1>Latest Articles</h1>
                <div class="search-container" style="margin-bottom: 30px;">
                    <form id="searchForm" class="search-form" style="display: flex; gap: 15px; align-items: center;">
                        <input type="text" id="searchTitle" name="searchTitle" placeholder="Search by title..." 
                               style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; flex: 1;">
                        <select id="searchCategory" name="searchCategory" 
                                style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; min-width: 150px;">
                            <option value="">All Categories</option>
                            <?php
                            $categories = array_unique(array_column($articles, 'categorie'));
                            foreach($categories as $category):
                            ?>
                                <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="sort" style="font-weight: bold; margin-left: 10px;">Sort by:</label>
                        <select name="sort" id="sort" onchange="this.form.submit()" style="padding: 7px 18px 7px 10px; border-radius: 8px; border: 1px solid #bfc9d9; font-size: 16px; background: #f6f8fa url('data:image/svg+xml;utf8,<svg fill='gray' height='20' viewBox='0 0 20 20' width='20' xmlns='http://www.w3.org/2000/svg/'><path d='M7.293 7.293a1 1 0 011.414 0L10 8.586l1.293-1.293a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 010-1.414z'/></svg>') no-repeat right 10px center/18px 18px;">
                            <option value="featured" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'featured') echo 'selected'; ?>>Featured</option>
                            <option value="name_az" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name_az') echo 'selected'; ?>>Title: A-Z</option>
                            <option value="name_za" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name_za') echo 'selected'; ?>>Title: Z-A</option>
                            <option value="date_newest" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'date_newest') echo 'selected'; ?>>Date: Newest</option>
                            <option value="date_oldest" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'date_oldest') echo 'selected'; ?>>Date: Oldest</option>
                        </select>
                        <button type="submit" style="padding: 8px 20px; background: #1a237e; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            Search
                        </button>
                    </form>
                </div>
                <div class="articles-grid" id="articlesGrid">
                    <?php if ($articles): ?>
                        <?php foreach ($articles as $article): ?>
                            <article class="article-card">
                                <?php if (!empty($article['imageArticle'])): ?>
                                <div class="article-image">
                                    <img src="../../../uploads/<?php echo htmlspecialchars($article['imageArticle']); ?>" alt="Article image">
                                </div>
                                <?php endif; ?>
                                <div class="article-content">
                                    <h2 class="article-title"><?php echo htmlspecialchars($article['titre']); ?></h2>
                                    <div class="article-meta">
                                        <p class="article-category">
                                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($article['categorie']); ?>
                                        </p>
                                        <p class="article-date">
                                            <i class="fas fa-calendar-alt"></i> 
                                            <?php echo date('d/m/Y', strtotime($article['date_article'])); ?>
                                        </p>
                                        <div class="article-rating" style="margin-top:10px;">
                                            <span style="font-weight:bold;">Average Rating:</span>
                                            <div style="display:inline-flex;">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star" style="font-size:20px;color:<?php echo $i <= round($article['average_rating']) ? '#ffcc00' : '#ccc'; ?>;"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <span>(<?php echo $article['rating_count']; ?> ratings)</span>
                                        </div>
                                    </div>
                                    <p class="article-excerpt">
                                        <?php echo substr(html_entity_decode($article['contenu']), 0, 150) . '...'; ?>
                                    </p>
                                    <a href="article.php?id=<?php echo $article['auteur_id']; ?>" class="read-more">Lire la suite</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No articles found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/search.js"></script>
</body>
</html>

<header id="header-top" class="header-top">
<ul>
<li>
<div class="header-top-left">
<ul>
<li class="select-opt">
<a href="#"><span class="lnr lnr-magnifier"></span></a>
</li>
</ul>
</div>
</li>
<li class="head-responsive-right pull-right">
<div class="header-top-right">
<ul>
<li class="header-top-contact">
<a href="#">sign in</a>
</li>
<li class="header-top-contact">
<a href="#">register</a>
</li>
</ul>
</div>
</li>
</ul>
</header><!--/.header-top-->