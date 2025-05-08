<?php
class Comment {
    private $id_comment;
    private $contenu_comment;
    private $image_comment;
    private $auteur_id;
    private $date_creation;

    public function __construct($contenu_comment, $image_comment, $auteur_id, $date_creation, $id_comment = null) {
        $this->id_comment = $id_comment;
        $this->contenu_comment = $contenu_comment;
        $this->image_comment = $image_comment;
        $this->auteur_id = $auteur_id;
        $this->date_creation = $date_creation;
    }

    public function get_id_comment() {
        return $this->id_comment;
    }
    public function set_id_comment($id_comment) {
        $this->id_comment = $id_comment;
    }

    public function get_contenu_comment() {
        return $this->contenu_comment;
    }
    public function set_contenu_comment($contenu_comment) {
        $this->contenu_comment = $contenu_comment;
    }

    public function get_image_comment() {
        return $this->image_comment;
    }
    public function set_image_comment($image_comment) {
        $this->image_comment = $image_comment;
    }

    public function get_auteur_id() {
        return $this->auteur_id;
    }
    public function set_auteur_id($auteur_id) {
        $this->auteur_id = $auteur_id;
    }

    public function get_date_creation() {
        return $this->date_creation;
    }
    public function set_date_creation($date_creation) {
        $this->date_creation = $date_creation;
    }
}