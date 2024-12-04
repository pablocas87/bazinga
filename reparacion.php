<?php
// Incluir configuraciones
$config = include('config.php');

// Conexión a la base de datos
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reparación de Audio - Bazinga</title>
    <link rel="stylesheet" href="assets/styles.css">
  <!-- Fuentes de Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">

</head>
<body>
    <div id="app">
    <header>
      <div class="navbar">
      <h1><a href="index.html">Bazinga</a></h1>
      <!-- Botón para menú móvil -->
        <div class="menu-toggle" onclick="toggleMenu()">
          <div class="menu-icon">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>
        <nav class="nav-links">
          <a href="index.html">Inicio</a>
          <a href="servicios.html">Servicios</a>
          <a href="reservas.php">Reservas</a>
          <a href="reparacion.php">Reparación</a>
        </nav>
      </div>
      <!-- Efecto RTA de audio -->
      <div class="rta-effect">
        <!-- Generar más barras para cubrir todo el ancho -->
        <!-- Usamos 20 barras para un efecto más completo -->
        <!-- Aquí están las 20 barras -->
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
      </div>
    </header>

        <main>
            <h2>Especifica el problema para solicitar un presupuesto</h2>
            <form id="reparacionForm" action="procesar_reparacion.php" method="POST">
                <label for="nombre">Tu Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="email">Tu Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>

                <label for="problema">Descripción del Problema:</label>
                <textarea id="problema" name="problema" rows="5" required></textarea>

                <button type="submit">Enviar Solicitud</button>
            </form>
        </main>

        <!-- Snackbar para confirmación -->
        <div id="snackbar" class="snackbar">Solicitud enviada con éxito</div>
    </div>

    <script>
        // Función para mostrar Snackbar
        function showSnackbar(message) {
            const snackbar = document.getElementById("snackbar");
            snackbar.textContent = message;
            snackbar.classList.add("show");
            setTimeout(() => {
                snackbar.classList.remove("show");
            }, 3000); // Ocultar después de 3 segundos
        }

        // Manejo dinámico del formulario para evitar redirección
        document.getElementById("reparacionForm").addEventListener("submit", async function (event) {
            event.preventDefault(); // Evita el envío tradicional

            const formData = new FormData(this);

            try {
                const response = await fetch(this.action, {
                    method: this.method,
                    body: formData,
                });

                if (response.ok) {
                    showSnackbar("Solicitud enviada con éxito");
                } else {
                    showSnackbar("Error al enviar la solicitud. Inténtalo nuevamente.");
                }
            } catch (error) {
                console.error("Error:", error);
                showSnackbar("Error en la conexión con el servidor.");
            }
        });

            // Funcionalidad del menú móvil
    function toggleMenu() {
  const navLinks = document.querySelector('.nav-links');
  navLinks.classList.toggle('active');
  const menuToggle = document.querySelector('.menu-toggle');
  menuToggle.classList.toggle('active');
}
    </script>
</body>
<footer>
    <h3>Contacto</h3>
    <a href="https://www.facebook.com/bazinga.amplificacion/" target="_blank">
        <img src="https://img.icons8.com/color/48/facebook-new.png" alt="Facebook" style="width: 24px;"> Facebook
    </a>
    <a href="https://wa.me/59895905719" target="_blank">
        <img src="https://img.icons8.com/color/48/whatsapp--v1.png" alt="WhatsApp" style="width: 24px;"> WhatsApp
    </a>
</footer>
</html>
<?php
$conn->close();
?>
