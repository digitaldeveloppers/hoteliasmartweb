<?php
class Post {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllPosts() {
        $query = "SELECT p.*, u.nickname 
                 FROM forum_posts p 
                 LEFT JOIN user_profiles u ON p.user_profile_id = u.id 
                 WHERE p.status = 'active' 
                 ORDER BY p.created_at DESC";
        return $this->db->query($query);
    }

    public function getPostById($id) {
        $stmt = $this->db->prepare("SELECT p.*, u.nickname 
                                  FROM forum_posts p 
                                  LEFT JOIN user_profiles u ON p.user_profile_id = u.id 
                                  WHERE p.id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createPost($title, $content, $userId, $image = null) {
        $stmt = $this->db->prepare("INSERT INTO forum_posts (title, content, user_profile_id, image_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssis', $title, $content, $userId, $image);
        return $stmt->execute();
    }
}