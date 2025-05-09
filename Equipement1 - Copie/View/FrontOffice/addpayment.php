<?php



include_once("../../config.php");
include_once("../../Controller/servivecontrolle.php");



$conn = config::getConnexion();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Initialize variables for the form data
    $paymentMethod = '';
    $selectedPaymentMethod = isset($_POST['payment-method']) ? $_POST['payment-method'] : '';
    $id_service = $_POST['id_service'];

    if ($selectedPaymentMethod == 'card') {
        $paymentMethod = 'Carte Bancaire';
    } elseif ($selectedPaymentMethod == 'mobile') {
        $paymentMethod = 'Paiement Mobile';
    }

    // Display the selected payment method
    if ($paymentMethod) {
        echo "<p>Mode de paiement sélectionné: $paymentMethod</p>";
    } else {
        echo "<p>Aucun mode de paiement sélectionné.</p>";
    }

    // Assuming the user is logged in, replace with actual user ID from session
    $userId = 8;
    $errorMessage = '';

    // Check if a payment method was selected
    if ($paymentMethod) {
        try {
            // Begin transaction for consistency
            $conn->beginTransaction();

            // Insert the payment record into the database
            $transactionId = insertPayment($conn, $id_service, $paymentMethod, $userId);

            // Handle Card Payment
            if ($paymentMethod == 'Carte Bancaire') {
                // Get card details from POST data
                $cardType = isset($_POST['card-type']) ? $_POST['card-type'] : '';
                $cardNumber = isset($_POST['card-number']) ? $_POST['card-number'] : '';
                $expiryDate = isset($_POST['expiry-date']) ? $_POST['expiry-date'] : '';
                
                // Format the expiry date to database format (YYYY-MM-DD)
                if (!empty($expiryDate)) {
                    $parts = explode('/', $expiryDate);
                    if (count($parts) == 2) {
                        $month = $parts[0];
                        $year = '20' . $parts[1]; // Add '20' to convert YY to YYYY
                        $lastDay = date('t', strtotime("$year-$month-01"));
                        $formattedExpiryDate = "$year-$month-$lastDay";
                    } else {
                        $formattedExpiryDate = date('Y-m-d', strtotime('+1 month'));
                    }
                } else {
                    $formattedExpiryDate = date('Y-m-d', strtotime('+1 month'));
                }

                // Insert card payment details into the database
                insertCardPayment($conn, $transactionId, $cardType, $cardNumber, $formattedExpiryDate);

                // Mark the service as paid
                updateServicePaymentStatus($conn, $id_service);
            }

            // Handle Mobile Payment
            if ($paymentMethod === 'Paiement Mobile') {
                // Get mobile payment details from POST data
                $phoneNumber = isset($_POST['phone-number']) ? $_POST['phone-number'] : '';
                $mobileProvider = isset($_POST['mobile-provider']) ? $_POST['mobile-provider'] : '';

                // Validate mobile payment details
                if (empty($phoneNumber) || empty($mobileProvider)) {
                    throw new Exception('Phone number and mobile provider are required for mobile payments.');
                }

                // Example of getting the current date and adding 1 month for expiration
                $expirationDate = date('Y-m-d', strtotime('+1 month'));

                // Insert mobile payment details into the database
                insertMobilePayment($conn, $transactionId, $mobileProvider, $phoneNumber, $expirationDate);

                // Mark the service as paid
                updateServicePaymentStatus($conn, $id_service);
            }

            // Commit the transaction if everything is successful
            $conn->commit();
            header("Location: showservive.php");
            exit();
            

        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $conn->rollback();
            $errorMessage = "Error processing the payment: " . $e->getMessage();
            echo "<div style='background-color: #f44336; color: white; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
            echo "<h3>Erreur lors du traitement du paiement</h3>";
            echo "<p>$errorMessage</p>";
            echo "</div>";
            echo "<p style='margin-top: 20px;'><a href='paiement.php' style='background-color: #4B0082; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;'><i class='fas fa-arrow-left'></i> Réessayer</a></p>";
        }
    } else {
        echo "<div style='background-color: #f44336; color: white; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
        echo "<h3>Erreur</h3>";
        echo "<p>Aucun mode de paiement sélectionné. Veuillez choisir un mode de paiement.</p>";
        echo "</div>";
        echo "<p style='margin-top: 20px;'><a href='paiement.php' style='background-color: #4B0082; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;'><i class='fas fa-arrow-left'></i> Retour au formulaire de paiement</a></p>";
    }
}
?>