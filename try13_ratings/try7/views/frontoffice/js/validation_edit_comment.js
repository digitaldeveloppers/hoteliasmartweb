function validateForm() {
    var content = document.getElementById('content').value.trim();
    var errorDiv = document.querySelector('.comment-errors');
    
    if (!content) {
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'comment-errors';
            errorDiv.style.color = 'red';
            document.querySelector('.comment-form').appendChild(errorDiv);
        }
        errorDiv.innerHTML = '<div>Le contenu du commentaire est obligatoire.</div>';
        return false;
    }
    
    if (errorDiv) {
        errorDiv.innerHTML = '';
    }
    return true;
}