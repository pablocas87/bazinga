<?php

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
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener reservas
$sql = "SELECT * FROM reservas ORDER BY fecha_hora ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Reservas</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="stylesheet" href="../assets/admin-styles.css">

</head>
<body>
<header>
    <h1>Reservas</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="ver_reservas.php">Ver Reservas</a>
        <a href="ver_reparaciones.php">Ver Reparaciones</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>
<main>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Servicio</th>
                <th>Fecha y Hora</th>
                <th>Equipos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['servicio']; ?></td>
                        <td><?php echo date('d M, Y H:i', strtotime($row['fecha_hora'])); ?></td>
                        <td><?php echo $row['equipos']; ?></td>
                        <td>
                            <button class="cancel-button" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['nombre']); ?>" data-service="<?php echo htmlspecialchars($row['servicio']); ?>" data-date="<?php echo date('d M, Y', strtotime($row['fecha_hora'])); ?>">Cancelar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No hay reservas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<!-- Modal flotante -->
<div id="modal" class="modal">
    <div class="modal-content">
        <h2>¿Confirmas la cancelación?</h2>
        <p id="modal-details"></p>
        <form id="cancel-form" method="POST" action="cancelar_reserva.php">
            <input type="hidden" name="id" id="reservation-id">
            <button type="submit">Confirmar</button>
            <button type="button" id="close-modal">Cancelar</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const modal = document.getElementById("modal");
        const closeModal = document.getElementById("close-modal");
        const cancelButtons = document.querySelectorAll(".cancel-button");
        const reservationIdInput = document.getElementById("reservation-id");
        const modalDetails = document.getElementById("modal-details");

        cancelButtons.forEach(button => {
            button.addEventListener("click", () => {
                const reservationId = button.getAttribute("data-id");
                const reservationName = button.getAttribute("data-name");
                const reservationService = button.getAttribute("data-service");
                const reservationDate = button.getAttribute("data-date");

                modalDetails.innerHTML = `
                    <strong>Nombre:</strong> ${reservationName}<br>
                    <strong>Servicio:</strong> ${reservationService}<br>
                    <strong>Fecha:</strong> ${reservationDate}
                `;
                reservationIdInput.value = reservationId;
                modal.classList.add("show");
            });
        });

        closeModal.addEventListener("click", () => {
            modal.classList.remove("show");
        });
    });
</script>
</body>
</html>
