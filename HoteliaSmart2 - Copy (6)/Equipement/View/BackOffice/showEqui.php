<?php
/**
 * Equipment List Display Page
 * Shows all equipment items in a tabular format with management options
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../../Controller/equipementController.php');

// Fetch all equipment items
$equipementC = new equipementController();
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'featured';
$listeEquipements = $equipementC->listEquipement($sort);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipement Dashboard</title>
    <!-- External CSS and Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="stylesback1.css">
</head>
<body>
    <!-- Admin Header with Navigation -->
    <header class="admin-header">
        <div class="admin-nav-container">
            <div class="admin-logo">
                <img src="../FrontOffice/assets/HS.png" alt="Hotelia Smart Logo">
                <span class="hotelia">HOTELIA</span>
                <span class="smart">SMART</span>
            </div>
            <nav class="admin-nav">
                <ul>
                <li><a href="addEqui.php" ><i class="fas fa-plus-circle"></i> New Equipement</a></li>
                <li><a href="showEqui.php"><i class="fas fa-list"></i> Equipements</a></li>
                <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistics</a></li>
                <li><a href="commandes.php"><i class="fa-solid fa-cart-shopping"></i>Orders</a></li>
                <li><button id="darkModeToggle" style="background: none; border: none; color: inherit; cursor: pointer;"><i class="fas fa-moon"></i> Dark Mode</button></li>
            </nav>
        </div>
    </header>

    <!-- Page Title -->
    <div class="admin-title">
        <h1><i class="fas fa-clipboard-list"></i> Equipements Managements</h1>
    </div>

    <!-- Equipment List Section -->
    <section class="form-section">
        <div class="form-container" style="max-width: 1200px;">
            <h2>Equipements list</h2>
            <!-- Action Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px; flex-wrap: wrap;">
                <div class="search-container" style="flex: 1; min-width: 220px; position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #666;"></i>
                    <input type="text" id="searchInput" placeholder="Search equipment..." style="width: 250px; padding: 10px 35px; border: 1px solid #ddd; border-radius: 4px; height: 40px; border-color: #003087;">
                    <i class="fas fa-times" id="clearSearch" style="position: absolute; right: 40px; top: 50%; transform: translateY(-50%); color: #666; cursor: pointer; display: none;"></i>
                    <i class="fas fa-microphone" id="voiceSearch" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #666; cursor: pointer;"></i>
                </div>
                <form method="get" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                    <label for="sort" style="font-weight: bold;">Sort by:</label>
                    <select name="sort" id="sort" onchange="this.form.submit()" style="padding: 7px 18px 7px 10px; border-radius: 8px; border: 1px solid #bfc9d9; font-size: 16px; background: #f6f8fa url('data:image/svg+xml;utf8,<svg fill=\'gray\' height=\'20\' viewBox=\'0 0 20 20\' width=\'20\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7.293 7.293a1 1 0 011.414 0L10 8.586l1.293-1.293a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 010-1.414z\'/></svg>') no-repeat right 10px center/18px 18px;">
                        <option value="featured" <?php if ($sort == 'featured') echo 'selected'; ?>>Featured</option>
                        <option value="name_az" <?php if ($sort == 'name_az') echo 'selected'; ?>>Name: A-Z</option>
                        <option value="name_za" <?php if ($sort == 'name_za') echo 'selected'; ?>>Name: Z-A</option>
                        <option value="price_asc" <?php if ($sort == 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php if ($sort == 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
                    </select>
                </form>
                <div>
                    <button id="exportExcelBtn" class="submit-button" style="margin-right:10px;"><i class="fas fa-file-excel"></i> Export to Excel</button>
                    <a href="exportEquipementPdf.php" class="submit-button" style="display: inline-block; text-decoration: none; margin-right: 10px;"><i class="fas fa-file-pdf"></i> Export to PDF</a>
                    <a href="addEqui.php" class="submit-button" style="display: inline-block; text-decoration: none;">
                        <i class="fas fa-plus"></i> Add New Equipement
                    </a>
                </div>
            </div>

        <!-- Equipment Data Table -->
        <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <thead>
                        <tr style="background-color: #003087; color: white;">
                            <th style="padding: 15px; text-align: left;">Reference</th>
                            <th style="padding: 15px; text-align: left;">Name</th>
                            <th style="padding: 15px; text-align: left;">Price</th>
                            <th style="padding: 15px; text-align: left;">Quantity</th>
                            <th style="padding: 15px; text-align: left;">Type</th>
                            <th style="padding: 15px; text-align: left;">Image</th>
                            <th style="padding: 15px; text-align: left;">Guide</th>
                            <th style="padding: 15px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listeEquipements as $equipement) { ?>
                            <tr style="border-bottom: 1px solid #e0e0e0;">
                                <td style="padding: 15px;"><?php echo $equipement['reference']; ?></td>
                                <td style="padding: 15px;"><?php echo $equipement['nom']; ?></td>
                                <td style="padding: 15px;"><?php echo $equipement['prix']; ?> tnd</td>
                                <td style="padding: 15px;"><?php echo $equipement['quantite']; ?></td>
                                <td style="padding: 15px;"><?php echo $equipement['type']; ?></td>
                                <td style="padding: 15px;">
                                    <?php if(!empty($equipement['image'])): ?>
                                        <img src="../FrontOffice/assets/images/equipements/<?php echo $equipement['image']; ?>" alt="<?php echo $equipement['nom']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px;">
                                    <?php if(!empty($equipement['guide'])): ?>
                                        <button class="view-guide-btn" onclick="showGuide(`<?php echo htmlspecialchars($equipement['guide'], ENT_QUOTES); ?>`)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">No guide</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px; text-align: center;">
                                    <!-- Edit and Delete Actions -->
                                    <a href="updateEqui.php?reference=<?php echo $equipement['reference']; ?>" 
                                       class="clear-button" style="padding: 8px 15px; margin-right: 10px; text-decoration: none;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="equipmentRatings.php?reference=<?php echo $equipement['reference']; ?>" 
                                       class="clear-button" style="padding: 8px 15px; margin-right: 10px; text-decoration: none; background-color: #ffd700;">
                                        <i class="fas fa-star"></i>
                                    </a>
                                    <a href="deleteEqui.php?reference=<?php echo $equipement['reference']; ?>" 
                                       class="submit-button" style="padding: 8px 15px; text-decoration: none; background-color: #dc3545;"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet équipement ?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <!-- SheetJS (xlsx) CDN for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        const searchInput = document.getElementById('searchInput');
        const clearSearch = document.getElementById('clearSearch');
        const voiceSearch = document.getElementById('voiceSearch');
        const rows = document.querySelectorAll('tbody tr');
    
        // Voice search functionality
        if ('webkitSpeechRecognition' in window) {
            const recognition = new webkitSpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'en-US';
    
            recognition.onresult = function(event) {
                const result = event.results[0][0].transcript;
                searchInput.value = result;
                searchInput.dispatchEvent(new Event('input'));
            };
    
            recognition.onerror = function(event) {
                console.error('Speech recognition error:', event.error);
                voiceSearch.style.color = '#666';
            };
    
            voiceSearch.addEventListener('click', function() {
                recognition.start();
                voiceSearch.style.color = '#003087';
            });
        } else {
            voiceSearch.style.display = 'none';
        }
    
        // Existing search functionality
        searchInput.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            clearSearch.style.display = searchValue ? 'block' : 'none';
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
    
        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            clearSearch.style.display = 'none';
            rows.forEach(row => row.style.display = '');
        });
    // Export to Excel functionality
        document.getElementById('exportExcelBtn').addEventListener('click', function() {
            var table = document.querySelector('table');
            var wb = XLSX.utils.table_to_book(table, {sheet: "Equipements"});
            XLSX.writeFile(wb, 'equipements.xlsx');
        });
    </script>
    <div id="guideModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <span class="close" onclick="closeGuideModal()">&times;</span>
            <h3 style="color: #007bff; margin-bottom: 20px;">Usage Guide</h3>
            <div id="guideContent" style="
                white-space: pre-wrap;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 5px;
                max-height: 60vh;
                overflow-y: auto;
                line-height: 1.6;
            ">
            </div>
            <button 
                onclick="closeGuideModal()" 
                style="
                    margin-top: 20px;
                    padding: 8px 15px;
                    background: #007bff;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                "
            >
                Close
            </button>
        </div>
    </div>

    <script>
    function showGuide(guide) {
        // Preserve line breaks and basic formatting
        const contentDiv = document.getElementById('guideContent');
        contentDiv.innerHTML = guide.replace(/\n/g, '<br>');
        document.getElementById('guideModal').style.display = 'block';
    }

    function closeGuideModal() {
        document.getElementById('guideModal').style.display = 'none';
    }

    // Close modal when clicking outside content
    window.onclick = function(event) {
        const modal = document.getElementById('guideModal');
        if (event.target == modal) {
            closeGuideModal();
        }
    }
    </script>

    <style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 700px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .view-guide-btn {
        background: none;
        border: none;
        color: #007bff;
        cursor: pointer;
        padding: 5px 10px;
    }

    .view-guide-btn:hover {
        text-decoration: underline;
    }
    </style>
    <style>
        .dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }
        .dark-mode .admin-header {
            background-color: #1e1e1e;
        }
        .dark-mode .admin-nav-container {
            background-color: #1e1e1e;
        }
        .dark-mode .form-container {
            background-color: #1e1e1e;
            color: #e0e0e0;
        }
        .dark-mode table {
            background-color: #1e1e1e;
            color: #e0e0e0;
        }
        .dark-mode th {
            background-color: #003087;
        }
        .dark-mode tr {
            border-bottom: 1px solid #333;
        }
        .dark-mode .modal-content {
            background-color: #1e1e1e;
            color: #e0e0e0;
        }
        .dark-mode .close {
            color: #e0e0e0;
        }
        .dark-mode input,
        .dark-mode select {
            background-color: #333;
            color: #fff;
            border-color: #555;
        }
        .dark-mode .admin-title h1 {
            color: #e0e0e0;
        }
        .dark-mode .submit-button {
            background-color: #003087;
            color: white;
        }
        .dark-mode .text-muted {
            color: #aaa !important;
        }
        .dark-mode .admin-logo span {
            color: #e0e0e0;
        }
    </style>
</body>
<script>
    // Dark mode toggle functionality
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;
    
    // Check for saved user preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.classList.add('dark-mode');
        darkModeToggle.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
    }
    
    darkModeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        
        if (body.classList.contains('dark-mode')) {
            localStorage.setItem('darkMode', 'enabled');
            darkModeToggle.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
        } else {
            localStorage.setItem('darkMode', 'disabled');
            darkModeToggle.innerHTML = '<i class="fas fa-moon"></i> Dark Mode';
        }
    });
</script>
</html>