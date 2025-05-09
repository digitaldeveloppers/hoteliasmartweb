<?php
ob_start();
require_once __DIR__ . '/../../config/db_connect.php';
$db = getPDO();

// Get reported posts
$reports = $db->query("
    SELECT r.*, f.title as post_title, f.content as post_content, 
           u.nickname as reporter_name, f.image_path,
           (SELECT COUNT(*) FROM post_reports WHERE post_id = f.id) as report_count
    FROM post_reports r
    JOIN forum_posts f ON r.post_id = f.id
    JOIN user_profiles u ON r.reporter_id = u.id
    GROUP BY f.id
    ORDER BY report_count DESC, r.created_at DESC
");
?>

<div class="container-fluid">
    <h1 class="mb-4">Reported Posts Management</h1>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Post Title</th>
                            <th>Content Preview</th>
                            <th>Reports</th>
                            <th>Reported By</th>
                            <th>Report Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($report = $reports->fetch()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($report['post_title']); ?></td>
                            <td><?php echo substr(htmlspecialchars($report['post_content']), 0, 100) . '...'; ?></td>
                            <td><?php echo $report['report_count']; ?></td>
                            <td><?php echo htmlspecialchars($report['reporter_name']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($report['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm" onclick="removePost(<?php echo $report['post_id']; ?>)">
                                    <i class="fas fa-trash"></i> Remove Post
                                </button>
                                <button class="btn btn-secondary btn-sm" onclick="ignoreReport(<?php echo $report['id']; ?>)">
                                    <i class="fas fa-ban"></i> Ignore Report
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function removePost(postId) {
    if (confirm('Are you sure you want to remove this post?')) {
        fetch('delete_reported_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'post_id=' + postId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the page after successful deletion
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

<script>
function ignoreReport(reportId) {
    if (confirm('Are you sure you want to ignore this report?')) {
        fetch('ignore_report.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'report_id=' + reportId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Error ignoring report');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error ignoring report');
        });
    }
}
</script>

<script>
function deleteReportedPost(postId) {
    // Input validation
    if (!postId || isNaN(postId) || postId <= 0) {
        alert('Invalid post ID');
        return;
    }

    // Confirmation dialog
    const confirmMessage = `Are you sure you want to delete reported post #${postId}?\nThis action cannot be undone.`;
    if (confirm(confirmMessage)) {
        fetch('delete_reported_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'post_id=' + encodeURIComponent(postId)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                throw new Error(data.message || 'Error deleting post');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
    }
}
</script>

<?php
$page_content = ob_get_clean();
$page_title = "Reported Posts";
include 'includes/layout.php';
?>