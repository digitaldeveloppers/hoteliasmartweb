<?php
require_once '../../../controllers/ArticleController.php';

// Récupérer les statistiques par catégorie
$articleController = new ArticleController();
$stats = $articleController->getStatsByCategory();

if (empty($stats)) {
    echo '<div class="alert alert-warning">Aucune donnée statistique disponible. Veuillez vérifier la base de données.</div>';
    exit;
}

// Préparer les données pour les graphiques
$categories = [];
$counts = [];
foreach ($stats as $stat) {
    if (isset($stat['categorie']) && isset($stat['count'])) {
        $categories[] = $stat['categorie'];
        $counts[] = $stat['count'];
    }
}

// Calculer le total des articles
$totalArticles = array_sum($counts);

// Calculer les pourcentages
$percentages = array_map(function($count) use ($totalArticles) {
    return round(($count / $totalArticles) * 100, 1);
}, $counts);
?>

<?php
// Start output buffering to capture content for the layout
ob_start();
?>
<div class="content-wrapper">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Statistiques des Articles</h4>
            <ul class="breadcrumbs">
                <li class="nav-home">
                    <a href="index.php">
                        <i class="flaticon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Statistiques</a>
                </li>
            </ul>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Distribution des Articles par Catégorie</h4>
                    </div>
                    <div class="card-body">
                        <div style="width: 100%; max-width: 600px; margin: 0 auto;">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('pieChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($categories); ?>,
                datasets: [{
                    data: <?php echo json_encode($percentages); ?>,
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>
<?php
$pageContent = ob_get_clean();
require 'layout.php';
?>