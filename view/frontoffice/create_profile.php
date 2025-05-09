<?php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';
$db = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and limit user_id
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $max_id = 2147483646; // Maximum value for INT(11)
    
    if ($user_id <= 0 || $user_id >= $max_id) {
        die("Error: Invalid user ID. Please enter a number between 1 and " . ($max_id - 1));
    }
    
    // Check if ID already exists
    $check_stmt = $db->prepare("SELECT id FROM user_profiles WHERE id = ?");
    $check_stmt->execute([$user_id]);
    $result = $check_stmt->rowCount();
    
    if ($result > 0) {
        die("Error: Profile ID already exists. Please choose a different ID.");
    }
    
    $nickname = $_POST['nickname'];
    $lastname = $_POST['lastname'];
    
    // Handle profile picture upload
    $upload_dir = 'uploads/profiles/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $profile_pic = $_FILES['profile_pic'];
    $file_extension = strtolower(pathinfo($profile_pic['name'], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $profile_pic_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($profile_pic['tmp_name'], $profile_pic_path)) {
        $stmt = $db->prepare("INSERT INTO user_profiles (id, nickname, lastname, profile_pic) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$user_id, $nickname, $lastname, $profile_pic_path])) {
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['nickname'] = $nickname;
            $_SESSION['lastname'] = $lastname;
            
            // Redirect to profile page with user ID
            header('Location: profile.php?id=' . $user_id);
            exit;
        } else {
            echo "Error creating profile: " . $stmt->errorInfo()[2];
        }
    } else {
        echo "Error uploading profile picture";
    }
}
?>