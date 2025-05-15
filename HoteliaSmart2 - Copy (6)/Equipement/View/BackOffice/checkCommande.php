<?php
require_once(__DIR__ . '/../../Controller/commandeController.php');
require_once(__DIR__ . '/../../Model/commande.php');

$commandeController = new commandeController();

// Get order ID from URL
$idCommande = isset($_GET['id']) ? $_GET['id'] : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commande = new commande(
        $_POST['idCommande'],
        $_POST['date'],
        $_POST['nomClient'],
        $_POST['prenomClient'],
        $_POST['adresseClient'],
        $_POST['mailClient'],
        $_POST['modePaiment'],
        $_POST['total']
    );

    $commandeController->updateCommande($commande);
    header('Location: commandes.php');
    exit();
}

// Get current order details
$commande = $commandeController->showCommande($idCommande);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order - Equipement Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="stylesback1.css">
    <style>
        .error-message { color: red; font-size: 0.9em; margin-top: 5px; display: none; }
        .success-message { color: green; font-size: 0.9em; margin-top: 5px; display: none; }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="admin-nav-container">
            <div class="admin-logo">
                <img src="../FrontOffice/assets/HS.png" alt="Hotelia Smart Logo">
                <span class="hotelia">HOTELIA</span>
                <span class="smart">SMART</span>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="addEqui.php"><i class="fas fa-plus-circle"></i> New Equipement</a></li>
                    <li><a href="showEqui.php"><i class="fas fa-list"></i> Equipements</a></li>
                    <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistics</a></li>
                    <li><a href="commandes.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-title">
        <h1><i class="fas fa-edit"></i> Update Order</h1>
    </div>

    <section class="form-section">
        <div class="form-container">
            <h2>Update Order Details</h2>
            <form method="POST" action="" id="orderForm" onsubmit="return validateForm()">
                <input type="hidden" name="idCommande" value="<?php echo htmlspecialchars($commande['idCommande']); ?>">
                
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($commande['date']); ?>" >
                </div>

                <div class="form-group">
                    <label for="nomClient">Client Last Name:</label>
                    <input type="text" id="nomClient" name="nomClient" value="<?php echo htmlspecialchars($commande['nomClient']); ?>" >
                </div>

                <div class="form-group">
                    <label for="prenomClient">Client First Name:</label>
                    <input type="text" id="prenomClient" name="prenomClient" value="<?php echo htmlspecialchars($commande['prenomClient']); ?>" >
                </div>

                <div class="form-group">
                    <label for="adresseClient">Client Address:</label>
                    <input type="text" id="adresseClient" name="adresseClient" value="<?php echo htmlspecialchars($commande['adresseClient']); ?>" >
                </div>

                <div class="form-group">
                    <label for="mailClient">Client Email:</label>
                    <input type="email" id="mailClient" name="mailClient" value="<?php echo htmlspecialchars($commande['mailClient']); ?>" >
                </div>

                <div class="form-group">
                    <label for="modePaiment">Payment Mode:</label>
                    <select id="modePaiment" name="modePaiment" >
                        <option value="Credit Card" <?php echo $commande['modePaiment'] === 'Credit Card' ? 'selected' : ''; ?>>Credit Card</option>
                        <option value="PayPal" <?php echo $commande['modePaiment'] === 'PayPal' ? 'selected' : ''; ?>>PayPal</option>
                        <option value="Bank Transfer" <?php echo $commande['modePaiment'] === 'Bank Transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="total">Total:</label>
                    <input type="number" id="total" name="total" step="0.01" value="<?php echo htmlspecialchars($commande['total']); ?>" >
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-button"><i class="fas fa-save"></i> Save Changes</button>
                    <a href="commandes.php" class="cancel-button"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </section>

    <script>
        function validateForm() {
            let isValid = true;
            const nameRegex = /^[A-Z][a-zA-Z]{2,}$/;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // Clear all previous messages
            document.querySelectorAll('.error-message, .success-message').forEach(msg => msg.style.display = 'none');

            // Validate Client Last Name
            const nomClient = document.getElementById('nomClient');
            if (!nameRegex.test(nomClient.value)) {
                showError(nomClient, 'Last name must start with uppercase and have at least 3 characters');
                isValid = false;
            } else {
                showSuccess(nomClient, 'Valid last name');
            }

            // Validate Client First Name
            const prenomClient = document.getElementById('prenomClient');
            if (!nameRegex.test(prenomClient.value)) {
                showError(prenomClient, 'First name must start with uppercase and have at least 3 characters');
                isValid = false;
            } else {
                showSuccess(prenomClient, 'Valid first name');
            }

            // Validate Address
            const adresseClient = document.getElementById('adresseClient');
            if (adresseClient.value.trim().length < 5) {
                showError(adresseClient, 'Address must be at least 5 characters long');
                isValid = false;
            } else {
                showSuccess(adresseClient, 'Valid address');
            }

            // Validate Email
            const mailClient = document.getElementById('mailClient');
            if (!emailRegex.test(mailClient.value)) {
                showError(mailClient, 'Please enter a valid email address');
                isValid = false;
            } else {
                showSuccess(mailClient, 'Valid email address');
            }

            // Validate Total
            const total = document.getElementById('total');
            if (isNaN(total.value) || parseFloat(total.value) <= 0) {
                showError(total, 'Total must be a positive number');
                isValid = false;
            } else {
                showSuccess(total, 'Valid total amount');
            }

            return isValid;
        }

        function showError(input, message) {
            const formGroup = input.parentElement;
            let errorDiv = formGroup.querySelector('.error-message');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                formGroup.appendChild(errorDiv);
            }
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            input.style.borderColor = 'red';
        }

        function showSuccess(input, message) {
            const formGroup = input.parentElement;
            let successDiv = formGroup.querySelector('.success-message');
            if (!successDiv) {
                successDiv = document.createElement('div');
                successDiv.className = 'success-message';
                formGroup.appendChild(successDiv);
            }
            successDiv.textContent = message;
            successDiv.style.display = 'block';
            input.style.borderColor = 'green';
        }

        // Add real-time validation
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                validateForm();
            });
        });
    </script>
</body>
</html>