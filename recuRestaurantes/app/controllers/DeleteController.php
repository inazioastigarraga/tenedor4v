<?php

session_start();
require_once '../models/Restaurant.php';
require_once '../../persistence/DAO/RestaurantDAO.php';
require_once '../../persistence/conf/PersistentManager.php';

// Validar si el usuario tiene permisos para esta acción
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'Admin') {
    // Redirigir a la página de error si no tiene permisos
    header("Location: ../../views/error_permission.php");
    exit;
}

// Verificar que la solicitud sea GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Verificar que el ID haya sido enviado y sea válido
    if (isset($_GET['id']) && is_numeric($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];
        deleteAction($id);
    } else {
        // Mostrar error si el ID no es válido
        echo "<script>alert('ID inválido o no proporcionado.');</script>";
        echo "<script>window.history.back();</script>";
    }
}

function deleteAction($id) {
    $errors = [];

    // Validar que el ID no esté vacío y sea un número
    if (empty($id) || !is_numeric($id)) {
        $errors[] = "ID inválido.";
    }

    if (!empty($errors)) {
        echo "<script>alert('" . implode("\\n", $errors) . "');</script>";
        echo "<script>window.history.back();</script>";
        return;
    }

    // Obtener conexión a la base de datos
    $conn = PersistentManager::getInstance()->get_connection();

    // Preparar la declaración SQL para eliminar el restaurante
    $sql = "DELETE FROM Restaurant WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Mostrar mensaje de éxito y redirigir
            echo "<script>alert('Restaurante eliminado exitosamente.');</script>";
            echo "<script>window.location.href='../../index.php';</script>"; // Redirigir a index.php
        } else {
            // Mostrar error en la ejecución
            echo "<script>alert('Error al eliminar el restaurante: " . $stmt->error . "');</script>";
            echo "<script>window.history.back();</script>";
        }

        $stmt->close();
    } else {
        // Mostrar error al preparar la declaración
        echo "<script>alert('Error preparando la declaración SQL: " . $conn->error . "');</script>";
        echo "<script>window.history.back();</script>";
    }

    PersistentManager::getInstance()->close_connection();
}
?>
