<?php
require_once __DIR__ . "/../classes/Database.php";

class User {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function loginUser($username, $password) {
        $stmt = $this->conn->prepare('SELECT user_id, password FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            return true;
        }

        return false;
    }

    public function createUser($username, $password) {
        // Check if the username already exists
        $stmt = $this->conn->prepare('SELECT user_id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            return false; // Username already exists
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $stmt = $this->conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        return $stmt->execute([$username, $hashedPassword]);
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getUsername() {
        $stmt = $this->conn->prepare('SELECT username FROM users WHERE user_id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        return $user['username'];
    }

    public function isAdmin() {
        $stmt = $this->conn->prepare('SELECT is_admin FROM users WHERE user_id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        return $user['is_admin'];
    }
}
?>