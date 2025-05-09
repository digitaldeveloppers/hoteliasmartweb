function handleVote(postId, voteType) {
    fetch('handle_vote.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `post_id=${postId}&vote_type=${voteType}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update vote counts
            const postCard = document.querySelector(`.post-card[data-post-id="${postId}"]`);
            postCard.querySelector('.upvotes').textContent = data.upvotes;
            postCard.querySelector('.downvotes').textContent = data.downvotes;
            
            // Toggle active state
            const clickedBtn = postCard.querySelector(`.${voteType}-btn`);
            clickedBtn.classList.toggle('active');
            
            // Remove active state from other button
            const otherType = voteType === 'upvote' ? 'downvote' : 'upvote';
            postCard.querySelector(`.${otherType}-btn`).classList.remove('active');
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error processing vote');
    });
}