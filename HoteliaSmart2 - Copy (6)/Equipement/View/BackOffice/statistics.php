<?php
require_once(__DIR__ . '/../../Controller/equipementController.php');
$equipementController = new equipementController();
$equipements = $equipementController->listEquipement();

// Count equipment by type
$typeCount = array('machine' => 0, 'material' => 0);

// Initialize price and quantity interval counters
$priceIntervals = array(
    '0-500' => 0,
    '501-1000' => 0,
    '1001-2000' => 0,
    '2000+' => 0
);

$quantityIntervals = array(
    '0-30' => 0,
    '31-60' => 0,
    '61-100' => 0,
    '100+' => 0
);

foreach($equipements as $equipement) {
    // Count by type
    $type = strtolower($equipement['type']);
    if (isset($typeCount[$type])) {
        $typeCount[$type]++;
    }

    // Count by price intervals
    $price = floatval($equipement['prix']);
    if ($price <= 500) {
        $priceIntervals['0-500']++;
    } elseif ($price <= 1000) {
        $priceIntervals['501-1000']++;
    } elseif ($price <= 2000) {
        $priceIntervals['1001-2000']++;
    } else {
        $priceIntervals['2000+']++;
    }

    // Count by quantity intervals
    $quantity = intval($equipement['quantite']);
    if ($quantity <= 30) {
        $quantityIntervals['0-30']++;
    } elseif ($quantity <= 60) {
        $quantityIntervals['31-60']++;
    } elseif ($quantity <= 100) {
        $quantityIntervals['61-100']++;
    } else {
        $quantityIntervals['100+']++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipement Dashboard - Statistics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="stylesback1.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
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
                    <li><a href="statistics.php" class="active"><i class="fas fa-chart-bar"></i> Statistics</a></li>
                    <li><a href="commandes.php"><i class="fa-solid fa-cart-shopping"></i>Orders</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-title">
        <h1><i class="fas fa-chart-bar"></i> Equipment Statistics</h1>
    </div>

    <section class="form-section" style="display: flex; flex-wrap: wrap; justify-content: center;">
        <div class="chart-container">
            <canvas id="equipmentChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="priceChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="quantityChart"></canvas>
        </div>
    </section>

    <script>
        // Equipment Type Chart
        const ctx = document.getElementById('equipmentChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Machines', 'Materials'],
                datasets: [{
                    data: [<?php echo $typeCount['machine']; ?>, <?php echo $typeCount['material']; ?>],
                    backgroundColor: ['#003087', '#FF6B6B'],
                    borderColor: ['#ffffff', '#ffffff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Equipment Distribution by Type',
                        font: { size: 16 }
                    }
                },
                layout: { padding: 10 },
                radius: '70%'
            }
        });

        // Price Intervals Chart
        const priceCtx = document.getElementById('priceChart').getContext('2d');
        new Chart(priceCtx, {
            type: 'pie',
            data: {
                labels: ['0-500', '501-1000', '1001-2000', '2000+'],
                datasets: [{
                    data: [
                        <?php echo $priceIntervals['0-500']; ?>,
                        <?php echo $priceIntervals['501-1000']; ?>,
                        <?php echo $priceIntervals['1001-2000']; ?>,
                        <?php echo $priceIntervals['2000+']; ?>
                    ],
                    backgroundColor: ['#4CAF50', '#2196F3', '#FFC107', '#9C27B0'],
                    borderColor: ['#ffffff', '#ffffff', '#ffffff', '#ffffff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Equipment Distribution by Price (DT)',
                        font: { size: 16 }
                    }
                },
                layout: { padding: 10 },
                radius: '70%'
            }
        });

        // Quantity Intervals Chart
        const quantityCtx = document.getElementById('quantityChart').getContext('2d');
        new Chart(quantityCtx, {
            type: 'pie',
            data: {
                labels: ['0-30', '31-60', '61-100', '100+'],
                datasets: [{
                    data: [
                        <?php echo $quantityIntervals['0-30']; ?>,
                        <?php echo $quantityIntervals['31-60']; ?>,
                        <?php echo $quantityIntervals['61-100']; ?>,
                        <?php echo $quantityIntervals['100+']; ?>
                    ],
                    backgroundColor: ['#E91E63', '#00BCD4', '#FF5722', '#795548'],
                    borderColor: ['#ffffff', '#ffffff', '#ffffff', '#ffffff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Equipment Distribution by Quantity',
                        font: { size: 16 }
                    }
                },
                layout: { padding: 10 },
                radius: '70%'
            }
        });
    </script>
</body>
</html>