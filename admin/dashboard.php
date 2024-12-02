<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir configuraciones
$config = include('../config.php');

session_start();

// Comprobar si está logueado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}


// Conexión a la base de datos
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el total de reservas futuras
$reservasQuery = "SELECT COUNT(*) as total FROM reservas WHERE fecha_hora >= NOW()";
$reservasResult = $conn->query($reservasQuery);
$totalReservas = $reservasResult->fetch_assoc()['total'];

// Obtener el total de reparaciones pendientes
$reparacionesQuery = "SELECT COUNT(*) as total FROM reparaciones WHERE fecha_solicitud >= NOW()";
$reparacionesResult = $conn->query($reparacionesQuery);
$totalReparaciones = $reparacionesResult->fetch_assoc()['total'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="stylesheet" href="../assets/admin-styles.css">
</head>
<body>
    <header>
        <h1>Panel de Administración</h1>
        <nav>
            <a href="ver_reservas.php">Ver Reservas</a>
            <a href="ver_reparaciones.php">Ver Reparaciones</a>
            <a href="cambiar_password.php">Cambiar Contraseña</a>
            <a href="crear_usuario.php">Crear Usuario</a>
            <a href="logout.php">Cerrar Sesión</a>
        </nav>
    </header>
    <main>
        <p>Bienvenido al panel de administración.</p>
        <div class="dashboard-container">
            <a href="ver_reservas.php" class="dashboard-card">
                <h2>Reservas</h2>
                <p>Total: <?php echo $totalReservas; ?></p>
            </a>
            <a href="ver_reparaciones.php" class="dashboard-card">
                <h2>Reparaciones</h2>
                <p>Total Pendientes: <?php echo $totalReparaciones; ?></p>
            </a>
        </div>
    </main>
</body>
</html>
