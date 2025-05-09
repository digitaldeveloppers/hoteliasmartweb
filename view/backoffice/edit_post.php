<?php
// Start output buffering
ob_start();

require_once __DIR__ . '/../../config/db_connect.php';
$db = getPDO();

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get post data
$stmt = $db->prepare("SELECT * FROM forum_posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $current_image = $post['image_path'];

    // Handle image deletion
    if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1') {
        if (!empty($current_image) && file_exists('../' . $current_image)) {
            unlink('../' . $current_image);
        }
        $image_path = null;
    }
    // Handle new image upload
    elseif (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $upload_dir = "../uploads/posts/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $new_filename;

        if (!empty($current_image) && file_exists('../' . $current_image)) {
            unlink('../' . $current_image);
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = 'uploads/posts/' . $new_filename;
        }
    } else {
        $image_path = $current_image;
    }

    // Update post
    $update_stmt = $db->prepare("UPDATE forum_posts SET title = ?, content = ?, image_path = ? WHERE id = ?");
    
    if ($update_stmt->execute([$title, $content, $image_path, $post_id])) {
        header('Location: index.php');
        exit;
    }
}
?>

<!-- Edit Post Form -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Post</h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Update Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                
                <?php if (!empty($post['image_path'])): ?>
                    <div class="mt-3">
                        <div class="card" style="max-width: 300px;">
                            <img src="<?php echo '../' . htmlspecialchars($post['image_path']); ?>" class="card-img-top" alt="Current post image">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="delete_image" name="delete_image" value="1">
                                    <label class="form-check-label" for="delete_image">
                                        Delete current image
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Post
                </button>
                <a href="index.php" class="btn btn-danger">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php
// Get the buffered content
$page_content = ob_get_clean();

// Set page title
$page_title = "Edit Post";

// Include the layout
include 'includes/layout.php';
?>