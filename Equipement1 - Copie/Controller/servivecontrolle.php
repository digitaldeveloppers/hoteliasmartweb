<?php 
include_once __DIR__ . '/../config.php'; // Using __DIR__ to ensure correct path resolution


function addService($pdo, $title, $solarNumber, $price, $service, $type, $id_user) {
    // Prepare SQL to insert data into the database
    $sql = "INSERT INTO services (title, quantity, price, category_id, description, user_id) 
            VALUES (:name, :solarNumber, :price, :service, :type, :user_id)";
    
    $stmt = $pdo->prepare($sql);

    // Bind the parameters to the query
    $stmt->bindParam(':name', $title);
    $stmt->bindParam(':solarNumber', $solarNumber, PDO::PARAM_INT);
    $stmt->bindParam(':price', $price, PDO::PARAM_STR);
    $stmt->bindParam(':service', $service, PDO::PARAM_INT);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':user_id', $id_user, PDO::PARAM_INT);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}



function afficherServices($pdo) {
    $sql = "SELECT * FROM services";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function afficherServicesbyid($pdo, $user_id) {
    $sql = "SELECT * FROM services WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}





function supprimerService($pdo, $id) {
    try {
        $sql = "DELETE FROM services WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression du service : " . $e->getMessage();
        return false;
    }
}


function updateUser($id, $newnumber, $pdo, $price) {
    try {
        $sql = "UPDATE services SET quantity = :number, price = :price WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        
        // Bind des valeurs
        $stmt->bindParam(':number', $newnumber);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // Exécution
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "Utilisateur mis à jour avec succès.";
            header("Location: showserv.php");
        } else {
            echo "Aucune mise à jour effectuée.";
            header("Location: showserv.php");
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

function insertPayment($conn, $id_service, $paymentMethod, $userId) {
    $stmt = $conn->prepare("INSERT INTO payment (service_id, payment_method, payment_date, user_id) VALUES (?, ?, NOW(), ?)");
    $stmt->execute([$id_service, $paymentMethod, $userId]);
    return $conn->lastInsertId(); // Return the transaction ID
}

// Function to insert card payment details
function insertCardPayment($conn, $transactionId, $cardType, $cardNumber, $formattedExpiryDate) {
    $stmt = $conn->prepare("INSERT INTO paiment_par_card (transaction_id, Type_Carte, Numero_Carte, Date_Expiration) VALUES (?, ?, ?, ?)");
    $stmt->execute([$transactionId, $cardType, $cardNumber, $formattedExpiryDate]);
}

// Function to insert mobile payment details
function insertMobilePayment($conn, $transactionId, $mobileProvider, $phoneNumber, $expirationDate) {
    $stmt = $conn->prepare("INSERT INTO paiment_mobile (transaction_id, mobile_provider, phone_number, Date_Expiration) VALUES (?, ?, ?, ?)");
    $stmt->execute([$transactionId, $mobileProvider, $phoneNumber, $expirationDate]);
}

// Function to update the payment status of a service
function updateServicePaymentStatus($conn, $id_service) {
    $stmt = $conn->prepare("UPDATE services SET is_paid = 1 WHERE id = ?");
    $stmt->execute([$id_service]);
}




function affichagePaiement() {
    try {
        // Connexion à la base de données
        $pdo = config::getConnexion();
        
        // Requête SQL correcte
        $sql = "SELECT ID, payment_date, user_id, payment_method FROM payment;";
        $stmt = $pdo->query($sql);
        
        // Récupération des résultats
        $paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Retourner les paiements
        return $paiements;
        
    } catch (PDOException $e) {
        // En cas d'erreur, démarrer la session si nécessaire
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Enregistrer l'erreur dans une session ou journal
        $_SESSION['erreur_db'] = $e->getMessage();

        // Retourner un tableau vide
        return [];
    }
}

function affichagePaiementscart() {
    try {
        // Connexion à la base de données
        $pdo = config::getConnexion();
        
        // Requête SQL correcte
        $sql = "SELECT ID, Type_Carte, Numero_Carte, Date_Expiration, Transaction_id FROM paiment_par_card";
        $stmt = $pdo->query($sql);
        
        // Récupération des résultats
        $paiementsCarte = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Retourner les paiements
        return $paiementsCarte;
        
    } catch (PDOException $e) {
        // En cas d'erreur, démarrer la session si nécessaire
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Enregistrer l'erreur dans une session ou journal
        $_SESSION['erreur_db'] = $e->getMessage();

        // Retourner un tableau vide
        return [];
    }
}





function affichagePaiementsMobile() {
    try {
        // Connexion à la base de données
        $pdo = config::getConnexion();

        // Requête SQL pour la table paiment_mobile
        $sql = "SELECT ID, mobile_provider, phone_number, Date_Expiration, transaction_id FROM paiment_mobile";
        $stmt = $pdo->query($sql);

        // Récupération des résultats
        $paiementsMobile = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retourner les paiements
        return $paiementsMobile;

    } catch (PDOException $e) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['erreur_db'] = $e->getMessage();
        return [];
    }
}




function deleteFromPayment($pdo, $song_id) {
    $deleteQuery = "DELETE FROM payment WHERE ID = :song_id";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->bindParam(':song_id', $song_id, PDO::PARAM_INT);
    return $stmt->execute();
}

function deleteFromPaymentCard($pdo, $song_id) {
    $deleteQuery = "DELETE FROM paiment_par_card WHERE ID = :song_id";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->bindParam(':song_id', $song_id, PDO::PARAM_INT);
    return $stmt->execute();
}

function deleteFromPaymentMobile($pdo, $song_id) {
    $deleteQuery = "DELETE FROM paiment_mobile WHERE ID = :song_id";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->bindParam(':song_id', $song_id, PDO::PARAM_INT);
    return $stmt->execute();
}

function getCategoryName($categoryId) {
    switch($categoryId) {
        case 1:
            return "Panneaux Solaires";
        case 2:
            return "Plomberie Écologique";
        case 3:
            return "Produits Écologiques";
        case 4:
            return "Gestion des Déchets";
        case 5:
            return "Technologie Smart";
        case 6:
            return "Smart Jardin";
        default:
            return "Catégorie Inconnue";
    }
}
function getServiceById($pdo, $serviceId) {
    try {
        $query = "SELECT * FROM services WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $serviceId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // En cas d'erreur, retourner un tableau vide ou null
        return null;
    }
}
?>