<?php
require_once __DIR__ . '/../../../config/github_config.php';
require_once __DIR__ . '/../../../controllers/UserController.php';
require_once __DIR__ . '/../../../models/User.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userController = new UserController();

if (isset($_GET['code'])) {
    try {
        // Get the access token
        $code = $_GET['code'];
        
        $token_data = [
            'client_id' => GITHUB_CLIENT_ID,
            'client_secret' => GITHUB_CLIENT_SECRET,
            'code' => $code,
            'redirect_uri' => GITHUB_REDIRECT_URI
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GITHUB_TOKEN_URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'User-Agent: PHP',
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code !== 200) {
            throw new Exception('Failed to get access token from GitHub');
        }
        
        $token_response = json_decode($response, true);
        if (!isset($token_response['access_token'])) {
            throw new Exception('Access token not found in GitHub response');
        }
        
        $access_token = $token_response['access_token'];

    // Get user data from GitHub
    $user_url = 'https://api.github.com/user';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $user_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: token ' . $access_token,
        'User-Agent: PHP'
    ]);
    
    $user_response = curl_exec($ch);
    curl_close($ch);
    
    $github_user = json_decode($user_response, true);

    // Get user's email
    $email_url = 'https://api.github.com/user/emails';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $email_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: token ' . $access_token,
        'User-Agent: PHP'
    ]);
    
    $email_response = curl_exec($ch);
    curl_close($ch);
    
    $emails = json_decode($email_response, true);
    $primary_email = '';
    foreach ($emails as $email) {
        if ($email['primary']) {
            $primary_email = $email['email'];
            break;
        }
    }

    // Check if user exists
    $existing_user = $userController->getUserByEmail($primary_email);
    
    if ($existing_user) {
        // Login existing user
        $login_result = $userController->loginUserWithGithub($primary_email);
        if ($login_result['status']) {
            header('Location: /projects/try3/views/user/front_office/dashboard.php');
            exit;
        }
    } else {
        // Create new user
        $name_parts = explode(' ', $github_user['name']);
        $first_name = $name_parts[0];
        $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
        
        $new_user = new User();
        $new_user->setFirstName($first_name);
        $new_user->setLastName($last_name);
        $new_user->setEmail($primary_email);
        $new_user->setPassword(password_hash(uniqid(), PASSWORD_DEFAULT));
        $new_user->setRole('user');
        $new_user->setVerified(1);
        $new_user->setBanned(0);
        $new_user->setAccountType('github');
        
        $userController->addUser($new_user);
        
        // Login the new user
        $login_result = $userController->loginUserWithGithub($primary_email);
        if ($login_result['status']) {
            header('Location: /projects/try3/views/user/front_office/dashboard.php');
            exit;
        }
    }
    } catch (Exception $e) {
        $_SESSION['error'] = 'GitHub authentication failed: ' . $e->getMessage();
        header('Location: /projects/try3/views/user/front_office/login.php');
        exit;
    }
} else {
    $_SESSION['error'] = 'GitHub authorization code not provided';
    header('Location: /projects/try3/views/user/front_office/login.php');
    exit;
}