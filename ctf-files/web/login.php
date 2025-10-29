<?php
// ------------------------------------------------------------------
// LÓGICA DE LOGIN (VULNERABLE A SQL INJECTION)
// ------------------------------------------------------------------

// 🔑 ¡ARREGLADO! Ya no hay credenciales aquí.
require_once 'config.php';

$message = ""; // Variable para almacenar mensajes de error/éxito

// Crear conexión
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    // Usamos la clase .error del CSS global
    $message = "<div class='message error'>Error de conexión a la base de datos: " . $conn->connect_error . "</div>";
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST" && !$conn->connect_error) {
    // Recoger datos del formulario (¡SIN SANITIZAR!)
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // CONSTRUCCIÓN DE LA CONSULTA SQL VULNERABLE
    $sql = "SELECT username, secret_key, role FROM users WHERE username = '$user' AND password_hash = MD5('$pass')";
    
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Usuario encontrado
        $row = $result->fetch_assoc();
        $role = $row['role'];
        $secret_key = $row['secret_key'];
        
        // Comprobar si es administrador y tiene clave
        if ($role === 'administrator' && !empty($secret_key)) {
            // CLAVE 3 ENCONTRADA
            $message = "<div class='message success'><strong>ACCESO ADMINISTRADOR CON ÉXITO.</strong><br>Bienvenido, $user.<br>Tu clave secreta (KEY_3) es: <h3>$secret_key</h3><p>Continúa la búsqueda.</p></div>";
        } else {
            // Usuario normal
            $message = "<div class='message success'><strong>ACCESO CON ÉXITO.</strong><br>Bienvenido, $user. Acceso de usuario estándar. No hay clave aquí para ti.</div>";
        }
    } else {
        // Error de login
        $message = "<div class='message error'>Usuario o contraseña incorrectos.</div>";
    }
}

// Cerrar la conexión
if ($conn && !$conn->connect_error) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Clientes - SecureTech Inc.</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="main-header">
        <div class="container header-content">
            <a href="index.php" class="logo">SecureTech Inc.</a>
            <nav class="main-nav">
                <a href="index.php">Inicio</a>
                <a href="blog.php">Blog</a>
                <a href="gallery.php">Galería</a>
                <a href="login.php" class="active">Acceso Clientes</a> </nav>
        </div>
    </header>

    <main class="page-content">
        <div class="container">
            
            <div class="content-box">
                <h2 style="text-align: center;">Portal de Acceso a Clientes</h2>
                
                <?php echo $message; ?>

                <form method="post" action="login.php">
                    <div class="form-group">
                        <label for="username">Nombre de Usuario:</label>
                        <input type="text" id="username" name="username" placeholder="Ingrese su usuario" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
                    </div>
                    
                    <input type="submit" value="Iniciar Sesión">
                </form>
            </div>

        </div> </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2025 SecureTech Inc. Todos los derechos reservados.</p>
            <p style="font-size: 0.9em; color: var(--text-light); margin-top