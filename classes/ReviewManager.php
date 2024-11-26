<?php
require_once __DIR__ . "/Database.php";

class ReviewManager {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function fetchReviews($productId) {
        error_log("Fetching reviews for product ID: $productId"); // Log the product ID
        $stmt = $this->conn->prepare('SELECT comment FROM product_reviews WHERE product_id = ?');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        $stmt->execute([$productId]);
        $reviews = $stmt->fetchAll();
        error_log("Fetched " . count($reviews) . " reviews"); // Log the number of reviews fetched
        return $reviews;
    }

    public function addReview($userId, $productId, $comment) {
        error_log("Adding review for product ID: $productId by user ID: $userId"); // Log the product ID and user ID
        if (!$this->productExists($productId)) {
            throw new Exception("Product not found.");
        }

        $stmt = $this->conn->prepare('INSERT INTO product_reviews (user_id, product_id, comment) VALUES (?, ?, ?)');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        if (!$stmt->execute([$userId, $productId, $comment])) {
            throw new Exception("Execute statement failed: " . $stmt->errorInfo()[2]);
        }
        error_log("Review added successfully"); // Log success message
    }

    private function productExists($productId) {
        error_log("Checking if product ID: $productId exists"); // Log the product ID
        $stmt = $this->conn->prepare('SELECT id FROM products WHERE id = ?');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        $stmt->execute([$productId]);
        return $stmt->fetch() !== false;
    }
}
?>