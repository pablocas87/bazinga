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

// Verificar si se recibió un ID válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Eliminar la reparación
    $query = "DELETE FROM reparaciones WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        header("Location: ver_reparaciones.php?success=1");
    } else {
        header("Location: ver_reparaciones.php?error=1");
    }

    $stmt->close();
} else {
    header("Location: ver_reparaciones.php?error=1");
}

$conn->close();
?>
