<?php
// Incluir configuraciones
$config = include('config.php');

// Conexión a la base de datos
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


// Consulta para obtener las fechas reservadas
$sql = "SELECT DISTINCT DATE(fecha_hora) AS fecha FROM reservas";
$result = $conn->query($sql);

$fechasReservadas = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fechasReservadas[] = $row['fecha'];
    }
}

echo json_encode($fechasReservadas);

$conn->close();
?>
