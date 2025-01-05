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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id']) && is_numeric($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];
        fetchRestaurant($id);
    } else {
        echo "<script>alert('ID inválido o no proporcionado.');</script>";
        echo "<script>window.history.back();</script>";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id']) && is_numeric($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];
        updateRestaurant($id);
    } else {
        echo "<script>alert('ID inválido o no proporcionado.');</script>";
        echo "<script>window.history.back();</script>";
    }
}

function fetchRestaurant($id) {
    $conn = PersistentManager::getInstance()->get_connection();
    $sql = "SELECT * FROM Restaurant WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $restaurant = $result->fetch_assoc();
            $_SESSION['restaurant'] = $restaurant;

            $categoryDAO = new CategoryDAO($conn);
            $categories = $categoryDAO->selectAll();
            $_SESSION['categories'] = $categories;

            header("Location: ../views/edit.php");
            exit;
        } else {
            echo "<script>alert('Restaurante no encontrado.');</script>";
            echo "<script>window.history.back();</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error al preparar la consulta SQL: " . $conn->error . "');</script>";
        echo "<script>window.history.back();</script>";
    }

    PersistentManager::getInstance()->close_connection();
}

function updateRestaurant($id) {
    $errors = [];

    if (empty($_POST["name"]) || empty($_POST["image"]) || empty($_POST["menu"]) || empty($_POST["priceRange"])) {
        $errors[] = "Por favor, rellena todos los campos obligatorios.";
    }

    if (!empty($_POST["priceRange"])) {
        $priceRange = $_POST["priceRange"];
        $regex = '/^\d+(\.\d{1,2})?-\d+(\.\d{1,2})?$/';

        if (!preg_match($regex, $priceRange)) {
            $errors[] = "El campo de precio debe estar en el formato: '20.00-30.00'.";
        } else {
            list($minorPrice, $majorPrice) = explode('-', $priceRange);
            if (!is_numeric($minorPrice) || !is_numeric($majorPrice) || $minorPrice <= 0 || $majorPrice <= 0 || $minorPrice >= $majorPrice) {
                $errors[] = "El rango de precios debe ser válido.";
            }
        }
    }

    if (!empty($errors)) {
        echo "<script>alert('" . implode("\\n", $errors) . "');</script>";
        echo "<script>window.history.back();</script>";
        return;
    }

    $conn = PersistentManager::getInstance()->get_connection();
    $idCategory = null;

    // Si se selecciona una categoría, obtener su ID
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

    // Actualizar el restaurante
    $sql = "UPDATE Restaurant SET name = ?, image = ?, menu = ?, minorprice = ?, mayorprice = ?, idcategory = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $name = $_POST["name"];
        $image = $_POST["image"];
        $menu = $_POST["menu"];

        $stmt->bind_param("sssddii", $name, $image, $menu, $minorPrice, $majorPrice, $idCategory, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Restaurante actualizado exitosamente.');</script>";
            echo "<script>window.location.href='../../index.php';</script>";
        } else {
            echo "<script>alert('Error al actualizar el restaurante: " . $stmt->error . "');</script>";
            echo "<script>window.history.back();</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error preparando la declaración SQL: " . $conn->error . "');</script>";
        echo "<script>window.history.back();</script>";
    }

    PersistentManager::getInstance()->close_connection();
}
?>
