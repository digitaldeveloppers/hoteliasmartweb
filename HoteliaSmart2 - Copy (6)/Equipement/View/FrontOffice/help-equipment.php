<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../Controller/equipementController.php');
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
    <title>Hotelia Smart - Equipment Help</title>

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
			                    <li><a href="home.php">home</a></li>
			                    <li><a href="./store.php">equipement</a></li>
			                    <li><a href="#">explore</a></li>
			                    <li><a href="#">review</a></li>
			                    <li><a href="#">blog</a></li>
							<li><a href="#">forum</a></li>
			                    <li><a href="#">about us</a></li>
			                    <li><a href="help-center.php">help center</a></li>
			                </ul><!--/.nav -->
			            </div><!-- /.navbar-collapse -->
			        </div><!--/.container-->
			    </nav><!--/nav-->
			    <!-- End Navigation -->
			</div><!--/.header-area-->
			<div class="clearfix"></div>

		</section><!--/.top-area-->
		<!-- top-area End -->

		<main class="container">
			<h1>Equipment Help</h1>
			
			<!-- Equipment Guide Section -->
			<div class="help-section" style="margin-bottom: 40px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
				<h2><i class="fas fa-book" style="margin-right: 10px;"></i>Equipment Usage Guide</h2>
				<form action="" method="post">
					<div class="form-group" style="margin-bottom: 15px;">
						<label for="reference">Enter Equipment Reference:</label>
						<input type="text" id="reference" name="reference" class="form-control" style="width: 200px; display: inline-block; margin-left: 10px;" value="<?php echo isset($_POST['check-equipment']) ? '' : ''; ?>">
						<button type="submit" name="check-equipment" class="btn btn-primary" style="margin-left: 10px;">Check Equipment</button>
					</div>
				</form>
				<div id="guide-result" style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
					<?php
					if (isset($_POST['check-equipment'])) {
						$equipmentRef = trim($_POST['reference']);
						if (empty($equipmentRef)) {
							echo '<div class="alert alert-warning">Please enter an equipment reference.</div>';
						} else {
							$equipementC = new equipementController();
							$allEquipment = $equipementC->listEquipement('all');
							$found = false;
							
							foreach ($allEquipment as $equipment) {
								if (isset($equipment['reference']) && $equipment['reference'] == $equipmentRef) {
									echo '<div class="alert alert-success">Equipment found: ' . (isset($equipment['nom']) ? htmlspecialchars($equipment['nom']) : 'Unknown Equipment') . '</div>';
									echo '<div class="equipment-guide">';
									echo '<h4>Usage Guide:</h4>';
									echo '<p>' . (isset($equipment['guide']) ? htmlspecialchars($equipment['guide']) : 'No guide available for this equipment.') . '</p>';
									echo '</div>';
									$found = true;
									break;
								}
							}
							
							if (!$found) {
								echo '<div class="alert alert-danger">Equipment with reference "' . htmlspecialchars($equipmentRef) . '" not found. Please check the reference and try again.</div>';
							}
						}
					}
					?>
				</div>
			</div>
			
			<style>
				.help-section {
					background: white;
					box-shadow: 0 2px 10px rgba(0,0,0,0.1);
				}
				.help-section h2 {
					color: #003087;
					margin-bottom: 20px;
				}
				.btn-primary {
					background-color: #003087;
					border-color: #003087;
				}
				.btn-primary:hover {
					background-color: #004abc;
					border-color: #004abc;
				}
			</style>
		</main>

		<script>
			// Clear input on page load
			document.addEventListener('DOMContentLoaded', function() {
			    document.getElementById('reference').value = '';
			    
			    // Clear results if page was refreshed
			    if (performance.navigation.type === 1) {
			        document.getElementById('guide-result').innerHTML = '';
			    }
			});
		</script>

</body>
</html>
