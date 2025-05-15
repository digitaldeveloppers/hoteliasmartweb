<?php
require_once(__DIR__ . '/../../Controller/commandeController.php');

$commandeController = new commandeController();
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'featured';
$commandes = $commandeController->listCommandes('', $sort);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipement Dashboard - Orders</title>
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
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Title -->
    <div class="admin-title">
        <h1><i class="fa-solid fa-cart-shopping"></i> Orders Management</h1>
    </div>

    <!-- Orders List Section -->
    <section class="form-section">
        <div class="form-container" style="max-width: 1200px;">
            <h2>Orders List</h2>

            <!-- Action Buttons, Search, and Sort Dropdown -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px; flex-wrap: wrap;">
                <div class="search-container" style="flex: 1; min-width: 250px; margin-right: 20px; position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #666;"></i>
                    <input type="text" id="searchInput" placeholder="Search orders..." style="width: 300px; padding: 10px 35px; border: 1px solid #ddd; border-radius: 4px; height: 40px; border-color: #003087;">
                    <i class="fas fa-times" id="clearSearch" style="position: absolute; right: 40px; top: 50%; transform: translateY(-50%); color: #666; cursor: pointer; display: none;"></i>
                    <i class="fas fa-microphone" id="voiceSearch" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #666; cursor: pointer;"></i>
                </div>
                <form method="get" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                    <label for="sort" style="font-weight: bold;">Sort by:</label>
                    <select name="sort" id="sort" onchange="this.form.submit()" style="padding: 7px 18px 7px 10px; border-radius: 8px; border: 1px solid #bfc9d9; font-size: 16px; background: #f6f8fa url('data:image/svg+xml;utf8,<svg fill=\'gray\' height=\'20\' viewBox=\'0 0 20 20\' width=\'20\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7.293 7.293a1 1 0 011.414 0L10 8.586l1.293-1.293a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 010-1.414z\'/></svg>') no-repeat right 10px center/18px 18px;">
                        <option value="featured" <?php if ($sort == 'featured') echo 'selected'; ?>>Featured</option>
                        <option value="order_id_asc" <?php if ($sort == 'order_id_asc') echo 'selected'; ?>>Order ID: Low to High</option>
                        <option value="order_id_desc" <?php if ($sort == 'order_id_desc') echo 'selected'; ?>>Order ID: High to Low</option>
                        <option value="date_desc" <?php if ($sort == 'date_desc') echo 'selected'; ?>>Date: Newest First</option>
                        <option value="date_asc" <?php if ($sort == 'date_asc') echo 'selected'; ?>>Date: Oldest First</option>
                        <option value="total_asc" <?php if ($sort == 'total_asc') echo 'selected'; ?>>Total: Low to High</option>
                        <option value="total_desc" <?php if ($sort == 'total_desc') echo 'selected'; ?>>Total: High to Low</option>
                    </select>
                </form>
                <div>
                    <a href="exportOrdersPdf.php" class="submit-button" style="text-decoration: none;"><i class="fas fa-file-pdf"></i> Export to PDF</a>
                    <button id="exportExcelBtn" class="submit-button" style="margin-left:10px;"><i class="fas fa-file-excel"></i> Export to Excel</button>
                </div>
            </div>
            
            <!-- Orders Data Table -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <thead>
                        <tr style="background-color: #003087; color: white;">
                            <th style="padding: 15px; text-align: left;">Order ID</th>
                            <th style="padding: 15px; text-align: left;">Date</th>
                            <th style="padding: 15px; text-align: left;">Client Name</th>
                            <th style="padding: 15px; text-align: left;">Client Address</th>
                            <th style="padding: 15px; text-align: left;">Client Email</th>
                            <th style="padding: 15px; text-align: left;">Payment Mode</th>
                            <th style="padding: 15px; text-align: left;">Total</th>
                            <th style="padding: 15px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $commande): ?>
                            <tr style="border-bottom: 1px solid #ddd;">
                                <td style="padding: 15px;"><?php echo htmlspecialchars($commande['idCommande']); ?></td>
                                <td style="padding: 15px;"><?php echo htmlspecialchars($commande['date']); ?></td>
                                <td style="padding: 15px;"><?php echo htmlspecialchars($commande['nomClient'] . ' ' . $commande['prenomClient']); ?></td>
                                <td style="padding: 15px;"><?php echo htmlspecialchars($commande['adresseClient']); ?></td>
                                <td style="padding: 15px;"><?php echo htmlspecialchars($commande['mailClient']); ?></td>
                                <td style="padding: 15px;"><?php echo htmlspecialchars($commande['modePaiment']); ?></td>
                                <td style="padding: 15px;"><?php echo htmlspecialchars($commande['total']); ?></td>
                                <td style="padding: 15px; text-align: center;">
                                    <a href="deleteCommande.php?id=<?php echo $commande['idCommande']; ?>" title="Delete" onclick="return confirm('Are you sure you want to delete this order?');" style="color: #dc3545; margin-right: 10px;"><i class="fas fa-trash-alt"></i></a>
                                    <a href="checkCommande.php?id=<?php echo $commande['idCommande']; ?>" title="Check" style="color: #28a745; margin-right: 10px;"><i class="fas fa-check-circle"></i></a>
                                    <a href="orderDetails.php?id=<?php echo $commande['idCommande']; ?>" title="Details" style="color: #007bff;"><i class="fas fa-info-circle"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($commandes->fetchAll())): // Check if the list is empty after fetching ?>
                            <tr>
                                <td colspan="8" style="padding: 15px; text-align: center;">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- SheetJS (xlsx) CDN for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        /* Dark mode styles */
        .dark-mode {
            background-color: #333;
            color: #fff;
        }
        .dark-mode .admin-logo img {
            filter: invert(1);
        }
        .dark-mode .admin-nav a {
            color: #fff;
        }
        .dark-mode .admin-nav a:hover {
            color: #ccc;
        }
        .dark-mode .admin-title {
            color: #fff;
        }
        .dark-mode .form-container {
            background-color: #444;
            color: #fff;
        }
        .dark-mode table {
            background-color: #444;
            color: #fff;
        }
        .dark-mode th {
            background-color: #003087;
        }
        .dark-mode tr {
            border-bottom: 1px solid #555;
        }
        .dark-mode input {
            background-color: #555;
            color: #fff;
            border-color: #555;
        }
    </style>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Export to Excel functionality
            document.getElementById('exportExcelBtn').addEventListener('click', function() {
                var table = document.querySelector('table');
                var wb = XLSX.utils.table_to_book(table, {sheet: "Orders"});
                XLSX.writeFile(wb, 'orders.xlsx');
            });
            const searchInput = document.getElementById('searchInput');
            const clearSearch = document.getElementById('clearSearch');
            const voiceSearch = document.getElementById('voiceSearch');
            const tableRows = document.querySelectorAll('tbody tr');

            // Text search functionality
            function performSearch(searchTerm) {
                searchTerm = searchTerm.toLowerCase();
                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
                clearSearch.style.display = searchTerm ? 'block' : 'none';
            }

            searchInput.addEventListener('input', (e) => performSearch(e.target.value));

            // Clear search
            clearSearch.addEventListener('click', () => {
                searchInput.value = '';
                performSearch('');
                clearSearch.style.display = 'none';
            });

            // Voice search
            voiceSearch.addEventListener('click', () => {
                if ('webkitSpeechRecognition' in window) {
                    const recognition = new webkitSpeechRecognition();
                    recognition.lang = 'en-US';
                    recognition.start();

                    recognition.onresult = (event) => {
                        const transcript = event.results[0][0].transcript;
                        searchInput.value = transcript;
                        performSearch(transcript);
                    };

                    recognition.onerror = (event) => {
                        console.error('Speech recognition error:', event.error);
                    };
                } else {
                    alert('Speech recognition is not supported in your browser.');
                }
            });
        });
    </script>
</body>
</html>