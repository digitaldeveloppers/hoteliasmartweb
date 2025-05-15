<?php
require_once(__DIR__ . '/../../Controller/commandeController.php');

$orderDetails = null;
$error = '';

if (isset($_GET['id'])) {
    $idCommande = $_GET['id'];
    $commandeController = new commandeController();

    try {
        // Assuming a method getCommandeDetails exists that fetches order details and associated equipment
        $orderDetails = $commandeController->getCommandeDetails($idCommande); 
        if (!$orderDetails) {
            $error = 'Order not found.';
        }
    } catch (Exception $e) {
        $error = 'Error fetching order details: ' . $e->getMessage();
        error_log($error);
    }
} else {
    $error = 'No Order ID provided.';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipement Dashboard - Order Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="stylesback1.css">
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
        <h1><i class="fas fa-info-circle"></i> Order Details</h1>
    </div>

    <section class="form-section">
        <div class="form-container" style="max-width: 900px;">
            <?php if ($error): ?>
                <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="commandes.php" class="submit-button" style="text-decoration: none;">Back to Orders</a>
                </div>
            <?php elseif ($orderDetails): ?>
                <h2>Order #<?php echo htmlspecialchars($orderDetails['idCommande']); ?></h2>
                
                <h3>Client Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($orderDetails['nomClient'] . ' ' . $orderDetails['prenomClient']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($orderDetails['mailClient']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($orderDetails['adresseClient']); ?></p>
                
                <h3>Order Summary</h3>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($orderDetails['date']); ?></p>
                <p><strong>Payment Mode:</strong> <?php echo htmlspecialchars($orderDetails['modePaiment']); ?></p>
                <p><strong>Total Amount:</strong> <?php echo htmlspecialchars($orderDetails['total']); ?></p>

                <h3>Equipment Ordered</h3>
                <?php if (!empty($orderDetails['equipements'])): ?>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                        <thead>
                            <tr style="background-color: #f2f2f2;">
                                <th style="padding: 10px; border: 1px solid #ddd;">Equipment Name</th>
                                <th style="padding: 10px; border: 1px solid #ddd;">Quantity</th>
                                <th style="padding: 10px; border: 1px solid #ddd;">Price per Unit</th>
                                <th style="padding: 10px; border: 1px solid #ddd;">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderDetails['equipements'] as $item): ?>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($item['nom']); ?></td>
                                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($item['quantite']); ?></td>
                                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($item['prix']); ?> €</td>
                                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($item['prix'] * $item['quantite']); ?> €</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No equipment details available for this order.</p>
                <?php endif; ?>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="commandes.php" class="submit-button" style="text-decoration: none;">Back to Orders List</a>
                </div>

            <?php endif; ?>
        </div>
    </section>

</body>
</html>