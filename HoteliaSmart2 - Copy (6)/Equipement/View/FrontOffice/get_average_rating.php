<?php
// --- Strong error reporting for debugging ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once(__DIR__ . '/../../../config.php');

header('Content-Type: application/json');

// Make sure we have a database connection
if (!isset($pdo) || !($pdo instanceof PDO)) {
    echo json_encode(['avg' => null, 'error' => 'Database connection not available']);
    exit;
}

$reference = $_GET['reference'] ?? '';
if (empty($reference)) {
    echo json_encode(['avg' => null, 'error' => 'Reference parameter is required']);
    exit;
}

try {
    // Get average rating directly from database
    $stmt = $pdo->prepare('SELECT AVG(rating) AS avg_rating FROM ratings WHERE reference = ?');
    $stmt->execute([$reference]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $avgRating = ($row && $row['avg_rating'] !== null) ? floatval($row['avg_rating']) : null;
    echo json_encode(['avg' => $avgRating]);
} catch (PDOException $e) {
    error_log('Rating average error: ' . $e->getMessage());
    echo json_encode(['avg' => null, 'error' => 'Database error']);
} catch (Exception $e) {
    error_log('General error: ' . $e->getMessage());
    echo json_encode(['avg' => null, 'error' => 'Server error']);
}
