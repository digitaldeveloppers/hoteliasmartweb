<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des articles</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <!-- Fonts and icons -->
    <script src="../template/assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {"families":["Lato:300,400,700,900"]},
            custom: {
                families: ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"],
                urls: ["../template/assets/css/fonts.min.css"]
            },
            active: function () {
                sessionStorage.fonts = true;
            }
        });
    </script>
    <!-- CSS Files -->
    <link rel="stylesheet" href="../template/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../template/assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../template/assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="../stylesback.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="show.php" class="logo">
                        <img src="../../FrontOffice/assets/HS.png" alt="Hotelia Smart Logo" class="navbar-brand" height="40" />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-secondary">
                        <li class="nav-item active">
                            <a href="show.php">
                                <i class="fas fa-newspaper"></i>
                                <p>Articles</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="create.php">
                                <i class="fas fa-plus-circle"></i>
                                <p>Ajouter Article</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="historique.php">
                                <i class="fas fa-history"></i>
                                <p>Statistics</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="settings.php">
                                <i class="fas fa-cog"></i>
                                <p>Paramètres</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="main-panel">
            <div class="content">
                <!-- Page content will be inserted here -->
                <?php if (isset($pageContent)) echo $pageContent; ?>
            </div>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="../template/assets/js/core/jquery.3.2.1.min.js"></script>
    <script src="../template/assets/js/core/bootstrap.bundle.min.js"></script>
    <script src="../template/assets/js/core/jquery.scrollbar.min.js"></script>
    <script src="../template/assets/js/core/jquery-scrollLock.min.js"></script>
    <script src="../template/assets/js/kaiadmin.min.js"></script>
</body>
</html>