<?php
include_once '../../config.php';
include_once '../../Controller/servivecontrolle.php';

$pdo = config::getConnexion();
$services = afficherServices($pdo);

// Calcul des statistiques des services
$serviceStats = [];
$totalServices = count($services);

// Première boucle pour calculer les totaux par titre de service
foreach ($services as $service) {
    $title = trim($service['title']); // Remove any extra whitespace
    $titleKey = strtolower($title); // Create a case-insensitive key
    
    if (!isset($serviceStats[$titleKey])) {
        $serviceStats[$titleKey] = [
            'category_name' => $title, // Keep original title for display
            'count' => 0,
            'total_quantity' => 0,
            'percentage' => 0
        ];
    }
    $serviceStats[$titleKey]['count']++;
    $serviceStats[$titleKey]['total_quantity'] += $service['quantity'];
}

// Calculer les pourcentages
foreach ($serviceStats as &$stats) {
    $stats['percentage'] = ($stats['count'] / $totalServices) * 100;
}

// Trier les services par nom pour un affichage cohérent
ksort($serviceStats);

$paiements = affichagePaiement();
$paiementsCarts = affichagePaiementscart();
$paiementsMobile = affichagePaiementsMobile();



// Build an array of lines you want in the PDF
$lines = [];
$lines[] = sprintf("%-4s %-20s %-8s %-8s %s", "#", "Title", "Qty", "Price", "Description");
$count = 1;
foreach ($services as $s) {
    $lines[] = sprintf(
        "%-4d %-20.20s %-8d %-8.2f %s",
        $count++,
        $s['title'],
        $s['quantity'],
        $s['price'],
        $s['description']
    );
}



?>

<?php
$pageTitle = 'Services List';
ob_start();
?>
<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 5px;
}

.close-modal {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close-modal:hover {
    color: black;
}

.form-group {
    margin-bottom: 15px;
}

.form-footer {
    text-align: right;
    margin-top: 20px;
}

.btn {
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    border: none;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    border: none;
}
</style>
<div class="services-container">
<style>
.services-container {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.services-header {
    margin-bottom: 1.5rem;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

th {
    background-color: #4CAF50;
    color: white;
    padding: 1rem;
    text-align: left;
}

td {
    padding: 1rem;
    border-bottom: 1px solid #eee;
}

tr:hover {
    background-color: #f5f5f5;
}

.action-btn {
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-decoration: none;
    color: white;
    font-size: 0.875rem;
    margin-right: 0.5rem;
    display: inline-block;
}

.edit-btn {
    background-color: #2196F3;
}

.delete-btn {
    background-color: #f44336;
}

.tab-content {
  display: none; /* Hide all contents by default */
}

.tab-content.active {
  display: block; /* Show active content */
}

.tab-link.active {
  background-color: #3498db; /* Highlight active tab */
  color: white;  /* Optional: change text color of the active tab */
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}
.song-table {
    width: 100%;
    border-collapse: collapse;
}

.song-table th {
    text-align: left;
    padding: 10px;
    color: var(--spotify-light-gray);
    font-weight: 500;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.song-table td {
    padding: 15px 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.song-table tr:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.song-cover {
    width: 40px;
    height: 40px;
    border-radius: 4px;
    overflow: hidden;
}

.song-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.action-btn {
    background: transparent;
    border: none;
    color: var(--spotify-light-gray);
    cursor: pointer;
    margin-right: 5px;
    font-size: 16px;
    transition: color 0.3s;
}

.action-btn:hover {
    color: var(--spotify-white);
}
.highlight {
    background-color: #c8e6c9; /* light green */
  }


</style>

<div class="services-header">
    <h2>Services Management</h2>
    <div style="text-align: right; margin-bottom: 20px;">
        <a href="historique_paiement.php" class="action-btn history-btn" style="margin-right: 10px;">
            <i class="fas fa-history"></i> Historique de paiement
        </a>
        <a href="export_pdf.php" class="action-btn export-btn">
            <i class="fas fa-file-pdf"></i> Exporter en PDF
        </a>
</div>

<h4>Tri par:</h4>
<select id="sort">
    <option value="choisir">choisir</option>
    <option value="quantity">quantity</option>
    <option value="price">price</option>
</select>


</div>

<?php if ($services): ?>
    <table border='1' cellpadding='10' cellspacing='0'>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>

        <?php $count = 1; foreach ($services as $service): ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= htmlspecialchars($service['title']) ?></td>
                <td><?= htmlspecialchars($service['quantity']) ?></td>
                <td><?= htmlspecialchars($service['price']) ?></td>
                <td><?= htmlspecialchars($service['description']) ?></td>
                <td>
                    <a href="#" 
                       class="action-btn edit-btn"
                       data-id="<?= $service['id'] ?>" 
                       data-title="<?= htmlspecialchars($service['title']) ?>"
                       data-quantity="<?= htmlspecialchars($service['quantity']) ?>"
                       data-price="<?= htmlspecialchars($service['price']) ?>"
                       data-description="<?= htmlspecialchars($service['description']) ?>">
                        Modifier
                    </a>
                    <a class="action-btn delete-btn" 
                       href="deleteserv.php?id=<?= $service['id'] ?>" 
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce service ?');">
                        Supprimer
                    </a>
                </td>
            </tr>
        <?php $count++; endforeach; ?>
    </table>
<?php else: ?>
    <p>Aucun service trouvé.</p>
<?php endif; ?>

<!-- Section des statistiques -->
<div class="chart-container" style="margin-top: 20px; padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); height: 400px; width: 100%; position: relative;">
    <canvas id="servicesChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('servicesChart').getContext('2d');
    const serviceData = <?php echo json_encode($serviceStats); ?>;
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: Object.values(serviceData).map(stat => stat.category_name),
            datasets: [{
                data: Object.values(serviceData).map(stat => stat.percentage),
                backgroundColor: [
                    '#2196F3',  // Bleu
                    '#4CAF50',  // Vert
                    '#607D8B',  // Gris foncé
                    '#FFC107',  // Jaune
                    '#9C27B0',  // Violet
                    '#E91E63'   // Rose
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1,
            layout: {
                padding: {
                    left: 50,
                    right: 50,
                    top: 20,
                    bottom: 20
                }
            },
            plugins: {
                datalabels: {
                    display: false  // Désactive les labels sur le camembert
                },
                legend: {
                    position: 'right',
                    align: 'center',
                    labels: {
                        padding: 20,
                        font: {
                            size: 12
                        },
                        usePointStyle: true,
                        boxWidth: 10
                    }
                },
                title: {
                    display: true,
                    text: 'Distribution des Services par Catégorie (%)',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: {
                        top: 10,
                        bottom: 30
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    // Gestion des boutons Edit
    const editButtons = document.querySelectorAll('.edit-paiment-song');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const date = this.getAttribute('data-date');
            const methode = this.getAttribute('data-methode');
            
            // Afficher le modal d'édition
            const modal = document.getElementById('edit-service-modal');
            modal.style.display = 'block';
            
            // Remplir le formulaire avec les données
            document.getElementById('service_id').value = id;
            document.getElementById('edit-date').value = date;
            document.getElementById('edit-methode').value = methode;
        });
    });

    // Gestion des boutons Delete
    const deleteButtons = document.querySelectorAll('.delete-song');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')) {
                const id = this.getAttribute('data-id');
                const type = this.getAttribute('data-type');
                window.location.href = `deleteserv.php?id=${id}&type=${type}`;
            }
        });
    });

    // Fermer le modal
    document.querySelector('.close-modal').addEventListener('click', function() {
        document.getElementById('edit-service-modal').style.display = 'none';
    });

    // Fermer le modal avec le bouton Annuler
    document.getElementById('cancel-edit').addEventListener('click', function() {
        document.getElementById('edit-service-modal').style.display = 'none';
    });
});
</script>

<!-- EDIT FORM MODAL -->
<form action="updateserve.php" method="POST" enctype="multipart/form-data" id="edit-form" style="background-color:white;">
    <div id="edit-service-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Modifier Service</div>
                <button type="button" class="close-modal">&times;</button>
            </div>

            <input type="hidden" name="service_id" id="service_id"> 

            <div class="form-group">
                <label for="edit-title">Titre</label>
                <input type="text" id="edit-title" name="edit-title" class="form-control" required readonly>
            </div>

            <div class="form-group">
                <label for="edit-quantity">Quantité</label>
                <input type="number" id="edit-quantity" name="edit-quantity" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="edit-price">Prix</label>
                <input type="number" id="edit-price" name="edit-price" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="edit-description">Description</label>
                <textarea id="edit-description" name="edit-description" class="form-control" require readonly></textarea>
            </div>

            <div class="form-footer">
                <button type="button" class="btn btn-secondary" id="cancel-edit">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </div>
    </div>
</form>


<script src="back.js"></script>
</div>

<div class="services-container" style="margin:20px 0px 0px 0px;">
        <div id="paiment" class="tab-content active">
            <div class="tabs">
                <button class="tab-link active" data-tab="paiment">Tous les paiements</button>
                <button class="tab-link" data-tab="paimentc">Paiement carte</button>
                <button class="tab-link" data-tab="paiement_mobile">Paiement mobile</button>
            </div>
            <br>
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">Tous les paiements</div>
                </div>
            <br>
            <div class="search-container">
                <input type="text" placeholder="Rechercher..." class="search-input">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                </div>

                <style>
                .search-container {
                position: relative;
                width: 300px;
                margin: 20px;
                }

                .search-input {
                width: 100%;
                padding: 12px 20px 12px 40px;
                border: 2px solid #e0e0e0;
                border-radius: 30px;
                font-size: 16px;
                outline: none;
                transition: all 0.3s ease;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                }

                .search-input:focus {
                border-color: #4a90e2;
                box-shadow: 0 2px 10px rgba(74, 144, 226, 0.2);
                }

                .search-icon {
                position: absolute;
                left: 15px;
                top: 50%;
                transform: translateY(-50%);
                width: 18px;
                height: 18px;
                color: #999;
                }

                .search-input:focus + .search-icon {
                color: #4a90e2;
                }
                </style>
            <table class="song-table" id="song-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>User ID</th>
            <th>Méthode de paiement</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php $counter = 1; foreach ($paiements as $paiment): ?>
            <tr>
                <td><?php echo $counter++; ?></td>
                <td>
                    <?php 
                    // Convert the payment date to dd-mm-yyyy format
                    $payment_date = new DateTime($paiment['payment_date']);
                    echo $payment_date->format('d-m-Y');
                    ?>
                </td>
                <td><?php echo htmlspecialchars($paiment['user_id']); ?></td>
                <td><?php echo htmlspecialchars($paiment['payment_method']); ?></td>
                
                    <button class="btn btn-secondary edit-paiment-song" data-id="<?= $paiment['ID']; ?>" 
                                                                    data-type="paiments"
                                                                    data-date="<?php echo htmlspecialchars($paiment['payment_date']); ?>"
                                                                    data-methode="<?php echo htmlspecialchars($paiment['payment_method']); ?>"
                                                                >
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button type="submit" class="btn btn-danger delete-song" id="supprimer" data-id="<?= $paiment['ID']; ?>" 
                                                                                                                data-type="paiments"
                                                                                                                >
                        <input type="text" class="type_c" hidden>
                        <i class="fas fa-trash"></i> Delete
                    </button>
                
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

        </table>
            </div>
    </div>

    <!-- Paiement par carte -->
    <div id="paimentc" class="tab-content">
        <div class="tabs">
            <button class="tab-link active" data-tab="paiment">Tous les paiements</button>
            <button class="tab-link" data-tab="paimentc">Paiement carte</button>
            <button class="tab-link" data-tab="paiement_mobile">Paiement mobile</button>
        </div>
        <br>
        <div class="content-section">
            <div class="section-header">
                <div class="section-title">Paiement par carte</div>
            </div>
            <div class="search-container">
                <input type="text" placeholder="Rechercher..." class="search-input">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                </div>

                <style>
                .search-container {
                position: relative;
                width: 300px;
                margin: 20px;
                }

                .search-input {
                width: 100%;
                padding: 12px 20px 12px 40px;
                border: 2px solid #e0e0e0;
                border-radius: 30px;
                font-size: 16px;
                outline: none;
                transition: all 0.3s ease;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                }

                .search-input:focus {
                border-color: #4a90e2;
                box-shadow: 0 2px 10px rgba(74, 144, 226, 0.2);
                }

                .search-icon {
                position: absolute;
                left: 15px;
                top: 50%;
                transform: translateY(-50%);
                width: 18px;
                height: 18px;
                color: #999;
                }

                .search-input:focus + .search-icon {
                color: #4a90e2;
                }
                </style>
                <br>
            <table class="song-table" id="song-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type de carte</th>
                        <th>Numéro de carte</th>
                        <th>Date d'expiration</th>
                        <th>ID de transaction</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1; foreach ($paiementsCarts as $paimentc): ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo htmlspecialchars($paimentc['Type_Carte']); ?></td>
                            <td><?php echo htmlspecialchars($paimentc['Numero_Carte']); ?></td>
                            <td><?php echo htmlspecialchars($paimentc['Date_Expiration']); ?></td>
                            <td><?php echo htmlspecialchars($paimentc['Transaction_id']); ?></td>

                            
                                <button class="btn btn-secondary edit-paimentc-song" data-id="<?= $paimentc['ID']; ?>" 
                                                                data-type="paimentc"
                                                                data-carte="<?php echo htmlspecialchars($paimentc['Type_Carte']); ?>"
                                                                data-numero="<?php echo htmlspecialchars($paimentc['Numero_Carte']); ?>"
                                                                data-expiration="<?php echo htmlspecialchars($paimentc['Date_Expiration']); ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="submit" class="btn btn-danger delete-song" id="supprimer" data-id="<?= $paimentc['ID']; ?>" 
                                                                                                        data-type="paimentc">
                                    <input type="text" class="type_c" hidden>
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>



    <!-- Paiement par mobile -->
    <div id="paiement_mobile" class="tab-content">
            <div class="tabs">
                <button class="tab-link active" data-tab="paiment">Tous les paiements</button>
                <button class="tab-link" data-tab="paimentc">Paiement carte</button>
                <button class="tab-link" data-tab="paiement_mobile">Paiement mobile</button>
            </div>
            <br>
        <div class="content-section">
            <div class="section-header">
                <div class="section-title">Paiement par Mobile</div>
            </div>
            <br>
            <table class="song-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fournisseur</th>
                        <th>Numéro de téléphone</th>
                        <th>Date d'expiration</th>
                        <th>Transaction ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <div class="search-container">
                <input type="text" placeholder="Rechercher..." class="search-input">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                </div>

                <style>
                .search-container {
                position: relative;
                width: 300px;
                margin: 20px;
                }

                .search-input {
                width: 100%;
                padding: 12px 20px 12px 40px;
                border: 2px solid #e0e0e0;
                border-radius: 30px;
                font-size: 16px;
                outline: none;
                transition: all 0.3s ease;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                }

                .search-input:focus {
                border-color: #4a90e2;
                box-shadow: 0 2px 10px rgba(74, 144, 226, 0.2);
                }

                .search-icon {
                position: absolute;
                left: 15px;
                top: 50%;
                transform: translateY(-50%);
                width: 18px;
                height: 18px;
                color: #999;
                }

                .search-input:focus + .search-icon {
                color: #4a90e2;
                }
                </style>
                <table class="song-table" id="song-table">
                <tbody>
                    <?php $counter = 1; foreach ($paiementsMobile as $paiment): ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo htmlspecialchars($paiment['mobile_provider']); ?></td>
                            <td><?php echo htmlspecialchars($paiment['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($paiment['Date_Expiration']); ?></td>
                            <td><?php echo htmlspecialchars($paiment['transaction_id']); ?></td>
                            
                                <button class="btn btn-secondary edit-paimentmobile-song" data-id="<?= $paiment['ID']; ?>" 
                                                                data-type="paiment_mobile"
                                                                data-provider="<?php echo htmlspecialchars($paiment['mobile_provider']); ?>"
                                                                data-numero="<?php echo htmlspecialchars($paiment['phone_number']); ?>"
                                                                date_exp="<?php echo htmlspecialchars($paiment['Date_Expiration']); ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="submit" class="btn btn-danger delete-song" id="supprimer" data-id="<?= $paiment['ID']; ?>" 
                                                                                                        data-type="paiment_mobile">
                                    <input type="text" class="type_c" hidden>
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                           
                            
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
</div>
<div id="delete-modal" class="modal">
        <div class="modal-content" style="width: 400px;">
            <div class="modal-header">
                <div class="modal-title">Confirm Delete</div>
                <button class="close-modal">&times;</button>
            </div>
            <p style="margin-bottom: 20px;">Are you sure you want to delete this song? This action cannot be undone.</p>
            <input type="hidden" id="delete-song-id">
            <div class="form-footer">
                <button class="btn btn-secondary" id="cancel-delete">Cancel</button>
                <button class="btn btn-primary" style="background-color: #e74c3c;" id="confirm-delete">Delete</button>
            </div>
        </div>
</div>
<form action="update_paiment.php" method="POST" enctype="multipart/form-data" id="edit-paiment-form">
        <div id="edit-paiment-modal" class="modal" >
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Edit Paiment</div>
                    <button type="button" class="close-modal">&times;</button>
                </div>
                <input type="text" name="song_id" id="song_paiment_id"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-date-title">Date Paiment</label>
                    <input type="date" id="edit-date-title" name="edit-paiment-title" class="form-control" placeholder="Enter song title" value="">
                </div>
                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-paiment-edit">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-paiment-edit">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
    <form action="update_paiment.php" method="POST" enctype="multipart/form-data" id="edit-paimentc-form">
        <div id="edit-paimentc-modal" class="modal" >
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Edit Paiment</div>
                    <button type="button" class="close-modal">&times;</button>
                </div>
                <input type="hidden" name="song_paimentc_id" id="song_paimentc_id"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-carte-title">Numero carte</label>
                    <input type="text" id="edit-carte-title" name="edit-paimentc-numero" class="form-control" placeholder="Enter song title" value="">
                    <div id="song-title-error-edit" class="error-message" style="color: red; display: none;"></div>
                </div>
                <div class="form-group">
                    <label for="edit-carte-type">type carte</label>
                    <select id="edit-carte-type" name="edit-paimentc-methode" class="form-control" required>
                        <option value="visa">visa</option>
                        <option value="mastercard">MasterCard</option>
                        <option value="amex">American Express</option>
                    </select>
                </div>
                <input type="hidden" name="type_paimentc_id" id="type_paimentc"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-date-title">date expiration</label>
                    <input type="text" id="edit-datec-title" name="edit-paimentc-title" class="form-control" placeholder="Enter song title" value="">
                </div>
                <input type="hidden" id="edit-type-paiment" name="edit-type-paiment">
                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-paimentc-edit">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-paimentc-edit">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
    <form action="update_paiment.php" method="POST" enctype="multipart/form-data" id="edit-paimentmobile-form">
        <div id="edit-paimentmobile-modal" class="modal" >
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Edit Paiment</div>
                    <button type="button" class="close-modal">&times;</button>
                </div>
                <input type="hidden" name="song_paimentmobile_id" id="song_paimentmobile_id"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-mobile-title">phone numero</label>
                    <input type="text" id="edit-mobile-title" name="edit-mobile-numero" class="form-control" placeholder="Enter song title" value="">
                    <div id="song-title-error-edit" class="error-message" style="color: red; display: none;"></div>
                </div>
                <div class="form-group">
                    <label for="edit-mobile-type">Mobile Provider</label>
                    <select id="edit-mobile-type" name="edit-mobile-methode" class="form-control" required>
                        <option value="" disabled selected>Choose mobile provider</option>
                        <option value="ooredoo">Ooredoo</option>
                        <option value="orange">Orange</option>
                        <option value="telecom">Tunisie Télécom</option>
                    </select>
                </div>

                <input type="hidden" name="type_paimentc_id" id="type_mobile"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-mobile-date">date expiration</label>
                    <input type="text" id="edit-mobile" name="edit-mobile" class="form-control" placeholder="Enter song title" value="">
                </div>
                <input type="hidden" id="edit-type-mobile" name="edit-type-mobile">
                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-mobile-edit">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-mobile-edit">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
<!-- Ajouter cette section après la table des services -->
<div class="statistics-container" style="margin-top: 30px; padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3>Statistiques des Services</h3>
    
    <!-- Graphique -->
    <div style="margin: 20px 0;">
        <canvas id="servicesChart" width="400" height="200"></canvas>
    </div>

    <!-- Tableau des statistiques -->
    <table class="song-table">
        <thead>
            <tr>
                <th>Catégorie de Service</th>
                <th>Nombre de Services</th>
                <th>Quantité Totale</th>
                <th>Pourcentage</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($serviceStats as $categoryId => $stats): ?>
            <tr>
                <td><?php echo htmlspecialchars($stats['category_name']); ?></td>
                <td><?php echo $stats['count']; ?></td>
                <td><?php echo $stats['total_quantity']; ?></td>
                <td><?php echo number_format($stats['percentage'], 2); ?>%</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Ajout de Chart.js pour le graphique -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('servicesChart').getContext('2d');
    const serviceData = <?php echo json_encode($serviceStats); ?>;
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: Object.values(serviceData).map(stat => stat.category_name),
            datasets: [{
                data: Object.values(serviceData).map(stat => stat.percentage),
                backgroundColor: [
                    '#2196F3',  // Bleu
                    '#4CAF50',  // Vert
                    '#607D8B',  // Gris foncé
                    '#FFC107',  // Jaune
                    '#9C27B0',  // Violet
                    '#E91E63'   // Rose
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1,
            layout: {
                padding: {
                    left: 50,
                    right: 50,
                    top: 20,
                    bottom: 20
                }
            },
            plugins: {
                datalabels: {
                    display: false  // Désactive les labels sur le camembert
                },
                legend: {
                    position: 'right',
                    align: 'center',
                    labels: {
                        padding: 20,
                        font: {
                            size: 12
                        },
                        usePointStyle: true,
                        boxWidth: 10
                    }
                },
                title: {
                    display: true,
                    text: 'Distribution des Services par Catégorie (%)',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: {
                        top: 10,
                        bottom: 30
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    // Gestion des boutons Edit
    const editButtons = document.querySelectorAll('.edit-paiment-song');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const date = this.getAttribute('data-date');
            const methode = this.getAttribute('data-methode');
            
            // Afficher le modal d'édition
            const modal = document.getElementById('edit-service-modal');
            modal.style.display = 'block';
            
            // Remplir le formulaire avec les données
            document.getElementById('service_id').value = id;
            document.getElementById('edit-date').value = date;
            document.getElementById('edit-methode').value = methode;
        });
    });

    // Gestion des boutons Delete
    const deleteButtons = document.querySelectorAll('.delete-song');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')) {
                const id = this.getAttribute('data-id');
                const type = this.getAttribute('data-type');
                window.location.href = `deleteserv.php?id=${id}&type=${type}`;
            }
        });
    });

    // Fermer le modal
    document.querySelector('.close-modal').addEventListener('click', function() {
        document.getElementById('edit-service-modal').style.display = 'none';
    });

    // Fermer le modal avec le bouton Annuler
    document.getElementById('cancel-edit').addEventListener('click', function() {
        document.getElementById('edit-service-modal').style.display = 'none';
    });
});
</script>
</div>


</div>
<div id="delete-modal" class="modal">
        <div class="modal-content" style="width: 400px;">
            <div class="modal-header">
                <div class="modal-title">Confirm Delete</div>
                <button class="close-modal">&times;</button>
            </div>
            <p style="margin-bottom: 20px;">Are you sure you want to delete this song? This action cannot be undone.</p>
            <input type="hidden" id="delete-song-id">
            <div class="form-footer">
                <button class="btn btn-secondary" id="cancel-delete">Cancel</button>
                <button class="btn btn-primary" style="background-color: #e74c3c;" id="confirm-delete">Delete</button>
            </div>
        </div>
</div>
<form action="update_paiment.php" method="POST" enctype="multipart/form-data" id="edit-paiment-form">
        <div id="edit-paiment-modal" class="modal" >
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Edit Paiment</div>
                    <button type="button" class="close-modal">&times;</button>
                </div>
                <input type="text" name="song_id" id="song_paiment_id"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-date-title">Date Paiment</label>
                    <input type="date" id="edit-date-title" name="edit-paiment-title" class="form-control" placeholder="Enter song title" value="">
                </div>
                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-paiment-edit">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-paiment-edit">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
    <form action="update_paiment.php" method="POST" enctype="multipart/form-data" id="edit-paimentc-form">
        <div id="edit-paimentc-modal" class="modal" >
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Edit Paiment</div>
                    <button type="button" class="close-modal">&times;</button>
                </div>
                <input type="hidden" name="song_paimentc_id" id="song_paimentc_id"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-carte-title">Numero carte</label>
                    <input type="text" id="edit-carte-title" name="edit-paimentc-numero" class="form-control" placeholder="Enter song title" value="">
                    <div id="song-title-error-edit" class="error-message" style="color: red; display: none;"></div>
                </div>
                <div class="form-group">
                    <label for="edit-carte-type">type carte</label>
                    <select id="edit-carte-type" name="edit-paimentc-methode" class="form-control" required>
                        <option value="visa">visa</option>
                        <option value="mastercard">MasterCard</option>
                        <option value="amex">American Express</option>
                    </select>
                </div>
                <input type="hidden" name="type_paimentc_id" id="type_paimentc"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-date-title">date expiration</label>
                    <input type="text" id="edit-datec-title" name="edit-paimentc-title" class="form-control" placeholder="Enter song title" value="">
                </div>
                <input type="hidden" id="edit-type-paiment" name="edit-type-paiment">
                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-paimentc-edit">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-paimentc-edit">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
    <form action="update_paiment.php" method="POST" enctype="multipart/form-data" id="edit-paimentmobile-form">
        <div id="edit-paimentmobile-modal" class="modal" >
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Edit Paiment</div>
                    <button type="button" class="close-modal">&times;</button>
                </div>
                <input type="hidden" name="song_paimentmobile_id" id="song_paimentmobile_id"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-mobile-title">phone numero</label>
                    <input type="text" id="edit-mobile-title" name="edit-mobile-numero" class="form-control" placeholder="Enter song title" value="">
                    <div id="song-title-error-edit" class="error-message" style="color: red; display: none;"></div>
                </div>
                <div class="form-group">
                    <label for="edit-mobile-type">Mobile Provider</label>
                    <select id="edit-mobile-type" name="edit-mobile-methode" class="form-control" required>
                        <option value="" disabled selected>Choose mobile provider</option>
                        <option value="ooredoo">Ooredoo</option>
                        <option value="orange">Orange</option>
                        <option value="telecom">Tunisie Télécom</option>
                    </select>
                </div>

                <input type="hidden" name="type_paimentc_id" id="type_mobile"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-mobile-date">date expiration</label>
                    <input type="text" id="edit-mobile" name="edit-mobile" class="form-control" placeholder="Enter song title" value="">
                </div>
                <input type="hidden" id="edit-type-mobile" name="edit-type-mobile">
                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-mobile-edit">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-mobile-edit">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
<?php
$content = ob_get_clean();
require_once 'template/base.php';
?>
