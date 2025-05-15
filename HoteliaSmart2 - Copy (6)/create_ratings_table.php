<?php
// This script will create the ratings table if it doesn't exist
require_once(__DIR__ . '/config.php');

try {
    // Check if table exists
    $tableExists = false;
    $tables = $pdo->query("SHOW TABLES LIKE 'ratings'")->fetchAll();
    if (count($tables) > 0) {
        $tableExists = true;
        echo "Table 'ratings' already exists.<br>";
    }
    
    if (!$tableExists) {
        // Create the ratings table
        $sql = "CREATE TABLE ratings (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            reference VARCHAR(255) NOT NULL,
            user_id INT(11) NOT NULL,
            rating INT(11) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sql);
        echo "Table 'ratings' created successfully!<br>";
    }
    
    echo "Database setup complete.";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
