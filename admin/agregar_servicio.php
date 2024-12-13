<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir configuración
$config = include('../config.php');

session_start();

// Comprobar si está logueado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Conectar a la base de datos
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';

    if ($tipo === 'servicio') {
        $nombreServicio = $_POST['servicio_nombre'] ?? '';

        if (!empty($nombreServicio)) {
            $stmt = $conn->prepare("INSERT INTO servicios (nombre) VALUES (?)");
            $stmt->bind_param("s", $nombreServicio);

            if ($stmt->execute()) {
                header("Location: dashboard.php?success=Servicio añadido correctamente");
                exit();
            } else {
                header("Location: dashboard.php?error=No se pudo añadir el servicio");
                exit();
            }
        } else {
            header("Location: dashboard.php?error=Nombre del servicio vacío");
            exit();
        }

    } elseif ($tipo === 'reparacion') {
        $nombre = $_POST['reparacion_nombre'] ?? '';
        $email = $_POST['reparacion_email'] ?? '';
        $problema = $_POST['reparacion_problema'] ?? '';

        if (!empty($nombre) && !empty($email) && !empty($problema)) {
            $stmt = $conn->prepare("INSERT INTO reparaciones (nombre, email, problema, fecha_solicitud) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $nombre, $email, $problema);

            if ($stmt->execute()) {
                header("Location: dashboard.php?success=Reparación añadida correctamente");
                exit();
            } else {
                header("Location: dashboard.php?error=No se pudo añadir la reparación");
                exit();
            }
        } else {
            header("Location: dashboard.php?error=Faltan datos para la reparación");
            exit();
        }
    } else {
        header("Location: dashboard.php?error=Tipo inválido");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}

$conn->close();
