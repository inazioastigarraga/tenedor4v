<?php

require_once '../models/Reservation.php';
require_once '../../persistence/DAO/ReservationDAO.php';
require_once '../../persistence/conf/PersistentManager.php';

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    createReservation();
}

function createReservation() {
    $errors = [];

    // Validar campos obligatorios
    if (empty($_POST["date"]) || empty($_POST["persons"]) || empty($_POST["id_restaurant"])) {
        $errors[] = "Todos los campos son obligatorios.";
    }

    // Validar número de comensales
    $persons = intval($_POST["persons"]);
    if ($persons <= 0 || $persons > 10) {
        $errors[] = "El número de comensales debe ser entre 1 y 10.";
    }

    // Validar fecha y hora
    $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $_POST["date"]); // Formato del input datetime-local
    if (!$dateTime) {
        $errors[] = "La fecha debe estar en un formato válido (YYYY-MM-DDTHH:MM).";
    } else {
        $now = new DateTime();
        if ($dateTime <= $now) {
            $errors[] = "La fecha debe ser posterior al momento actual.";
        }

        // Validar que la hora sea 14:00 o 21:00
        $hour = $dateTime->format('H:i');
        if ($hour !== "14:00" && $hour !== "21:00") {
            $errors[] = "La hora debe ser 14:00 o 21:00.";
        }
    }

    // Si hay errores, mostrar mensaje y detener la ejecución
    if (!empty($errors)) {
        echo "<script>alert('" . implode("\\n", $errors) . "');</script>";
        echo "<script>window.history.back();</script>";
        exit;
    }

    // Crear el objeto Reservation
    $reservation = new Reservation();
    $reservation->setIdRestaurant(intval($_POST["id_restaurant"]));
    $reservation->setDate($dateTime->format('Y-m-d H:i:s')); // Convertir al formato de base de datos
    $reservation->setPersons($persons);
    $reservation->setIP($_SERVER['REMOTE_ADDR']); // Capturar la IP del cliente

    // Guardar en la base de datos
    $conn = PersistentManager::get_connection();
    $reservationDAO = new ReservationDAO($conn);

    if ($reservationDAO->create($reservation)) {
        echo "<script>alert('Reserva realizada exitosamente.');</script>";
        echo "<script>window.location.href='../../index.php';</script>";
    } else {
        echo "<script>alert('Error al realizar la reserva.');</script>";
        echo "<script>window.history.back();</script>";
    }

    PersistentManager::getInstance()->close_connection();
}

?>
