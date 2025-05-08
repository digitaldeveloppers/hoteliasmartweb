document.addEventListener("DOMContentLoaded", function() {
    var form = document.querySelector('form[action="signup.php"]');
    if (!form) return;

    form.addEventListener("submit", function(e) {
        var firstName = form.elements["first_name"].value.trim();
        var lastName = form.elements["last_name"].value.trim();
        var email = form.elements["email"].value.trim();
        var password = form.elements["password"].value;
        var errorMessages = [];

        // Validate first name
        if (firstName.length === 0) {
            errorMessages.push("First name is required.");
        } else if (!/^[A-Za-zÀ-ÿ' -]+$/.test(firstName)) {
            errorMessages.push("First name contains invalid characters.");
        }

        // Validate last name
        if (lastName.length === 0) {
            errorMessages.push("Last name is required.");
        } else if (!/^[A-Za-zÀ-ÿ' -]+$/.test(lastName)) {
            errorMessages.push("Last name contains invalid characters.");
        }

        // Validate email
        if (email.length === 0) {
            errorMessages.push("Email is required.");
        } else if (!/^\S+@\S+\.\S+$/.test(email)) {
            errorMessages.push("Invalid email format.");
        }

        // Validate password (at least 6 chars, can be customized)
        if (password.length === 0) {
            errorMessages.push("Password is required.");
        } else if (password.length < 6) {
            errorMessages.push("Password must be at least 6 characters long.");
        }

        // Remove previous error
        var prevError = document.querySelector('.js-error');
        if (prevError) prevError.remove();

        if (errorMessages.length > 0) {
            e.preventDefault();
            var errorDiv = document.createElement('div');
            errorDiv.className = 'error js-error';
            errorDiv.innerHTML = errorMessages.join('<br>');
            form.parentNode.insertBefore(errorDiv, form);
        }
    });
});