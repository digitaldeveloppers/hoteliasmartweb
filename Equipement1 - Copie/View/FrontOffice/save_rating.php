<?php
include_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['service_id']) && isset($data['rating'])) {
        $service_id = $data['service_id'];
        $rating = $data['rating'];
        
        try {
            $pdo = config::getConnexion();
            
            // Check if rating already exists
            $stmt = $pdo->prepare("SELECT * FROM ratings WHERE service_id = ?");
            $stmt->execute([$service_id]);
            
            if ($stmt->rowCount() > 0) {
                // Update existing rating
                $stmt = $pdo->prepare("UPDATE ratings SET rating = ?, updated_at = NOW() WHERE service_id = ?");
                $stmt->execute([$rating, $service_id]);
            } else {
                // Insert new rating
                $stmt = $pdo->prepare("INSERT INTO ratings (service_id, rating, created_at) VALUES (?, ?, NOW())");
                $stmt->execute([$service_id, $rating]);
            }
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing required data']);
    }
}
?>