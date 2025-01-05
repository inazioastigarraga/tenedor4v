<?php

class User {
    private $idUser;
    private $email;
    private $password;
    private $type;

    // Getters
    public function getIdUser() {
        return $this->idUser;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getType() {
        return $this->type;
    }

    // Setters
    public function setIdUser($idUser) {
        $this->idUser = $idUser;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setType($type) {
        $this->type = $type;
    }
}
?>


