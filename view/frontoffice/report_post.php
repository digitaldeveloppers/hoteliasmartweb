<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Please login to report posts']));
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$user_id = $_SESSION['user_id'];

try {
    $db = getPDO();
    
    // Check if already reported
    $check = $db->prepare("SELECT id FROM post_reports WHERE post_id = ? AND reporter_id = ?");
    $check->execute([$post_id, $user_id]);
    
    if ($check->rowCount() > 0) {
        die(json_encode(['success' => false, 'message' => 'You have already reported this post']));
    }
    
    // Add report
    $stmt = $db->prepare("INSERT INTO post_reports (post_id, reporter_id) VALUES (?, ?)");
    $success = $stmt->execute([$post_id, $user_id]);
    
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error reporting post']);
}