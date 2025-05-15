<?php
// --- Database connection settings ---
define('DB_DSN', 'mysql:host=localhost;dbname=hoteliasmart;charset=utf8');
define('DB_USER', 'root');
define('DB_PASS', ''); // Change this if your MySQL password is not empty

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
