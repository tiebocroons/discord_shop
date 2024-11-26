<?php
class Database {
    private static $instance = null;
    private $conn;

    // Database configuration
    private $host = 'discordshop.railway.internal';  // Replace with your database host
    private $db_name = 'railway';  // Replace with your database name
    private $username = 'root';  // Replace with your database username
    private $password = 'rLCyCNYAXMcMPUeNfjeAgyjxLPdOihTk';  // Replace with your database password

    // Private constructor to prevent direct instantiation
    private function __construct() {
        // Create the database connection
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        // Check if the connection has any errors
        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }

    // Method to get the single instance of the database connection
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Method to get the connection object
    public function getConnection() {
        return $this->conn;
    }

    // Method to close the connection
    public function closeConnection() {
        if ($this->conn != null) {
            $this->conn->close();
        }
    }

    // Prevent cloning of the object
    private function __clone() { }
}
?>