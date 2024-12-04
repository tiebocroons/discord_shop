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
    public function getDigitalCurrency($userId) {
        $stmt = $this->conn->prepare('SELECT digital_currency_units FROM users WHERE user_id = ?');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . implode(" ", $this->conn->errorInfo()));
        }
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public function deductDigitalCurrency($userId, $amount) {
        $stmt = $this->conn->prepare('UPDATE users SET digital_currency_units = digital_currency_units - ? WHERE user_id = ?');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . implode(" ", $this->conn->errorInfo()));
        }
        if (!$stmt->execute([$amount, $userId])) {
            throw new Exception("Execute statement failed: " . implode(" ", $stmt->errorInfo()));
        }
    }
    public function changeUsername($userId, $newUsername) {
        $stmt = $this->conn->prepare('UPDATE users SET username = ? WHERE user_id = ?');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        if (!$stmt->execute([$newUsername, $userId])) {
            throw new Exception("Execute statement failed: " . $stmt->errorInfo()[2]);
        }
    }

    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare('UPDATE users SET password = ? WHERE user_id = ?');
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $this->conn->errorInfo()[2]);
        }
        if (!$stmt->execute([$hashedPassword, $userId])) {
            throw new Exception("Execute statement failed: " . $stmt->errorInfo()[2]);
        }
    }
}
?>