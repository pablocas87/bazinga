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
$reparacionesQuery = "SELECT COUNT(*) as total FROM reparaciones";
$reparacionesResult = $conn->query($reparacionesQuery);
$totalReparaciones = $reparacionesResult->fetch_assoc()['total'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
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
            <!-- Tarjeta para abrir el modal de añadir servicio o reparación -->
            <div class="dashboard-card" id="add-service-btn" style="cursor: pointer;">
                <h2>Añadir Servicio o Reparación</h2>
                <p>Click para agregar un nuevo ítem</p>
            </div>
        </div>
        
        <!-- Modal para añadir servicio o reparación -->
        <div id="add-service-modal" class="modal">
            <div class="modal-content">
                <h2>Añadir Ítem</h2>
                <form action="agregar_servicio.php" method="POST">
                    <label for="tipo">Tipo:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Seleccionar...</option>
                        <option value="servicio">Servicio</option>
                        <option value="reparacion">Reparación</option>
                    </select>

                    <!-- Campos para servicio -->
                    <div id="servicio-fields" class="hidden">
                        <label for="servicio-nombre">Nombre del Servicio:</label>
                        <input type="text" id="servicio-nombre" name="servicio_nombre">
                    </div>

                    <!-- Campos para reparación -->
                    <div id="reparacion-fields" class="hidden">
                        <label for="reparacion-nombre">Nombre:</label>
                        <input type="text" id="reparacion-nombre" name="reparacion_nombre">

                        <label for="reparacion-email">Email:</label>
                        <input type="email" id="reparacion-email" name="reparacion_email">

                        <label for="reparacion-problema">Problema:</label>
                        <textarea id="reparacion-problema" name="reparacion_problema"></textarea>
                    </div>

                    <button type="submit">Agregar</button>
                    <button type="button" id="close-modal">Cancelar</button>
                </form>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const addServiceBtn = document.getElementById("add-service-btn");
        const addServiceModal = document.getElementById("add-service-modal");
        const closeModalBtn = document.getElementById("close-modal");
        
        const tipoSelect = document.getElementById("tipo");
        const servicioFields = document.getElementById("servicio-fields");
        const reparacionFields = document.getElementById("reparacion-fields");

        addServiceBtn.addEventListener("click", () => {
            addServiceModal.classList.add("show");
        });

        closeModalBtn.addEventListener("click", () => {
            addServiceModal.classList.remove("show");
        });

        tipoSelect.addEventListener("change", () => {
            const tipo = tipoSelect.value;
            if (tipo === 'servicio') {
                servicioFields.classList.remove('hidden');
                reparacionFields.classList.add('hidden');
            } else if (tipo === 'reparacion') {
                reparacionFields.classList.remove('hidden');
                servicioFields.classList.add('hidden');
            } else {
                servicioFields.classList.add('hidden');
                reparacionFields.classList.add('hidden');
            }
        });
    });
    </script>
</body>
</html>
