<?php
/**
 * Equipment Update Page
 * Handles the modification of existing equipment items including image updates
 */

require_once '../../Model/equipement.php';
require_once '../../Controller/equipementController.php';
require_once '../../config.php';

// Check if reference parameter exists
if (!isset($_GET['reference'])) {
    header('Location: showEqui.php');
    exit();
}

// Fetch existing equipment data
$reference = $_GET['reference'];
$equipementController = new equipementController();
$result = $equipementController->showEquipement($reference);
if ($result) {
    $equipement = new equipement(
        $result['reference'],
        $result['nom'],
        $result['prix'],
        $result['quantite'],
        $result['type'],
        $result['image'],
        $result['guide'] ?? ''
    );
}

// Redirect if equipment not found
if (!$equipement) {
    header('Location: showEqui.php');
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $image = $equipement->getImage();
    
    // Handle image upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../FrontOffice/assets/images/equipements/';
        $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $imageName = uniqid() . '.' . $imageFileType;
        $targetPath = $uploadDir . $imageName;
        
        if(move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            // Delete old image if exists
            if($image && file_exists($uploadDir . $image)) {
                unlink($uploadDir . $image);
            }
            $image = $imageName;
        }
    }
    
    // Create updated equipment object
    $equipement = new equipement(
        $_GET['reference'], // Use reference from URL, not form
        $_POST['nom'],
        $_POST['prix'],
        $_POST['quantite'],
        $_POST['type'],
        $image,
        $_POST['guide']
    );
    
    // Update in database
    $equipementC = new equipementController();
    $success = $equipementC->updateEquipement($equipement);
    
    if ($success) {
        header('Location: showEqui.php');
        exit();
    } else {
        echo '<div class="alert alert-danger">Failed to update equipment. Please try again.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipement Dashboard</title>
    <!-- External CSS and Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="stylesback1.css">
    <style>
        /* Form validation styles */
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        .success-message {
            color: #28a745;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        .form-group input.invalid {
            border-color: #dc3545;
        }
        .form-group input.valid {
            border-color: #28a745;
        }
        
        /* Dark mode styles */
        .dark-mode {
            background-color: #333;
            color: #fff;
        }
        .dark-mode .admin-logo img {
            filter: invert(1);
        }
        .dark-mode .admin-nav a {
            color: #fff;
        }
        .dark-mode .admin-nav a:hover {
            color: #ccc;
        }
        .dark-mode .admin-title {
            color: #fff;
        }
        .dark-mode .form-container {
            background-color: #444;
            color: #fff;
        }
        .dark-mode .form-container label {
            color: #fff;
        }
        .dark-mode .form-container input, .dark-mode .form-container textarea {
            background-color: #555;
            color: #fff;
        }
        .dark-mode .form-container input.invalid, .dark-mode .form-container textarea.invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .dark-mode .form-container input.valid, .dark-mode .form-container textarea.valid {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
    </style>
</head>
<body>
    <!-- Admin Header with Navigation -->
    <header class="admin-header">
        <div class="admin-nav-container">
            <div class="admin-logo">
                <img src="../FrontOffice/assets/HS.png" alt="Hotelia Smart Logo">
                <span class="hotelia">HOTELIA</span>
                <span class="smart">SMART</span>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="addEqui.php" ><i class="fas fa-plus-circle"></i> Add</a></li>
                    <li><a href="showEqui.php"><i class="fas fa-list"></i> Equipements</a></li>
                    <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistics</a></li>
                    <li><a href="commandes.php"><i class="fa-solid fa-cart-shopping"></i>Orders</a></li>
                    <li><button id="darkModeToggle" style="background: none; border: none; color: inherit; cursor: pointer;"><i class="fas fa-moon"></i> Dark Mode</button></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Title -->
    <div class="admin-title">
        <h1><i class="fas fa-edit"></i> Update Equipement</h1>
    </div>
    <!-- Equipment Update Form -->
    <section class="form-section">
        <div class="form-container">
            <h2>Update Equipement</h2>
            <form id="equipmentForm" action="updateEqui.php?reference=<?php echo htmlspecialchars($equipement->getReference()); ?>" method="POST" enctype="multipart/form-data" novalidate>
                <div class="form-group">
                    <label for="nom">Name</label>
                    <input type="text" id="nom" name="nom" placeholder="Entrez le nom de l'équipement" value="<?php echo htmlspecialchars($equipement->getNom()); ?>">
                    <div id="nom-error" class="error-message"></div>
                    <div id="nom-success" class="success-message">Nom valide</div>
                </div>
                <div class="form-group">
                    <label for="prix">Price</label>
                    <input type="number" id="prix" name="prix" placeholder="Entrez le prix" value="<?php echo htmlspecialchars($equipement->getPrix()); ?>">
                    <div id="prix-error" class="error-message"></div>
                    <div id="prix-success" class="success-message">Prix valide</div>
                </div>
                <div class="form-group">
                    <label for="quantite">Quantity</label>
                    <input type="number" id="quantite" name="quantite" placeholder="Entrez la quantité" value="<?php echo htmlspecialchars($equipement->getQuantite()); ?>">
                    <div id="quantite-error" class="error-message"></div>
                    <div id="quantite-success" class="success-message">Quantité valide</div>
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <input type="text" id="type" name="type" placeholder="Entrez le type d'équipement" value="<?php echo htmlspecialchars($equipement->getType()); ?>">
                    <div id="type-error" class="error-message"></div>
                    <div id="type-success" class="success-message">Type valide</div>
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <?php if($equipement->getImage()): ?>
                        <img src="../FrontOffice/assets/images/equipements/<?php echo $equipement->getImage(); ?>" style="width: 100px; margin-bottom: 10px;">
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/*">
                    <div id="image-error" class="error-message"></div>
                    <div id="image-success" class="success-message">Image valide</div>
                </div>
                <div class="form-group">
                    <label for="guide">Usage Guide:</label>
                    <textarea class="form-control" id="guide" name="guide" rows="5"><?php echo $equipement->getGuide(); ?></textarea>
                </div>
                <div class="button-group">
                    <button type="submit" class="submit-button"><i class="fas fa-save"></i> Save</button>
                    <a href="showEqui.php" class="clear-button"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </section>
    <!-- Form validation and handling scripts -->
    <script src="formValidation.js"></script>
    <script>
        // Dark mode toggle functionality
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        
        // Check for saved user preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            darkModeToggle.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
        }
        
        darkModeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
                darkModeToggle.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
            } else {
                localStorage.setItem('darkMode', 'disabled');
                darkModeToggle.innerHTML = '<i class="fas fa-moon"></i> Dark Mode';
            }
        });
    </script>
</body>
</html>