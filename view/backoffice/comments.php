<?php
// Start output buffering
ob_start();

// Database connection
require_once __DIR__ . '/../../config/db_connect.php';
$db = getPDO();

// Get comments with post titles
$comments = $db->query("
    SELECT c.*, p.title as post_title, u.username 
    FROM post_comments c
    LEFT JOIN forum_posts p ON c.post_id = p.id
    LEFT JOIN user_profiles u ON c.user_profile_id = u.id
    ORDER BY c.created_at DESC
");

// Create delete_comment.php handler if it doesn't exist
if (!file_exists('delete_comment.php')) {
    $delete_comment_content = '<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/db_connect.php";
$db = getPDO();

$comment_id = isset($_POST["comment_id"]) ? (int)$_POST["comment_id"] : 0;

if ($comment_id <= 0) {
    die(json_encode(["success" => false, "message" => "Invalid comment ID"]));
}

$delete_comment = $db->prepare("DELETE FROM post_comments WHERE id = ?");

if ($delete_comment->execute([$comment_id])) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete comment"]);
}
?>';
    file_put_contents('delete_comment.php', $delete_comment_content);
}
?>

<!-- Comments Content -->
<div class="container-fluid">
    <h1 class="mb-4">Manage Comments</h1>
    
    <!-- Comments Table -->
    <div class="data-table mb-4">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <h2 class="h4 mb-0">All Comments</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Post</th>
                        <th>User</th>
                        <th>Comment</th>
                        <th>Upvotes</th>
                        <th>Downvotes</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($comment = $comments->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $comment['id']; ?></td>
                        <td><?php echo htmlspecialchars($comment['post_title']); ?></td>
                        <td><?php echo htmlspecialchars($comment['username'] ?? 'Anonymous'); ?></td>
                        <td><?php echo htmlspecialchars(substr($comment['content'], 0, 100)) . (strlen($comment['content']) > 100 ? '...' : ''); ?></td>
                        <td><?php echo $comment['upvotes'] ?? 0; ?></td>
                        <td><?php echo $comment['downvotes'] ?? 0; ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-danger btn-action delete-comment" data-id="<?php echo $comment['id']; ?>">
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

<!-- Delete Comment Modal -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this comment? This action cannot be undone.
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
        let commentIdToDelete;
        
        // Handle delete button click
        $('.delete-comment').click(function() {
            commentIdToDelete = $(this).data('id');
            $('#deleteCommentModal').modal('show');
        });
        
        // Handle confirm delete
        $('#confirmDelete').click(function() {
            $.ajax({
                url: 'delete_comment.php',
                type: 'POST',
                data: { comment_id: commentIdToDelete },
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
$page_title = "Manage Comments";

// Include the layout
include 'includes/layout.php';
?>
