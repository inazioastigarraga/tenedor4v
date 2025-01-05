<?php
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
        <title>Restaurante</title>
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
            <h2 id="formTitle">Agregar Nuevo Restaurante</h2>
            <form class="form-horizontal" method="post" action="" onsubmit="return validateForm();">
                <input type="hidden" name="id" id="id" value="<?= isset($restaurant['id']) ? $restaurant['id'] : ''; ?>">
                <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Nombre</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="name" id="name" value="<?= isset($restaurant['name']) ? $restaurant['name'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="image" class="col-sm-2 control-label">Imagen</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="image" name="image" placeholder="URL de la imagen" value="<?= isset($restaurant['image']) ? $restaurant['image'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="menu" class="col-sm-2 control-label">Menú</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="menu" name="menu" value="<?= isset($restaurant['menu']) ? $restaurant['menu'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="priceRange" class="col-sm-2 control-label">Precio (rango)</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="priceRange" name="priceRange" placeholder="Ejemplo: 20.00-30.00" value="<?= isset($restaurant['minorprice']) && isset($restaurant['mayorprice']) ? $restaurant['minorprice'] . '-' . $restaurant['mayorprice'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="category">Categoría</label>
                    <select class="form-control" id="category" name="category">
                        <option value="">Sin Categoría</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category->getName()); ?>">
                                <?= htmlspecialchars($category->getName()); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <br>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" id="submitButton">Insertar</button>
                    </div>
                </div>
            </form>
        </div>

        <script>
            // Cambiar texto del formulario si se trata de edición
            const restaurantId = document.getElementById('id').value;
            if (restaurantId) {
                document.getElementById('formTitle').innerText = 'Editar Restaurante';
                document.getElementById('submitButton').innerText = 'Actualizar';
                document.querySelector('form').action = '../controllers/EditController.php';
            } else {
                document.querySelector('form').action = '../controllers/InsertController.php';
            }
        </script>
    </body>
</html>
