<?php
// Incluir configuraciones
$config = include('config.php');

// Conexión a la base de datos
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener los servicios
$sql = "SELECT id, nombre FROM servicios";
$result = $conn->query($sql);

// Capturar el servicio enviado desde `servicios.html`
$servicioSeleccionado = isset($_GET['servicio']) ? $_GET['servicio'] : '';

// Consulta para obtener los equipos disponibles
$equiposQuery = "SELECT nombre, disponible FROM equipos WHERE disponible > 0";
$equiposResult = $conn->query($equiposQuery);
$equipos = [];
if ($equiposResult->num_rows > 0) {
    while ($row = $equiposResult->fetch_assoc()) {
        $equipos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - Bazinga</title>
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<body>
    <div id="app">
        <header>
            <h1>Reserva tu Servicio</h1>
            <nav>
                <a href="index.html">Inicio</a>
                <a href="servicios.html">Servicios</a>
                <a href="reservas.php">Reservas</a>
                <a href="reparacion.php">Reparación</a>
            </nav>
        </header>

        <main>
            <form id="reservaForm" action="procesar_reserva.php" method="POST">
                <label for="servicio">Selecciona un servicio:</label>
                <select id="servicio" name="servicio" required>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $selected = $row['nombre'] === $servicioSeleccionado ? 'selected' : '';
                            echo "<option value='" . $row['nombre'] . "' $selected>" . $row['nombre'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay servicios disponibles</option>";
                    }
                    ?>
                </select>

                <div id="equiposContainer" style="display: <?php echo $servicioSeleccionado === 'Alquiler de Equipos' ? 'block' : 'none'; ?>;">
                    <h3>Selecciona los equipos y cantidades:</h3>
                    <div class="equipos-grid">
                        <?php
                        foreach ($equipos as $equipo) {
                            echo "
                            <div class='equipo-item'>
                                <label for='{$equipo['nombre']}'>{$equipo['nombre']} (Disponibles: {$equipo['disponible']})</label>
                                <input
                                    type='number'
                                    id='{$equipo['nombre']}'
                                    name='equipos[{$equipo['nombre']}]'
                                    min='0'
                                    max='{$equipo['disponible']}'
                                    placeholder='0'
                                >
                            </div>";
                        }
                        ?>
                    </div>
                </div>

                <label for="nombre">Tu Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="fecha_hora">Fecha y Hora:</label>
                <input type="text" id="fecha_hora" name="fecha_hora" required>

                <button type="submit">Reservar</button>
            </form>
        </main>

        <!-- Modal de confirmación -->
        <div id="modalConfirmacion" class="modal">
            <div class="modal-content">
                <h2>Confirmar Reserva</h2>
                <p id="detalleReserva"></p>
                <button id="confirmarReserva">Confirmar</button>
                <button id="cancelarReserva">Cancelar</button>
            </div>
        </div>

        <div id="snackbar" class="snackbar">Reserva realizada con éxito</div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", async () => {
            const fechaInput = document.getElementById("fecha_hora");

            try {
                const response = await fetch("verificar_fechas.php");
                const fechasReservadas = await response.json();

                flatpickr(fechaInput, {
                    enableTime: true,
                    inline: true,
                    dateFormat: "Y-m-d H:i",
                    minDate: "today",
                    disable: fechasReservadas,
                });
            } catch (error) {
                console.error("Error al cargar las fechas reservadas:", error);
            }

            const servicioSelect = document.getElementById("servicio");
            if (servicioSelect.value === "Alquiler de Equipos") {
                document.getElementById("equiposContainer").style.display = "block";
            }
        });

        document.getElementById("servicio").addEventListener("change", function () {
            const equiposContainer = document.getElementById("equiposContainer");
            if (this.value === "Alquiler de Equipos") {
                equiposContainer.style.display = "block";
            } else {
                equiposContainer.style.display = "none";
            }
        });

        document.getElementById("reservaForm").addEventListener("submit", function (event) {
            event.preventDefault();
            const servicio = document.getElementById("servicio").value;
            const nombre = document.getElementById("nombre").value;
            const fechaHora = document.getElementById("fecha_hora").value;

            const equipos = Array.from(document.querySelectorAll("#equiposContainer input"))
                .filter(input => input.value > 0)
                .map(input => `${input.id}: ${input.value}`);

            const detalle = `
                <strong>Servicio:</strong> ${servicio}<br>
                <strong>Nombre:</strong> ${nombre}<br>
                <strong>Fecha y Hora:</strong> ${fechaHora}<br>
                <strong>Equipos:</strong> ${equipos.join(", ")}
            `;

            document.getElementById("detalleReserva").innerHTML = detalle;
            document.getElementById("modalConfirmacion").classList.add("show");
        });

        document.getElementById("confirmarReserva").addEventListener("click", async function () {
            const formData = new FormData(document.getElementById("reservaForm"));

            try {
                const response = await fetch("procesar_reserva.php", {
                    method: "POST",
                    body: formData,
                });

                if (response.ok) {
                    const result = await response.text();
                    showSnackbar(result);
                    document.getElementById("modalConfirmacion").classList.remove("show");
                } else {
                    showSnackbar("Error al realizar la reserva");
                }
            } catch (error) {
                console.error(error);
                showSnackbar("Error en la conexión con el servidor");
            }
        });

        document.getElementById("cancelarReserva").addEventListener("click", function () {
            document.getElementById("modalConfirmacion").classList.remove("show");
        });

        function showSnackbar(message) {
            const snackbar = document.getElementById("snackbar");
            snackbar.textContent = message;
            snackbar.classList.add("show");
            setTimeout(() => {
                snackbar.classList.remove("show");
            }, 3000);
        }
    </script>
</body>
<footer>
    <h3>Contacto</h3>
    <a href="https://www.facebook.com/bazinga.amplificacion/" target="_blank">
        <img src="https://img.icons8.com/color/48/facebook-new.png" alt="Facebook"> Facebook
    </a>
    <a href="https://wa.me/59895905719" target="_blank">
        <img src="https://img.icons8.com/color/48/whatsapp--v1.png" alt="WhatsApp"> WhatsApp
    </a>
</footer>
</html>
<?php
$conn->close();
?>
