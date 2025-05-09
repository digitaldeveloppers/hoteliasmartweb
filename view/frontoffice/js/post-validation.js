function validateCreatePost(form) {
    const title = form.querySelector('#title').value.trim();
    const content = form.querySelector('#content').value.trim();
    const image = form.querySelector('#image');

    // Title validation
    if (title.length < 5) {
        alert('Title must be at least 5 characters long');
        return false;
    }
    if (title.length > 100) {
        alert('Title cannot exceed 100 characters');
        return false;
    }

    // Content validation
    if (content.length < 20) {
        alert('Post content must be at least 20 characters long');
        return false;
    }
    if (content.length > 1000) {
        alert('Post content cannot exceed 1000 characters');
        return false;
    }

    // Image validation
    if (image.files.length > 0) {
        const file = image.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (!allowedTypes.includes(file.type)) {
            alert('Only JPG, PNG and GIF images are allowed');
            return false;
        }

        if (file.size > maxSize) {
            alert('Image size must be less than 5MB');
            return false;
        }
    }

    return true;
}