<?php

require_once __DIR__ . '/../../app/models/Reservation.php';
require_once 'GenericDAO.php';

class ReservationDAO extends GenericDAO {

    const USER_TABLE = 'reservations';

    // Método para insertar una nueva reserva
    public function create($reservation) {
        $query = "INSERT INTO " . self::USER_TABLE . " (idRestaurant, date, persons, IP) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $idRestaurant = $reservation->getIdRestaurant();
            $date = $reservation->getDate();
            $persons = $reservation->getPersons();
            $ip = $reservation->getIP();
            $stmt->bind_param("isis", $idRestaurant, $date, $persons, $ip);

            if ($stmt->execute()) {
                return true;
            }
        }
        return false;
    }

    // Método para obtener todas las reservas
    public function selectAll() {
        $query = "SELECT * FROM " . self::USER_TABLE;
        $result = mysqli_query($this->conn, $query);
        $reservations = array();

        while ($row = mysqli_fetch_array($result)) {
            $reservation = new Reservation();
            $reservation->setId($row["id"]);
            $reservation->setIdRestaurant($row["id_restaurant"]);
            $reservation->setDate($row["date"]);
            $reservation->setPersons($row["persons"]);
            $reservation->setIP($row["IP"]);
            array_push($reservations, $reservation);
        }
        return $reservations;
    }

    // Método para buscar reservas por ID de restaurante
    public function findByRestaurantId($idRestaurant) {
        $query = "SELECT * FROM " . self::USER_TABLE . " WHERE id_restaurant = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idRestaurant);
        $stmt->execute();

        $result = $stmt->get_result();
        $reservations = array();

        while ($row = $result->fetch_assoc()) {
            $reservation = new Reservation();
            $reservation->setId($row["id"]);
            $reservation->setIdRestaurant($row["id_restaurant"]);
            $reservation->setDate($row["date"]);
            $reservation->setPersons($row["persons"]);
            $reservation->setIP($row["IP"]);
            array_push($reservations, $reservation);
        }
        return $reservations;
    }
}

?>

