function validatePostForm(form) {
    const title = form.querySelector('#postTitle').value.trim();
    const content = form.querySelector('#postContent').value.trim();
    const image = form.querySelector('#postImage').value;

    if (title.length < 3 || title.length > 100) {
        alert('Title must be between 3 and 100 characters');
        return false;
    }

    if (content.length < 10 || content.length > 5000) {
        alert('Content must be between 10 and 5000 characters');
        return false;
    }

    if (image) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const fileInput = form.querySelector('#postImage');
        if (!allowedTypes.includes(fileInput.files[0].type)) {
            alert('Only JPG, PNG and GIF images are allowed');
            return false;
        }
        if (fileInput.files[0].size > 5 * 1024 * 1024) { // 5MB
            alert('Image size must be less than 5MB');
            return false;
        }
    }

    return true;
}

function validateCommentForm(form) {
    const comment = form.querySelector('textarea[name="comment_text"]').value.trim();
    
    if (comment.length < 1 || comment.length > 1000) {
        alert('Comment must be between 1 and 1000 characters');
        return false;
    }

    return true;
}