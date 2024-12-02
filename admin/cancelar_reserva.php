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

// Cancelar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $sql = "DELETE FROM reservas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        header("Location: ver_reservas.php?success=1");
        exit();
    } else {
        header("Location: ver_reservas.php?error=1");
        exit();
    }
}
?>
