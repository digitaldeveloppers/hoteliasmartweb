function editComment(commentId) {
    const commentText = document.getElementById('comment-text-' + commentId);
    const editForm = document.getElementById('edit-form-' + commentId);
    commentText.style.display = 'none';
    editForm.style.display = 'block';
}

function cancelEdit(commentId) {
    const commentText = document.getElementById('comment-text-' + commentId);
    const editForm = document.getElementById('edit-form-' + commentId);
    commentText.style.display = 'block';
    editForm.style.display = 'none';
}

function saveComment(commentId) {
    const editForm = document.getElementById('edit-form-' + commentId);
    const textarea = editForm.querySelector('.edit-comment-textarea');
    const formData = new URLSearchParams();
    formData.append('action', 'update');
    formData.append('comment_id', commentId);
    formData.append('comment_text', textarea.value);

    fetch('comment_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const commentText = document.getElementById('comment-text-' + commentId);
            commentText.innerHTML = textarea.value.replace(/\n/g, '<br>');
            cancelEdit(commentId);
        } else {
            throw new Error(data.message || 'Failed to update comment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Error updating comment');
    });
}

function deleteComment(commentId) {
    if (confirm('Are you sure you want to delete this comment?')) {
        const formData = new URLSearchParams();
        formData.append('action', 'delete');
        formData.append('comment_id', commentId);

        fetch('comment_actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
                if (commentElement) {
                    commentElement.remove();
                }
            } else {
                throw new Error(data.message || 'Failed to delete comment');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Error deleting comment');
        });
    }
}