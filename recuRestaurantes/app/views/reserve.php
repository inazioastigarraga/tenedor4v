<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Restaurante inválido.');</script>";
    echo "<script>window.history.back();</script>";
    exit;
}

$id_restaurant = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Restaurante</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Realizar una Reserva</h2>
        <form method="POST" action="../controllers/ReservationController.php">
            <input type="hidden" name="id_restaurant" value="<?= htmlspecialchars($id_restaurant); ?>">
            <div class="mb-3">
                <label for="date" class="form-label">Fecha y Hora</label>
                <input type="datetime-local" class="form-control" id="date" name="date" required>
            </div>
            <div class="mb-3">
                <label for="persons" class="form-label">Número de Comensales</label>
                <input type="number" class="form-control" id="persons" name="persons" min="1" max="10" required>
            </div>
            <button type="submit" class="btn btn-primary">Reservar</button>
        </form>
    </div>
</body>
</html>


