
<?php
// Incluir configuraciones
$config = include('config.php');

// Conexión a la base de datos
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


// Obtener datos del formulario
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$problema = $_POST['problema'];

// Insertar datos en la base de datos
$sql = "INSERT INTO reparaciones (nombre, email, problema) VALUES ('$nombre', '$email', '$problema')";

if ($conn->query($sql) === TRUE) {
    echo "Solicitud enviada con éxito. Nos pondremos en contacto contigo pronto.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

