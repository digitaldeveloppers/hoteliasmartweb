<?php
/**
 * Equipment Deletion Handler
 * Processes the deletion of equipment items based on reference ID
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../Controller/equipementController.php');

// Check if reference parameter is provided
if (isset($_GET['reference'])) {
    $reference = $_GET['reference'];
    $equipementC = new equipementController();
    // Delete the equipment and redirect to list
    $equipementC->deleteEquipement($reference);
    header('Location: showEqui.php');
    exit();
} else {
    // Redirect if no reference provided
    header('Location: showEqui.php');
    exit();
}
?>