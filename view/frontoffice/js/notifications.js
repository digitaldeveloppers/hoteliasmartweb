document.addEventListener('DOMContentLoaded', function() {
    const notificationsBtn = document.getElementById('notificationsBtn');
    const notificationsContainer = document.querySelector('.notifications-container');
    const badge = document.querySelector('.notification-badge');
    
    function fetchNotifications() {
        fetch('notifications.php?action=get')
            .then(response => response.json())
            .then(data => {
                if (data.notifications) {
                    const unreadCount = data.notifications.filter(n => !n.is_read).length;
                    updateNotificationBadge(unreadCount);
                    updateNotificationsList(data.notifications);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function updateNotificationBadge(count) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }

    function updateNotificationsList(notifications) {
        notificationsContainer.innerHTML = notifications.length ? notifications.map(notification => `
            <div class="notification-item ${notification.is_read ? 'read' : 'unread'}" 
                 data-id="${notification.id}">
                <div class="notification-content">
                    <i class="fas fa-file-alt"></i> ${notification.message}
                </div>
                <div class="notification-time">
                    <i class="far fa-clock"></i> ${formatTimestamp(notification.created_at)}
                </div>
                ${!notification.is_read ? `
                    <button class="mark-read-btn" onclick="markAsRead(${notification.id})">
                        <i class="fas fa-check"></i> Mark as Read
                    </button>
                ` : ''}
            </div>
        `).join('') : '<div class="no-notifications">No new notifications</div>';
    }

    // Add this helper function for better timestamp formatting
    function formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        
        // Less than 24 hours
        if (diff < 24 * 60 * 60 * 1000) {
            if (diff < 60 * 1000) return 'Just now';
            if (diff < 60 * 60 * 1000) return `${Math.floor(diff / (60 * 1000))} minutes ago`;
            return `${Math.floor(diff / (60 * 60 * 1000))} hours ago`;
        }
        
        // Less than 7 days
        if (diff < 7 * 24 * 60 * 60 * 1000) {
            return `${Math.floor(diff / (24 * 60 * 60 * 1000))} days ago`;
        }
        
        // Otherwise, show full date
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    notificationsBtn.addEventListener('click', function() {
        notificationsContainer.style.display = 
            notificationsContainer.style.display === 'none' ? 'block' : 'none';
        fetchNotifications();
    });

    // Close notifications when clicking outside
    document.addEventListener('click', function(e) {
        if (!notificationsContainer.contains(e.target) && 
            !notificationsBtn.contains(e.target)) {
            notificationsContainer.style.display = 'none';
        }
    });

    // Initial fetch
    fetchNotifications();
    // Check for new notifications every 30 seconds
    setInterval(fetchNotifications, 30000);
});

function markAsRead(notificationId) {
    fetch('notifications.php?action=mark-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notification = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
            if (notification) {
                notification.remove(); // Remove the notification from DOM
                updateUnreadCount();
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateUnreadCount() {
    const unreadNotifications = document.querySelectorAll('.notification-item').length;
    const badge = document.querySelector('.notification-badge');
    const noNotificationsMsg = document.querySelector('.no-notifications');
    
    if (unreadNotifications > 0) {
        badge.textContent = unreadNotifications;
        badge.style.display = 'block';
        if (noNotificationsMsg) noNotificationsMsg.style.display = 'none';
    } else {
        badge.style.display = 'none';
        const container = document.querySelector('.notifications-container');
        container.innerHTML = '<div class="no-notifications">No new notifications</div>';
    }
}