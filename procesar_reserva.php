<?php
// Incluir configuraciones
$config = include('config.php');

// Conexión a la base de datos
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$servicio = $_POST['servicio'];
$nombre = $_POST['nombre'];
$fecha_hora = $_POST['fecha_hora'];
$equiposSeleccionados = isset($_POST['equipos']) ? $_POST['equipos'] : [];

// Construir una cadena con los equipos y cantidades seleccionados
$equiposDetalle = [];
foreach ($equiposSeleccionados as $equipo => $cantidad) {
    if ($cantidad > 0) {
        $equiposDetalle[] = "$equipo: $cantidad";
    }
}
$equiposDetalleStr = implode(", ", $equiposDetalle);

// Extraer solo la fecha para verificar disponibilidad
$fecha = explode(" ", $fecha_hora)[0];

// Verificar si ya existe una reserva en la misma fecha
$stmt = $conn->prepare("SELECT COUNT(*) FROM reservas WHERE DATE(fecha_hora) = ?");
$stmt->bind_param("s", $fecha);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    echo "No hay disponibilidad para esta fecha. Por favor, selecciona otra.";
} else {
    // Insertar la reserva si está disponible
    $stmt = $conn->prepare("INSERT INTO reservas (servicio, nombre, fecha_hora, equipos) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $servicio, $nombre, $fecha_hora, $equiposDetalleStr);

    if ($stmt->execute()) {
        echo "Reserva realizada con éxito.";
    } else {
        echo "Error al realizar la reserva: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
