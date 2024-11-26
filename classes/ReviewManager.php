<?php
require 'db_connect.php';

class ReviewManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function fetchReviews($productId) {
        $stmt = $this->pdo->prepare('SELECT rating, comment FROM reviews WHERE product_id = :product_id');
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function addReview($userId, $productId, $rating, $comment) {
        $stmt = $this->pdo->prepare('INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (:user_id, :product_id, :rating, :comment)');
        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
            'rating' => $rating,
            'comment' => $comment
        ]);
        return $this->pdo->lastInsertId();
    }
}

// Usage example
$reviewManager = new ReviewManager($pdo);
?>