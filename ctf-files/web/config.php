<?php
// --------------------------------------------------------
// ARCHIVO DE CONFIGURACIÓN DE LA BASE DE DATOS
// --------------------------------------------------------
// Almacena todas las credenciales en un solo lugar.
// --------------------------------------------------------

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'ctf_user');
define('DB_PASSWORD', 'ctf_pass');
define('DB_NAME', 'ctf_lab');

// Conexión reutilizable (opcional pero bueno)
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    // Si la BD falla, no tiene sentido continuar.
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
?>