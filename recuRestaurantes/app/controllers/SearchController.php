<?php
// Incluir los archivos necesarios para el controlador
require_once '../models/Restaurant.php';
require_once '../../persistence/DAO/RestaurantDAO.php';
require_once '../../persistence/DAO/CategoryDAO.php';
require_once '../../persistence/conf/PersistentManager.php';

session_start();

// Inicializar un mensaje para la consola
$consoleMessage = '';

if (isset($_POST['category']) && !empty($_POST['category'])) {
    $category = $_POST['category'];

    // Normalizar el nombre de la categoría a minúsculas para búsqueda insensible a mayúsculas/minúsculas
    $category = strtolower(trim($category));

    // Utilizar PersistentManager para obtener la conexión
    $conn = PersistentManager::get_connection();

    // Crear instancia del DAO de Category
    $categoryDAO = new CategoryDAO($conn);

    // Buscar la categoría por nombre en minúsculas
    $categoryObj = $categoryDAO->findByName($category);

    if ($categoryObj) {
        // Crear instancia del DAO de Restaurant
        $restaurantDAO = new RestaurantDAO($conn);

        // Buscar los restaurantes que tienen el idCategoria correspondiente
        $restaurants = $restaurantDAO->findByCategory($categoryObj->getId());

        // Verificar si se encontraron resultados
        if (!empty($restaurants)) {
            // Guardar los resultados en la sesión
            $_SESSION['search_results'] = $restaurants;
            $_SESSION['category_name'] = ucfirst($category); // Almacenar el nombre de la categoría en la sesión

            // Redirigir a la vista de resultados
           header('Location: ../../index.php');
            exit;
        } else {
            // Mensaje si no se encontraron restaurantes
            $_SESSION['search_results_message'] = 'No se encontraron restaurantes para esa categoría.';
            $consoleMessage = 'No se encontraron restaurantes para la categoría: ' . $category;
        }
    } else {
        // Si no se encuentra la categoría, devolver un mensaje
        $_SESSION['search_results_message'] = 'Categoría no encontrada.';
        $consoleMessage = 'Categoría no encontrada: ' . $category;
    }
} else {
    $_SESSION['search_results_message'] = 'Por favor, ingresa una categoría válida.';
    $consoleMessage = 'No se ingresó una categoría válida.';
}

// Redirigir a index.php con el mensaje para consola incluido
header('Location: ../../index.php?console_message=' . urlencode($consoleMessage));
exit;
?>
