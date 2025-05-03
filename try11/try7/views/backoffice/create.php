<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/ArticleController.php';

$controller = new ArticleController();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_article'])) {
    require_once __DIR__ . '/../../models/Article.php';
    $article = new Article(
        $_POST['titre'],
        $_POST['contenu'],
        null,
        date('Y-m-d H:i:s'),
        $_POST['categorie'],
        $_POST['imageArticle'] ?? null,
        $_POST['shared_from'] ?? null
    );
    if ($controller->addArticle($article, $_FILES['imageArticle'])) {
        header('Location: show.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ajouter un Article</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="stylesback.css">
    <script src="js/validation.js" defer></script>
</head>
<body>
    

  
    

</body>
</html>
<?php
// Start output buffering to capture content for the layout
ob_start();
?>
<div class="content-wrapper">
  <div class="page-inner">
    <div class="page-header">
      <h4 class="page-title">Ajouter un Article</h4>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="index.php">
            <i class="flaticon-home"></i>
          </a>
        </li>
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a href="#">Articles</a>
        </li>
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a href="#">Ajouter</a>
        </li>
      </ul>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Nouvel Article</h4>
            <a href="show.php" class="btn btn-secondary btn-round ml-auto">
              <i class="fas fa-list"></i> Retour à la liste
            </a>
          </div>
          <div class="card-body">
            <form id="articleForm" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(event, true)">
              <input type="hidden" name="add_article" value="1">
              <div class="form-group">
                <label for="titre">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" placeholder="Entrez le titre de l'article">
                <div class="error" id="titreError" style="color: red;"></div>
              </div>
              <div class="form-group">
                <label for="contenu">Contenu</label>
                <textarea class="form-control" id="contenu" name="contenu" rows="5" placeholder="Entrez le contenu de l'article"></textarea>
                <div class="error" id="contenuError" style="color: red;"></div>
              </div>
              <div class="form-group">
                <label for="categorie">Catégorie</label>
                <select class="form-control" id="categorie" name="categorie">
                  <option value="">Sélectionnez une catégorie</option>
                  <option value="Services">Services</option>
                  <option value="Equipements">Equipements</option>
                </select>
                <div class="error" id="categorieError" style="color: red;"></div>
              </div>
              <div class="form-group">
                <label for="imageArticle">Image de l'article</label>
                <input type="file" class="form-control" id="imageArticle" name="imageArticle" accept="image/*">
                <div class="error" id="imageError" style="color: red;"></div>
              </div>
              <div class="form-group">
                <label for="shared_from">Partagé depuis</label>
                <input type="text" class="form-control" id="shared_from" name="shared_from" placeholder="Source de partage (optionnel)">
              </div>
              <div class="button-group">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enregistrer</button>
                <button type="button" class="btn btn-danger" onclick="clearForm()"><i class="fas fa-undo"></i> Réinitialiser</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$pageContent = ob_get_clean();
require 'layout.php';
?>
</body>
</html>