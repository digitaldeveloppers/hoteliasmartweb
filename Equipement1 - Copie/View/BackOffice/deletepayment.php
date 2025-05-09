<?php 
session_start();
include_once '../../config.php';
include_once '../../Controller/servivecontrolle.php';


if (isset($_GET['song_id']) && isset($_GET['type'])) {
    $pdo = config::getConnexion();
    $song_id = $_GET['song_id'];
    $type = $_GET['type'];

    $success = false;

    if ($type == 'paiments') {
        $success = deleteFromPayment($pdo, $song_id);
    } elseif ($type == 'paimentc') {
        $success = deleteFromPaymentCard($pdo, $song_id);
    } else {
        $success = deleteFromPaymentMobile($pdo, $song_id);
    }

    if ($success) {
        $_SESSION['message'] = "Paiement supprimé avec succès !";
        header("Location: showserv.php?deleted=true");
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression du paiement.";
        header("Location: showserv.php?error=true");
    }
    exit;

} else {
    $_SESSION['message'] = "Erreur: ID ou type de paiement manquant.";
    header("Location: showserv.php?error=true");
    exit;
}
?>