<?php
require 'persistence/DAO/RestaurantDAO.php';
require 'app/models/Restaurant.php';

function getAllRestaurantes(){
    $restaurantDao = new RestaurantDAO();
    return $restaurantDao->selectAll();
}

