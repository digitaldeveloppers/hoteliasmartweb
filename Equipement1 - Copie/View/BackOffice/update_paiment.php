<?php
session_start();
require_once '../../config.php';
require_once '../../Controller/servivecontrolle.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo = config::getConnexion();
    $type_paiment = isset($_POST['type_paimentc_id']) ? trim($_POST['type_paimentc_id']) : null;

    // Validate date format (YYYY-MM-DD)
    function isValidDate($date) {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
    }

    // Validate datetime format (YYYY-MM-DD HH:MM:SS)
    function isValidDatetime($datetime) {
        return preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $datetime);
    }

    if ($type_paiment == 'paimentc') {
        // Card payment
        $song_paimentc_id = trim($_POST['song_paimentc_id'] ?? '');
        $numero_carte = trim($_POST['edit-paimentc-numero'] ?? '');
        $type_carte = trim($_POST['edit-paimentc-methode'] ?? '');
        $date_expiration = trim($_POST['edit-paimentc-title'] ?? '');

        if (empty($song_paimentc_id) || empty($numero_carte) || empty($type_carte) || empty($date_expiration)) {
            $_SESSION['message'] = "All fields are required!";
            header("Location: showserv.php?error=true");
            exit();
        }

        try {
            $query = "UPDATE paiment_par_card 
                      SET Numero_Carte = :numero_carte, 
                          Type_Carte = :type_carte, 
                          Date_Expiration = :date_expiration
                      WHERE ID = :id";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':numero_carte', $numero_carte);
            $stmt->bindParam(':type_carte', $type_carte);
            $stmt->bindParam(':date_expiration', $date_expiration);
            $stmt->bindParam(':id', $song_paimentc_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Card transaction updated successfully!";
                header("Location: showserv.php?updated=true");
                exit();
            } else {
                $_SESSION['message'] = "Failed to update the card transaction.";
                header("Location: showserv.php?error=true");
                exit();
            }
        } catch (PDOException $e) {
            error_log("Card Update Error: " . $e->getMessage());
            $_SESSION['message'] = "Internal error occurred.";
            header("Location: showserv.php?error=true");
            exit();
        }

    } elseif ($type_paiment == 'paiment_mobile') {
        // Mobile payment
        $id = trim($_POST['song_paimentmobile_id'] ?? '');
        $numero = trim($_POST['edit-mobile-numero'] ?? '');
        $provider = trim($_POST['edit-mobile-methode'] ?? '');
        $expiration = trim($_POST['edit-mobile'] ?? '');

        if (empty($id) || empty($numero) || empty($provider) || empty($expiration)) {
            $_SESSION['message'] = "All fields are required!";
            header("Location: showserv.php?error=true");
            exit();
        }

        try {
            $query = "UPDATE paiment_mobile 
                      SET phone_number = :numero, 
                          mobile_provider = :provider, 
                          date_expiration = :expiration
                      WHERE ID = :id";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':numero', $numero);
            $stmt->bindParam(':provider', $provider);
            $stmt->bindParam(':expiration', $expiration);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Mobile transaction updated successfully!";
                header("Location: showserv.php?updated=true");
                exit();
            } else {
                $_SESSION['message'] = "Failed to update the mobile transaction.";
                header("Location: showserv.php?error=true");
                exit();
            }
        } catch (PDOException $e) {
            error_log("Mobile Update Error: " . $e->getMessage());
            $_SESSION['message'] = "Internal error occurred.";
            header("Location: showserv.php?error=true");
            exit();
        }

    } else {
        // General payment
        $song_id = trim($_POST['song_id'] ?? '');
        $date_paiement = trim($_POST['edit-paiment-title'] ?? '');

        if (empty($song_id) || empty($date_paiement)) {
            $_SESSION['message'] = "All fields are required!";
            header("Location: showserv.php?error=true");
            exit();
        }

        if (!isValidDate($date_paiement)) {
            $_SESSION['message'] = "Invalid date format! The date should be in YYYY-MM-DD format.";
            header("Location: showserv.php?error=true");
            exit();
        }

        try {
            $query = "UPDATE payment 
                      SET payment_date = :date_paiement
                      WHERE ID = :id";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':date_paiement', $date_paiement);
            $stmt->bindParam(':id', $song_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Payment updated successfully!";
                header("Location: showserv.php?updated=true");
                exit();
            } else {
                $_SESSION['message'] = "Failed to update the payment.";
                header("Location: showserv.php?error=true");
                exit();
            }
        } catch (PDOException $e) {
            error_log("Payment Update Error: " . $e->getMessage());
            $_SESSION['message'] = "Internal error occurred.";
            header("Location: showserv.php?error=true");
            exit();
        }
    }

} else {
    echo "Invalid request method!";
}
?>
