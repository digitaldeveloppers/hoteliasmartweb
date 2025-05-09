<?php
class PostController {
    private $db;

    public function __construct() {
        $this->db = new mysqli('localhost', 'root', '', 'hotel_forum');
        if ($this->db->connect_error) {
            die('Connection failed: ' . $this->db->connect_error);
        }
    }

    public function createPost($title, $content, $userId, $image = null) {
        $authorName = $this->getAuthorName($userId);
        $imagePath = $this->handleImageUpload($image);
        
        $stmt = $this->db->prepare("INSERT INTO forum_posts (title, content, author_name, image_path, user_profile_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssi', $title, $content, $authorName, $imagePath, $userId);
        
        if ($stmt->execute()) {
            $post_id = $this->db->insert_id;
            // Create notifications for all users except the author
            $this->createNotifications($post_id, $userId, $title);
            return true;
        }
        return false;
    }

    private function createNotifications($post_id, $author_id, $post_title) {
        $message = "New post: " . substr($post_title, 0, 50) . "...";
        
        $stmt = $this->db->prepare("
            INSERT INTO post_notifications (user_profile_id, post_id, message)
            SELECT id, ?, ?
            FROM user_profiles
            WHERE id != ?
        ");
        $stmt->bind_param('isi', $post_id, $message, $author_id);
        $stmt->execute();
    }

    public function updatePost($postId, $title, $content, $userId, $image = null, $deleteImage = false) {
        $currentImage = $this->getPostImage($postId);
        $imagePath = $this->handlePostImage($image, $currentImage, $deleteImage);

        $stmt = $this->db->prepare("UPDATE forum_posts SET title = ?, content = ?, image_path = ? WHERE id = ? AND user_profile_id = ?");
        $stmt->bind_param('sssii', $title, $content, $imagePath, $postId, $userId);
        
        return $stmt->execute();
    }

    public function deletePost($postId, $userId) {
        // First delete associated comments
        $stmt = $this->db->prepare("DELETE FROM post_comments WHERE post_id = ?");
        $stmt->bind_param('i', $postId);
        $stmt->execute();

        // Then delete the post
        $stmt = $this->db->prepare("DELETE FROM forum_posts WHERE id = ? AND user_profile_id = ?");
        $stmt->bind_param('ii', $postId, $userId);
        
        return $stmt->execute();
    }

    public function getAllPosts() {
        $query = "SELECT forum_posts.*, user_profiles.nickname as author_name 
                 FROM forum_posts 
                 LEFT JOIN user_profiles ON forum_posts.user_profile_id = user_profiles.id
                 WHERE forum_posts.status = 'active' 
                 ORDER BY forum_posts.created_at DESC";
        
        return $this->db->query($query);
    }

    public function getUserPosts($userId) {
        $stmt = $this->db->prepare("SELECT * FROM forum_posts 
                                   WHERE user_profile_id = ? AND status = 'active' 
                                   ORDER BY created_at DESC");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    private function getAuthorName($userId) {
        $stmt = $this->db->prepare("SELECT nickname, lastname FROM user_profiles WHERE id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
        
        return $profile ? $profile['nickname'] . ' ' . $profile['lastname'] : 'Anonymous';
    }

    private function handleImageUpload($image) {
        if (!$image || $image['error'] !== 0) {
            return null;
        }

        $uploadDir = 'uploads/posts/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        $newFilename = uniqid() . '.' . $fileExtension;
        $imagePath = $uploadDir . $newFilename;

        move_uploaded_file($image['tmp_name'], $imagePath);
        return $imagePath;
    }

    private function getPostImage($postId) {
        $stmt = $this->db->prepare("SELECT image_path FROM forum_posts WHERE id = ?");
        $stmt->bind_param('i', $postId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result ? $result['image_path'] : null;
    }

    private function handlePostImage($newImage, $currentImage, $deleteImage) {
        if ($deleteImage) {
            if ($currentImage && file_exists($currentImage)) {
                unlink($currentImage);
            }
            return null;
        }

        if ($newImage && $newImage['size'] > 0) {
            if ($currentImage && file_exists($currentImage)) {
                unlink($currentImage);
            }
            return $this->handleImageUpload($newImage);
        }

        return $currentImage;
    }
}
?>