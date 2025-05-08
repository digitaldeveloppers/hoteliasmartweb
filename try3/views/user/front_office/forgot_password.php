<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/', '', true, true);
    session_start();
}

require_once __DIR__ . '/../../../controllers/UserController.php';
require_once __DIR__ . '/../../../config/mail_config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $userController = new UserController();
    
    // Check if email exists
    $user = $userController->getUserByEmail($email);
    
    if ($user) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store token in database
        if ($userController->storeResetToken($email, $token, $expiry)) {
            // Send reset email
            $resetLink = "http://localhost/projects/try3/views/user/front_office/reset_password.php?token=" . $token;
            
            try {
                if (MailConfig::sendPasswordResetEmail($email, $resetLink)) {
                    $success = "Password reset instructions have been sent to your email.";
                } else {
                    $error = "Failed to send reset email. Please try again.";
                }
            } catch (Exception $e) {
                $error = "Failed to send reset email: " . $e->getMessage();
            }
        } else {
            $error = "Failed to process reset request. Please try again.";
        }
    } else {
        // Don't reveal if email exists or not for security
        $success = "If your email exists in our system, you will receive password reset instructions.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        .form-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <header>
        <a href="home.php" class="home-link">Home</a>
    </header>
    <div class="form-container">
        <h2>Forgot Password</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            
            <button type="submit" class="btn">Reset Password</button>
        </form>
        
        <a href="login.php" class="back-link">Back to Login</a>
    </div>
</body>
</html>