<?php 
session_start();
require_once '../models/Restaurant.php';
require_once '../../persistence/DAO/RestaurantDAO.php';
require_once '../../persistence/DAO/CategoryDAO.php';
require_once '../../persistence/conf/PersistentManager.php';

// Validar si el usuario está autenticado y tiene permisos para esta acción
if (!isset($_SESSION['userType']) || ($_SESSION['userType'] !== 'Gestor' && $_SESSION['userType'] !== 'Admin')) {
    // Redirigir a la página de error si no tiene permisos
    header("Location: ../../views/error_permission.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    createAction();
}

function createAction() {
    $errors = [];

    // Validar que todos los campos están presentes y no están vacíos
    if (empty($_POST["name"]) || empty($_POST["image"]) || empty($_POST["menu"]) || empty($_POST["priceRange"])) {
        $errors[] = "Por favor, rellena todos los campos obligatorios.";
    }

    // Validar formato del campo de precio
    if (!empty($_POST["priceRange"])) {
        $priceRange = $_POST["priceRange"];
        $regex = '/^\d+(\.\d{1,2})?-\d+(\.\d{1,2})?$/';

        if (!preg_match($regex, $priceRange)) {
            $errors[] = "El campo de precio debe estar en el formato: '20.00-30.00'.";
        } else {
            list($minorPrice, $mayorPrice) = explode('-', $priceRange);

            // Validar que los precios son números y el precio mínimo es menor que el máximo
            if (!is_numeric($minorPrice) || !is_numeric($mayorPrice) || $minorPrice <= 0 || $mayorPrice <= 0 || $minorPrice >= $mayorPrice) {
                $errors[] = "El rango de precios debe tener un valor mínimo menor al máximo, y ambos deben ser mayores que 0.";
            }
        }
    }

    if (!empty($errors)) {
        echo "<script>alert('" . implode("\\n", $errors) . "');</script>";
        echo "<script>window.history.back();</script>";
        return;
    }

    // Obtener el idCategory según el nombre de la categoría seleccionada, o dejarlo en NULL
    $conn = PersistentManager::getInstance()->get_connection();
    $idCategory = null;

    if (!empty($_POST["category"])) {
        $categoryDAO = new CategoryDAO($conn);
        $categoryObj = $categoryDAO->findByName($_POST["category"]);

        if (!$categoryObj) {
            echo "<script>alert('Categoría no encontrada.');</script>";
            echo "<script>window.history.back();</script>";
            return;
        }

        $idCategory = $categoryObj->getId();
    }

    // Crear un nuevo objeto Restaurante y establecer sus propiedades
    $restaurant = new Restaurant();
    $restaurant->setName($_POST["name"]);
    $restaurant->setImage($_POST["image"]);
    $restaurant->setMenu($_POST["menu"]);
    $restaurant->setMinorPrice($minorPrice);
    $restaurant->setMayorPrice($mayorPrice);
    $restaurant->setIdCategory($idCategory);

    // Insertar datos en la base de datos
    $sql = "INSERT INTO Restaurant (name, image, menu, minorprice, mayorprice, idcategory) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Vincular los parámetros a la consulta
        $name = $restaurant->getName();
        $image = $restaurant->getImage();
        $menu = $restaurant->getMenu();

        $stmt->bind_param('sssddi', $name, $image, $menu, $minorPrice, $mayorPrice, $idCategory);

        // Ejecutar la consulta e insertar los datos
        if ($stmt->execute()) {
            echo "<script>alert('Nuevo registro creado exitosamente');</script>";
            echo "<script>window.location.href='../../index.php';</script>";
        } else {
            $errors[] = "Error al insertar el restaurante: " . $stmt->error;
            echo "<script>alert('" . implode("\\n", $errors) . "');</script>";
            echo "<script>window.history.back();</script>";
        }

        // Cerrar la declaración
        $stmt = null;
    } else {
        $errors[] = "Error al preparar la declaración: " . $conn->error;
        echo "<script>alert('" . implode("\\n", $errors) . "');</script>";
        echo "<script>window.history.back();</script>";
    }

    PersistentManager::getInstance()->close_connection();
}
?>
