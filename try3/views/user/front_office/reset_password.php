<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/', '', true, true);
    session_start();
}

require_once __DIR__ . '/../../../controllers/UserController.php';

$error = '';
$success = '';
$validToken = false;
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (empty($token)) {
    $error = "No reset token provided. Please use the link from your email.";
} else {
    try {
        $userController = new UserController();
        $validToken = $userController->validateResetToken($token);
        
        if (!$validToken) {
            $error = "Invalid or expired reset token. Please request a new password reset.";
        }
    } catch (Exception $e) {
        error_log("Password reset validation error: " . $e->getMessage());
        $error = "An error occurred while validating your reset token. Please try again.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    
    if (empty($password) || empty($confirmPassword)) {
        $error = "Both password fields are required.";
    } else if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "Password must contain at least one uppercase letter, one lowercase letter, and one number.";
    } else {
        try {
            $userController = new UserController();
            
            if ($userController->resetPassword($token, $password)) {
                $success = "Password has been reset successfully. You can now login with your new password.";
                $userController->invalidateResetToken($token);
                // Clear any sensitive data from session
                session_regenerate_id(true);
                
                // Redirect to login page with success message
                if (!headers_sent()) {
                    header('Location: login.php?success=' . urlencode($success));
                    exit();
                }
            } else {
                $error = "Failed to reset password. Please try again.";
                error_log("Password reset failed for token: " . $token);
            }
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            $error = "An error occurred while resetting your password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
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
        <h2>Reset Password</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($validToken && !$success): ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="password" required minlength="8">
            </div>
            
            <div class="form-group">
                <label>Confirm New Password:</label>
                <input type="password" name="confirm_password" required minlength="8">
            </div>
            
            <button type="submit" class="btn">Reset Password</button>
        </form>
        <?php endif; ?>
        
        <?php if ($success || !$validToken): ?>
            <a href="login.php" class="back-link">Back to Login</a>
        <?php endif; ?>
    </div>
</body>
</html>