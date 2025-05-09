<?php
class DatabasePDO {
    private $host = 'localhost';
    private $db_name = 'hotel_forum';
    private $username = 'root';
    private $password = '';
    private $conn;
    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    public function connect() {
        $this->conn = null;
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password, $this->options);
        } catch(PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }
        return $this->conn;
    }

    // Helper method to get a single database connection instance
    public static function getInstance() {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance->connect();
    }
}

// Function to get PDO connection quickly
function getPDO() {
    return DatabasePDO::getInstance();
}