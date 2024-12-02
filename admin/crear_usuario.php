<?php
session_start();

// Verificar si el administrador está logueado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Incluir configuración de la base de datos
$config = include('../config.php');

// Conectar a la base de datos
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Inicializar mensaje
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password === $confirmPassword) {
        // Verificar si el usuario ya existe
        $query = "SELECT * FROM admin_users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Insertar nuevo usuario
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insertQuery = "INSERT INTO admin_users (username, password_hash) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param('ss', $username, $passwordHash);
            $insertStmt->execute();

            $message = "Usuario creado con éxito.";
        } else {
            $message = "El usuario ya existe.";
        }

        $stmt->close();
    } else {
        $message = "Las contraseñas no coinciden.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="stylesheet" href="../assets/admin-styles.css">

</head>
<body>
<header>
    <h1>Crear Nuevo Usuario</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="ver_reservas.php">Ver Reservas</a>
        <a href="ver_reparaciones.php">Ver Reparaciones</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>
<main>
    <form method="POST" action="">
        <label for="username">Nombre de Usuario:</label>
        <input type="text" id="username" name="username" required autocomplete="username">

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required autocomplete="new-password">

        <label for="confirm_password">Confirmar Contraseña:</label>
        <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">

        <button type="submit">Crear Usuario</button>
    </form>
    <?php if ($message): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>
</main>
</body>
</html>
