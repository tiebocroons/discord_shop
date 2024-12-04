<?php
require_once __DIR__ . "/Database.php";

class Cart {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function addToCart($userId, $productId, $quantity = 1) {
        $stmt = $this->conn->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        if (!$stmt->execute([$userId, $productId, $quantity])) {
            throw new Exception("Execute statement failed: " . $stmt->errorInfo()[2]);
        }
    }

    public function getCartItems($userId) {
        $stmt = $this->conn->prepare('SELECT p.id AS product_id, p.title, p.price, c.quantity, (p.price * c.quantity) AS total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateQuantity($userId, $productId, $quantity) {
        $stmt = $this->conn->prepare('UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        if (!$stmt->execute([$quantity, $userId, $productId])) {
            throw new Exception("Execute statement failed: " . $stmt->errorInfo()[2]);
        }
    }

    public function removeFromCart($userId, $productId) {
        $stmt = $this->conn->prepare('DELETE FROM cart WHERE user_id = ? AND product_id = ?');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        if (!$stmt->execute([$userId, $productId])) {
            throw new Exception("Execute statement failed: " . $stmt->errorInfo()[2]);
        }
    }

    public function getTotalPrice($userId) {
        $stmt = $this->conn->prepare('SELECT SUM(p.price * c.quantity) AS total_price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public function clearCart($userId) {
        $stmt = $this->conn->prepare('DELETE FROM cart WHERE user_id = ?');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        if (!$stmt->execute([$userId])) {
            throw new Exception("Execute statement failed: " . $stmt->errorInfo()[2]);
        }
    }
}
?>