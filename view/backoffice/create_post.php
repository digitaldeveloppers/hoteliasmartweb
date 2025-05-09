<?php
// Start output buffering
ob_start();

require_once __DIR__ . '/../../config/db_connect.php';
$db = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image_path = null;
    
    // Handle image upload if present
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../uploads/posts/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = 'uploads/posts/' . $new_filename;
        }
    }
    
    // Insert post with "Admin" as author
    $stmt = $db->prepare("INSERT INTO forum_posts (title, content, image_path, author_name, created_at) VALUES (?, ?, ?, 'Admin', NOW())");
    
    if ($stmt->execute([$title, $content, $image_path])) {
        header('Location: index.php');
        exit;
    }
}
?>

<!-- Create Post Form -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create New Post</h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image (optional)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Post
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
$page_title = "Create New Post";

// Include the layout
include 'includes/layout.php';
?>