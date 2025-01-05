<?php
require_once 'GenericDAO.php';

class RestaurantDAO extends GenericDAO {

  // Se define una constante con el nombre de la tabla
  const USER_TABLE = 'restaurant';

  // Método para obtener todos los restaurantes
  public function selectAll() {
    $query = "SELECT * FROM " . RestaurantDAO::USER_TABLE;
    $result = mysqli_query($this->conn, $query);
    $restaurant = array();
    while ($restaurantBD = mysqli_fetch_array($result)) {
      $r = new Restaurant();
      $r->setId($restaurantBD["id"]);
      $r->setName($restaurantBD["name"]);
      $r->setImage($restaurantBD["image"]);
      $r->setMenu($restaurantBD["menu"]);
      $r->setMinorPrice($restaurantBD["minorprice"]);
      $r->setMayorPrice($restaurantBD["mayorprice"]);
      array_push($restaurant, $r);
    }
    return $restaurant;
  }

  // Método para obtener restaurantes por categoría
  public function findByCategory($categoryId) {
        // Consulta para obtener restaurantes que pertenezcan a una categoría específica
        $query = "SELECT * FROM " . RestaurantDAO::USER_TABLE . " WHERE idCategory = ?";
        
        // Preparar y ejecutar la consulta
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $categoryId);  // Se espera un entero como ID de categoría
        $stmt->execute();
        
        // Obtener el resultado
        $result = $stmt->get_result();
        
        $restaurants = array();
        while ($row = $result->fetch_assoc()) {
            $restaurant = new Restaurant();
            $restaurant->setId($row['id']);
            $restaurant->setName($row['name']);
            $restaurant->setImage($row['image']);
            $restaurant->setMenu($row['menu']);
            $restaurant->setMinorPrice($row['minorprice']);
            $restaurant->setMayorPrice($row['mayorprice']);
            array_push($restaurants, $restaurant);
        }

        return $restaurants;
    }
}
?>
