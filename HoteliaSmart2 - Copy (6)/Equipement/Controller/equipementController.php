<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../Model/equipement.php');

class equipementController
{ 
    public function listEquipement($sort = 'featured')
    {
        $orderBy = '';
        switch ($sort) {
            case 'name_az':
                $orderBy = 'ORDER BY nom ASC';
                break;
            case 'name_za':
                $orderBy = 'ORDER BY nom DESC';
                break;
            case 'price_asc':
                $orderBy = 'ORDER BY prix ASC';
                break;
            case 'price_desc':
                $orderBy = 'ORDER BY prix DESC';
                break;
            case 'date_newest':
                $orderBy = 'ORDER BY date_ajout DESC';
                break;
            case 'date_oldest':
                $orderBy = 'ORDER BY date_ajout ASC';
                break;
            default:
                $orderBy = '';
        }
        $sql = "SELECT reference, nom, prix, quantite, type, image, guide FROM equipement $orderBy";
        $db = config::getConnexion();
        try {
            $liste = $db->query($sql);
            return $liste->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    public function deleteEquipement($reference)
    {
        var_dump($reference);
        $sql = "DELETE FROM equipement WHERE reference=:reference";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':reference', $reference);
        try
        {
            $req->execute();
        }
        catch (Exception $e)
        {
            die('Erreur:'.$e->getMessage());
        }
    }
    public function addEquipement($equipement)
    {
        $sql = "INSERT INTO equipement(nom, prix, quantite, type, image, guide) VALUES (:nom, :prix, :quantite, :type, :image, :guide)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $equipement->getNom(),
                'prix' => $equipement->getPrix(),
                'quantite' => $equipement->getQuantite(),
                'type' => $equipement->getType(),
                'image' => $equipement->getImage(),
                'guide' => $equipement->getGuide()
            ]);
            return true;
        } catch (Exception $e) {
            error_log('Add error: ' . $e->getMessage());
            return false;
        }
    }
    public function updateEquipement($equipement)
    {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE equipement SET 
                reference = :reference, 
                nom = :nom, 
                prix = :prix, 
                quantite = :quantite, 
                type = :type,
                image = :image,
                guide = :guide
                WHERE reference = :reference'
            );
            
            $query->execute([
                'reference' => $equipement->getReference(),
                'nom' => $equipement->getNom(),
                'prix' => $equipement->getPrix(),
                'quantite' => $equipement->getQuantite(),
                'type' => $equipement->getType(),
                'image' => $equipement->getImage(),
                'guide' => $equipement->getGuide()
            ]);
            
            return $query->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Update error: ' . $e->getMessage());
            return false;
        }
    }
    public function showEquipement($reference)
    {
        $sql = "SELECT * FROM equipement WHERE reference=:reference";
        $db = config::getConnexion();
        try
        {
            $query = $db->prepare($sql);
            $query->execute(array('reference' => $reference));
            $equipement = $query->fetch();
            return $equipement;
        }
        catch (Exception $e)
        {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    public function getRelatedEquipement($type, $currentId, $limit = 3)
    {
        $sql = "SELECT * FROM equipement WHERE type=:type AND reference!=:currentId LIMIT :limit";
        $db = config::getConnexion();
        try
        {
            $query = $db->prepare($sql);
            $query->bindValue(':type', $type);
            $query->bindValue(':currentId', $currentId);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            $relatedEquipments = $query->fetchAll(PDO::FETCH_ASSOC);
            return $relatedEquipments;
        }
        catch (Exception $e)
        {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function reduceEquipmentQuantity($reference, $orderedQuantity)
    {
        try {
            $db = config::getConnexion();
            
            // First check if we have enough quantity
            $checkSql = "SELECT quantite FROM equipement WHERE reference = :reference";
            $checkQuery = $db->prepare($checkSql);
            $checkQuery->bindValue(':reference', $reference);
            $checkQuery->execute();
            $currentQuantity = $checkQuery->fetchColumn();
            
            if ($currentQuantity < $orderedQuantity) {
                throw new Exception("Insufficient stock. Available: " . $currentQuantity);
            }
            
            // Update the quantity
            $sql = "UPDATE equipement SET quantite = quantite - :orderedQuantity WHERE reference = :reference";
            $query = $db->prepare($sql);
            $query->bindValue(':reference', $reference);
            $query->bindValue(':orderedQuantity', $orderedQuantity, PDO::PARAM_INT);
            $query->execute();
            
            return true;
        } catch (Exception $e) {
            throw new Exception('Error reducing quantity: ' . $e->getMessage());
        }
    }
}
?>