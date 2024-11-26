<?php
class Database {
    private static $instance = null;
    private $conn;

    // Database configuration
    private $host = 'discordshop.railway.internal';  // Replace with your database host
    private $db_name = 'railway';  // Replace with your database name
    private $username = 'root';  // Replace with your database username
    private $password = 'rLCyCNYAXMcMPUeNfjeAgyjxLPdOihTk';  // Replace with your database password
    private $charset = 'utf8mb4';

    // Private constructor to prevent direct instantiation
    private function __construct() {
        $dsn = "mysql:host=$this->host;dbname=$this->db_name;charset=$this->charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
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

    // Prevent cloning of the object
    private function __clone() { }
}
?>