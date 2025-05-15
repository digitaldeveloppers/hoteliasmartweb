<?php
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../../Controller/ratingController.php');

$reference = isset($_GET['reference']) ? $_GET['reference'] : null;
$ratingController = new ratingController();

if ($reference) {
    $ratings = $ratingController->getRatingsByReference($reference);
    $averageRating = $ratingController->getAverageRating($reference);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Ratings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="stylesback1.css">
    <style>
        .rating-card {
            background: #fff;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .average-rating {
            font-size: 24px;
            color: #003087;
            margin-bottom: 20px;
        }
        .star-rating {
            color: #ffd700;
        }
        .rating-date {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="admin-nav-container">
            <div class="admin-logo">
                <img src="../FrontOffice/assets/HS.png" alt="Hotelia Smart Logo">
                <span class="hotelia">HOTELIA</span>
                <span class="smart">SMART</span>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="addEqui.php"><i class="fas fa-plus-circle"></i> New Equipement</a></li>
                    <li><a href="showEqui.php"><i class="fas fa-list"></i> Equipements</a></li>
                    <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistics</a></li>
                    <li><a href="commandes.php"><i class="fa-solid fa-cart-shopping"></i>Orders</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-title">
        <h1><i class="fas fa-star"></i> Equipment Ratings</h1>
    </div>

    <section class="form-section">
        <div class="form-container" style="max-width: 800px;">
            <?php if ($reference && !empty($ratings)): ?>
                <div class="average-rating">
                    <h2>Average Rating: 
                        <span class="star-rating">
                            <?php
                            $avgRating = number_format($averageRating, 1);
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $avgRating) {
                                    echo '<i class="fas fa-star"></i>';
                                } elseif ($i - 0.5 <= $avgRating) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            echo " ($avgRating)";
                            ?>
                        </span>
                    </h2>
                </div>

                <?php foreach ($ratings as $rating): ?>
                    <div class="rating-card">
                        <div class="star-rating">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $rating->getRating() ? 
                                    '<i class="fas fa-star"></i>' : 
                                    '<i class="far fa-star"></i>';
                            }
                            ?>
                        </div>
                        <div class="rating-date">
                            Rated on: <?php echo date('F j, Y', strtotime($rating->getCreatedAt())); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No ratings found for this equipment.</p>
            <?php endif; ?>
            
            <div style="margin-top: 20px;">
                <a href="showEqui.php" class="submit-button" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Equipment List
                </a>
            </div>
        </div>
    </section>
</body>
</html>