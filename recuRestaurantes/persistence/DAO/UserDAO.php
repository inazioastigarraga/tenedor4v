<?php

require_once __DIR__ . '/../../app/models/User.php';
require_once 'GenericDAO.php';

class UserDAO extends GenericDAO {

    const USER_TABLE = 'user';

    // Obtener todos los usuarios
    public function selectAll() {
        $query = "SELECT * FROM " . self::USER_TABLE;
        $result = mysqli_query($this->conn, $query);
        $users = array();

        while ($userBD = mysqli_fetch_array($result)) {
            $user = new User();
            $user->setIdUser($userBD["idUser"]);
            $user->setEmail($userBD["email"]);
            $user->setPassword($userBD["password"]);
            $user->setType($userBD["type"]);
            array_push($users, $user);
        }

        return $users;
    }

    // Buscar un usuario por email y contraseña
    public function findByEmailAndPassword($email, $password) {
        $query = "SELECT * FROM " . self::USER_TABLE . " WHERE email = ? AND password = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $user = new User();
            $user->setIdUser($row["idUser"]);
            $user->setEmail($row["email"]);
            $user->setPassword($row["password"]);
            $user->setType($row["type"]);
            return $user;
        }

        return null;
    }

    // Insertar un nuevo usuario
    public function insert(User $user) {
        $query = "INSERT INTO " . self::USER_TABLE . " (email, password, type) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $email = $user->getEmail();
        $password = $user->getPassword();
        $type = $user->getType();
        $stmt->bind_param("sss", $email, $password, $type);

        return $stmt->execute();
    }
}

?>
