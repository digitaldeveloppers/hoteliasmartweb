<?php
require_once __DIR__ . '/../config.php';


class CommentCon {
    private $tab_name;

    public function __construct($tab_name = 'commentaire') {
        $this->tab_name = $tab_name;
    }

    function addComment($comment) {
        $sql = "INSERT INTO $this->tab_name (contenu_comment, image_comment, auteur_id, date_creation) VALUES (:contenu_comment, :image_comment, :auteur_id, :date_creation)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'contenu_comment' => $comment->get_contenu_comment(),
                'image_comment' => $comment->get_image_comment(),
                'auteur_id' => $comment->get_auteur_id(),
                'date_creation' => $comment->get_date_creation()
            ]);
            if (!$result) {
                throw new Exception('Failed to add comment');
            }
            return $result;
        } catch (Exception $e) {
            error_log('Error adding comment: ' . $e->getMessage());
            throw $e;
        }
    }

    function updateComment($comment) {
        $sql = "UPDATE $this->tab_name SET contenu_comment = :contenu_comment, image_comment = :image_comment, auteur_id = :auteur_id, date_creation = :date_creation WHERE id_comment = :id_comment";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'contenu_comment' => $comment->get_contenu_comment(),
                'image_comment' => $comment->get_image_comment(),
                'auteur_id' => $comment->get_auteur_id(),
                'date_creation' => $comment->get_date_creation(),
                'id_comment' => $comment->get_id_comment()
            ]);
            if (!$result) {
                throw new Exception('Failed to update comment');
            }
            return $result;
        } catch (Exception $e) {
            error_log('Error updating comment: ' . $e->getMessage());
            throw $e;
        }
    }

    function deleteComment($id_comment) {
        $sql = "DELETE FROM $this->tab_name WHERE id_comment = :id_comment";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id_comment', $id_comment);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    function listComments() {
        $sql = "SELECT * FROM $this->tab_name";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log('Error fetching comments: ' . $e->getMessage());
            throw $e;
        }
    }

    function getComment($id_comment) {
        $sql = "SELECT * FROM $this->tab_name WHERE id_comment = :id_comment";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_comment', $id_comment, PDO::PARAM_INT);
            $query->execute();
            return $query->fetch();
        } catch (Exception $e) {
            error_log('Error fetching comment: ' . $e->getMessage());
            throw $e;
        }
    }

    function generateBlogOptionsSelectedId($selected_id) {
        $sql = "SELECT id_blog, nom_blog FROM blog";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $blogs = $query->fetchAll();
            $options = '';
            foreach ($blogs as $blog) {
                $selected = ($blog['id_blog'] == $selected_id) ? 'selected' : '';
                $options .= '<option value="' . htmlspecialchars($blog['id_blog']) . '" ' . $selected . '>' . htmlspecialchars($blog['nom_blog']) . '</option>';
            }
            return $options;
        } catch (Exception $e) {
            error_log('Error generating blog options: ' . $e->getMessage());
            throw $e;
        }
    }

    function getCommentsByArticleId($auteur_id) {
        $sql = "SELECT * FROM $this->tab_name WHERE auteur_id = :auteur_id ORDER BY date_creation DESC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':auteur_id', $auteur_id, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log('Error fetching comments by article ID: ' . $e->getMessage());
            throw $e;
        }
    }
}