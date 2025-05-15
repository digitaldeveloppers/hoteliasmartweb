<?php
/**
 * Equipment Addition Page
 * Handles the creation of new equipment items with image upload functionality
 */

require_once '../../Model/equipement.php';
require_once '../../Controller/equipementController.php';
require_once '../../config.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $image = '';
    
    // Handle image upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../FrontOffice/assets/images/equipements/';
        $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $imageName = uniqid() . '.' . $imageFileType;
        $targetPath = $uploadDir . $imageName;
        
        if(move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $imageName;
        }
    }
    
    // Create new equipment object
    $equipement = new equipement(
        null, // Auto-increment reference
        $_POST['nom'],
        $_POST['prix'],
        $_POST['quantite'],
        $_POST['type'],
        $image,
        $_POST['guide'] ?? ''
    );
    
    // Add to database
    $equipementC = new equipementController();
    $success = $equipementC->addEquipement($equipement);
    
    if ($success) {
        header('Location: showEqui.php');
        exit();
    } else {
        echo '<div class="alert alert-danger">Failed to add equipment. Please try again.</div>';
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
    <script src="formValidation.js"></script>
    <style>
        /* Form validation styles */
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
            padding: 5px;
            border-radius: 4px;
            background-color: rgba(220, 53, 69, 0.1);
        }
        .success-message {
            color: #28a745;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
            padding: 5px;
            border-radius: 4px;
            background-color: rgba(40, 167, 69, 0.1);
        }
        .form-group input.invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .form-group input.valid {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
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
                    <li><a href="addEqui.php" ><i class="fas fa-plus-circle"></i> New Equipement</a></li>
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
        <h1><i class="fas fa-plus-circle"></i> Add a new equipement</h1>
    </div>

    <!-- Equipment Addition Form -->
    <section class="form-section">
        <div class="form-container">
            <h2>New Equipement</h2>
            <form id="equipmentForm" action="#" method="POST" enctype="multipart/form-data" novalidate>
                <div class="form-group">
                    <label for="nom">Name</label>
                    <input type="text" id="nom" name="nom" placeholder="Enter equipment name" required>
                    <div id="nom-error" class="error-message"></div>
                    <div id="nom-success" class="success-message">Valid name</div>
                </div>
                <div class="form-group">
                    <label for="prix">Price</label>
                    <input type="number" id="prix" name="prix" placeholder="Enter price" >
                    <div id="prix-error" class="error-message"></div>
                    <div id="prix-success" class="success-message">Valid price</div>
                </div>
                <div class="form-group">
                    <label for="quantite">Quantity</label>
                    <input type="number" id="quantite" name="quantite" placeholder="Enter quantity"  >
                    <div id="quantite-error" class="error-message"></div>
                    <div id="quantite-success" class="success-message">Valid quantity</div>
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <input type="text" id="type" name="type" placeholder="Enter equipment type" >
                    <div id="type-error" class="error-message"></div>
                    <div id="type-success" class="success-message">Valid type</div>
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image" accept="image/*" >
                    <div id="image-error" class="error-message"></div>
                    <div id="image-success" class="success-message">Valid image</div>
                </div>
                <div class="form-group">
                    <label for="guide">Usage Guide:</label>
                    <textarea class="form-control" id="guide" name="guide" rows="5" placeholder="Enter detailed usage instructions..."></textarea>
                </div>
                <div class="button-group">
                    <button type="submit" class="submit-button"><i class="fas fa-save"></i> Add</button>
                    <button type="button" class="clear-button" onclick="clearForm()"><i class="fas fa-undo"></i> Clear</button>
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

        darkModeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
        });
    </script>
</body>
</html>