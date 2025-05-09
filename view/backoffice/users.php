<?php
// Start output buffering
ob_start();

// Database connection
require_once __DIR__ . '/../../config/db_connect.php';
$db = getPDO();

// Check if sorting by posts
$sort_by_posts = isset($_GET['sort_posts']) && $_GET['sort_posts'] == 1;

// Get users with post count if sorting by posts
if ($sort_by_posts) {
    $users = $db->query("
        SELECT u.*, 
               (SELECT COUNT(*) FROM forum_posts WHERE user_profile_id = u.id) as post_count
        FROM user_profiles u
        ORDER BY post_count DESC, created_at DESC
    ");
} else {
    // Default sorting
    $users = $db->query("SELECT * FROM user_profiles ORDER BY created_at DESC");
}
?>

<!-- Users Content -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Users</h1>
        
        <?php if ($sort_by_posts): ?>
            <a href="users.php" class="btn btn-secondary">
                <i class="fas fa-sort"></i> Default Sorting
            </a>
        <?php else: ?>
            <a href="users.php?sort_posts=1" class="btn btn-primary">
                <i class="fas fa-sort-amount-down"></i> Tri croissante by posts
            </a>
        <?php endif; ?>
    </div>
    
    <!-- Users Table -->
    <div class="data-table mb-4">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <h2 class="h4 mb-0">
                <?php if ($sort_by_posts): ?>
                    Users Sorted by Post Count
                <?php else: ?>
                    All Users
                <?php endif; ?>
            </h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <?php if ($sort_by_posts): ?>
                            <th>Posts</th>
                        <?php endif; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                        <?php if ($sort_by_posts): ?>
                            <td>
                                <a href="posts.php?user_id=<?php echo $user['id']; ?>" class="badge bg-primary">
                                    <?php echo $user['post_count']; ?> posts
                                </a>
                            </td>
                        <?php endif; ?>
                        <td>
                            <button class="btn btn-sm btn-danger btn-action delete-user" data-id="<?php echo $user['id']; ?>">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this user? This will also delete all their posts and comments. This action cannot be undone.
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
        let userIdToDelete;
        
        // Handle delete button click
        $('.delete-user').click(function() {
            userIdToDelete = $(this).data('id');
            $('#deleteUserModal').modal('show');
        });
        
        // Handle confirm delete
        $('#confirmDelete').click(function() {
            $.ajax({
                url: 'delete_user.php',
                type: 'POST',
                data: { user_id: userIdToDelete },
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
    });
</script>

<?php
// Get the buffered content
$page_content = ob_get_clean();

// Set page title
$page_title = "Manage Users";

// Include the layout
include 'includes/layout.php';
?>