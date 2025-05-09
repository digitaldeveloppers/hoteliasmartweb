/**
 * Validation améliorée pour les commentaires
 * Empêche la soumission de commentaires vides
 */

function validateCommentForm(form) {
    const commentTextarea = form.querySelector('textarea[name="comment_text"]');
    const comment = commentTextarea.value.trim();
    
    // Vérification si le commentaire est vide après suppression des espaces
    if (comment.length === 0) {
        // Mettre en évidence le champ avec une bordure rouge
        commentTextarea.style.border = '1px solid red';
        // Afficher un message d'erreur
        alert('Le commentaire ne peut pas être vide.');
        // Empêcher la soumission du formulaire
        return false;
    }
    
    // Vérification de la longueur maximale
    if (comment.length > 1000) {
        commentTextarea.style.border = '1px solid red';
        alert('Le commentaire ne doit pas dépasser 1000 caractères.');
        return false;
    }
    
    // Réinitialiser le style si tout est correct
    commentTextarea.style.border = '';
    return true;
}

// Ajouter un écouteur d'événement pour réinitialiser le style lors de la saisie
document.addEventListener('DOMContentLoaded', function() {
    const commentForms = document.querySelectorAll('.comment-form');
    
    commentForms.forEach(form => {
        const textarea = form.querySelector('textarea[name="comment_text"]');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.border = '';
            });
        }
    });
});