<?php
// Start output buffering
ob_start();

// Database connection
require_once __DIR__ . '/../../config/db_connect.php';
$db = getPDO();

// Get statistics
$stmt = $db->query("SELECT COUNT(*) as count FROM forum_posts");
$total_posts = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM user_profiles");
$total_users = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM post_comments");
$total_comments = $stmt->fetch()['count'];

// Get recent posts
$posts = $db->query("SELECT fp.*, 
    CASE 
        WHEN fp.author_name = 'Admin' THEN 'Admin'
        ELSE up.nickname 
    END as author_name,
    (SELECT COUNT(*) FROM post_votes WHERE post_id = fp.id AND vote_type = 'upvote') as upvotes,
    (SELECT COUNT(*) FROM post_votes WHERE post_id = fp.id AND vote_type = 'downvote') as downvotes
FROM forum_posts fp
LEFT JOIN user_profiles up ON fp.user_profile_id = up.id
ORDER BY fp.created_at DESC LIMIT 10");

// Page content
?>
<!-- Dashboard Content -->
<div class="container-fluid">
    <h1 class="mb-4">Backoffice Dashboard</h1>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-card-icon" style="background-color: #4361ee;">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-card-value"><?php echo $total_posts; ?></div>
                <div class="stat-card-title">Total Posts</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-card-icon" style="background-color: #3a86ff;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-card-value"><?php echo $total_users; ?></div>
                <div class="stat-card-title">Total Users</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-card-icon" style="background-color: #38b000;">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-card-value"><?php echo $total_comments; ?></div>
                <div class="stat-card-title">Total Comments</div>
            </div>
        </div>
    </div>
    
    <!-- Sync Votes Button -->
    <div class="mb-4">
        <a href="sync_votes.php" class="btn btn-info">
            <i class="fas fa-sync"></i> Synchronize Votes
        </a>
        <small class="text-muted ms-2">Click to sync votes between front office and backoffice</small>
    </div>
    
    <!-- Posts Statistics Chart -->
    <div class="data-table mb-4">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <h2 class="h4 mb-0">Posts in Last 7 Days</h2>
        </div>
        <div class="p-3">
            <canvas id="postsChart" height="100"></canvas>
        </div>
    </div>
    
    <!-- Posts Table -->
    <div class="data-table mb-4">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <h2 class="h4 mb-0">All Posts</h2>
            <a href="create_post.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Post
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Upvotes</th>
                        <th>Downvotes</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($post = $posts->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $post['id']; ?></td>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo htmlspecialchars($post['author_name']); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2"><?php echo $post['upvotes'] ?? 0; ?></span>
                                <button class="btn btn-sm btn-outline-success vote-btn" data-id="<?php echo $post['id']; ?>" data-type="upvote">
                                    <i class="fas fa-thumbs-up"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2"><?php echo $post['downvotes'] ?? 0; ?></span>
                                <button class="btn btn-sm btn-outline-danger vote-btn" data-id="<?php echo $post['id']; ?>" data-type="downvote">
                                    <i class="fas fa-thumbs-down"></i>
                                </button>
                            </div>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($post['created_at'])); ?></td>
                        <!-- In your posts table -->
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="deletePost(<?php echo $post['id']; ?>)">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                        
                        <!-- Add this JavaScript at the bottom of your file -->
                        <script>
                        function deletePost(postId) {
                            if (confirm('Are you sure you want to delete this post?')) {
                                fetch('delete_post.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: 'post_id=' + postId
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        window.location.reload();
                                    } else {
                                        alert(data.message || 'Error deleting post');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Error deleting post');
                                });
                            }
                        }
                        </script>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Post Modal -->
<div class="modal fade" id="deletePostModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this post? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let postIdToDelete;
        
        // Handle delete button click
        $('.delete-post').click(function() {
            postIdToDelete = $(this).data('id');
            $('#deletePostModal').modal('show');
        });
        
        // Handle confirm delete
        $('#confirmDelete').click(function() {
            $.ajax({
                url: 'delete_post.php',
                type: 'POST',
                data: { post_id: postIdToDelete },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while processing your request.');
                }
            });
        });
        
        // Handle vote buttons
        $('.vote-btn').click(function() {
            const postId = $(this).data('id');
            const voteType = $(this).data('type');
            const voteButton = $(this);
            
            $.ajax({
                url: 'vote_post.php',
                type: 'POST',
                data: { 
                    post_id: postId,
                    vote_type: voteType
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update the vote count in the UI
                        if (voteType === 'upvote') {
                            voteButton.parent().find('span').text(response.upvotes);
                        } else {
                            voteButton.parent().find('span').text(response.downvotes);
                        }
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while processing your vote.');
                }
            });
        });
    });
</script>

<?php

// Get post counts for the last 7 days
$stats_stmt = $db->prepare("
    SELECT DATE(created_at) as post_date, COUNT(*) as count
    FROM forum_posts
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY post_date
    ORDER BY post_date ASC
");
$stats_stmt->execute();
$post_stats = [];
$labels = [];
$counts = [];

// Fill in all 7 days (even if 0 posts)
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[$date] = date('D', strtotime($date)); // e.g., Mon, Tue
    $counts[$date] = 0;
}
while ($row = $stats_stmt->fetch(PDO::FETCH_ASSOC)) {
    $counts[$row['post_date']] = (int)$row['count'];
}

// Pass to JS
$chart_labels = json_encode(array_values($labels));
$chart_counts = json_encode(array_values($counts));

// Get the buffered content
$page_content = ob_get_clean();

// Set page title
$page_title = "Backoffice Dashboard";

// Add extra JS for chart
$extra_js = <<<EOT
<script>
    // Posts per day chart
    const ctx = document.getElementById('postsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {$chart_labels},
            datasets: [{
                label: 'Posts per Day',
                data: {$chart_counts},
                backgroundColor: 'rgba(67, 97, 238, 0.7)',
                borderColor: 'rgba(67, 97, 238, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
EOT;

// Include the layout
include 'includes/layout.php';
