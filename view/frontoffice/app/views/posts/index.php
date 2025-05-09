<!DOCTYPE html>
<html>
<head>
    <title>Posts</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <div class="posts-grid">
            <?php if ($posts && $posts->num_rows > 0): ?>
                <?php while($post = $posts->fetch_assoc()): ?>
                    <div class="post-card" id="post-<?php echo $post['id']; ?>">
                        <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                        <p class="post-author">By <?php echo htmlspecialchars($post['nickname']); ?></p>
                        <?php if (!empty($post['image_path'])): ?>
                            <img src="../../public/<?php echo htmlspecialchars($post['image_path']); ?>" alt="Post image">
                        <?php endif; ?>
                        <p class="post-content"><?php echo htmlspecialchars($post['content']); ?></p>
                        <div class="post-actions">
                            <a href="view.php?id=<?php echo $post['id']; ?>" class="btn-view">Read More</a>
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_profile_id']): ?>
                                <button onclick="deletePost(<?php echo $post['id']; ?>)" class="btn-delete">Delete</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-posts">No posts found.</p>
            <?php endif; ?>
        </div>
    </div>

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
                    document.getElementById('post-' + postId).remove();
                }
            });
        }
    }
    </script>
</body>
</html>