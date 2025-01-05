<?php
session_start();

// Destruir todas las variables de sesión
session_unset();

// Destruir la sesión completamente
session_destroy();

// Redirigir al index.php (o a cualquier página de inicio)
header("Location: ../../index.php");
exit;


