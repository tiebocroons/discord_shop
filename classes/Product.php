<?php
require_once __DIR__ . "/Database.php";

class Product {
    private $conn;
    private $table = 'products';

    // Constructor with database connection
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Add a product
    public function addProduct($title, $description, $price, $img_url, $category) {
        // Basic validation for price and title
        if (empty($title)) {
            throw new Exception("Product title cannot be empty.");
        }
        if ($price < 0) {
            throw new Exception("Product price cannot be negative.");
        }

        // SQL query to add product
        $sql = "INSERT INTO " . $this->table . " (title, description, price, img_url, category) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        $stmt->execute([$title, $description, $price, $img_url, $category]);

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Fetch all products
    public function getProducts() {
        $sql = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch product by ID
    public function getProductById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update a product
    public function updateProduct($id, $title, $description, $price, $img_url, $category) {
        // Basic validation for price and title
        if (empty($title)) {
            throw new Exception("Product title cannot be empty.");
        }
        if ($price < 0) {
            throw new Exception("Product price cannot be negative.");
        }

        // SQL query to update product
        $sql = "UPDATE " . $this->table . " SET title = ?, description = ?, price = ?, img_url = ?, category = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        $stmt->execute([$title, $description, $price, $img_url, $category, $id]);

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Delete a product
    public function deleteProduct($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
?>