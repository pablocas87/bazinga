<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir configuraciones
$config = include('../config.php');
session_start();

// Verificar si el usuario ya está logueado
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

// Procesar el login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Conectar a la base de datos
    $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Verificar si el usuario existe
    $query = "SELECT * FROM admin_users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $user['password_hash'])) {
            // Almacenar datos de sesión
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['username'] = $username; // Guardar el nombre de usuario en la sesión
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Contraseña incorrecta.";
        }
    } else {
        $error_message = "Usuario no encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <form method="POST" action="">
        <h1>Login Administrador</h1>
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <label for="username">Usuario:</label>
        <input type="text" name="username" id="username" required autocomplete="username">
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required autocomplete="current-password">
        <button type="submit">Iniciar Sesión</button>
    </form>
</body>
</html>
