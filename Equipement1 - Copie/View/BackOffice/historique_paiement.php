<?php
include_once '../../config.php';
include_once '../../Controller/servivecontrolle.php';

$pdo = config::getConnexion();
$paiements = affichagePaiement();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Paiements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .back-btn {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 500;
        }

        .status-badge.success {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .status-badge.pending {
            background-color: #fff3e0;
            color: #f57c00;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Historique des Paiements</h1>
            <a href="showserv.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Service</th>
                    <th>Date de Paiement</th>
                    <th>Montant</th>
                    <th>Méthode de Paiement</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $count = 1;
                foreach ($paiements as $paiement): 
                    $service = null;
                    if (isset($paiement['service_id'])) {
                        $service = getServiceById($pdo, $paiement['service_id']);
                    }
                ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= $service ? htmlspecialchars($service['title']) : 'N/A' ?></td>
                        <td>
                            <?php 
                            if (isset($paiement['payment_date'])) {
                                $payment_date = new DateTime($paiement['payment_date']);
                                echo $payment_date->format('d-m-Y');
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td><?= $service ? htmlspecialchars($service['price']) . ' DT' : 'N/A' ?></td>
                        <td><?= isset($paiement['payment_method']) ? htmlspecialchars($paiement['payment_method']) : 'N/A' ?></td>
                        <td>
                            <span class="status-badge <?= isset($paiement['status']) && $paiement['status'] == 'completed' ? 'success' : 'pending' ?>">
                                <?= isset($paiement['status']) ? ucfirst($paiement['status']) : 'En attente' ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>