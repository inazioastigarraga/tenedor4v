<?php
session_start();
require_once '../../persistence/DAO/UserDAO.php';
require_once '../../persistence/conf/PersistentManager.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validar que los campos no estén vacíos
    if (empty($email) || empty($password)) {
        echo "<script>alert('Por favor, ingresa tu correo y contraseña.');</script>";
        echo "<script>window.history.back();</script>";
        exit;
    }

    // Conectar a la base de datos y validar el usuario
    $conn = PersistentManager::get_connection();
    $userDAO = new UserDAO($conn);

    $user = $userDAO->findByEmailAndPassword($email, $password);

    if ($user) {
        // Guardar datos del usuario en la sesión usando los métodos get
        $_SESSION['userName'] = $user->getEmail();
        $_SESSION['userType'] = $user->getType();

        // Redirigir al index.php
        header("Location: ../../index.php");
        exit;
    } else {
        // Mostrar error si las credenciales son incorrectas
        echo "<script>alert('Correo o contraseña incorrectos.');</script>";
        echo "<script>window.history.back();</script>";
    }
} else {
    // Si se accede al controlador sin usar POST, redirigir al index
    header("Location: ../../index.php");
    exit;
}
