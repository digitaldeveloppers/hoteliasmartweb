<?php
require_once '../../config.php';
require_once '../../Controller/servivecontrolle.php';

$pdo = config::getConnexion();
?>

<?php
$pageTitle = 'Add New Service';
ob_start();
?>
<div class="add-service-container">
<style>
.add-service-container {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-width: 800px;
    margin: 0 auto;
}

.form-header {
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: 500;
}

input[type="text"],
input[type="number"],
textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s;
}

input[type="submit"]:hover {
    background-color: #45a049;
}
</style>

<div class="form-header">
    <h2>Add New Service</h2>
</div>
        <form action="" method="POST" class="service-form">
            <div class="form-group">
                <label for="title">Titre du Service:</label>
                <input type="text" id="title" name="name">
            </div>

            <div class="form-group">
                <label for="category">Catégorie de Service:</label>
                <select id="category" name="service">
                    <option value="1">Installation de Panneaux Solaires</option>
                    <option value="2">Conservation de l'Eau</option>
                    <option value="3">Nettoyage Écologique</option>
                    <option value="4">Gestion Intelligente des Déchets</option>
                    <option value="5">Intégration de Technologies Intelligentes</option>
                    <option value="6">Espaces Verts & Jardinage</option>
                </select>
            </div>

            <div class="form-group">
                <label for="quantity">Quantité:</label>
                <input type="number" id="quantity" name="solarNumber">
            </div>

            <div class="form-group">
                <label for="price">Prix:</label>
                <input type="number" id="price" name="price" step="0.01">
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="type"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Ajouter le Service</button>
                <a href="showserv.php" class="btn-cancel">Annuler</a>
            </div>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = $_POST['name'];
            $quantity = $_POST['solarNumber'];
            $price = $_POST['price'];
            $service = $_POST['service'];
            $type = $_POST['type'];

            $result = addService($pdo, $title, $quantity, $price, $service, $type);
            
            if ($result) {
                header("Location: showserv.php");
                exit();
            } else {
                echo "<div class='error-message'>Erreur lors de l'ajout du service. Veuillez réessayer.</div>";
            }
        }
        ?>
    </div>
    <script src="addService.js"></script>
</body>
</html>