<?php
require_once __DIR__ . "/../db_connect.php";
require_once __DIR__ . "/../classes/Database.php";

class User {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function loginUser($username, $password) {
        $stmt = $this->conn->prepare('SELECT user_id, password FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashedPassword);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $id;
                return true;
            }
        }

        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getUsername() {
        $stmt = $this->conn->prepare('SELECT username FROM users WHERE user_id = ?');
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($username);
        $stmt->fetch();
        return $username;
    }

    public function isAdmin() {
        $stmt = $this->conn->prepare('SELECT is_admin FROM users WHERE user_id = ?');
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($isAdmin);
        $stmt->fetch();
        return $isAdmin;
    }
}
?>