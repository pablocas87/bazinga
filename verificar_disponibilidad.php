<?php
// Incluir configuraciones
$config = include('config.php');

// Conexión a la base de datos
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


// Obtener la fecha desde la solicitud
$fecha = $_GET['fecha'];

// Verificar disponibilidad
$stmt = $conn->prepare("SELECT COUNT(*) FROM reservas WHERE DATE(fecha_hora) = ?");
$stmt->bind_param("s", $fecha);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

echo $count > 0 ? "no" : "si";

$conn->close();
?>
