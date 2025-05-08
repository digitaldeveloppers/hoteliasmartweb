// Messages d'erreur pour la validation du formulaire
const validationErrors = {
    titre: 'Le titre est requis (minimum 3 caractères)',
    contenu: 'Le contenu est requis (minimum 10 caractères)',
    categorie: 'Veuillez sélectionner une catégorie',
    imageArticle: 'Le fichier doit être une image valide (jpg, png, gif)',
   
};

// Fonction de validation commune
function validateCommonFields() {
    let isValid = true;

    // Réinitialiser les erreurs
    document.querySelectorAll('.error').forEach(el => el.style.display = 'none');

    // Valider le titre
    const titre = document.getElementById('titre').value.trim();
    if (titre.length < 3) {
        document.getElementById('titreError').textContent = validationErrors.titre;
        document.getElementById('titreError').style.display = 'block';
        isValid = false;
    }

    // Valider le contenu
    const contenu = document.getElementById('contenu').value.trim();
    const regexAlphanumerique = /[a-zA-Z0-9]/;
    if (contenu.length < 10 || !regexAlphanumerique.test(contenu)) {
        document.getElementById('contenuError').textContent = validationErrors.contenu;
        document.getElementById('contenuError').style.display = 'block';
        isValid = false;
    }

    // Valider la catégorie
    const categorie = document.getElementById('categorie').value;
    if (!categorie) {
        document.getElementById('categorieError').textContent = validationErrors.categorie;
        document.getElementById('categorieError').style.display = 'block';
        isValid = false;
    }

    return isValid;
}

// Fonction de validation pour le formulaire de création
function validateForm(event, isCreate) {
    event.preventDefault();
    let isValid = validateCommonFields();

    // Valider le fichier image
    const imageFile = document.getElementById('imageArticle').files[0];
    if (isCreate && !imageFile) {
        document.getElementById('imageError').textContent = 'Une image est requise pour la création';
        document.getElementById('imageError').style.display = 'block';
        isValid = false;
    } else if (imageFile) {
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validTypes.includes(imageFile.type)) {
            document.getElementById('imageError').textContent = validationErrors.imageArticle;
            document.getElementById('imageError').style.display = 'block';
            isValid = false;
        }
    }

    if (isValid) {
        document.getElementById('articleForm').submit();
        return true;
    }

    return false;
}

// Fonction pour réinitialiser le formulaire
function clearForm() {
    const form = document.getElementById('articleForm');
    form.reset();
    document.querySelectorAll('.error').forEach(el => el.style.display = 'none');
}