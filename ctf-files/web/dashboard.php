<?php
// Iniciar la sesión para leer las variables
session_start();

// -----------------------------------------------------------------
// PROTEGER LA PÁGINA
// -----------------------------------------------------------------
// Si el usuario no está logueado o no es admin, lo echamos.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrator') {
    die("Acceso denegado. <a href='login.php'>Inicia sesión</a>");
}

// Incluir config y conectar a la BD
require_once 'config.php';
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Error de conexión.");
}

// -----------------------------------------------------------------
// OBTENER LA CLAVE SQLi
// -----------------------------------------------------------------
// Usamos el ID de la sesión para buscar la clave del admin
$user_id = (int)$_SESSION['user_id'];
$secret_key = "CLAVE NO ENCONTRADA";

$stmt = $conn->prepare("SELECT secret_key FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $secret_key = htmlspecialchars($row['secret_key']); // Sanitizar por si acaso
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Admin - SecureTech Inc.</title>
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
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <main class="page-content">
        <div class="container">
            
            <h1>Panel de Administrador</h1>
            <p style="font-size: 1.25rem; color: var(--text-light); margin-top: -10px;">
                Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?>.
            </p>
            
            <div class="content-box" style="margin-bottom: 2rem;">
                <h2>Acceso Confirmado</h2>
                <p>Tu clave de API interna (KEY_3) para el acceso a la base de datos es:</p>
                <div class="message success">
                    <h3 style="text-align: center; margin: 0; font-family: 'Courier New', monospace;">
                        <?php echo $secret_key; ?>
                    </h3>
                </div>
            </div>

            <div class="content-box">
                <h2>Bandeja de Entrada (1)</h2>
                
                <div class="blog-post" style="background-color: var(--bg-light);">
                    <h3 style="margin-top:0;">
                        <span style="color: #dc3545;">[ALERTA]</span> Hallazgos de la auditoría del Blog
                    </h3>
                    <p class="post-meta">
                        <strong>De:</strong> security.audit@securetech.inc
                    </p>
                    <p>Equipo,</p>
                    <p>La auditoría del blog está completa. El *único* hallazgo fue que el *scripting* del lado del cliente podía leer las cookies no protegidas (`HttpOnly`) del navegador.</p>
                    <p>Nuestra cookie de <strong><code>document.cookie</code></strong> fue expuesta por un `alert()` en la prueba. Consideren esto como un hallazgo de prioridad media.</p>
                </div>
            </div>

        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2025 SecureTech Inc. Todos los derechos reservados.</p>
        </div>
    </footer>

</body>
</html>