<?php
class ReviewManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function fetchReviews($productId) {
        $stmt = $this->conn->prepare('SELECT rating, comment FROM product_reviews WHERE product_id = ?');
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addReview($userId, $productId, $rating, $comment) {
        $stmt = $this->conn->prepare('INSERT INTO product_reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)');
        if (!$stmt) {
            throw new Exception($this->conn->error);
        }
        $stmt->bind_param('iiis', $userId, $productId, $rating, $comment);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
    }
}
?>