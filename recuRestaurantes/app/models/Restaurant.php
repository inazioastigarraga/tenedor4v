<?php

class Restaurant {
    private $id;
    private $name;
    private $image;
    private $menu;
    private $minorPrice;
    private $mayorPrice;
    private $idCategory; // Nueva propiedad para manejar la categoría

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getImage() {
        return $this->image;
    }

    public function getMenu() {
        return $this->menu;
    }

    public function getMinorPrice() {
        return $this->minorPrice;
    }

    public function getMayorPrice() {
        return $this->mayorPrice;
    }

    public function getIdCategory() {
        return $this->idCategory;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function setMenu($menu) {
        $this->menu = $menu;
    }

    public function setMinorPrice($minorPrice) {
        $this->minorPrice = $minorPrice;
    }

    public function setMayorPrice($mayorPrice) {
        $this->mayorPrice = $mayorPrice;
    }

    public function setIdCategory($idCategory) {
        $this->idCategory = $idCategory;
    }

    // Método para generar la representación HTML del restaurante
    public function restaurant2HTML() {
    // Iniciar sesión para verificar roles

    $userType = isset($_SESSION['userType']) ? $_SESSION['userType'] : null;

    $result = '<div class="col-md-4">';
    $result .= '<div class="card">';
    // Mostrar la imagen del restaurante
    $result .= '<img class="card-img-top rounded mx-auto d-block email" src="' . $this->getImage() . '" alt="Imagen de ' . $this->getName() . '" style="height: 250px;">';
    $result .= '<div class="card-block"><br>';
    // Mostrar rango de precios y nombre del restaurante
    $result .= '<p class="card-text price">' . $this->getMinorPrice() . '-' . $this->getMayorPrice() . ' €</p>';
    $result .= '<h3 class="card-title">' . $this->getName() . '</h3>';
    // Mostrar el menú del restaurante
    $result .= '<p class="card-text menu">' . $this->getMenu() . '</p>';
    $result .= '</div>';

    // Verificar el rol del usuario y mostrar botones según corresponda
    if (!$userType) {
        $result .= '<div class="card-footer">';
        // Botón de reservar (solo para usuarios no identificados)
        $result .= '<a type="button" class="btn btn-success" href="app/views/reserve.php?id=' . $this->getId() . '">Reservar</a>';
        $result .= '</div>';
    } else {
        // Verificar el rol del usuario y mostrar botones de administración si corresponde
        if ($userType === 'Gestor' || $userType === 'Admin') {
            $result .= '<div class="btn-group card-footer" role="group">';
            if ($userType === 'Admin') {
                // Botón para eliminar (solo Admins)
                $result .= '<a type="button" class="btn btn-danger" href="app/controllers/DeleteController.php?id=' . $this->getId() . '" onclick="return confirm(\'¿Estás seguro de que deseas eliminar este restaurante?\');">Borrar</a>';
            }
            // Botón para editar (Gestores y Admins)
            $result .= '<a type="button" class="btn btn-primary" href="app/controllers/EditController.php?id=' . $this->getId() . '">Editar</a>';
            $result .= '</div>';
        }
    }

    $result .= '</div>';
    $result .= '</div>';
    
    return $result;
}

}
?>
