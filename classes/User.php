<?php
require_once 'Database.php'; // Include the database connection

class User {
    private $db;
    private $userId;
    private $username;
    private $isAdmin;
    
    public function __construct() {
        // Initialize the database connection using the Database class
        $this->db = Database::getInstance()->getConnection();
        
        // Check if the user is already logged in by checking session data
        if (isset($_SESSION['user_id'])) {
            $this->userId = $_SESSION['user_id'];
            $this->username = $_SESSION['username'];
            $this->isAdmin = $_SESSION['is_admin'];
        }
    }

    // Method to check if user is logged in
    public function isLoggedIn() {
        return isset($this->userId);
    }

    // Method to get the logged-in user's username
    public function getUsername() {
        return $this->username;
    }

    // Method to check if the logged-in user is an admin
    public function isAdmin() {
        return $this->isAdmin;
    }

    // Method to create a new user (without email)
    public function createUser($username, $password) {
        // Check if the username is already in use
        $sql = "SELECT username FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return false; // Username is already in use
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ss', $username, $hashed_password);
        
        return $stmt->execute();
    }

    // Method to login a user
    public function loginUser($username, $password) {
        // Find the user in the database
        $sql = "SELECT user_id, username, password, is_admin FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            // Bind the results to variables
            $stmt->bind_result($userId, $dbUsername, $dbPassword, $isAdmin);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $dbPassword)) {
                // Store user data in the session
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $dbUsername;
                $_SESSION['is_admin'] = $isAdmin;

                return true; // Successful login
            } else {
                return false; // Invalid password
            }
        } else {
            return false; // User not found
        }
    }

    // Method to logout the user
    public function logout() {
        session_unset();
        session_destroy();
    }
}