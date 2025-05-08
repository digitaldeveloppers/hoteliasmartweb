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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>User Management - Hotelia Smart Admin Dashboard</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="template/assets/logo/HS.png" type="image/png" />
    <script src="template/assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["template/assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">User Management</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="usersTableBody">
                                                <?php foreach ($users as $user): ?>
                                                <tr data-user-id="<?php echo $user['user_id']; ?>">
                                                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                                    <td><?php echo $user['banned'] ? 'Banned' : 'Active'; ?></td>
                                                    <td>
                                                        <?php if ($user['role'] !== 'admin'): ?>
                                                            <?php if ($user['banned']): ?>
                                                                <button onclick="unbanUser(<?php echo $user['user_id']; ?>)" class="btn btn-success btn-sm btn-unban">Unban</button>
                                                            <?php else: ?>
                                                                <button onclick="banUser(<?php echo $user['user_id']; ?>)" class="btn btn-danger btn-sm btn-ban">Ban</button>
                                                            <?php endif; ?>
                                                            <button onclick="setRole(<?php echo $user['user_id']; ?>, 'admin')" class="btn btn-warning btn-sm btn-role">Make Admin</button>
                                                        <?php else: ?>
                                                            <button onclick="setRole(<?php echo $user['user_id']; ?>, 'user')" class="btn btn-info btn-sm btn-role">Make User</button>
                                                        <?php endif; ?>
                                                        <button onclick="deleteUser(<?php echo $user['user_id']; ?>)" class="btn btn-outline-danger btn-sm">Delete</button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="template/assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="template/assets/js/core/popper.min.js"></script>
    <script src="template/assets/js/core/bootstrap.min.js"></script>
    <script src="template/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="template/assets/js/plugin/chart.js/chart.min.js"></script>
    <script src="template/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>
    <script src="template/assets/js/plugin/chart-circle/circles.min.js"></script>
    <script src="template/assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="template/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
    <script src="template/assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="template/assets/js/plugin/jsvectormap/world.js"></script>
    <script src="template/assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="template/assets/js/kaiadmin.min.js"></script>
    <script src="template/assets/js/setting-demo.js"></script>
    <script src="template/assets/js/demo.js"></script>
    <script src="users_management.js"></script>
</body>
</html>