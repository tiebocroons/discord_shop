<?php
require_once __DIR__ . "/Database.php";

class Comment {
    private $conn;
    private $comment;
    private $userId;
    private $productId;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function setComment($comment) {
        $this->comment = $comment;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setProductId($productId) {
        $this->productId = $productId;
    }

    public function save() {
        $stmt = $this->conn->prepare('INSERT INTO product_reviews (user_id, product_id, comment) VALUES (?, ?, ?)');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        if (!$stmt->execute([$this->userId, $this->productId, $this->comment])) {
            throw new Exception("Execute statement failed: " . $stmt->errorInfo()[2]);
        }
    }
}
?>
