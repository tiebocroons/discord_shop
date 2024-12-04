<?php
class Database {
    private static $instance = null;
    private $conn;

    // Database configuration for local and online environments
    private $config = [
        'local' => [
            'host' => 'localhost',  // Replace with your local database host
            'db_name' => 'discord_shop',  // Replace with your local database name
            'username' => 'Hypha',  // Replace with your local database username
            'password' => 'UAuj*yaiEXP5)ZT@',  // Replace with your local database password
            'charset' => 'utf8mb4',
        ],
        'online' => [
            'host' => 'discordshop.railway.internal',  // Replace with your online database host
            'db_name' => 'railway',  // Replace with your online database name
            'username' => 'root',  // Replace with your online database username
            'password' => 'rLCyCNYAXMcMPUeNfjeAgyjxLPdOihTk',  // Replace with your online database password
            'charset' => 'utf8mb4',
        ],
    ];

    // Private constructor to prevent direct instantiation
    private function __construct($environment) {
        $dbConfig = $this->config[$environment];
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['db_name']};charset={$dbConfig['charset']}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    // Method to get the single instance of the database connection
    public static function getInstance($environment = 'online') {
        if (self::$instance == null) {
            self::$instance = new Database($environment);
        }
        return self::$instance;
    }

    // Method to get the connection object
    public function getConnection() {
        return $this->conn;
    }
}
?>