<?php
// ctf-files/web/logout.php

session_start(); // Unirse a la sesión existente
session_unset(); // Borrar todas las variables de sesión
session_destroy(); // Destruir la sesión

// Redirigir de vuelta al login
header("Location: login.php");
exit;
?>