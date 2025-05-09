<?php
// Start output buffering
ob_start();

// Database connection
require_once __DIR__ . '/../../config/db_connect.php';
$db = getPDO();

// Check if filtering by user ID
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$user_info = null;

// If filtering by user ID, get user info
if ($user_id > 0) {
    $stmt = $db->prepare("SELECT * FROM user_profiles WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build query based on filters
$where_clause = $user_id > 0 ? "WHERE p.user_profile_id = :user_id" : "";

// Get total posts count
$count_query = "SELECT COUNT(*) FROM forum_posts p $where_clause";
$count_stmt = $db->prepare($count_query);
if ($user_id > 0) {
    $count_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}
$count_stmt->execute();
$total_posts = $count_stmt->fetchColumn();
$total_pages = ceil($total_posts / $limit);

// Get posts with pagination
$query = "
    SELECT p.*, 
           u.nickname as author_name,
           (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) as comment_count
    FROM forum_posts p
    LEFT JOIN user_profiles u ON p.user_profile_id = u.id
    $where_clause
    ORDER BY p.created_at DESC
    LIMIT :limit OFFSET :offset
";
$stmt = $db->prepare($query);
if ($user_id > 0) {
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Rest of your posts.php code...
?>

<!-- Posts Content -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <?php if ($user_info): ?>
                Posts by <?php echo htmlspecialchars($user_info['nickname']); ?> (ID: <?php echo $user_id; ?>)
            <?php else: ?>
                Manage Posts
            <?php endif; ?>
        </h1>
        
        <?php if ($user_info): ?>
            <a href="posts.php" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Back to All Posts
            </a>
        <?php endif; ?>
        
        <a href="create_post.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Post
        </a>
    </div>
    
    <?php if ($user_info): ?>
        <div class="alert alert-info">
            Showing <?php echo $total_posts; ?> post(s) by user <?php echo htmlspecialchars($user_info['nickname']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Rest of your posts.php code... -->
</div>