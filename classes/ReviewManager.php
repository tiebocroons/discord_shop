<?php
require_once __DIR__ . "Database.php";

class ReviewManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function fetchReviews($productId) {
        $stmt = $this->conn->prepare('SELECT comment FROM product_reviews WHERE product_id = ?');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function addReview($userId, $productId, $comment) {
        $stmt = $this->conn->prepare('INSERT INTO product_reviews (user_id, product_id, comment) VALUES (?, ?, ?)');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        if (!$stmt->execute([$userId, $productId, $comment])) {
            throw new Exception("Execute statement failed: " . $stmt->errorInfo()[2]);
        }
    }
}

// Usage example
$reviewManager = new ReviewManager($conn);
?>