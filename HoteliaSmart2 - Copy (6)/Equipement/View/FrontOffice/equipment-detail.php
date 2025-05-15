<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../../Controller/equipementController.php');

// Get equipment ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to store if no ID provided
    header('Location: store.php');
    exit;
}

$equipementId = $_GET['id'];
$equipementC = new equipementController();
$equipement = $equipementC->showEquipement($equipementId);

// Redirect if equipment doesn't exist
if (!$equipement) {
    header('Location: store.php');
    exit;
}

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    if (!in_array($equipementId, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $equipementId;
    }
    
    // Redirect to prevent form resubmission
    header('Location: equipment-detail.php?id=' . $equipementId . '&added=true');
    exit;
}

// Get related equipment (same type, exclude current)
$relatedEquipment = $equipementC->getRelatedEquipement($equipement['type'], $equipementId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!--font-family-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <title><?php echo $equipement['nom']; ?> - Hotelia Smart</title>
    <link rel="shortcut icon" type="image/icon" href="assets/HS.png"/>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bootsnav.css">
    <link rel="stylesheet" href="assets/css/stylefront3.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    
    <style>
        .equipment-detail-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        .back-arrow {
            color: #003087;
            font-size: 20px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        .back-arrow:hover {
            color: #004abc;
        }
        .equipment-img-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .equipment-img-container img {
            max-width: 100%;
            height: auto;
            max-height: 400px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .equipment-title {
            color: #003087;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .equipment-price {
            font-size: 24px;
            font-weight: 600;
            color: #003087;
            margin-bottom: 20px;
        }
        .equipment-type {
            background-color: #e9f0f8;
            color: #003087;
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .equipment-description {
            margin-bottom: 30px;
            line-height: 1.8;
            color: #444;
        }
        .equipment-specs {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .equipment-specs h4 {
            margin-bottom: 15px;
            color: #003087;
            font-weight: 500;
        }
        .specs-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .specs-list li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            display: flex;
        }
        .specs-list li:last-child {
            border-bottom: none;
        }
        .spec-name {
            font-weight: 500;
            flex: 1;
            color: #666;
        }
        .add-to-cart-btn {
            background-color: #003087;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .add-to-cart-btn:hover {
            background-color: #004abc;
            color: white;
        }
        .success-alert {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .related-title {
            margin: 60px 0 30px;
            color: #003087;
            text-align: center;
            font-weight: 600;
        }
        .related-equipment {
            margin-bottom: 40px;
        }
        .related-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .related-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .related-img {
            height: 180px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            background: #f8f9fa;
        }
        .related-img img {
            max-width: 100%;
            max-height: 160px;
            object-fit: contain;
        }
        .related-info {
            padding: 15px;
        }
        .related-info h4 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #333;
        }
        .related-info p {
            margin: 0;
            color: #003087;
            font-weight: 600;
        }
        .related-link {
            color: inherit;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <section class="equipment-detail-section">
        <div class="container">
            <a href="store.php" class="back-arrow"><i class="fas fa-arrow-left"></i> Back to Store</a>
            
            <?php if (isset($_GET['added']) && $_GET['added'] == 'true'): ?>
            <div class="success-alert">
                <i class="fas fa-check-circle"></i> <?php echo $equipement['nom']; ?> has been added to your cart.
                <a href="cart.php" style="color: #155724; text-decoration: underline;">View Cart</a>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-5">
                    <div class="equipment-img-container">
                        <?php if(!empty($equipement['image'])): ?>
                            <img src="assets/images/equipements/<?php echo $equipement['image']; ?>" alt="<?php echo $equipement['nom']; ?>">
                        <?php else: ?>
                            <i class="fas fa-box fa-8x" style="color: #003087;"></i>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-7">
                    <h1 class="equipment-title"><?php echo $equipement['nom']; ?></h1>
                    <div class="equipment-price"><?php echo $equipement['prix']; ?> TND</div>

                    <!-- Rating Section -->
                    <div id="rating-section" style="margin-bottom: 30px; background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                        <h4 style="color: #003087; margin-bottom: 15px; font-weight: 500;">Rate this Product</h4>
                        <div id="star-rating" style="font-size: 2em; color: #FFD700; cursor: pointer; margin-bottom: 15px;"></div>
                        <div class="rating-form" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                            <input type="email" id="rating-email" placeholder="Enter your email to rate" style="flex: 1; min-width: 200px; padding: 10px 15px; border: 1px solid #ced4da; border-radius: 5px;" required>
                            <button id="submit-rating" type="button" style="background-color: #003087; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: 500; transition: background-color 0.3s ease;">Submit Rating</button>
                        </div>
                        <div id="rating-message" style="color: #dc3545; margin-top: 10px; font-size: 0.9rem;"></div>
                        <div id="rating-debug" style="color: #333; background: #f8f9fa; padding: 8px; margin-top: 8px; font-size: 0.85em; border-radius: 5px; display: none;"></div>
                        <div id="average-rating" style="margin-top: 15px; color: #666; font-weight: 500;"></div>
                    </div>
                    <!-- End Rating Section -->
                    <div class="equipment-type"><?php echo $equipement['type']; ?></div>
                    
                    <p class="equipment-description">
                        <?php 
                        // If description exists, display it, otherwise show a default message
                        if (isset($equipement['description']) && !empty($equipement['description'])) {
                            echo $equipement['description'];
                        } else {
                            echo "This " . $equipement['type'] . " is designed to enhance your hotel experience.";
                        }
                        ?>
                    </p>
                    
                    <div class="equipment-specs">
                        <h4>Specifications</h4>
                        <ul class="specs-list">
                            <li>
                                <span class="spec-name">Reference:</span>
                                <span><?php echo $equipement['reference']; ?></span>
                            </li>
                            <li>
                                <span class="spec-name">Type:</span>
                                <span><?php echo $equipement['type']; ?></span>
                            </li>
                            <?php if (isset($equipement['marque']) && !empty($equipement['marque'])): ?>
                            <li>
                                <span class="spec-name">Brand:</span>
                                <span><?php echo $equipement['marque']; ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (isset($equipement['date']) && !empty($equipement['date'])): ?>
                            <li>
                                <span class="spec-name">Date Added:</span>
                                <span><?php echo date('F j, Y', strtotime($equipement['date'])); ?></span>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <form action="equipment-detail.php?id=<?php echo $equipementId; ?>" method="post">
                        <input type="hidden" name="add_to_cart" value="1">
                        <button type="submit" class="add-to-cart-btn">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </form>
                </div>
            </div>
            
            <?php if (count($relatedEquipment) > 0): ?>
            <h2 class="related-title">Related Equipment</h2>
            <div class="row related-equipment">
                <?php foreach ($relatedEquipment as $related): ?>
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="equipment-detail.php?id=<?php echo $related['reference']; ?>" class="related-link">
                        <div class="related-card">
                            <div class="related-img">
                                <?php if(!empty($related['image'])): ?>
                                    <img src="assets/images/equipements/<?php echo $related['image']; ?>" alt="<?php echo $related['nom']; ?>">
                                <?php else: ?>
                                    <i class="fas fa-box fa-3x" style="color: #003087;"></i>
                                <?php endif; ?>
                            </div>
                            <div class="related-info">
                                <h4><?php echo $related['nom']; ?></h4>
                                <p><?php echo $related['prix']; ?> TND</p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="assets/js/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/bootsnav.js"></script>
    <script src="assets/js/custom.js"></script>
    <!-- Bootstrap Modal for Success Popup -->
    <div class="modal fade" id="ratingSuccessModal" tabindex="-1" aria-labelledby="ratingSuccessModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,48,135,0.2);">
          <div class="modal-header" style="background-color: #003087; color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 20px 25px;">
            <h5 class="modal-title" id="ratingSuccessModalLabel" style="font-weight: 600; font-size: 1.3rem; margin: 0;">Thank You!</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-color: white; opacity: 0.8;"></button>
          </div>
          <div class="modal-body" style="padding: 25px; text-align: center;">
            <div style="margin-bottom: 15px; font-size: 3rem; color: #28a745;">
              <i class="fas fa-check-circle"></i>
            </div>
            <p style="font-size: 1.1rem; margin-bottom: 5px; color: #333;">Your rating has been submitted successfully!</p>
            <p style="color: #666; margin-bottom: 20px;">Thank you for sharing your feedback.</p>
            <div style="margin-top: 10px;">
              <button type="button" class="btn btn-primary" data-bs-dismiss="modal" style="background-color: #003087; border: none; padding: 10px 25px; border-radius: 5px; font-weight: 500; transition: all 0.3s ease;">Continue Shopping</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
    // --- Star Rating UI ---
    const maxStars = 5;
    let selectedRating = 0;
    function renderStars(rating) {
        let html = '';
        for (let i = 1; i <= maxStars; i++) {
            html += `<span class="star${i <= rating ? ' filled' : ''}" data-value="${i}">&#9733;</span>`;
        }
        document.getElementById('star-rating').innerHTML = html;
    }
    renderStars(0);
    document.getElementById('star-rating').addEventListener('click', function(e) {
        if (e.target.classList.contains('star')) {
            selectedRating = parseInt(e.target.getAttribute('data-value'));
            renderStars(selectedRating);
        }
    });
    document.getElementById('submit-rating').addEventListener('click', function() {
        const email = document.getElementById('rating-email').value;
        const reference = '<?php echo $equipement["reference"]; ?>';
        if (!email || selectedRating === 0) {
            document.getElementById('rating-message').textContent = 'Please provide your email and select a rating.';
            return;
        }
        fetch('rate_equipment.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `email=${encodeURIComponent(email)}&reference=${encodeURIComponent(reference)}&rating=${selectedRating}`
        })
        .then(response => response.json())
        .then(data => {
            // Only show debug info in development mode
            const isDevMode = false; // Set to true for development, false for production
            if (isDevMode) {
                document.getElementById('rating-debug').style.display = 'block';
                document.getElementById('rating-debug').textContent = JSON.stringify(data);
            }
            if (data.success) {
                // Show Bootstrap modal or fallback alert
                try {
                  let modal = new bootstrap.Modal(document.getElementById('ratingSuccessModal'));
                  modal.show();
                } catch (e) {
                  alert('Your review has been submitted.');
                }
                // Clear input and stars
                document.getElementById('rating-email').value = '';
                selectedRating = 0;
                renderStars(0);
                document.getElementById('rating-message').textContent = '';
                // Refresh average rating
                fetch('get_average_rating.php?reference=<?php echo $equipement["reference"]; ?>')
                  .then(response => response.json())
                  .then(data => {
                    if (data && typeof data.avg === 'number') {
                      let avg = data.avg.toFixed(2);
                      let stars = '';
                      for (let i = 1; i <= maxStars; i++) {
                        stars += `<span style='color:${i <= Math.round(avg) ? '#FFD700' : '#ccc'};'>&#9733;</span>`;
                      }
                      document.getElementById('average-rating').innerHTML = `Average Rating: ${stars} (${avg})`;
                    }
                  });
            } else {
                document.getElementById('rating-message').textContent = data.message;
            }
        });
    });
    // --- Show average rating (AJAX fetch) ---
    fetch('get_average_rating.php?reference=<?php echo $equipement["reference"]; ?>')
      .then(response => response.json())
      .then(data => {
        if (data && typeof data.avg === 'number') {
          let avg = data.avg.toFixed(2);
          let stars = '';
          for (let i = 1; i <= maxStars; i++) {
            stars += `<span style='color:${i <= Math.round(avg) ? '#FFD700' : '#ccc'};'>&#9733;</span>`;
          }
          document.getElementById('average-rating').innerHTML = `Average Rating: ${stars} (${avg})`;
        }
      });
    </script>
    <style>
    #star-rating .star {
        font-size: 2em;
        color: #ccc;
        cursor: pointer;
        transition: color 0.2s;
    }
    #star-rating .star.filled {
        color: #FFD700;
    }
    </style>
</body>
</html>

