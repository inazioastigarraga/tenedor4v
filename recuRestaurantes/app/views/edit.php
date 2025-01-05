<?php
session_start();

// Validar si los datos del restaurante están disponibles en la sesión
if (!isset($_SESSION['restaurant'])) {
    echo "<script>alert('No se encontraron datos del restaurante.');</script>";
    echo "<script>window.history.back();</script>";
    exit;
}

// Recuperar los datos del restaurante
$restaurant = $_SESSION['restaurant'];
require_once '../../persistence/DAO/CategoryDAO.php';
require_once '../../persistence/conf/PersistentManager.php';

$conn = PersistentManager::get_connection();
$categoryDAO = new CategoryDAO($conn);
$categories = $categoryDAO->selectAll();

if (!$categories) {
    echo "<script>alert('No se encontraron categorías.');</script>";
    echo "<script>window.history.back();</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Restaurante</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script>
        function validateForm() {
            const priceInput = document.getElementById('priceRange').value;
            const regex = /^\d+(\.\d{1,2})?-\d+(\.\d{1,2})?$/;

            if (!regex.test(priceInput)) {
                alert('El campo de precio debe estar en el formato: "20.00-30.00".');
                return false;
            }

            const [minorPrice, majorPrice] = priceInput.split('-').map(Number);
            if (minorPrice >= majorPrice) {
                alert('El precio mínimo debe ser menor que el precio máximo.');
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <br>
        <h2>Editar Restaurante</h2>
        <form class="form-horizontal" method="post" action="../controllers/EditController.php" onsubmit="return validateForm();">
            <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($restaurant['id']); ?>">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($restaurant['name']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="image" class="col-sm-2 control-label">Imagen</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="image" name="image" placeholder="URL de la imagen" value="<?= htmlspecialchars($restaurant['image']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="menu" class="col-sm-2 control-label">Menú</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="menu" name="menu" value="<?= htmlspecialchars($restaurant['menu']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="priceRange" class="col-sm-2 control-label">Precio (rango)</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="priceRange" name="priceRange" placeholder="Ejemplo: 20.00-30.00" value="<?= htmlspecialchars($restaurant['minorprice'] . '-' . $restaurant['mayorprice']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="category">Categoría</label>
                <select class="form-control" name="category">
                    <option value="" <?= is_null($restaurant['idCategory']) ? 'selected' : ''; ?>>Sin Categoría</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category->getName()); ?>"
                            <?= $category->getId() == $restaurant['idCategory'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($category->getName()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <br>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
