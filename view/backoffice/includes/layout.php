<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if current page matches the given path
function isActivePage($path) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($current_page == $path) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Backoffice Dashboard'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --text-color: #333;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            color: var(--text-color);
        }
        
        /* Sidebar styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #2b2d42 0%, #1a1b2e 100%);
            color: white;
            padding-top: 20px;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header h3 {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 0;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .menu-item:hover, .menu-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main content area */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }
        
        /* Navbar styles */
        .top-navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .search-bar {
            position: relative;
            max-width: 300px;
        }
        
        .search-bar input {
            padding: 8px 15px 8px 35px;
            border-radius: 20px;
            border: 1px solid #e0e0e0;
            width: 100%;
        }
        
        .search-bar i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 15px;
        }
        
        /* Search results popup */
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            margin-top: 5px;
            z-index: 1000;
            display: none;
        }
        
        .search-results-content {
            padding: 15px;
        }
        
        .search-results h4 {
            margin-bottom: 10px;
            font-size: 1rem;
        }
        
        .search-results .user-stats {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .search-results .view-all {
            text-align: center;
            padding: 8px;
            border-top: 1px solid #f0f0f0;
        }
        
        /* Dashboard cards */
        .stat-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            color: white;
            font-size: 1.5rem;
        }
        
        .stat-card-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-card-title {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Table styles */
        .data-table {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        
        .data-table table {
            width: 100%;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            padding: 15px;
            font-weight: 600;
        }
        
        .data-table td {
            padding: 15px;
            border-top: 1px solid #f0f0f0;
        }
        
        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-action {
            padding: 5px 10px;
            font-size: 0.8rem;
            border-radius: 4px;
        }
        
        /* Form styles */
        .form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 25px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
    </style>
    <?php if (isset($extra_css)): echo $extra_css; endif; ?>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Hotel Forum</h3>
        </div>
        <div class="sidebar-menu">
            <a href="index.php" class="menu-item <?php echo isActivePage('index.php'); ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="posts.php" class="menu-item <?php echo isActivePage('posts.php'); ?>">
                <i class="fas fa-file-alt"></i> Posts
            </a>
            <a href="users.php" class="menu-item <?php echo isActivePage('users.php'); ?>">
                <i class="fas fa-users"></i> Users
            </a>
            <a href="comments.php" class="menu-item <?php echo isActivePage('comments.php'); ?>">
                <i class="fas fa-comments"></i> Comments
            </a>
            <a href="reported_posts.php" class="menu-item <?php echo isActivePage('reported_posts.php'); ?>">
                <i class="fas fa-flag"></i> Reported Posts
            </a>
            <a href="../index.php" class="menu-item">
                <i class="fas fa-home"></i> Back to Site
            </a>
            <!-- Remove the duplicate reported posts link that was here -->
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="userIdSearch" placeholder="Search user ID...">
                <div class="search-results" id="searchResults">
                    <div class="search-results-content" id="searchResultsContent">
                        <!-- Results will be displayed here -->
                    </div>
                </div>
            </div>
            <div class="user-info">
                <span>Admin</span>
                <img src="https://via.placeholder.com/40" alt="Admin">
            </div>
        </div>

        <!-- Page content will be inserted here -->
        <?php if (isset($page_content)): ?>
            <?php echo $page_content; ?>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- User ID Search Script -->
    <script>
        $(document).ready(function() {
            const searchInput = $('#userIdSearch');
            const searchResults = $('#searchResults');
            const searchResultsContent = $('#searchResultsContent');
            
            // Handle search input
            searchInput.on('keyup', function(e) {
                const userId = $(this).val().trim();
                
                // If Enter key is pressed and there's a value
                if (e.key === 'Enter' && userId !== '') {
                    searchUserById(userId);
                }
                
                // Hide results if input is empty
                if (userId === '') {
                    searchResults.hide();
                }
            });
            
            // Close search results when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-bar').length) {
                    searchResults.hide();
                }
            });
            
            // Function to search user by ID
            function searchUserById(userId) {
                // Show loading state
                searchResultsContent.html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Searching...</div>');
                searchResults.show();
                
                // Make AJAX request to search for user
                $.ajax({
                    url: 'search_user.php',
                    type: 'POST',
                    data: { user_id: userId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Display user information and post count
                            let html = `
                                <h4>User ID: ${response.user.id}</h4>
                                <div class="user-stats">
                                    <div>
                                        <strong>Nickname:</strong> ${response.user.nickname}
                                    </div>
                                    <div>
                                        <strong>Posts:</strong> ${response.post_count}
                                    </div>
                                </div>
                                <div class="user-stats">
                                    <div>
                                        <strong>Joined:</strong> ${response.user.created_at}
                                    </div>
                                    <div>
                                        <strong>Comments:</strong> ${response.comment_count}
                                    </div>
                                </div>
                            `;
                            
                            if (response.post_count > 0) {
                                html += `<div class="view-all">
                                    <a href="posts.php?user_id=${response.user.id}" class="btn btn-sm btn-primary">
                                        View All Posts
                                    </a>
                                </div>`;
                            }
                            
                            searchResultsContent.html(html);
                        } else {
                            // Display error message
                            searchResultsContent.html(`
                                <div class="alert alert-warning mb-0">
                                    ${response.message}
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        // Display error message
                        searchResultsContent.html(`
                            <div class="alert alert-danger mb-0">
                                An error occurred while searching. Please try again.
                            </div>
                        `);
                    }
                });
            }
        });
    </script>
    
    <?php if (isset($extra_js)): echo $extra_js; endif; ?>
</body>
</html>