<?php
require_once 'app/models/Restaurant.php';
session_start();

require_once 'persistence/DAO/RestaurantDAO.php';
require_once 'persistence/conf/PersistentManager.php';

// Validar si el usuario está autenticado y definir el tipo de usuario
$loggedInUser = isset($_SESSION['userName']) ? $_SESSION['userName'] : null;
$userType = isset($_SESSION['userType']) ? $_SESSION['userType'] : null;

// Obtener los restaurantes
if (isset($_SESSION['search_results']) && !empty($_SESSION['search_results'])) {
    $restaurantes = $_SESSION['search_results'];
    unset($_SESSION['search_results']);
} else {
    $conn = PersistentManager::get_connection();
    $restaurantDAO = new RestaurantDAO($conn);
    $restaurantes = $restaurantDAO->selectAll();
}

if (isset($_GET['console_message']) && !empty($_GET['console_message'])) {
    $consoleMessage = htmlspecialchars($_GET['console_message'], ENT_QUOTES, 'UTF-8');
    echo "<script>console.log('$consoleMessage');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>4V EL Tenedor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/octicons/3.5.0/octicons.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <nav class="navbar navbar-light bg-light px-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><strong>El Tenedor 4V</strong></a>

            <?php if ($loggedInUser): ?>
                <!-- Si el usuario está autenticado -->
                <div class="d-flex align-items-center">
                    <span class="me-3">Bienvenido, <strong><?= htmlspecialchars($loggedInUser); ?></strong> (<?= htmlspecialchars($userType); ?>)</span>
                    <?php if ($userType === 'Gestor' || $userType === 'Admin'): ?>
                        <!-- Botón de nuevo restaurante visible para Gestores y Admins -->
                        <a class="btn btn-primary me-2" href="app/views/insert.php">Nuevo Restaurante</a>
                    <?php endif; ?>
                    <a class="btn btn-outline-danger" href="app/controllers/LogoutController.php">Cerrar Sesión</a>
                </div>
            <?php else: ?>
                <!-- Si el usuario no está autenticado -->
                <form class="d-flex" method="POST" action="app/controllers/LoginController.php">
                    <input class="form-control me-2" type="text" name="email" placeholder="User (email)" aria-label="User" required>
                    <input class="form-control me-2" type="password" name="password" placeholder="Password" aria-label="Password" required>
                    <button class="btn btn-outline-success" type="submit"><i class="bi bi-box-arrow-in-right"></i> Acceder</button>
                </form>
            <?php endif; ?>
        </div>
    </nav>


    <div class="container-fluid d-flex jumbotron pb-2 bg-info">
        <div class="container claro">
            <h3 class="display-4">Descubre y reserva el mejor restaurante</h3>
            <div class="row">
                <div class="col-md-4">
                    <img class="img-fluid rounded" src="./assets/img/logo.png" alt="">
                </div>
                <div class="col-md-8 minamespace">
                    <p class="lead">Una aplicación de 4Vientos.</p>
                </div>
            </div>
            <form action="app/controllers/SearchController.php" method="POST">
                <div class="input-group mb-2">
                    <input type="text" class="form-control mr-2" id="category" name="category" placeholder="Ingresa la categoría" required>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="container my-3">
        <div class="row">
            <?php
            if (!empty($restaurantes)) {
                foreach ($restaurantes as $r) {
                    echo $r->restaurant2HTML();
                }
            } else {
                echo '<div class="alert alert-info">No se encontraron restaurantes para mostrar.</div>';
            }
            ?>
        </div>
    </div>

    <footer class="footer fixed-bottom">
        Cuatrovientos - Desarrollo de WEB
    </footer>

    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery-3.6.0.min.js"></script>
</body>
</html>
