<?php
require_once '../../../config.php';
require_once '../../../controllers/ArticleController.php';

$articleController = new ArticleController();
$articles = $articleController->getAllArticles();

// Start output buffering to capture content for the layout
ob_start();

?>

<div class="content-wrapper">
  <div class="page-inner">
    <div class="page-header">
      <h4 class="page-title">Articles</h4>
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
      </ul>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title">Liste des Articles</h4>
            <a href="create.php" class="btn btn-primary btn-round ml-auto">
              <i class="fas fa-plus"></i> Ajouter un Article
            </a>
          </div>
          <div class="card-body">
            <div class="row mb-4">
              <div class="col-md-6">
                <input type="text" id="searchTitle" class="form-control" placeholder="Rechercher par titre...">
              </div>
              <div class="col-md-4">
                <select id="filterCategory" class="form-control">
                  <option value="">Toutes les catégories</option>
                  <option value="Services">Services</option>
                  <option value="Equipements">Equipements</option>
                </select>
              </div>
            </div>
            <?php if (isset($error_message)): ?>
              <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
              </div>
            <?php endif; ?>
            <?php if (empty($articles)): ?>
              <div class="text-center py-5">
                <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
                <p class="h4">Aucun article n'a été trouvé.</p>
                <a href="create.php" class="btn btn-primary mt-3">
                  <i class="fas fa-plus-circle"></i> Créer un nouvel article
                </a>
              </div>
            <?php else: ?>
              <div class="row">
                <?php foreach ($articles as $article): ?>
                  <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card card-post card-round h-100">
                      <?php if (!empty($article['imageArticle'])): ?>
                        <div class="card-img-top position-relative" style="height:200px;overflow:hidden;background:#f5f6fa;display:flex;align-items:center;justify-content:center;">
                          <img src="../../../uploads/<?php echo htmlspecialchars($article['imageArticle']); ?>" alt="Image de l'article" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                      <?php endif; ?>
                      <div class="card-body">
                        <h5 class="card-title text-primary mb-2"><?php echo htmlspecialchars($article['titre']); ?></h5>
                        <div class="d-flex align-items-center mb-2">
                          <span class="badge bg-info text-white me-2"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($article['categorie']); ?></span>
                          <span class="text-muted ms-auto"><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($article['date_article'])); ?></span>
                        </div>
                        <div class="separator-solid"></div>
                        <div class="d-flex justify-content-between mt-3">
                          <a href="show_article.php?auteur_id=<?php echo htmlspecialchars($article['auteur_id']); ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Voir</a>
                          <a href="edit.php?auteur_id=<?php echo htmlspecialchars($article['auteur_id']); ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Modifier</a>
                          <a href="delete.php?auteur_id=<?php echo htmlspecialchars($article['auteur_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');"><i class="fas fa-trash"></i> Supprimer</a>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchTitle = document.getElementById('searchTitle');
  const filterCategory = document.getElementById('filterCategory');
  const articleCards = document.querySelectorAll('.card-post');

  function filterArticles() {
    const searchText = searchTitle.value.toLowerCase();
    const selectedCategory = filterCategory.value;

    articleCards.forEach(card => {
      const title = card.querySelector('.card-title').textContent.toLowerCase();
      const category = card.querySelector('.badge').textContent.trim();
      
      const matchesTitle = title.includes(searchText);
      const matchesCategory = !selectedCategory || category === selectedCategory;

      card.closest('.col-lg-4').style.display = 
        matchesTitle && matchesCategory ? 'block' : 'none';
    });
  }

  searchTitle.addEventListener('input', filterArticles);
  filterCategory.addEventListener('change', filterArticles);
});
</script>

<?php

$pageContent = ob_get_clean();
require 'layout.php'
?>