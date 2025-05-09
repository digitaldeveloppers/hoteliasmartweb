<?php
/**
 * PDO Database Connection
 * This file establishes a PDO connection to the database that can be reused across the application
 */

function getPDO() {
    static $pdo = null;
    
    if ($pdo === null) {
        $host = 'localhost';
        $dbname = 'hotel_forum';
        $username = 'root';
        $password = '';
        $charset = 'utf8mb4';
        
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }
    
    return $pdo;
}