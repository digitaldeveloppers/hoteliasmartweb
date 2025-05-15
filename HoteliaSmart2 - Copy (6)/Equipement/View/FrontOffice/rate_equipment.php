<?php
// --- Strong error reporting for debugging ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once(__DIR__ . '/../../../config.php');

header('Content-Type: application/json');

// Make sure we have a database connection
if (!isset($pdo) || !($pdo instanceof PDO)) {
    echo json_encode(['success' => false, 'message' => 'Database connection not available']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input data
    $email = $_POST['email'] ?? '';
    $reference = $_POST['reference'] ?? '';
    $rating = intval($_POST['rating'] ?? 0);
    
    // Validate input
    if (empty($email) || empty($reference) || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Please provide your email and select a rating (1-5).']);
        exit;
    }
    
    try {
        // Check if email exists in orders (optional validation)
        // Comment this section out if you want to allow any email to submit ratings
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM commande WHERE mailClient = ?');
        $stmt->execute([$email]);
        $hasOrdered = $stmt->fetchColumn() > 0;
        
        if (!$hasOrdered) {
            echo json_encode(['success' => false, 'message' => 'Email not found. Only customers who have placed orders can submit ratings.']);
            exit;
        }
        
        // Insert the rating directly
        $stmt = $pdo->prepare('INSERT INTO ratings (reference, user_id, rating, created_at) VALUES (?, ?, ?, NOW())');
        $success = $stmt->execute([$reference, 0, $rating]);
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Thank you for your rating!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not save your rating. Please try again.']);
        }
    } catch (PDOException $e) {
        // Log the error but don't expose details to user
        error_log('Rating error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'A database error occurred. Please try again later.']);
    } catch (Exception $e) {
        error_log('General error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method. Please use POST.']);
