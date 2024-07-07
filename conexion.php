<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registro";

// Crear conexión
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Verificar la conexión
if (!$conn) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}
?>
