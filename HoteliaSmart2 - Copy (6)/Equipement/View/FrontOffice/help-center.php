<?php
require_once(__DIR__ . '/../../config.php');
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
    <title>Hotelia Smart - Help Center</title>

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
			<h1>Help Center</h1>
			<div class="help-categories" style="display: flex; gap: 20px; margin-top: 30px;">
				<a href="help-equipment.php" class="btn-help" style="flex: 1; background: #003087; color: white; padding: 20px; text-align: center; border-radius: 8px; text-decoration: none; font-weight: bold; transition: background 0.3s ease;">Help with Equipment</a>
				<a href="#" class="btn-help" style="flex: 1; background: #003087; color: white; padding: 20px; text-align: center; border-radius: 8px; text-decoration: none; font-weight: bold; transition: background 0.3s ease;">Help with Service</a>
				<a href="#" class="btn-help" style="flex: 1; background: #003087; color: white; padding: 20px; text-align: center; border-radius: 8px; text-decoration: none; font-weight: bold; transition: background 0.3s ease;">Help with Staff</a>
			</div>
			<style>
				.btn-help:hover {
					background: #004abc;
				}
			</style>
		</main>

</body>
</html>