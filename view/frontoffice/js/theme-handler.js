document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    const icon = themeToggle.querySelector('i');
    const text = themeToggle.querySelector('span');

    // Check saved preference
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    
    // Apply saved theme
    if (isDarkMode) {
        body.classList.add('dark-mode');
        icon.className = 'fas fa-sun';
        text.textContent = 'Light Mode';
    }

    // Toggle theme
    themeToggle.addEventListener('click', function() {
        body.classList.toggle('dark-mode');
        const isDark = body.classList.contains('dark-mode');
        
        // Update button appearance
        if (isDark) {
            icon.className = 'fas fa-sun';
            text.textContent = 'Light Mode';
        } else {
            icon.className = 'fas fa-moon';
            text.textContent = 'Dark Mode';
        }
        
        // Save preference
        localStorage.setItem('darkMode', isDark);
    });
});