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
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Obtener el usuario actual (admin único)
    $username = $_SESSION['username'];
    $query = "SELECT * FROM admin_users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña actual
        if (password_verify($currentPassword, $user['password_hash'])) {
            if ($newPassword === $confirmPassword) {
                // Actualizar contraseña
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE admin_users SET password_hash = ? WHERE username = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param('ss', $newPasswordHash, $username);
                $updateStmt->execute();

                $message = "Contraseña actualizada con éxito.";
            } else {
                $message = "La nueva contraseña no coincide con la confirmación.";
            }
        } else {
            $message = "La contraseña actual es incorrecta.";
        }
    } else {
        $message = "Usuario no encontrado.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="stylesheet" href="../assets/admin-styles.css">

</head>
<body>
<header>
    <h1>Cambiar Contraseña</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="ver_reservas.php">Ver Reservas</a>
        <a href="ver_reparaciones.php">Ver Reparaciones</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>
<main>
    <form method="POST" action="">
        <label for="current_password">Contraseña Actual:</label>
        <input type="password" id="current_password" name="current_password" required autocomplete="current-password">

        <label for="new_password">Nueva Contraseña:</label>
        <input type="password" id="new_password" name="new_password" required autocomplete="new-password">

        <label for="confirm_password">Confirmar Nueva Contraseña:</label>
        <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">

        <button type="submit">Actualizar Contraseña</button>
    </form>
    <?php if ($message): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>
</main>
</body>
</html>
