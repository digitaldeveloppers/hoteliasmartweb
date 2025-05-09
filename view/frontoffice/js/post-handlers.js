function editPost(postId) {
    window.location.href = 'edit_post.php?id=' + postId;
}

function deletePost(postId) {
    if (!confirm('Are you sure you want to delete this post?')) {
        return;
    }

    const formData = new URLSearchParams();
    formData.append('post_id', postId);

    fetch('delete_post.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const postElement = document.querySelector(`.post-card[data-post-id="${postId}"]`);
            if (postElement) {
                postElement.remove();
            }
        } else {
            alert(data.message || 'Failed to delete post');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the post');
    });
}

function toggleComments(postId) {
    console.log('Toggle comments for post:', postId); // For debugging
    const commentsSection = document.getElementById('comments-' + postId);
    if (commentsSection) {
        if (commentsSection.style.display === 'none' || commentsSection.style.display === '') {
            commentsSection.style.display = 'block';
        } else {
            commentsSection.style.display = 'none';
        }
    }
}