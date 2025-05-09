<?php
/**
 * PDO Database Connection
 * This file provides a consistent PDO connection to be used across all files
 */

function getPDO() {
    $host = 'localhost';
    $db_name = 'hotel_forum';
    $username = 'root';
    $password = '';
    $charset = 'utf8mb4';
    
    $dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    try {
        return new PDO($dsn, $username, $password, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}