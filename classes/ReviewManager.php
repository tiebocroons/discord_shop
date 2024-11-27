<?php
require_once __DIR__ . "/Database.php";

class ReviewManager {
    private $conn;
    private $logs = [];

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function fetchReviews($productId) {
        $this->log("Fetching reviews for product ID: $productId");
        $stmt = $this->conn->prepare('SELECT comment FROM product_reviews WHERE product_id = ?');
        if (!$stmt) {
            $this->log("Prepare statement failed: " . $this->conn->errorInfo()[2]);
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        $stmt->execute([$productId]);
        $reviews = $stmt->fetchAll();
        $this->log("Fetched " . count($reviews) . " reviews");
        return $reviews;
    }

    public function addReview($userId, $productId, $comment) {
        $this->log("Adding review for product ID: $productId by user ID: $userId");
        if (!$this->productExists($productId)) {
            $this->log("Product not found.");
            throw new Exception("Product not found.");
        }

        $stmt = $this->conn->prepare('INSERT INTO product_reviews (user_id, product_id, comment) VALUES (?, ?, ?)');
        if (!$stmt) {
            $this->log("Prepare statement failed: " . $this->conn->errorInfo()[2]);
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        if (!$stmt->execute([$userId, $productId, $comment])) {
            $this->log("Execute statement failed: " . $stmt->errorInfo()[2]);
            throw new Exception("Execute statement failed: " . $stmt->errorInfo()[2]);
        }
        $this->log("Review added successfully");
    }

    private function productExists($productId) {
        $this->log("Checking if product ID: $productId exists");
        $stmt = $this->conn->prepare('SELECT id FROM products WHERE id = ?');
        if (!$stmt) {
            $this->log("Prepare statement failed: " . $this->conn->errorInfo()[2]);
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        $stmt->execute([$productId]);
        return $stmt->fetch() !== false;
    }

    private function log($message) {
        $this->logs[] = $message;
        error_log($message);
    }

    public function getLogs() {
        return $this->logs;
    }
}
?>