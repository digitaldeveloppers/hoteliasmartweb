function togglePostMenu(event, button) {
    event.stopPropagation();
    const menu = button.nextElementSibling;
    const allMenus = document.querySelectorAll('.post-options-menu');
    
    // Close all other menus
    allMenus.forEach(m => {
        if (m !== menu) m.classList.remove('show');
    });
    
    // Toggle current menu
    menu.classList.toggle('show');
}

function editPost(postId) {
    window.location.href = 'edit_post.php?id=' + postId;
}

function deletePost(postId) {
    if (confirm('Are you sure you want to delete this post?')) {
        fetch('delete_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ post_id: postId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const postElement = document.querySelector(`[data-post-id="${postId}"]`);
                postElement.remove();
            }
        });
    }
}

// Close menus when clicking outside
document.addEventListener('click', function() {
    document.querySelectorAll('.post-options-menu').forEach(menu => {
        menu.classList.remove('show');
    });
});