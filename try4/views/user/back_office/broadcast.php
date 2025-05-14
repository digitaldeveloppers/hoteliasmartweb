<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../front_office/login.php');
    exit();
}
require_once '../../../controllers/UserController.php';
$userController = new UserController();
$users = $userController->getAllUsers();

// Handle broadcast form submission
$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['broadcast_message'])) {
    $message = trim($_POST['broadcast_message']);
    if ($message !== '') {
        // Insert broadcast message for each user (except admin)
        require_once '../../../config.php';
        $host = 'localhost';
        $db = 'test1';
        $user = 'root';
        $pass = '';
        $conn = new mysqli($host, $user, $pass, $db);
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }
        $stmt = $conn->prepare("INSERT INTO support_messages (sender_id, receiver_id, message_content, created_at) VALUES (?, ?, ?, NOW())");
        foreach ($users as $user) {
            if ($user['role'] !== 'admin') {
                $stmt->bind_param('iis', $_SESSION['user_id'], $user['user_id'], $message);
                $stmt->execute();
            }
        }
        $stmt->close();
        $success = true;
    } else {
        $error = 'Message cannot be empty.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Broadcast Message - Hotelia Smart Admin Dashboard</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="template/assets/logo/HS.png" type="image/png" />
    <link rel="stylesheet" href="template/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="template/assets/css/plugins.min.css" />
    <link rel="stylesheet" href="template/assets/css/hotelia smart.min.css" />
    <link rel="stylesheet" href="template/assets/css/demo.css" />
</head>
<body>
    <div class="wrapper">
        <?php include 'dashboard_side_bar.php'; ?>
        <div class="main-panel">
            <?php include 'header_bar.php'; ?>
            <div class="content">
                <div class="page-inner">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Broadcast Message to All Users</h4>
                                </div>
                                <div class="card-body">
                                    <?php if ($success): ?>
                                        <div class="alert alert-success">Broadcast sent to all users!</div>
                                    <?php elseif ($error): ?>
                                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                    <?php endif; ?>
                                    <form method="POST" action="">
                                        <div class="form-group">
                                            <label for="broadcast_message">Message</label>
                                            <textarea class="form-control" id="broadcast_message" name="broadcast_message" rows="5" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Send Broadcast</button>
                                        <a href="users_management.php" class="btn btn-secondary">Back</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="template/assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="template/assets/js/core/bootstrap.min.js"></script>
</body>
</html>