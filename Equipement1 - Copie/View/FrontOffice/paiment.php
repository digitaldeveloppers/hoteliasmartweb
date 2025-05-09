<?php
    if (isset($_GET['service_id'])) {
        $service_id = $_GET['service_id'];
    } else {
        // Optional: handle missing ID
        echo "Service ID not provided.";
    }
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tunify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="paiment.css">
</head>
<body>
    <!-- Sidebar -->
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


    
    <!-- Main Content -->
    <div class="main-content" style="margin:60px 0px 0px 0px;">
        <form action="addpayment.php" method="post" id="payment-form">
            <input type="text" name="id_service" value="<?php echo  $service_id; ?>">
            <h1 style="font-size:40px; text-align:center;color:rgb(50, 205, 50);">Paiment</h1>
            <div class="payment-container">
                <h2>Choisissez un mode de paiement</h2>

                <!-- Carte Bancaire -->
                <div class="payment-option">
                    <input type="radio" id="payment-method-card" name="payment-method" value="card" onchange="togglePaymentForms()">
                    <label for="payment-method-card"><i class="fas fa-credit-card"></i> Carte Bancaire</label>
                </div>
                <div class="payment-images" style="text-align: center;">
                    <img src="image/carte1.png" alt="carte" width="30">
                    <img src="image/carte2.png" alt="2" width="32">
                </div>
                <div class="form-container" id="card-form" style="display: none;">
                    <h3>Information Carte Bancaire</h3>
                    <div class="card-input">
                        <span class="card-icon"><i class="fas fa-credit-card"></i></span>
                        <input type="text" id="card-number" name="card-number" placeholder="0000 0000 0000 0000" maxlength="19">
                    </div>
                    <div id="card-number-error" style="color: red; display: none;"></div>
                    
                    <div class="form-group">
                        <label for="expiry-date">Date d'expiration (MM/AA):</label>
                        <input type="text" id="expiry-date" name="expiry-date" placeholder="MM/AA" maxlength="5">
                        <div id="expiry-date-error" style="color: red; display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="security-code">Code de sécurité (CVC):</label>
                        <input type="tel" id="security-code" name="security-code" placeholder="XXX" maxlength="3">
                        <div id="security-code-error" style="color: red; display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="card-type">Type de carte:</label>
                        <select id="card-type" name="card-type">
                            <option value="">-- Sélectionner --</option>
                            <option value="visa">Visa</option>
                            <option value="mastercard">MasterCard</option>
                            <option value="amex">American Express</option>
                        </select>
                        <div id="card-type-error" style="color: red; display: none;"></div>
                    </div>
                </div>

                <!-- Paiement Mobile -->
                <div class="payment-option">
                    <input type="radio" id="payment-method-mobile" name="payment-method" value="mobile" onchange="togglePaymentForms()">
                    <label for="payment-method-mobile"><i class="fas fa-mobile-alt"></i> Paiement Mobile</label>
                </div>
                <div class="payment-images" style="text-align: center;">
                    <img src="image/orange.png" alt="Orange" width="30">
                    <img src="image/tt.png" alt="TT" width="32">
                </div>
                <div class="form-container" id="mobile-form" style="display: none;">
                    <h3>Information Paiement Mobile</h3>
                    <div class="form-group">
                        <label for="phone-number">Numéro de téléphone:</label>
                        <input type="tel" id="phone-number" name="phone-number" placeholder="12 345 678" maxlength="8">
                        <div id="phone-number-error" style="color: red; display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mobile-provider">Fournisseur Mobile:</label>
                        <select id="mobile-provider" name="mobile-provider">
                            <option value="">-- Sélectionner --</option>
                            <option value="orange">Orange</option>
                            <option value="ooredoo">Ooredoo</option>
                            <option value="telecom">Tunisie Telecom</option>
                        </select>
                        <div id="mobile-provider-error" style="color: red; display: none;"></div>
                    </div>
                </div>

                <div id="payment-error" style="color: red; display: none; margin-top: 10px;"></div>

                <button id="submit-button" type="submit">Soumettre le paiement</button>
            </div>
        </form>
    </div>

    <footer id="footer"  class="footer">
			<div class="container">
				<div class="footer-menu">
		           	<div class="row">
			           	<div class="col-sm-3">
			           		 <div class="navbar-header">
								<a class="navbar-brand" href="home.php">Hotelia<span>Smart</span></a>
							</div><!--/.navbar-header-->
			           	</div>
			           	<div class="col-sm-9">
			           		<ul class="footer-menu-item">
							    <li class=" scroll active"><a href="home.php">home</a></li>
			                    <li class="scroll"><a href="store.php">store</a></li>
			                    <li class="scroll"><a href="explore.php">explore</a></li>
			                    <li class="scroll"><a href="reviews.php">review</a></li>
			                    <li class="scroll"><a href="blog.php">blog</a></li>
								<li class="scroll"><a href="forum.php">forum</a></li>
			                    <li class="scroll"><a href="Aboutus.php">about us</a></li>
			                </ul><!--/.nav -->
			           	</div>
		           </div>
				</div>
				<div class="hm-footer-copyright">
					<div class="row">
						<div class="col-sm-5">
							<p>
								&copy;copyright. designed and developed by <a href="https://www.themesine.com/">themesine</a>
							</p><!--/p-->
						</div>
						<div class="col-sm-7">
							<div class="footer-social">
								<span><i class="fa fa-phone"> +216 53 601 795</i></span>
								<a href="#"><i class="fa fa-facebook"></i></a>	
								<a href="#"><i class="fa fa-twitter"></i></a>
								<a href="#"><i class="fa fa-linkedin"></i></a>
								<a href="#"><i class="fa fa-google-plus"></i></a>
							</div>
						</div>
					</div>
					
				</div><!--/.hm-footer-copyright-->
			</div><!--/.container-->

			<div id="scroll-Top">
				<div class="return-to-top">
					<i class="fa fa-angle-up " id="scroll-top" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back to Top" aria-hidden="true"></i>
				</div>
				
			</div><!--/.scroll-Top-->
			
        </footer><!--/.footer-->
    <script>
    // Fonction pour basculer l'affichage des formulaires de paiement
    function togglePaymentForms() {
        console.log("togglePaymentForms called");
        const cardForm = document.getElementById("card-form");
        const mobileForm = document.getElementById("mobile-form");
        const cardRadio = document.getElementById("payment-method-card");
        const mobileRadio = document.getElementById("payment-method-mobile");

        if (cardRadio && mobileForm && cardForm && mobileRadio) {
            if (cardRadio.checked) {
                cardForm.style.display = "block";
                mobileForm.style.display = "none";
                console.log("Card form displayed");
            } else if (mobileRadio.checked) {
                cardForm.style.display = "none";
                mobileForm.style.display = "block";
                console.log("Mobile form displayed");
            }
        } else {
            console.error("Elements not found for toggling payment forms");
        }
    }
    
    </script>
    <script src="paiment.js"></script>
</body>
</html>