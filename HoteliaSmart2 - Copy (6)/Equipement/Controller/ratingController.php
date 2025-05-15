<?php
require_once(__DIR__ . '/../Model/rating.php');
require_once(__DIR__ . '/../../config.php');

class ratingController {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    public function addRating($reference, $user_id, $rating) {
        $stmt = $this->pdo->prepare('INSERT INTO ratings (reference, user_id, rating, created_at) VALUES (?, ?, ?, NOW())');
        return $stmt->execute([$reference, $user_id, $rating]);
    }
    public function getAverageRating($reference) {
        $stmt = $this->pdo->prepare('SELECT AVG(rating) AS avg_rating FROM ratings WHERE reference = ?');
        $stmt->execute([$reference]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && $row['avg_rating'] !== null ? floatval($row['avg_rating']) : null;
    }
    public function getRatingsByReference($reference) {
    $stmt = $this->pdo->prepare('SELECT * FROM ratings WHERE reference = ?');
    $stmt->execute([$reference]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $ratings = [];
    foreach ($results as $row) {
        $ratings[] = new Rating(
            $row['id'],
            $row['reference'],
            $row['user_id'],
            $row['rating'],
            $row['created_at']
        );
    }
    return $ratings;
}
public function getRatingByUser($reference, $user_id) {
    $stmt = $this->pdo->prepare('SELECT * FROM ratings WHERE reference = ? AND user_id = ?');
    $stmt->execute([$reference, $user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? new Rating(
        $row['id'],
        $row['reference'],
        $row['user_id'],
        $row['rating'],
        $row['created_at']
    ) : null;
}
}
