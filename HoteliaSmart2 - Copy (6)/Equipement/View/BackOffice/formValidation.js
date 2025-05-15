/**
 * Form Validation Module
 * Provides validation functions for equipment form fields
 */

// Validation configuration
const validationConfig = {
    nom: {
        minLength: 3,
        errorMessage: 'Le nom doit contenir au moins 3 caractères.',
        successMessage: 'Le nom est valide!'
    },
    prix: {
        min: 0,
        errorMessage: 'Le prix doit être positif et non nul.',
        successMessage: 'Le prix est valide!'
    },
    quantite: {
        min: 1,
        errorMessage: 'La quantité doit être positive et non nulle.',
        successMessage: 'La quantité est valide!'
    },
    type: {
        minLength: 3,
        errorMessage: 'Le type doit contenir au moins 3 caractères.',
        successMessage: 'Le type est valide!'
    },
    image: {
        acceptedTypes: ['image/jpeg', 'image/png', 'image/gif'],
        errorMessage: 'Veuillez sélectionner une image valide.',
        successMessage: 'L\'image est valide!'
    }
};

/**
 * Validates a text input field
 * @param {HTMLInputElement} input - The input element to validate
 * @returns {boolean} - Whether the input is valid
 */
function validateTextInput(input) {
    const config = validationConfig[input.id];
    const value = input.value.trim();
    return value.length >= config.minLength;
}

/**
 * Validates a numeric input field
 * @param {HTMLInputElement} input - The input element to validate
 * @returns {boolean} - Whether the input is valid
 */
function validateNumericInput(input) {
    const config = validationConfig[input.id];
    const value = parseFloat(input.value);
    return !isNaN(value) && value >= config.min;
}

/**
 * Validates an image file input
 * @param {HTMLInputElement} input - The input element to validate
 * @returns {boolean} - Whether the input is valid
 */
function validateImageInput(input) {
    if (!input.files || input.files.length === 0) return true;
    const file = input.files[0];
    return validationConfig.image.acceptedTypes.includes(file.type);
}

/**
 * Updates the validation UI for an input
 * @param {HTMLInputElement} input - The input element
 * @param {boolean} isValid - Whether the input is valid
 */
/**
 * Clears the equipment form and resets validation states
 */
function clearForm() {
    const form = document.getElementById('equipmentForm');
    form.reset();
    const inputs = form.querySelectorAll('input');
    inputs.forEach(input => {
        input.classList.remove('valid', 'invalid');
        const errorElement = document.getElementById(`${input.id}-error`);
        const successElement = document.getElementById(`${input.id}-success`);
        if (errorElement) errorElement.style.display = 'none';
        if (successElement) successElement.style.display = 'none';
    });
}

// Enhanced validateInput to support detailed error/success messages and UI updates
function validateInput(input) {
    const errorElement = document.getElementById(`${input.id}-error`);
    const successElement = document.getElementById(`${input.id}-success`);
    let isValid = true;
    let errorMessage = '';

    if (input.required && !input.value.trim()) {
        errorMessage = 'Ce champ est obligatoire';
        isValid = false;
    } else {
        switch(input.id) {
            case 'nom':
                if (input.value.trim().length < 3) {
                    errorMessage = 'Le nom doit contenir au moins 3 caractères';
                    isValid = false;
                } else if (input.value.trim().length > 50) {
                    errorMessage = 'Le nom ne doit pas dépasser 50 caractères';
                    isValid = false;
                }
                break;
            case 'prix':
                const prix = parseFloat(input.value);
                if (isNaN(prix)) {
                    errorMessage = 'Veuillez entrer un prix valide';
                    isValid = false;
                } else if (prix <= 0) {
                    errorMessage = 'Le prix doit être supérieur à 0';
                    isValid = false;
                } else if (prix > 1000000) {
                    errorMessage = 'Le prix ne peut pas dépasser 1,000,000';
                    isValid = false;
                }
                break;
            case 'quantite':
                const quantite = parseInt(input.value);
                if (isNaN(quantite)) {
                    errorMessage = 'Veuillez entrer une quantité valide';
                    isValid = false;
                } else if (quantite < 1) {
                    errorMessage = 'La quantité doit être au moins 1';
                    isValid = false;
                } else if (quantite > 10000) {
                    errorMessage = 'La quantité ne peut pas dépasser 10,000';
                    isValid = false;
                }
                break;
            case 'type':
                if (input.value.trim().length < 2) {
                    errorMessage = 'Le type doit contenir au moins 2 caractères';
                    isValid = false;
                } else if (input.value.trim().length > 30) {
                    errorMessage = 'Le type ne doit pas dépasser 30 caractères';
                    isValid = false;
                }
                break;
            case 'image':
                if (input.files.length > 0) {
                    const file = input.files[0];
                    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    if (!validTypes.includes(file.type)) {
                        errorMessage = 'Format d\'image invalide. Utilisez JPG, PNG ou GIF';
                        isValid = false;
                    } else if (file.size > maxSize) {
                        errorMessage = 'L\'image ne doit pas dépasser 5MB';
                        isValid = false;
                    }
                }
                break;
        }
    }
    if (isValid) {
        input.classList.remove('invalid');
        input.classList.add('valid');
        if (errorElement) errorElement.style.display = 'none';
        if (successElement) {
            successElement.style.display = 'block';
            successElement.textContent = `${input.id.charAt(0).toUpperCase() + input.id.slice(1)} valide`;
        }
    } else {
        input.classList.remove('valid');
        input.classList.add('invalid');
        if (errorElement) {
            errorElement.textContent = errorMessage;
            errorElement.style.display = 'block';
        }
        if (successElement) successElement.style.display = 'none';
    }
    return isValid;
}

// Enhanced form validation initialization
// (replaces previous DOMContentLoaded block)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('equipmentForm');
    const inputs = form.querySelectorAll('input');
    // Real-time validation with delay and blur
    inputs.forEach(input => {
        let timeout;
        input.addEventListener('input', () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => validateInput(input), 500);
        });
        input.addEventListener('blur', () => validateInput(input));
    });
    // Submission validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        let isFormValid = true;
        inputs.forEach(input => {
            if (!validateInput(input)) {
                isFormValid = false;
                input.focus();
                return false;
            }
        });
        if (isFormValid) {
            form.submit();
        }
    });
});