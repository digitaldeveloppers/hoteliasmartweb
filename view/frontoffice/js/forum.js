document.addEventListener('DOMContentLoaded', function() {
    const newPostBtn = document.getElementById('newPostBtn');
    const createPostForm = document.getElementById('createPostForm');

    newPostBtn.addEventListener('click', function() {
        createPostForm.classList.remove('hidden');
    });
});

function validateForm() {
    const title = document.getElementById('postTitle').value;
    const content = document.getElementById('postContent').value;
    
    // Title validation
    if (title.trim().length < 5) {
        alert('Title must be at least 5 characters long');
        return false;
    }
    
    if (title.trim().length > 100) {
        alert('Title must not exceed 100 characters');
        return false;
    }
    
    // Content validation
    if (content.trim().length < 20) {
        alert('Post content must be at least 20 characters long');
        return false;
    }
    
    if (content.trim().length > 1000) {
        alert('Post content must not exceed 1000 characters');
        return false;
    }
    
    submitPost(title, content);
    return false;
}

function submitPost(title, content) {
    const formData = new FormData();
    const imageFile = document.getElementById('postImage').files[0];
    
    formData.append('title', title);
    formData.append('content', content);
    if (imageFile) {
        formData.append('image', imageFile);
    }

    fetch('submit_post.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            window.location.href = 'index.php';
        } else {
            alert('Error creating post: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating post');
    });
    
    return false;
}


function togglePostMenu(button) {
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
    // Redirect to edit page
    window.location.href = `edit_post.php?id=${postId}`;
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
                // Remove the post from DOM
                const postElement = document.querySelector(`[data-post-id="${postId}"]`);
                postElement.remove();
            } else {
                alert('Error deleting post');
            }
        });
    }
}

// Close menus when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.matches('.post-options-btn')) {
        document.querySelectorAll('.post-options-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});