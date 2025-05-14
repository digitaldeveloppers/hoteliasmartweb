-- SQL for support_messages table
CREATE TABLE support_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('unread','read','closed') DEFAULT 'unread',
    FOREIGN KEY (sender_id) REFERENCES user(user_id),
    FOREIGN KEY (receiver_id) REFERENCES user(user_id)
);