<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/', '', true, true);
    session_start();
}

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../config/google_config.php';
require_once __DIR__ . '/../../../config/github_config.php';
require_once __DIR__ . '/../../../controllers/UserController.php';

$google_client = new Google\Client();
$google_client->setClientId(GOOGLE_CLIENT_ID);
$google_client->setClientSecret(GOOGLE_CLIENT_SECRET);
$google_client->setRedirectUri(GOOGLE_REDIRECT_URI);
$google_client->addScope('email');
$google_client->addScope('profile');

$userController = new UserController();
$github_auth_url = $userController->get_github_link();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recaptchaSecret = '6LfoESorAAAAAA95d8vIESOrFaW4e4dVsNBohfiX';
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha = file_get_contents($recaptchaUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
    $recaptcha = json_decode($recaptcha);
    if (!$recaptchaResponse || !$recaptcha || !$recaptcha->success) {
        $error = 'Please complete the reCAPTCHA verification.';
    } else {
        $controller = new UserController();
        $result = $controller->loginUser($_POST['email'], $_POST['password']);
        if (!$result['status']) {
            $error = $result['message'];
        } else {
            if ($_SESSION['role'] === 'admin') {
                header('Location: ../back_office/users_management.php');
                exit();
            } else {
                header('Location: profile.php');
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Hotelia Smart</title>
    <link rel="shortcut icon" type="image/icon" href="assets/HS.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/linearicons.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/flaticon.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/slick-theme.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bootsnav.css">
    <link rel="stylesheet" href="assets/css/stylefront3.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>
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
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="header-top-contact"><a href="profile.php">Profile</a></li>
                        <li class="header-top-contact"><a href="logout.php">Logout</a></li>
                        <?php else: ?>
                        <li class="header-top-contact"><a href="login.php">Sign In</a></li>
                        <li class="header-top-contact"><a href="register.php">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>
        </ul>
    </header>
    <section class="top-area">
        <div class="header-area">
            <nav class="navbar navbar-default bootsnav navbar-sticky navbar-scrollspy" data-minus-value-desktop="70" data-minus-value-mobile="55" data-speed="1000">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
                            <i class="fa fa-bars"></i>
                        </button>
                        <a class="navbar-brand" href="home.php">Hotelia<span>Smart</span></a>
                    </div>
                    <div class="collapse navbar-collapse menu-ui-design" id="navbar-menu">
                        <ul class="nav navbar-nav navbar-right" data-in="fadeInDown" data-out="fadeOutUp">
                            <li class="header-top-contact"><a href="home.php">home</a></li>
                            <li class="header-top-contact"><a href="store.php">store</a></li>
                            <li class="header-top-contact"><a href="explore.php">explore</a></li>
                            <li class="header-top-contact"><a href="reviews.php">review</a></li>
                            <li class="header-top-contact"><a href="blog.php">blog</a></li>
                            <li class="header-top-contact"><a href="forum.php">forum</a></li>
                            <li class="header-top-contact"><a href="Aboutus.php">about us</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <div class="clearfix"></div>
    </section>
    <section class="form-section" style="background: linear-gradient(135deg, #e0e7ef 0%, #f5f7fa 100%); min-height: 100vh; display: flex; align-items: center;">
        <div class="container">
            <div class="row justify-content-center" style="display: flex; align-items: center; min-height: 100vh;">
                <div class="col-md-6" style="margin: auto;">
                    <div class="form-container" style="background: #fff; border-radius: 18px; box-shadow: 0 8px 32px 0 rgba(31,38,135,0.15); padding: 40px 32px 32px 32px; margin: 0 auto;">
                        <div style="text-align:center; margin-bottom:24px;">

                            <img src="assets/HS.png" alt="Hotelia Smart Logo" style="width:60px; height:60px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                        </div>
                        <h2 style="text-align:center; font-weight:700; color:#2d3e50; margin-bottom:18px; letter-spacing:1px;">Sign In to Hotelia Smart</h2>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="error-message" style="background:#ffeaea; color:#d93025; border-radius:6px; padding:10px 16px; margin-bottom:10px; text-align:center; font-weight:500; letter-spacing:0.5px;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="success-message" style="background:#e6f4ea; color:#188038; border-radius:6px; padding:10px 16px; margin-bottom:10px; text-align:center; font-weight:500; letter-spacing:0.5px;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>
                        <?php
                        require_once '../../../controllers/UserController.php';
                        $error = '';
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            $controller = new UserController();
                            $result = $controller->loginUser($_POST['email'], $_POST['password']);
                            if (!$result['status']) {
                                $error = $result['message'];
                            } else {
                                if ($_SESSION['role'] === 'admin') {
                                    header('Location: ../back_office/users_management.php');
                                } else {
                                    header('Location: user_profile.php');
                                }
                                exit();
                            }
                        }
                        ?>
                        <?php if ($error): ?>
                            <div class="error-message" style="background:#ffeaea; color:#d93025; border-radius:6px; padding:10px 16px; margin-bottom:10px; text-align:center; font-weight:500; letter-spacing:0.5px;">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:18px;">
                            <a href="<?php echo $google_client->createAuthUrl(); ?>" class="welcome-hero-btn" style="background:#4285f4; color:white; margin-bottom:0; display:flex; align-items:center; justify-content:center; font-weight:600; border-radius:6px; box-shadow:0 2px 8px rgba(66,133,244,0.08); padding:10px 0;">
                                <img src="assets/images/google-icon.svg" alt="Google Icon" style="width:20px; height:20px; margin-right:10px;">Sign in with Google
                            </a>
                            <a href="<?php echo $github_auth_url; ?>" class="welcome-hero-btn" style="background:#24292e; color:white; margin-bottom:0; display:flex; align-items:center; justify-content:center; font-weight:600; border-radius:6px; box-shadow:0 2px 8px rgba(36,41,46,0.08); padding:10px 0;">
                                <svg style="margin-right:10px; width:20px; height:20px; vertical-align:middle;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61-.546-1.385-1.335-1.755-1.335-1.755-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.605-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 21.795 24 17.295 24 12c0-6.63-5.37-12-12-12"/></svg>Sign in with GitHub
                            </a>
                        </div>
                        <div style="margin: 18px 0 0 0; border-bottom:1px solid #e0e7ef; text-align:center; position:relative;">
                            <span style="background:#fff; position:relative; top:12px; padding:0 12px; color:#b0b8c1; font-size:14px;">or sign in with email</span>
                        </div>
                        <form method="POST" action="" style="margin-top:24px;">
                            <div class="form-group" style="margin-bottom:18px;">
                                <label style="font-weight:600; color:#2d3e50;">Email:</label>
                                <input type="email" name="email" required style="width:100%; padding:10px 12px; border-radius:6px; border:1px solid #e0e7ef; background:#f7fafd; font-size:16px; margin-top:6px;">
                            </div>
                            <div class="form-group" style="margin-bottom:18px;">
                                <label style="font-weight:600; color:#2d3e50;">Password:</label>
                                <input type="password" name="password" required style="width:100%; padding:10px 12px; border-radius:6px; border:1px solid #e0e7ef; background:#f7fafd; font-size:16px; margin-top:6px;">
                            </div>
                            <div class="form-group" style="margin-bottom:18px;">
                                <div class="g-recaptcha" data-sitekey="6LfoESorAAAAAJuQ5a556gj04ZPpOYTKnpy7O64v"></div>
                            </div>
                            <button type="submit" class="welcome-hero-btn" style="width:100%; background:#2d3e50; color:#fff; font-weight:700; border-radius:6px; padding:12px 0; font-size:18px; letter-spacing:1px; margin-bottom:10px; box-shadow:0 2px 8px rgba(45,62,80,0.08); margin:0%;">Login</button>
                        </form>
                        <p style="text-align:center; margin-top:10px;"><a href="forgot_password.php" style="color:#4285f4; font-weight:500;">Forgot Password?</a></p>
                        <p style="text-align:center;">Don't have an account? <a href="register.php" style="color:#2d3e50; font-weight:600;">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/bootsnav.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="../../../js/user.js"></script>
</body>
</html>