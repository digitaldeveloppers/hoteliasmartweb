<?php
class Rating {
    public $id;
    public $reference;
    public $user_id;
    public $rating;
    public $created_at;

    public function __construct($id = null, $reference = null, $user_id = null, $rating = null, $created_at = null) {
        // If first parameter is an array, use it to populate properties
        if (is_array($id)) {
            foreach ($id as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        } else {
            // Otherwise use individual parameters
            $this->id = $id;
            $this->reference = $reference;
            $this->user_id = $user_id;
            $this->rating = $rating;
            $this->created_at = $created_at;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getReference() {
        return $this->reference;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getRating() {
        return $this->rating;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setReference($reference) {
        $this->reference = $reference;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setRating($rating) {
        $this->rating = $rating;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }
}
