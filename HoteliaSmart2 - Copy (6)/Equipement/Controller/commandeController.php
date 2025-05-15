<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../Model/commande.php');
require_once(__DIR__ . '/equipementController.php');

class commandeController
{
    private $equipementController;

    public function __construct()
    {
        $this->equipementController = new equipementController();
    }

    public function listCommandes($filter = '', $sort = 'featured')
    {
        $db = config::getConnexion();
        try {
            $orderBy = '';
            switch ($sort) {
                case 'order_id_asc':
                    $orderBy = 'ORDER BY idCommande ASC';
                    break;
                case 'order_id_desc':
                    $orderBy = 'ORDER BY idCommande DESC';
                    break;
                case 'date_asc':
                    $orderBy = 'ORDER BY date ASC';
                    break;
                case 'date_desc':
                    $orderBy = 'ORDER BY date DESC';
                    break;
                case 'total_asc':
                    $orderBy = 'ORDER BY total ASC';
                    break;
                case 'total_desc':
                    $orderBy = 'ORDER BY total DESC';
                    break;
                default:
                    $orderBy = '';
            }
            if ($filter !== '') {
                $sql = "SELECT * FROM commande WHERE (LEFT(nomClient, 1) = :filter OR LEFT(prenomClient, 1) = :filter) $orderBy";
                $query = $db->prepare($sql);
                $query->bindValue(':filter', $filter);
                $query->execute();
                return $query;
            } else {
                $sql = "SELECT * FROM commande $orderBy";
                $liste = $db->query($sql);
                return $liste;
            }
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteCommande($idCommande)
    {
        $sql = "DELETE FROM commande WHERE idCommande = :idCommande";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':idCommande', $idCommande);
        try
        {
            $req->execute();
        }
        catch (Exception $e)
        {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function addCommande($commande, $orderedItems)
    {
        $db = config::getConnexion();
        try {
            $db->beginTransaction();

            // Insert the order
            $sql = "INSERT INTO commande (date, nomClient, prenomClient, adresseClient, mailClient, modePaiment, total) 
                    VALUES (:date, :nomClient, :prenomClient, :adresseClient, :mailClient, :modePaiment, :total)";
            $query = $db->prepare($sql);
            $query->execute([
                'date' => $commande->getDate(),
                'nomClient' => $commande->getNomClient(),
                'prenomClient' => $commande->getPrenomClient(),
                'adresseClient' => $commande->getAdresseClient(),
                'mailClient' => $commande->getMailClient(),
                'modePaiment' => $commande->getModePaiment(),
                'total' => $commande->getTotal()
            ]);

            $orderId = $db->lastInsertId();

            foreach ($orderedItems as $item) {
                $equipment = $this->equipementController->showEquipement($item['reference']);
                if (!$equipment) {
                    throw new Exception("Equipment not found: " . $item['reference']);
                }

                // Use the correct table name and columns
                $sql = "INSERT INTO commande_equipement (idCommande, reference, quantite, prixUnitaire) 
                        VALUES (:idCommande, :reference, :quantite, :prixUnitaire)";
                $query = $db->prepare($sql);
                $query->execute([
                    'idCommande' => $orderId,
                    'reference' => $item['reference'],
                    'quantite' => $item['quantity'],
                    'prixUnitaire' => $equipment['prix']
                ]);

                // Remove duplicate call to reduceEquipmentQuantity
                $this->equipementController->reduceEquipmentQuantity($item['reference'], $item['quantity']);
            }

            $db->commit();
            return $orderId;
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception('Error processing order: ' . $e->getMessage());
        }
    }

    public function updateCommande($commande)
    {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE commande SET 
                    date = :date,
                    nomClient = :nomClient,
                    prenomClient = :prenomClient,
                    adresseClient = :adresseClient,
                    mailClient = :mailClient,
                    modePaiment = :modePaiment,
                    total = :total
                 WHERE idCommande = :idCommande'
            );
            
            $query->execute([
                'idCommande' => $commande->getIdCommande(),
                'date' => $commande->getDate(),
                'nomClient' => $commande->getNomClient(),
                'prenomClient' => $commande->getPrenomClient(),
                'adresseClient' => $commande->getAdresseClient(),
                'mailClient' => $commande->getMailClient(),
                'modePaiment' => $commande->getmodePaiment(),
                'total' => $commande->getTotal()
            ]);
            echo $query->rowCount() . " records UPDATED successfully <br>";
        }
        catch (PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
    }

    public function showCommande($idCommande)
    {
        $sql = "SELECT * FROM commande WHERE idCommande = :idCommande";
        $db = config::getConnexion();
        try
        {
            $query = $db->prepare($sql);
            $query->execute(['idCommande' => $idCommande]);
            $commande = $query->fetch();
            return $commande;
        }
        catch (Exception $e)
        {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getCommandeDetails($idCommande)
    {
        // Corrected column name from e.nomEquipement to e.nom
        $sql = "SELECT c.*, ce.quantite, e.nom, e.prix, e.type, e.image
                FROM commande c
                LEFT JOIN commande_equipement ce ON c.idCommande = ce.idCommande
                LEFT JOIN equipement e ON ce.reference = e.reference
                WHERE c.idCommande = :idCommande";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['idCommande' => $idCommande]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);

            if (empty($results)) {
                return null; // Order not found
            }

            // Structure the data
            $orderDetails = [
                'idCommande' => $results[0]['idCommande'],
                'date' => $results[0]['date'],
                'nomClient' => $results[0]['nomClient'],
                'prenomClient' => $results[0]['prenomClient'],
                'adresseClient' => $results[0]['adresseClient'],
                'mailClient' => $results[0]['mailClient'],
                'modePaiment' => $results[0]['modePaiment'],
                'total' => $results[0]['total'],
                'equipements' => []
            ];

            foreach ($results as $row) {
                // Corrected array key from 'nomEquipement' to 'nom'
                if (!empty($row['nom'])) { // Check if there's associated equipment
                    $orderDetails['equipements'][] = [
                        'nom' => $row['nom'], // Keep 'nomEquipement' key for consistency in the returned array structure, but fetch from 'nom'
                        'quantite' => $row['quantite'],
                        'prix' => $row['prix'],
                        'type' => $row['type'],
                        'image' => $row['image']
                    ];
                }
            }

            return $orderDetails;
        } catch (Exception $e) {
            // Log error or handle it as needed
            error_log('Error fetching order details: ' . $e->getMessage());
            throw $e; // Re-throw the exception to be caught by the caller
        }
    }
}
?>
