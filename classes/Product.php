<?php
class Product {
    private $conn;
    private $table = 'products';

    // Constructor met databaseverbinding
    public function __construct($db) {
        $this->conn = $db;
    }

    // Product toevoegen
    public function addProduct($title, $description, $price, $img_url, $category) {
        // Basisvalidatie voor prijs en titel
        if (empty($title)) {
            throw new Exception("Product title cannot be empty.");
        }
        if ($price < 0) {
            throw new Exception("Product price cannot be negative.");
        }

        // SQL query om product toe te voegen
        $sql = "INSERT INTO " . $this->table . " (title, description, price, img_url, category) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssdss", $title, $description, $price, $img_url, $category);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Producten ophalen
    public function getProducts() {
        $sql = "SELECT * FROM " . $this->table;
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Product per ID ophalen
    public function getProductById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>