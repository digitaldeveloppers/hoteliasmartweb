<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../Controller/equipementController.php');

$equipementC = new equipementController();
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'featured';
$listeEquipements = $equipementC->listEquipement($sort);
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
    
    <!-- title of site -->
    <title>Hotelia Smart</title>

    <!-- For favicon png -->
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
    
    <!--store.css-->
    <link rel="stylesheet" href="assets/css/store.css">
    <style>
        .col-md-4 {
            padding: 20px;
        }
        .equipment-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        .section-header {
            margin-bottom: 50px;
            position: relative;
        }
        .cart-icon {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 24px;
            color: #003087;
            transition: color 0.3s ease;
        }
        .cart-icon:hover {
            color: #004abc;
        }
        .section-header h2 { color: #003087; font-size: 36px; margin-bottom: 15px; }
        .section-header p { color: #666; font-size: 16px; }
        .equipment-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 25px;
            height: 100%;
            display: flex;
            flex-direction: column;
            margin-bottom: 30px;
            transition: background-color 0.3s ease;
        }
        .equipment-card:hover { 
            background-color: #f8f9fa;
        }
        .equipment-img {
            text-align: center;
            padding: 20px;
            color: #003087;
            height: 300px;
            width: 300px;
        }
        .equipment-info { padding: 20px; text-align: center; }
        .equipment-info h3 { color: #333; font-size: 20px; margin-bottom: 10px; }
        .equipment-type { color: #666; font-size: 14px; margin-bottom: 15px; }
        .equipment-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        .price { color: #003087; font-weight: bold; font-size: 18px; }
        .stock { color: #666; font-size: 14px; }
        
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .btn-view {
            background-color: #003087;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-view:hover {
            background-color: #004abc;
        }
    </style>

</head>

<body>
    <!--header-top start -->
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
		<!--header-top end -->


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
			                    <li ><a href="home.php">home</a></li>
			                    <li ><a href="./store.php">equipement</a></li>
			                    <li ><a href="#">explore</a></li>
			                    <li ><a href="#">review</a></li>
			                    <li ><a href="#">blog</a></li>
								<li ><a href="#">forum</a></li>
			                    <li ><a href="#">about us</a></li>
			                    <li ><a href="help-center.php">help center</a></li>
			                </ul><!--/.nav -->
			            </div><!-- /.navbar-collapse -->
			        </div><!--/.container-->
			    </nav><!--/nav-->
			    <!-- End Navigation -->
			</div><!--/.header-area-->
		    <div class="clearfix"></div>

		</section><!-- /.top-area-->
		<!-- top-area End -->

        <!-- Equipment Section Start -->
        <section class="equipment-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-header text-center">
                            <a href="cart.php" class="cart-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </a>
                            <h2>Our Equipments</h2>
                            <p>Discover our high-quality smart equipments</p>
                        </div>
                    </div>
                </div>
                <!-- Search and Sort Row -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 15px; flex-wrap: wrap;">
                    <div class="search-container" style="flex: 1; min-width: 220px; position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #666;"></i>
                        <input type="text" id="searchInput" placeholder="Search equipment..." style="width: 250px; padding: 10px 35px; border: 1px solid #ddd; border-radius: 4px; height: 40px; border-color: #003087;">
                        <i class="fas fa-times" id="clearSearch" style="position: absolute; right: 40px; top: 50%; transform: translateY(-50%); color: #666; cursor: pointer; display: none;"></i>
                        <i class="fas fa-microphone" id="voiceSearch" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #666; cursor: pointer;"></i>
                    </div>
                    <form method="get" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                        <label for="sort" style="font-weight: bold;">Sort by:</label>
                        <select name="sort" id="sort" onchange="this.form.submit()" style="padding: 7px 18px 7px 10px; border-radius: 8px; border: 1px solid #bfc9d9; font-size: 16px; background: #f6f8fa url('data:image/svg+xml;utf8,<svg fill=\'gray\' height=\'20\' viewBox=\'0 0 20 20\' width=\'20\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7.293 7.293a1 1 0 011.414 0L10 8.586l1.293-1.293a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 010-1.414z\'/></svg>') no-repeat right 10px center/18px 18px;">
                            <option value="featured" <?php if ($sort == 'featured') echo 'selected'; ?>>Featured</option>
                            <option value="name_az" <?php if ($sort == 'name_az') echo 'selected'; ?>>Name: A-Z</option>
                            <option value="name_za" <?php if ($sort == 'name_za') echo 'selected'; ?>>Name: Z-A</option>
                            <option value="price_asc" <?php if ($sort == 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
                            <option value="price_desc" <?php if ($sort == 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
                            <option value="date_newest" <?php if ($sort == 'date_newest') echo 'selected'; ?>>Newest</option>
                            <option value="date_oldest" <?php if ($sort == 'date_oldest') echo 'selected'; ?>>Oldest</option>
                        </select>
                    </form>
                </div>
                
                <!-- Dynamic Search Bar End -->
                <div class="row" id="equipmentRow">
                    <?php foreach ($listeEquipements as $equipement) { ?>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="equipment-card">
                            <div class="equipment-img text-center">
                                <a href="equipment-detail.php?id=<?php echo $equipement['reference']; ?>">
                                    <?php if(!empty($equipement['image'])): ?>
                                        <img src="assets/images/equipements/<?php echo $equipement['image']; ?>" alt="<?php echo $equipement['nom']; ?>" style="width: 200px; height: 200px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <i class="fas fa-box fa-4x"></i>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="equipment-info">
                                <h3><a href="equipment-detail.php?id=<?php echo $equipement['reference']; ?>" style="text-decoration: none; color: inherit;"><?php echo $equipement['nom']; ?></a></h3>
                                <p class="equipment-type"><?php echo $equipement['type']; ?></p>
                                <div class="equipment-details">
                                    <span class="price"><?php echo $equipement['prix']; ?> TND</span>
                                    <span class="stock">Stock: <?php echo $equipement['quantite']; ?></span>
                                </div>
                                <div class="button-group">
                                    <button onclick="addToCart(<?php echo $equipement['reference']; ?>)" class="btn-view">Add to cart</button>
                                    <a href="equipment-detail.php?id=<?php echo $equipement['reference']; ?>" class="btn-view" style="display: inline-block; text-decoration: none;">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </section>
        <!-- Equipment Section End -->

<!-- Include all js compiled plugins (below), or include individual files as needed -->

		<script src="assets/js/jquery.js"></script>
        
        <!--modernizr.min.js-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
		
		<!--bootstrap.min.js-->
        <script src="assets/js/bootstrap.min.js"></script>
		
		<!-- bootsnav js -->
		<script src="assets/js/bootsnav.js"></script>

        <!--feather.min.js-->
        <script  src="assets/js/feather.min.js"></script>

        <!-- counter js -->
		<script src="assets/js/jquery.counterup.min.js"></script>
		<script src="assets/js/waypoints.min.js"></script>

        <!--slick.min.js-->
        <script src="assets/js/slick.min.js"></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
		     
        <!--Custom JS-->
        <script src="assets/js/custom.js"></script>

        <script src="gemini-chatbot.js"></script>
        <script>
        function addToCart(equipId) {
            $.ajax({
                url: 'cart.php',
                type: 'POST',
                data: {
                    action: 'add',
                    equipId: equipId
                },
                success: function(response) {
                    if (response.success) {
                        alert('Equipment added to cart!');
                    } else {
                        alert(response.message || 'Failed to add equipment to cart');
                    }
                },
                error: function() {
                    alert('Error occurred while adding to cart');
                }
            });
        }
        </script>
        <script>
            // Dynamic Search and Voice Search for Equipment Cards
            const searchInput = document.getElementById('searchInput');
            const clearSearch = document.getElementById('clearSearch');
            const voiceSearch = document.getElementById('voiceSearch');
            const equipmentRow = document.getElementById('equipmentRow');
            const equipmentCards = equipmentRow.querySelectorAll('.col-md-4, .col-sm-6');

            // Function to filter equipment cards
            function filterEquipments() {
                const query = searchInput.value.trim().toLowerCase();
                equipmentCards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    if (text.includes(query)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            // Search input event
            searchInput.addEventListener('input', filterEquipments);

            // Clear search event
            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                filterEquipments();
                searchInput.focus();
            });

            // Voice search functionality
            if ('webkitSpeechRecognition' in window) {
                const recognition = new webkitSpeechRecognition();
                recognition.continuous = false;
                recognition.interimResults = false;
                recognition.lang = 'en-US';

                recognition.onresult = function(event) {
                    const result = event.results[0][0].transcript;
                    searchInput.value = result;
                    searchInput.dispatchEvent(new Event('input'));
                    voiceSearch.style.color = '#003087';
                };

                recognition.onerror = function(event) {
                    console.error('Speech recognition error:', event.error);
                    voiceSearch.style.color = '#003087';
                };

                voiceSearch.addEventListener('click', function() {
                    voiceSearch.style.color = '#e74c3c'; // Indicate listening
                    recognition.start();
                });
            } else {
                voiceSearch.style.display = 'none'; // Hide if not supported
            }
        </script>
    </body>
	
</html>