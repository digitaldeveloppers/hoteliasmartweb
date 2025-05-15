<?php
require_once(__DIR__ . '/../../Controller/commandeController.php');

if (isset($_GET['id'])) {
    $idCommande = $_GET['id'];
    $commandeController = new commandeController();

    // Attempt to delete the order
    try {
        $commandeController->deleteCommande($idCommande); // Assuming a deleteCommande method exists in the controller
        // Redirect back to the orders list with a success message (optional)
        header('Location: commandes.php?status=deleted');
        exit;
    } catch (Exception $e) {
        // Handle error, maybe redirect with an error message
        error_log('Error deleting commande: ' . $e->getMessage());
        header('Location: commandes.php?status=error');
        exit;
    }
} else {
    // Redirect back if no ID is provided
    header('Location: commandes.php');
    exit;
}
?>