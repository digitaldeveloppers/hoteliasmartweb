<?php
require_once __DIR__ . '/../config/Database.php';

class Comment {
    private $conn;
    private $table = 'post_comments';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getComments($postId) {
        $stmt = $this->conn->prepare("SELECT c.*, up.nickname 
                                    FROM {$this->table} c 
                                    LEFT JOIN user_profiles up ON c.user_profile_id = up.id 
                                    WHERE c.post_id = ? 
                                    ORDER BY c.created_at DESC");
        $stmt->bind_param('i', $postId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function deleteComment($commentId, $userId) {
        try {
            $this->conn->begin_transaction();
            
            $stmt = $this->conn->prepare("DELETE FROM {$this->table} 
                                        WHERE id = ? AND user_profile_id = ?");
            $stmt->bind_param('ii', $commentId, $userId);
            $result = $stmt->execute();
            
            if ($result) {
                $this->conn->commit();
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            } else {
                throw new Exception('Failed to delete comment');
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function deleteCommentsByPost($postId) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE post_id = ?");
            $stmt->bind_param('i', $postId);
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception('Failed to delete comments');
            }
            return ['success' => true, 'message' => 'Comments deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function addComment($postId, $userId, $content) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} 
                                    (post_id, user_profile_id, comment_text) 
                                    VALUES (?, ?, ?)");
        $stmt->bind_param('iis', $postId, $userId, $content);
        return $stmt->execute();
    }
}