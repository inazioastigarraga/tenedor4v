<?php

require_once __DIR__ . '/../../app/models/Category.php';
require_once 'GenericDAO.php';

class CategoryDAO extends GenericDAO {

    const USER_TABLE = 'category';

    public function selectAll() {
        $query = "SELECT * FROM " . self::USER_TABLE;
        $result = mysqli_query($this->conn, $query);
        $category = array();
        while ($categoryBD = mysqli_fetch_array($result)) {
            $c = new Category();
            $c->setId($categoryBD["id"]);
            $c->setName($categoryBD["name"]);
            array_push($category, $c);
        }
        return $category;
    }

    public function findByName($name) {
        $name = strtolower($name);
        
        $query = "SELECT * FROM " . self::USER_TABLE . " WHERE LOWER(name) = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $category = new Category();
            $category->setId($row["id"]);
            $category->setName($row["name"]);
            return $category;
        }

        return null;
    }
}

?>
