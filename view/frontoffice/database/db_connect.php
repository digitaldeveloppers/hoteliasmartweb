<?php
/**
 * PDO Database Connection
 * This file establishes a PDO connection to the database
 */

function getPDO() {
    static $pdo = null;
    
    if ($pdo === null) {
        $host = 'localhost';
        $dbname = 'hotel_forum';
        $username = 'root';
        $password = '';
        
        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }
    
    return $pdo;
}