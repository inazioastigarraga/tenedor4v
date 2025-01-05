<?php
class Reservation {
    private $id;
    private $idRestaurant;
    private $date;
    private $persons;
    private $IP;

    // Getters y Setters para cada propiedad
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getIdRestaurant() { return $this->idRestaurant; }
    public function setIdRestaurant($idRestaurant) { $this->idRestaurant = $idRestaurant; }

    public function getDate() { return $this->date; }
    public function setDate($date) { $this->date = $date; }

    public function getPersons() { return $this->persons; }
    public function setPersons($persons) { $this->persons = $persons; }

    public function getIP() { return $this->IP; }
    public function setIP($ip) { $this->IP = $ip; }
}


