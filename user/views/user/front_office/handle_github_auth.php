<?php
require_once __DIR__ . '/../../../config/github_config.php';
require_once __DIR__ . '/../../../controllers/UserController.php';
require_once __DIR__ . '/../../../models/User.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userController = new UserController();

$state = isset($_GET['state']) ? $_GET['state'] : 'login';

if (!isset($_GET['code'])) {
    $_SESSION['error'] = 'GitHub login failed: missing code';
    header('Location: /projects/try3/views/user/front_office/login.php');
    exit;
}

$code = $_GET['code'];

// Exchange code for access token
$token_data = [
    'client_id' => GITHUB_CLIENT_ID,
    'client_secret' => GITHUB_CLIENT_SECRET,
    'code' => $code,
    'redirect_uri' => GITHUB_REDIRECT_URI
];

$ch = curl_init(GITHUB_TOKEN_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$token_response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($token_response, true);

if (!isset($token_data['access_token'])) {
    $_SESSION['error'] = 'GitHub login failed: token error';
    header('Location: /projects/try3/views/user/front_office/login.php');
    exit;
}

$access_token = $token_data['access_token'];

// Fetch user email
$email_url = 'https://api.github.com/user/emails';
$ch = curl_init($email_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: token ' . $access_token,
    'User-Agent: StartupAcademyApp'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$email_response = curl_exec($ch);
curl_close($ch);

$user_emails = json_decode($email_response, true);
$email = '';
if (is_array($user_emails)) {
    foreach ($user_emails as $e) {
        if (isset($e['primary']) && $e['primary'] && isset($e['email'])) {
            $email = $e['email'];
            break;
        }
    }
}
if (!$email && isset($user_emails[0]['email'])) {
    $email = $user_emails[0]['email'];
}

if ($email) {
    $existing_user = $userController->getUserByEmail($email);
    if ($existing_user) {
        // LOGIN EXISTING USER
        $login_result = $userController->loginUserWithGithub($email);
        if ($login_result['status']) {
            header('Location: /projects/try3/views/user/front_office/home.php');
            exit;
        } else {
            $_SESSION['error'] = 'GitHub login failed: login error';
            header('Location: /projects/try3/views/user/front_office/login.php');
            exit;
        }
    } else {
        // CREATE NEW USER AND LOGIN
        $profile_url = 'https://api.github.com/user';
        $ch = curl_init($profile_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: token ' . $access_token,
            'User-Agent: StartupAcademyApp'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $profile_response = curl_exec($ch);
        curl_close($ch);
        $profile = json_decode($profile_response, true);
        $name = isset($profile['name']) ? $profile['name'] : '';
        $name_parts = explode(' ', $name);
        $first_name = $name_parts[0];
        $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
        $new_user = new User();
        $new_user->setFirstName($first_name);
        $new_user->setLastName($last_name);
        $new_user->setEmail($email);
        $new_user->setPassword(password_hash(uniqid(), PASSWORD_DEFAULT));
        $new_user->setRole('user');
        $new_user->setVerified(1);
        $new_user->setBanned(0);
        $new_user->setAccountType('github');
        $userController->addUser($new_user);
        $login_result = $userController->loginUserWithGithub($email);
        if ($login_result['status']) {
            header('Location: /projects/try3/views/user/front_office/dashboard.php');
            exit;
        } else {
            $_SESSION['error'] = 'GitHub login failed: login error';
            header('Location: /projects/try3/views/user/front_office/login.php');
            exit;
        }
    }
} else {
    $_SESSION['error'] = 'GitHub login failed: userinfo error';
    header('Location: /projects/try3/views/user/front_office/login.php');
    exit;
}