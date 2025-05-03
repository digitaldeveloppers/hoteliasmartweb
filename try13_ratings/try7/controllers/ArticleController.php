<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Article.php';

class ArticleController {
    private $tableName;
    private $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private $maxFileSize = 2 * 1024 * 1024; // 2MB
    private $uploadDir = __DIR__ . '/../uploads/';
    private $validCategories = ['Services', 'Equipements'];

    public function getStatsByCategory() {
        $db = config::getConnexion();
        
        try {
            if (!isset($this->tableName)) {
                $this->tableName = 'article';
            }
            
            // Vérifier si la table existe et contient des données
            $checkTable = "SELECT COUNT(*) as total FROM {$this->tableName}";
            $stmt = $db->prepare($checkTable);
            $stmt->execute();
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($total == 0) {
                error_log("La table {$this->tableName} est vide");
                return [];
            }
            
            // Vérifier si la colonne categorie existe
            $checkColumn = "SHOW COLUMNS FROM {$this->tableName} LIKE 'categorie'";
            $stmt = $db->prepare($checkColumn);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                error_log("La colonne 'categorie' n'existe pas dans la table {$this->tableName}");
                return [];
            }
            
            // Obtenir les statistiques par catégorie
            $query = "SELECT categorie, COUNT(*) as count FROM {$this->tableName} GROUP BY categorie ORDER BY count DESC";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($results)) {
                error_log("Aucune donnée trouvée avec des catégories dans la table {$this->tableName}");
                return [];
            }
            
            foreach ($results as $result) {
                if (!isset($result['categorie']) || !isset($result['count'])) {
                    error_log('Structure de données invalide dans les résultats');
                    return [];
                }
            }
            
            return $results;
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération des statistiques par catégorie: ' . $e->getMessage());
            return [];
        }
    }

    public function __construct($tableName = 'article') {
        $this->tableName = $tableName;
        
        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function getArticleByAuteurId($auteur_id) {
        $sql = "SELECT * FROM $this->tableName WHERE auteur_id = :auteur_id";
        $db = config::getConnexion();
    
        try {
            $query = $db->prepare($sql);
            $query->execute(['auteur_id' => $auteur_id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error fetching article by auteur_id: ' . $e->getMessage());
            return false;
        }
    }

    public function getAllArticles() {
        $sql = "SELECT * FROM $this->tableName ORDER BY date_article DESC";
        $db = config::getConnexion();
        
        try {
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error fetching articles: ' . $e->getMessage());
            return false;
        }
    }

    public function addArticle($article, $imageFile) {
        // Validate required fields
        if (empty($article->getTitre()) || empty($article->getContenu())) {
            error_log('Title and content are required');
            return false;
        }

        // Handle image upload
        $imageName = $this->handleImageUpload($imageFile);
        if ($imageName === false) {
            error_log('Image upload failed');
            return false;
        }

        // Save article to database
        $sql = "INSERT INTO $this->tableName 
                (titre, contenu, date_article, categorie, imageArticle, shared_from) 
                VALUES (:titre, :contenu, :date_article, :categorie, :image, :shared_from)";

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $success = $query->execute([
                'titre' => htmlspecialchars($article->getTitre()),
                'contenu' => htmlspecialchars($article->getContenu()),
                'date_article' => $article->getDateArticle(),
                'categorie' => htmlspecialchars($article->getCategorie()),
                'image' => $imageName,
                'shared_from' => !empty($article->getSharedFrom()) ? htmlspecialchars($article->getSharedFrom()) : null
            ]);
            return $success ? $db->lastInsertId() : false;
        } catch (Exception $e) {
            error_log('Error adding article: ' . $e->getMessage());
            return false;
        }
    }

   

    public function updateArticle($article, $imageFile = null) {
        // Validate required fields
        if (empty($article->getAuteurId()) || empty($article->getTitre()) || empty($article->getContenu())) {
            error_log('Missing required fields for update');
            return false;
        }

        // Get current article data
        $currentArticle = $this->getArticleByAuteurId($article->getAuteurId());
        if (!$currentArticle) {
            error_log('Article not found');
            return false;
        }

        // Handle image upload if new image is provided
        $imageName = $currentArticle['imageArticle'];
        if ($imageFile !== null && $imageFile['error'] === UPLOAD_ERR_OK) {
            $newImageName = $this->handleImageUpload($imageFile);
            if ($newImageName === false) {
                return false;
            }
            
            // Delete old image if it's not the default
            if ($imageName !== 'default.jpg') {
                $this->deleteImage($imageName);
            }
            
            $imageName = $newImageName;
        }

        // Update article in database
        $sql = "UPDATE $this->tableName SET 
                titre = :titre, 
                contenu = :contenu, 
                auteur_id = :auteur_id,
                date_article = :date_article,
                categorie = :categorie,
                imageArticle = :image,
                shared_from = :shared_from
                WHERE auteur_id = :auteur_id";

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            return $query->execute([
                //'id' => (int)$article->getId(),
                'titre' => htmlspecialchars($article->getTitre()),
                'contenu' => htmlspecialchars($article->getContenu()),
                'auteur_id' => (int)$article->getAuteurId(),
                'date_article' => $article->getDateArticle(),
                'categorie' => htmlspecialchars($article->getCategorie()),
                'image' => $imageName,
                'shared_from' => !empty($article->getSharedFrom()) ? htmlspecialchars($article->getSharedFrom()) : null
            ]);
        } catch (Exception $e) {
            error_log('Error updating article: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteArticle($id) {
        // Get article data first to handle image deletion
        $article = $this->getArticleByAuteurId($id);
        if (!$article) {
            error_log('Article not found for deletion');
            return false;
        }

        // Delete associated image if it's not the default
        if ($article['imageArticle'] !== 'default.jpg') {
            $this->deleteImage($article['imageArticle']);
        }

        // Delete from database
        $sql = "DELETE FROM $this->tableName WHERE auteur_id = :auteur_id";
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            return $query->execute(['auteur_id' => (int)$id]);
        } catch (Exception $e) {
            error_log('Error deleting article: ' . $e->getMessage());
            return false;
        }
    }

    private function handleImageUpload($file) {
        // Debug information
        error_log('Starting file upload process...');
        error_log('Upload directory: ' . $this->uploadDir);
        
        // Check if file was uploaded
        if ($file === null) {
            error_log('No file provided');
            return false;
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
            ];
            $errorMessage = isset($uploadErrors[$file['error']]) 
                         ? $uploadErrors[$file['error']] 
                         : 'Unknown upload error';
            error_log('File upload error: ' . $errorMessage);
            return false;
        }
        
        // Debug file information
        error_log('File details: ' . json_encode([
            'name' => $file['name'],
            'type' => $file['type'],
            'size' => $file['size'],
            'tmp_name' => $file['tmp_name']
        ]));
        
        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        error_log('Detected MIME type: ' . $mimeType);
        error_log('Allowed types: ' . implode(', ', $this->allowedImageTypes));
        
        if (!in_array($mimeType, $this->allowedImageTypes)) {
            error_log('Invalid file type: ' . $mimeType);
            return false;
        }
        
        // Validate file size
        if ($file['size'] > $this->maxFileSize) {
            error_log('File too large: ' . $file['size'] . ' bytes (max: ' . $this->maxFileSize . ' bytes)');
            return false;
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_') . '.' . $extension;
        $destination = $this->uploadDir . $filename;
        
        error_log('Attempting to move file to: ' . $destination);
        
        // Check directory permissions
        if (!is_writable($this->uploadDir)) {
            error_log('Upload directory is not writable: ' . $this->uploadDir);
            return false;
        }
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            error_log('Failed to move uploaded file. PHP error: ' . error_get_last()['message']);
            return false;
        }
        
        error_log('File successfully uploaded as: ' . $filename);
        return $filename;
    }

    private function deleteImage($filename) {
        $filepath = $this->uploadDir . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return true;
    }

    public function displayArticles($articles) {
        if (!is_array($articles) || empty($articles)) {
            echo '<div class="alert alert-info">No articles found</div>';
            return;
        }

        $html = '<div id="main" class="col-md-9">';
        
        foreach (array_chunk($articles, 2) as $articleRow) {
            $html .= '<div class="row">';
            
            foreach ($articleRow as $article) {
                $html .= '<div class="col-md-6 mb-4">';
                $html .= '<div class="card h-100">';
                
                // Article Image
                if (!empty($article['imageArticle'])) {
                    $imagePath = '/uploads/' . htmlspecialchars($article['imageArticle']);
                    $html .= '<img src="' . $imagePath . '" class="card-img-top" alt="' . htmlspecialchars($article['titre']) . '">';
                }
                
                // Article Body
                $html .= '<div class="card-body">';
                $html .= '<h5 class="card-title">' . htmlspecialchars($article['categorie']) . ': ' . htmlspecialchars($article['titre']) . '</h5>';
                $html .= '<p class="card-text">' . nl2br(htmlspecialchars(substr($article['contenu'], 0, 200))) . '...</p>';
                $html .= '</div>';
                
                // Article Footer
                $html .= '<div class="card-footer bg-transparent">';
                $html .= '<small class="text-muted">Posted by User ' . (int)$article['auteur_id'] . ' on ' . htmlspecialchars($article['date_article']) . '</small>';
                
                if (!empty($article['shared_from'])) {
                    $html .= '<br><small class="text-muted">Shared from: ' . htmlspecialchars($article['shared_from']) . '</small>';
                }
                
                $html .= '</div>';
                $html .= '</div>'; // end card
                $html .= '</div>'; // end col
            }
            
            $html .= '</div>'; // end row
        }
        
        $html .= '</div>'; // end main
        
        echo $html;
    }

    public function addRating($auteur_id, $rating_value) {
        $sql = "INSERT INTO ratings (auteur_id, rating_value) VALUES (:auteur_id, :rating_value)";
        $db = config::getConnexion();
    
        try {
            $query = $db->prepare($sql);
            $query->execute(['auteur_id' => $auteur_id, 'rating_value' => $rating_value]);
            return true;
        } catch (Exception $e) {
            error_log('Error adding rating: ' . $e->getMessage());
            return false;
        }
    }

    public function getAverageRating($auteur_id) {
        $sql = "SELECT AVG(rating_value) as average, COUNT(rating_value) as count FROM ratings WHERE auteur_id = :auteur_id";
        $db = config::getConnexion();
    
        try {
            $query = $db->prepare($sql);
            $query->execute(['auteur_id' => $auteur_id]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return [
                'average' => $result['average'] ? (float)$result['average'] : 0,
                'count' => $result['count'] ? (int)$result['count'] : 0
            ];
        } catch (Exception $e) {
            error_log('Error fetching average rating: ' . $e->getMessage());
            return ['average' => 0, 'count' => 0];
        }
    }
}