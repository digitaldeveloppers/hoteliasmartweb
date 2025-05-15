<?php
session_start();
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../../Controller/equipementController.php');
require_once(__DIR__ . '/../../Controller/commandeController.php');

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    // Redirect to cart if it's empty
    header('Location: cart.php');
    exit;
}

$equipementC = new equipementController();
$cartItems = array();
$total = 0;
foreach ($_SESSION['cart'] as $equipId) {
    $equipment = $equipementC->showEquipement($equipId);
    if ($equipment) {
        $cartItems[] = $equipment;
        $total += $equipment['prix'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $nomClient = $_POST['nomClient'];
    $prenomClient = $_POST['prenomClient'];
    $mailClient = $_POST['mailClient'];
    $adresseClient = $_POST['adresseClient'];
    $modePaiment = $_POST['modePaiment'];
    $totalCommande = $_POST['total']; // Use the hidden total field
    $date = date('Y-m-d H:i:s'); // Current date and time
    $idCommande = null; // Assuming ID is auto-incremented in the database

    // Create a new commande object
    $commande = new commande(
        $idCommande, 
        $date, 
        $nomClient, 
        $prenomClient, 
        $adresseClient, 
        $mailClient, 
        $modePaiment, 
        $totalCommande
    );

    // Prepare ordered items array
    $orderedItems = array();
    foreach ($cartItems as $item) {
        $orderedItems[] = array(
            'reference' => $item['reference'],
            'quantity' => 1 // Default to 1 since current cart implementation doesn't track quantity
        );
    }

    // Add the commande to the database
    $commandeC = new commandeController();
    $commandeC->addCommande($commande, $orderedItems);

    // Clear the cart
    unset($_SESSION['cart']);

    // Redirect to a confirmation page or store
    // For now, redirect back to the store
    header('Location: store.php?order=success'); // Add a query param for feedback
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!--font-family-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    
    <title>Checkout - Hotelia Smart</title>
    <link rel="shortcut icon" type="image/icon" href="assets/HS.png"/>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bootsnav.css">
    <link rel="stylesheet" href="assets/css/stylefront3.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    
    <style>
        .checkout-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        .checkout-header {
            margin-bottom: 40px;
            text-align: center;
            position: relative;
        }
        .back-arrow {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            color: #003087;
            font-size: 24px;
            text-decoration: none;
        }
        .back-arrow:hover {
            color: #004abc;
        }
        .checkout-header h2 {
            color: #003087;
            font-size: 36px;
            margin-bottom: 15px;
        }
        .checkout-form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 30px;
            max-width: 700px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        .form-control {
            display: block;
            width: 100%;
            padding: 10px 15px;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 .2rem rgba(0,123,255,.25);
        }
        .order-summary {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: right;
        }
        .order-summary h4 {
            color: #003087;
            margin-bottom: 15px;
        }
        .submit-btn {
            background-color: #003087;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            font-size: 16px;
        }
        .submit-btn:hover {
            background-color: #004abc;
        }
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
        .success-message {
            color: #28a745;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <section class="checkout-section">
        <div class="container">
            <div class="checkout-header">
                <a href="cart.php" class="back-arrow"><i class="fas fa-arrow-left"></i></a>
                <h2>Checkout</h2>
            </div>
            <div class="checkout-form-container">
                <form action="checkout.php" method="POST">
                    <h4>Contact Information</h4>
                    <div class="form-group">
                        <label for="nomClient">Last Name</label>
                        <input type="text" class="form-control" id="nomClient" name="nomClient" >
                    </div>
                    <div class="form-group">
                        <label for="prenomClient">First Name</label>
                        <input type="text" class="form-control" id="prenomClient" name="prenomClient" >
                    </div>
                    <div class="form-group">
                        <label for="mailClient">Email Address</label>
                        <input type="email" class="form-control" id="mailClient" name="mailClient" >
                    </div>

                    <h4>Shipping Address</h4>
                    <div class="form-group">
                        <label for="adresseClient">Address</label>
                        <div id="map" style="width:100%;height:300px;margin-bottom:10px;border-radius:8px;"></div>
                        <small class="form-text text-muted mb-2 d-block">
                            <i class="fas fa-info-circle"></i> Click on the map or drag the marker to select your delivery address.
                        </small>
                        <input type="text" class="form-control" id="adresseClient" name="adresseClient" placeholder="Select your address on the map" readonly required>
                    </div>

                    <h4>Payment Method</h4>
                    <div class="form-group">
                        <label for="modePaiment">Payment Mode</label>
                        <select class="form-control" id="modePaiment" name="modePaiment" >
                            <option value="" disabled selected>Select Payment Method</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="PayPal">PayPal</option>
                            <option value="Cash on Delivery">Cash on Delivery</option>
                        </select>
                    </div>

                    <div class="order-summary">
                        <h4>Order Total: <?php echo number_format($total, 2); ?> TND</h4>
                        <input type="hidden" name="total" value="<?php echo $total; ?>">
                        <button type="submit" class="submit-btn">Place Order</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/bootsnav.js"></script>
    <script src="assets/js/custom.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <script src="assets/js/map-address.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const nomClient = document.getElementById('nomClient');
        const prenomClient = document.getElementById('prenomClient');
        const mailClient = document.getElementById('mailClient');
        const adresseClient = document.getElementById('adresseClient');
        const modePaiment = document.getElementById('modePaiment');

        function createMessageElement(id, isError = true) {
            const messageDiv = document.createElement('div');
            messageDiv.id = id;
            messageDiv.className = isError ? 'error-message' : 'success-message';
            return messageDiv;
        }

        function validateName(input, errorId) {
            const value = input.value.trim();
            const messageElement = document.getElementById(errorId) || createMessageElement(errorId);
            input.parentNode.appendChild(messageElement);
    
            if (value.length < 3) {
                messageElement.className = 'error-message';
                messageElement.textContent = 'Must be at least 3 characters long';
                return false;
            }
            if (!/^[A-Z][a-zA-Z]*$/.test(value)) {
                messageElement.className = 'error-message';
                messageElement.textContent = 'Must start with uppercase letter and contain only letters';
                return false;
            }
            messageElement.className = 'success-message';
            messageElement.textContent = 'Perfect!';
            return true;
        }

        function validateEmail(input) {
            const value = input.value.trim();
            const messageElement = document.getElementById('mailError') || createMessageElement('mailError');
            input.parentNode.appendChild(messageElement);
    
            if (!value.includes('@')) {
                messageElement.className = 'error-message';
                messageElement.textContent = 'Email must contain @';
                return false;
            }
            messageElement.className = 'success-message';
            messageElement.textContent = 'Valid email format!';
            return true;
        }

        function validateAddress(input) {
            const value = input.value.trim();
            const messageElement = document.getElementById('addressError') || createMessageElement('addressError');
            input.parentNode.appendChild(messageElement);
    
            if (value.length === 0) {
                messageElement.className = 'error-message';
                messageElement.textContent = 'Address is required';
                return false;
            }
            if (value.length < 5) {
                messageElement.className = 'error-message';
                messageElement.textContent = 'Address must be at least 5 characters long';
                return false;
            }
            messageElement.className = 'success-message';
            messageElement.textContent = 'Valid address!';
            return true;
        }

        function validatePayment(input) {
            const value = input.value;
            const messageElement = document.getElementById('paymentError') || createMessageElement('paymentError');
            input.parentNode.appendChild(messageElement);
    
            if (!value) {
                messageElement.className = 'error-message';
                messageElement.textContent = 'Please select a payment method';
                return false;
            }
            messageElement.className = 'success-message';
            messageElement.textContent = 'Payment method selected!';
            return true;
        }

        nomClient.addEventListener('input', () => validateName(nomClient, 'nomError'));
        prenomClient.addEventListener('input', () => validateName(prenomClient, 'prenomError'));
        mailClient.addEventListener('input', () => validateEmail(mailClient));
        adresseClient.addEventListener('input', () => validateAddress(adresseClient));
        modePaiment.addEventListener('change', () => validatePayment(modePaiment));

        form.addEventListener('submit', function(e) {
            const isNomValid = validateName(nomClient, 'nomError');
            const isPrenomValid = validateName(prenomClient, 'prenomError');
            const isEmailValid = validateEmail(mailClient);
            const isAddressValid = validateAddress(adresseClient);
            const isPaymentValid = validatePayment(modePaiment);

            if (!isNomValid || !isPrenomValid || !isEmailValid || !isAddressValid || !isPaymentValid) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>