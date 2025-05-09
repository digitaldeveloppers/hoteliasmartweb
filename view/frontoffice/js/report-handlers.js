function showReportModal(postId) {
    const modal = document.getElementById('reportModal');
    document.getElementById('reportPostId').value = postId;
    modal.style.display = 'block';
}

function reportPost(confirm) {
    const modal = document.getElementById('reportModal');
    const postId = document.getElementById('reportPostId').value;
    
    if (confirm) {
        fetch('report_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${postId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Post has been reported successfully');
            } else {
                alert(data.message || 'Error reporting post');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error reporting post');
        });
    }
    
    modal.style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('reportModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}