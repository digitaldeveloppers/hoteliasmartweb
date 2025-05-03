document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.querySelector('.comment-form');
    const contentInput = document.getElementById('content');
    const imageInput = document.getElementById('image');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'validation-errors';
    errorDiv.style.color = 'red';
    errorDiv.style.marginTop = '10px';
    errorDiv.style.fontWeight = 'bold';
    errorDiv.style.padding = '8px';
    errorDiv.style.borderRadius = '4px';
    errorDiv.style.backgroundColor = 'rgba(255, 0, 0, 0.1)';
    commentForm.insertBefore(errorDiv, commentForm.querySelector('button'));

    commentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        errorDiv.innerHTML = '';
        let errors = [];

        // Validate content
        const content = contentInput.value.trim();
        if (!content) {
            errors.push('Le contenu du commentaire est obligatoire.');
        } else if (content.length > 50) {
            errors.push('Le contenu du commentaire ne doit pas dépasser 50 caractères.');
        }

        // Validate image if one is selected
        if (imageInput.files.length > 0) {
            const file = imageInput.files[0];
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                errors.push('Le format de l\'image doit être JPG, JPEG ou PNG.');
            }
        }

        // Display errors or submit form via AJAX
        if (errors.length > 0) {
            errors.forEach(error => {
                const errorP = document.createElement('p');
                errorP.style.margin = '5px 0';
                errorP.style.color = 'red';
                errorP.style.fontWeight = 'bold';
                errorP.textContent = error;
                errorDiv.appendChild(errorP);
            });
        } else {
            const formData = new FormData(commentForm);
            formData.append('add_comment', '1');
            
            fetch('add_comment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(() => {
                const articleId = formData.get('article_id');
                window.location.href = 'article.php?id=' + articleId;
            })
            .catch(error => {
                console.error('Error:', error);
                const errorP = document.createElement('p');
                errorP.style.margin = '5px 0';
                errorP.style.color = 'red';
                errorP.style.fontWeight = 'bold';
                errorP.textContent = 'Une erreur est survenue lors de l\'envoi du commentaire.';
                errorDiv.appendChild(errorP);
            });
        }
    });
});