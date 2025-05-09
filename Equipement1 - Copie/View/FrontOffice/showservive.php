<?php
include_once '../../config.php';
include_once '../../Controller/servivecontrolle.php';


$id_user=8;
$pdo = config::getConnexion();
$services = afficherServicesbyid($pdo,$id_user);
?>

<?php
$pageTitle = 'Services List';
ob_start();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotelia Smart</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .type-button {
            transition: all 0.3s ease;
        }
        .type-button.active {
            background-color: #003087;
            color: white;
            transform: scale(1.05);
        }
        .property-image img {
            transition: opacity 0.3s ease;
        }
        table {
    width: 98%;
    margin: 30px auto;
    border-collapse: collapse;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    background-color: white;
}
th, td {
    padding: 12px 15px;
    text-align: center;
}
th {
    background-color: #4CAF50;
    color: white;
    font-size: 18px;
}
tr:nth-child(even) {
    background-color: #f2f2f2;
}
tr:hover {
    background-color: #ddd;
}
td {
    font-size: 20 px;
}
a.action-btn {
    padding: 6px 12px;
    border-radius: 4px;
    text-decoration: none;
    color: white;
    font-size: 14px;
    margin: 0 5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
a.edit-btn {
    background-color: #2196F3;
}
a.delete-btn {
    background-color: #f44336;
}

a.export-btn {
    background-color: #FF9800;
}

a.action-btn:hover {
    opacity: 0.8;
}
p {
    text-align: center;
    font-size: 18px;
    color: #333;
    margin-top: 50px;
}

        /* Star Rating Styles */
        .rating-container {
            margin: 20px 0;
            text-align: center;
        }

        .rating-container label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 500;
        }

        .rating {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .rating .star {
            cursor: pointer;
            font-size: 24px;
            color: #ddd;
            transition: all 0.3s ease;
        }

        .rating .star:hover,
        .rating .star.active {
            color: #ffd700;
            transform: scale(1.2);
        }

        .rating:hover .star {
            color: #ddd;
        }

        .rating:hover .star:hover,
        .rating:hover .star:hover ~ .star {
            color: #ffd700;
        }
        
        /* Add visual feedback on hover */
        .rating:hover {
            transform: translateY(-2px);
        }
        
        /* Add touch support for mobile devices */
        @media (hover: none) {
            .rating .star {
                font-size: 28px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="nav-container">
            <div class="logo">
                <img src="assets/HS.png" >
                <span class="hotelia">HOTELIA</span>
                <span class="smart">SMART</span>
            </div>
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="addEquipement.php">Explore</a></li>
                    <li><a href="#property-details">Equipements</a></li>
                    <li><a href="#contact">Historique</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <body>
    <!-- Decorative eco elements -->
    <i class="eco-decoration leaf-1 fas fa-leaf"></i>
    <i class="eco-decoration leaf-2 fas fa-leaf"></i>
    <i class="eco-decoration water-drop fas fa-tint"></i>

    <div class="header">
        <h1>Green Energy & Sustainability Services</h1>
        <p>Comprehensive eco-friendly solutions for businesses committed to environmental responsibility and energy efficiency</p>
    </div>
    
    <div class="blur-overlay"></div>
    <div class="services-grid">
        <!-- Green Energy Solutions -->
        <div class="category energy">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-solar-panel"></i>
                </div>
                <h2 class="category-title">Green Energy Solutions</h2>
            </div>
            
            <div class="service-card" id="serviceCard">
                <div class="service-badge">Popular</div>
                <h3 class="service-title">Solar Panel Installation</h3>
                <p class="service-description">Power buildings using clean, renewable energy with our high-efficiency solar systems and professional installation.</p>
                <button class="service-cta">Learn more <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>
        <div class="contact-form" id="contactForm" style="display: none;">
            <h2>Green Energy Solutions</h2>
            <form action="addserv.php" method="POST" id="contactForm">
                <div class="form-group">
                    <label for="name">Title:</label>
                    <input type="text" id="name" name="name"  value="Solar Panel Installation" readonly>
                </div>
                <div class="form-group">
                    <label for="solarNumber">Number of Solar Panels:</label>
                    <input type="text" id="solarNumber" name="solarNumber" required>
                    <div id="erreur" style="color:red;"></div>
                </div>
                <div class="form-group">
                    <label for="price">Price Total:</label>
                    <input type="number" id="price" name="price" required readonly>
                </div>
                <input type="hidden" name="service" value="1">
                <input type="hidden" name="type" value="Power buildings using clean, renewable energy with our high-efficiency solar systems and professional installation">

                <div class="rating-container">
                    <label>Votre évaluation :</label>
                    <div class="rating" data-service-id="1">
                        <i class="star fas fa-star" data-rating="1"></i>
                        <i class="star fas fa-star" data-rating="2"></i>
                        <i class="star fas fa-star" data-rating="3"></i>
                        <i class="star fas fa-star" data-rating="4"></i>
                        <i class="star fas fa-star" data-rating="5"></i>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-send">Send</button>
                    <button type="button" class="btn btn-close" id="closeFormBtn">Close</button>
                </div>
            </form>
        </div>
        <div id="blurOverlay" class="blurred-background"></div>

        <!-- Water Conservation Services -->
        <div class="category water">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-tint"></i>
                </div>
                <h2 class="category-title">Water Conservation Services</h2>
            </div>
            
            <div class="service-card" id="serviceCard_water">
                <h3 class="service-title">Low-Flow Fixtures</h3>
                <p class="service-description">Install eco-friendly taps, showerheads, and toilets that reduce water usage without sacrificing performance.</p>
                <button class="service-cta">Learn more <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>
        <div class="contact-form" id="contactForm_water" style="display: none;">
            <h2>Water Conservation Services</h2>
            <form action="addserv.php" method="POST" id="contactForm">
                <div class="form-group">
                    <label for="name">Title:</label>
                    <input type="text" id="name" name="name" value="Water Conservation Services" readonly>
                </div>
                <div class="form-group">
                    <label for="solarNumber">Number of taps:</label>
                    <div class="inputs-wrapper">
                        <input type="text" id="taps" name="taps" required placeholder="Number of taps">
                        <input type="text" id="showerheads" name="showerheads" required placeholder="Number of showerheads">
                        <input type="text" id="toilets" name="toilets" required placeholder="Number of toilets">
                    </div>
                    <div id="erreur_water" style="color:red;"></div>
                </div>
                <div class="form-group">
                    <label for="price">Price Total:</label>
                    <input type="number" id="price_water" name="price" required placeholder="Enter total price">
                </div>
                <input type="hidden" name="service" value="2">
                <input type="hidden" name="type" value="Install eco-friendly taps, showerheads, and toilets that reduce water usage without sacrificing performance">

                <div class="rating-container">
                    <label>Votre évaluation :</label>
                    <div class="rating" data-service-id="2">
                        <i class="star fas fa-star" data-rating="1"></i>
                        <i class="star fas fa-star" data-rating="2"></i>
                        <i class="star fas fa-star" data-rating="3"></i>
                        <i class="star fas fa-star" data-rating="4"></i>
                        <i class="star fas fa-star" data-rating="5"></i>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-send" id="submitBtn">Send</button>
                    <button type="button" class="btn btn-close" id="closeFormBtn_water">Close</button>
                </div>
            </form>
        </div>
        <div id="blurOverlay_water" class="blurred-background"></div>



        <!-- Eco-Friendly Cleaning -->
        <div class="category cleaning">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-broom"></i>
                </div>
                <h2 class="category-title">Eco-Friendly Cleaning</h2>
            </div>
            
            <div class="service-card" id="serviceCard_cleaning"> 
                <h3 class="service-title">Green Cleaning Products</h3>
                <p class="service-description">Use biodegradable and non-toxic products that are safe for both people and the environment.</p>
                <button class="service-cta">Learn more <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>
        <div class="contact-form" id="contactForm_cleaning" style="display: none;">
            <h2>Eco-Friendly Cleaning</h2>
            <form id="miniForm" action="addserv.php" method="POST">
                <div class="form-group">
                    <label for="name">Title:</label>
                    <input type="text" id="name" name="name" value="Green Cleaning Products" readonly>
                </div>
                <div class="form-group">
                    <label for="eco_soap">Eco Soap (liters):</label>
                    <input type="text" name="eco_soap" id="eco_soap" required placeholder="e.g. 5">
                </div>

                <div class="form-group">
                    <label for="disinfectant">Disinfectant (liters):</label>
                    <input type="tex" name="disinfectant" id="disinfectant" required placeholder="e.g. 3">
                </div>

                <div class="form-group">
                    <label for="total_price">Total Price (TND):</label>
                    <input type="text" name="price" id="total_price" readonly>
                    <div id="errorCleaning" style="color:red; display:none;"></div>
                </div>

                <input type="hidden" name="service" value="3">
                <input type="hidden" name="type" value="Use biodegradable and non-toxic products that are safe for both people and the environment">

                <div class="rating-container">
                    <label>Votre évaluation :</label>
                    <div class="rating" data-service-id="3">
                        <i class="star fas fa-star" data-rating="1"></i>
                        <i class="star fas fa-star" data-rating="2"></i>
                        <i class="star fas fa-star" data-rating="3"></i>
                        <i class="star fas fa-star" data-rating="4"></i>
                        <i class="star fas fa-star" data-rating="5"></i>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-send" id="submitBtn">Send</button>
                    <button type="button" class="btn btn-close" id="closeCleaningFormBtn">Close</button>
                </div>

            </form>
        </div>
        <div id="blurOverlay_cleaning" class="blurred-background"></div>




        <!-- Smart Waste Management -->
        <div class="category waste">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-recycle"></i>
                </div>
                <h2 class="category-title">Smart Waste Management</h2>
            </div>
            
            <div class="service-card" id="serviceCard_waste">
                <h3 class="service-title">Recycling Programs</h3>
                <p class="service-description">Educate staff and provide complete systems for sorting and recycling waste materials.</p>
                <button class="service-cta">Learn more <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>
        <div class="contact-form" id="contactForm_waste" style="display: none;">
            <h2>Smart Waste Management</h2>
            <form id="form_waste" action="addserv.php" method="POST">
                <div class="form-group">
                    <label for="name">Title:</label>
                    <input type="text" id="name" name="name" value="Smart Waste Management" readonly>
                </div>
                <div class="form-group">
                    <label for="personne">Nombre de personne:</label>
                    <input type="text" name="personne" id="personne" required placeholder="nombre de personne">
                </div>
                <div id="erreurwaste" style="color:red; display:none;"></div>

                <div class="form-group">
                    <label for="price">prix totale:</label>
                    <input type="tex" name="price" id="price_waste" required placeholder="prix totale" readonly>
                </div>
                <input type="hidden" name="service" value="4">
                <input type="hidden" name="type" value="Educate staff and provide complete systems for sorting and recycling waste materials">

                <div class="rating-container">
                    <label>Votre évaluation :</label>
                    <div class="rating" data-service-id="4">
                        <i class="star fas fa-star" data-rating="1"></i>
                        <i class="star fas fa-star" data-rating="2"></i>
                        <i class="star fas fa-star" data-rating="3"></i>
                        <i class="star fas fa-star" data-rating="4"></i>
                        <i class="star fas fa-star" data-rating="5"></i>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-send" id="submitBtn">Send</button>
                    <button type="button" class="btn btn-close" id="closewasteFormBtn">Close</button>
                </div>

            </form>
        </div>
        <div id="blurOverlay_waste" class="blurred-background"></div>
        <!-- Smart Technology Integration -->
        <div class="category tech">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-microchip"></i>
                </div>
                <h2 class="category-title">Smart Technology Integration</h2>
            </div>
            
            <div class="service-card" id="serviceCard_tech">
                <h3 class="service-title">Room Automation</h3>
                <p class="service-description">Guests control lights, AC, and curtains via app or voice commands for optimal energy use.</p>
                <button class="service-cta">Learn more <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>
        <div class="contact-form" id="contactForm_tech" style="display: none;">
            <h2>Smart Waste Management</h2>
            <form id="form_tech" action="addserv.php" method="POST">
                <div class="form-group">
                    <label for="name">Title:</label>
                    <input type="text" id="name" name="name" value="Smart Technology Integration" readonly>
                </div>
                <div class="form-group">
                    <label for="personne">Nombre des chambres:</label>
                    <input type="text" name="chambres" id="chambres" required placeholder="nombre de chambres">
                </div>
                <div id="erreurtech" style="color:red;"></div>

                <div class="form-group">
                    <label for="price">prix totale:</label>
                    <input type="text" name="price" id="price_tech" required placeholder="prix totale" readonly>
                </div>
                <input type="hidden" name="service" value="5">
                <input type="hidden" name="type" value="Guests control lights, AC, and curtains via app or voice commands for optimal energy use">

                <div class="rating-container">
                    <label>Votre évaluation :</label>
                    <div class="rating" data-service-id="5">
                        <i class="star fas fa-star" data-rating="1"></i>
                        <i class="star fas fa-star" data-rating="2"></i>
                        <i class="star fas fa-star" data-rating="3"></i>
                        <i class="star fas fa-star" data-rating="4"></i>
                        <i class="star fas fa-star" data-rating="5"></i>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-send" id="submitBtn">Send</button>
                    <button type="button" class="btn btn-close" id="closetechFormBtn">Close</button>
                </div>

            </form>
        </div>
        <div id="blurOverlay_tech" class="blurred-background"></div>

        <!-- Green Spaces & Gardening -->
        <div class="category garden">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-spa"></i>
                </div>
                <h2 class="category-title">Green Spaces & Gardening</h2>
            </div>
            
            <div class="service-card" id="serviceCard_jard">
                <div class="service-badge">Trending</div>
                <h3 class="service-title">Vertical Gardens / Green Walls</h3>
                <p class="service-description">Beautify your spaces while improving air quality with living wall installations.</p>
                <button class="service-cta">Learn more <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>
        <div class="contact-form" id="contactForm_jard" style="display: none;">
            <h2>Green Spaces & Gardening</h2>
            <form id="form_jard" action="addserv.php" method="POST">
                <div class="form-group">
                    <label for="name">Title:</label>
                    <input type="text" id="name" name="name" value="Green Spaces & Gardening" readonly>
                </div>
                <div class="form-group">
                    <label for="personne">Nombre de jardins:</label>
                    <input type="text" name="jardin" id="jardin" required placeholder="nombre de chambres">
                </div>
                <div id="erreurjard" style="color:red;"></div>

                <div class="form-group">
                    <label for="price">prix totale:</label>
                    <input type="text" name="price" id="price_jardin" required placeholder="prix totale" readonly>
                </div>
                <input type="hidden" name="service" value="6">
                <input type="hidden" name="type" value="Beautify your spaces while improving air quality with living wall installations">

                <div class="rating-container">
                    <label>Votre évaluation :</label>
                    <div class="rating" data-service-id="6">
                        <i class="star fas fa-star" data-rating="1"></i>
                        <i class="star fas fa-star" data-rating="2"></i>
                        <i class="star fas fa-star" data-rating="3"></i>
                        <i class="star fas fa-star" data-rating="4"></i>
                        <i class="star fas fa-star" data-rating="5"></i>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-send" id="submitBtn">Send</button>
                    <button type="button" class="btn btn-close" id="closejardFormBtn">Close</button>
                </div>

            </form>
        </div>
        <div id="blurOverlay_jard" class="blurred-background"></div>
    </div>
    <div>
		<?php if ($services): ?>
			<table border='1' cellpadding='10' cellspacing='0'>
				<tr>
					<th>#</th>
					<th>Title</th>
					<th>Quantity</th>
					<th>Price</th>
					<th>Description</th>
					<th>Actions</th>
				</tr>

				<?php $count = 1; foreach ($services as $service): ?>
					<tr>
						<td><?= $count ?></td>
						<td><?= htmlspecialchars($service['title']) ?></td>
						<td><?= htmlspecialchars($service['quantity']) ?></td>
						<td><?= htmlspecialchars($service['price']) ?></td>
						<td><?= htmlspecialchars($service['description']) ?></td>
                        <?php 
                            if ($service['is_paid'] == 0) { ?>
                                <td>
                                    <a href="paiment.php?service_id=<?= $service['id'] ?>&amount=<?= $service['price'] ?>" class="payment-btn">
                                        Paiement
                                    </a>
                                </td>
                            <?php 
                            } else { ?>
                                <td>
                                    <span style="color: green;">Already Paid</span>
                                </td>
                            <?php 
                            }
                        ?>
					</tr>
				<?php $count++; endforeach; ?>
			</table>
		<?php else: ?>
			<p>Aucun service trouvé.</p>
		<?php endif; ?>


		</div>
    <footer class="footer">
        <div class="footer-container">
            <!-- Contact Information -->
            <div class="footer-column">
                <h3>Contact Us</h3>
                <p><i class="fas fa-map-marker-alt"></i> 123 Smart Street, Tech City</p>
                <p><i class="fas fa-phone"></i> +1 234 567 8900</p>
                <p><i class="fas fa-envelope"></i> contact@hoteliasmart.com</p>
            </div>

            <!-- Social Media Links -->
            <div class="footer-column">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>

            <!-- Newsletter Subscription -->
            <div class="footer-column">
                <h3>Newsletter</h3>
                <p>Subscribe to our newsletter for updates</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit" class="subscribe-btn">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Hotelia Smart. All rights reserved.</p>
        </div>
    </footer>

   <script>
        // You would connect this to your form system
        document.querySelectorAll('.service-cta').forEach(button => {
            button.addEventListener('click', function() {
                const card = this.closest('.service-card');
                const title = card.querySelector('.service-title').textContent;
                console.log(`Selected service: ${title}`);
                // Open your form modal here
            });
        });

    </script>

<script src="servive.js"></script>
</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratings = document.querySelectorAll('.rating');
    
    ratings.forEach(rating => {
        const stars = rating.querySelectorAll('.star');
        const serviceId = rating.dataset.serviceId;
        
        stars.forEach(star => {
            star.addEventListener('click', function(e) {
                e.stopPropagation(); // Empêche la propagation du clic
                const value = this.dataset.rating;
                
                // Réinitialiser toutes les étoiles
                stars.forEach(s => {
                    s.style.color = '#ddd';
                    s.classList.remove('active');
                });
                
                // Colorer les étoiles jusqu'à celle cliquée
                stars.forEach(s => {
                    if (s.dataset.rating <= value) {
                        s.style.color = '#ffd700';
                        s.classList.add('active');
                    }
                });
                
                // Ici vous pouvez ajouter le code pour sauvegarder la note
                alert('Note de ' + value + ' étoiles enregistrée !');
            });
            
            // Effet de survol
            star.addEventListener('mouseenter', function() {
                const value = this.dataset.rating;
                stars.forEach(s => {
                    if (s.dataset.rating <= value) {
                        s.style.color = '#ffd700';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
            
            // Réinitialiser au survol
            rating.addEventListener('mouseleave', function() {
                stars.forEach(s => {
                    if (s.classList.contains('active')) {
                        s.style.color = '#ffd700';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        });
    });
});
</script>