<?php
class ReviewManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function fetchReviews($productId) {
        $stmt = $this->conn->prepare('SELECT rating, comment FROM reviews WHERE product_id = ?');
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addReview($productId, $rating, $comment) {
        $stmt = $this->conn->prepare('INSERT INTO reviews (product_id, rating, comment) VALUES (?, ?, ?)');
        $stmt->bind_param('iis', $productId, $rating, $comment);
        $stmt->execute();
    }
}
?>