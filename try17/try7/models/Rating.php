<?php

class Rating {
    private $ratingId;
    private $auteur_id;
    private $userId;
    private $ratingValue;

    public function __construct($auteur_id, $userId, $ratingValue) {
        $this->auteur_id =$auteur_id;
        $this->userId = $userId;
        $this->ratingValue = $ratingValue;
    }

    public function getRatingId() {
        return $this->ratingId;
    }

    public function getArticleId() {
        return $this->auteur_id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getRatingValue() {
        return $this->ratingValue;
    }
}