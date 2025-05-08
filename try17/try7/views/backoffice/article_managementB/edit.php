<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../controllers/ArticleController.php';
require_once __DIR__ . '/../../../models/Article.php';

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

$controller = new ArticleController();
$article = null;

// Récupérer l'article à modifier
if (isset($_GET['auteur_id'])) {
    $article = $controller->getArticleByAuteurId($_GET['auteur_id']);
    if (!$article) {
        header('Location: show.php?error=article_not_found');
        exit();
    }
} else {
    header('Location: show.php?error=missing_id');
    exit();
}

// Traiter la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $article = new Article(
        $_POST['titre'],
        $_POST['contenu'],
        $_POST['auteur_id'],
        date('Y-m-d H:i:s'),
        $_POST['categorie'],
        $_POST['imageArticle'] ?? null,
        $_POST['shared_from'] ?? null
    );
    $article->setAuteurId($_POST['update_id']);

    if ($controller->updateArticle($article, isset($_FILES['imageArticle']) ? $_FILES['imageArticle'] : null)) {
        header('Location: show.php?success=1');
        exit();
    }
    header('Location: edit.php?auteur_id=' . $_POST['update_id'] . '&error=1');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Modifier un Article</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../template/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../template/assets/css/plugins.min.css">
    <link rel="stylesheet" href="../template/assets/css/kaiadmin.min.css">
    <link rel="stylesheet" href="../template/assets/css/demo.css">
    <script src="../template/assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["../template/assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>
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
      <h4 class="page-title">Modifier un Article</h4>
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
          <a href="#">Modifier</a>
        </li>
      </ul>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Modifier l'Article</h4>
            <a href="show.php" class="btn btn-secondary btn-round ml-auto">
              <i class="fas fa-list"></i> Retour à la liste
            </a>
          </div>
          <div class="card-body">
            <form id="articleForm" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(event);">
              <input type="hidden" name="update_id" value="<?= htmlspecialchars($article['auteur_id'] ?? '') ?>">
              <input type="hidden" name="auteur_id" value="<?= htmlspecialchars($article['auteur_id'] ?? '') ?>">
              
              <div class="form-group">
                <label for="titre">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" value="<?= htmlspecialchars($article['titre'] ?? '') ?>" placeholder="Entrez le titre de l'article">
                <div class="error" id="titreError" style="color: red;"></div>
              </div>

              <div class="form-group">
                <label for="contenu">Contenu</label>
                <textarea class="form-control" id="contenu" name="contenu" rows="5" placeholder="Entrez le contenu de l'article"><?= htmlspecialchars($article['contenu'] ?? '') ?></textarea>
                <div class="error" id="contenuError" style="color: red;"></div>
              </div>

              <div class="form-group">
                <label for="categorie">Catégorie</label>
                <select class="form-control" id="categorie" name="categorie">
                  <option value="">Sélectionnez une catégorie</option>
                  <?php
                  $categories = ['Services', 'Equipements'];
                  foreach ($categories as $cat) {
                      $selected = (isset($article['categorie']) && $article['categorie'] === $cat) ? 'selected' : '';
                      echo "<option value=\"".htmlspecialchars($cat)."\""." $selected>".htmlspecialchars($cat)."</option>";
                  }
                  ?>
                </select>
                <div class="error" id="categorieError" style="color: red;"></div>
              </div>

              <div class="form-group">
                <label for="imageArticle">Image de l'article</label>
                <input type="file" class="form-control" id="imageArticle" name="imageArticle" accept="image/*">
                <?php if (!empty($article['imageArticle'])): ?>
                  <p class="current-image">Image actuelle: <?= htmlspecialchars($article['imageArticle']) ?></p>
                  <input type="hidden" name="current_image" value="<?= htmlspecialchars($article['imageArticle']) ?>">
                <?php endif; ?>
                <div class="error" id="imageError" style="color: red;"></div>
              </div>

              <div class="form-group">
                <label for="shared_from">Partagé depuis</label>
                <input type="text" class="form-control" id="shared_from" name="shared_from" value="<?= htmlspecialchars($article['shared_from'] ?? '') ?>" placeholder="Source de partage (optionnel)">
              </div>

              <div class="button-group">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Mettre à jour</button>
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