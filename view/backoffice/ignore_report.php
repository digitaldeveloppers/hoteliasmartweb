<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db_connect.php';

try {
    $db = getPDO();
    $report_id = isset($_POST['report_id']) ? (int)$_POST['report_id'] : 0;

    if ($report_id <= 0) {
        throw new Exception('Invalid report ID');
    }

    // Start transaction
    $db->beginTransaction();

    // Get post_id before deleting the report
    $stmt = $db->prepare("SELECT post_id FROM post_reports WHERE id = ?");
    $stmt->execute([$report_id]);
    $post_id = $stmt->fetch(PDO::FETCH_COLUMN);

    // Delete the specific report
    $stmt = $db->prepare("DELETE FROM post_reports WHERE id = ?");
    $success = $stmt->execute([$report_id]);

    // Commit transaction
    $db->commit();
    
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}