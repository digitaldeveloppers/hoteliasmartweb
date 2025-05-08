<?php

require_once '../../../config.php';
require_once '../../../controllers/UserController.php';

// Create a new instance of UserController
$userC = new UserController();

if (
    isset($_POST["first_name"]) &&
    isset($_POST["last_name"]) &&
    isset($_POST["email"]) &&
    isset($_POST["password"])
) {
    if (
        !empty($_POST['first_name']) &&
        !empty($_POST['last_name']) &&
        !empty($_POST["email"]) &&
        !empty($_POST["password"])
    ) {
        // Initialize and set user properties
        $user = new User();
        $user->setFirstName($_POST['first_name']);
        $user->setLastName($_POST['last_name']);
        $user->setEmail($_POST['email']);
        $user->setPassword($_POST['password']);
        $user->setRole('user');
        $user->setVerified(0);
        $user->setBanned(0);
        $user->setAccountType('normal');
        
        try {
            // Attempt to create the user using UserController
            $userC->addUser($user);
            $success_message = "Account created successfully!";
            header('Location: login.php?success_global=' . urlencode($success_message) . '&email=' . urlencode($user->getEmail()));
            exit();
        } catch (Exception $e) {
            $error_message = "Failed to create account. Please try again later.";
            header('Location: register.php?error_global=' . urlencode($error_message));
            exit();
        }
    } else {
        $error_message = "All fields are required.";
        header('Location: register.php?error_global=' . urlencode($error_message));
        exit();
    }
}

?>