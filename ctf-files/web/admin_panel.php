<?php
// ------------------------------------------------------------------
// LÓGICA DE LOGIN (VULNERABLE A FUERZA BRUTA - VERSIÓN CON BD)
// ------------------------------------------------------------------

// ¡IMPORTANTE! Iniciar la sesión al principio de todo.
session_start();

// 🔑 Incluir el archivo de configuración
require_once 'config.php';

$message = "";
$is_logged_in = false; // Variable para controlar qué mostramos

// -----------------------------------------------------------------
// 1. MANEJO DE CIERRE DE SESIÓN (LOGOUT)
// -----------------------------------------------------------------
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: admin_panel.php");
    exit;
}

// -----------------------------------------------------------------
// 2. COMPROBAR SI YA EXISTE UNA SESIÓN
// -----------------------------------------------------------------
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $is_logged_in = true;
}

// -----------------------------------------------------------------
// 3. PROCESAR INTENTO DE LOGIN (SI NO ESTÁ LOGUEADO)
// -----------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && !$is_logged_in) {
    
    // Conectar a la base de datos
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        $message = "<div class='message error'>Error de conexión a la base de datos.</div>";
    } else {
        
        $user = $_POST['admin_user'] ?? '';
        $pass = $_POST['admin_pass'] ?? '';
        $pass_hash = md5($pass); 

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password_hash = ?");
        $stmt->bind_param("ss", $user, $pass_hash);
        $stmt->execute();
        $result = $stmt->get_result();

        // Lógica de autenticación
        if ($result->num_rows > 0 && $user === 'sysadmin') {
            
            // ¡ÉXITO! Establecemos la sesión
            $_SESSION['admin_logged_in'] = true;
            $is_logged_in = true;
            
            // Recargamos la página para mostrar el panel de admin
            header("Location: admin_panel.php"); 
            exit;
            
        } else {
            $message = "<div class='message error'>Acceso Denegado. Credenciales incorrectas.</div>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - SecureTech Inc.</title>
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
                <a href="login.php">Acceso Clientes</a>
            </nav>
        </div>
    </header>

    <main class="page-content">
        <div class="container">
            
            <?php if ($is_logged_in): ?>

                <div class="content-box">
                    <h2 style="text-align: center;">Panel de Administración</h2>
                    <p style="text-align: center; color: var(--text-light);">Bienvenido, sysadmin.</p>
                    <hr>
                    
                    <h3>Clave de Administrador (Key 2)</h3>
                    <p>Has completado el desafío de fuerza bruta. Tu clave de recompensa está aquí:</p>
                    
                    <div class="clave-box" style="text-align: center;">
                        <h3 style="color: var(--color-warning);">KEY_2_HYDRA_BRUTEFORCE_SUCCESS</h3>
                    </div>

                    <p style="text-align: center; margin-top: 30px;">
                        <a href="admin_panel.php?logout=true" style="color: #dc3545; text-decoration: none;">Cerrar Sesión</a>
                    </p>
                </div>

            <?php else: ?>

                <div class="content-box">
                    <h2 style="text-align: center;">Acceso de Administrador del Sistema</h2>
                    <p style="text-align: center; color: var(--text-light); margin-top: -10px;">
                        Este panel es solo para personal autorizado.
                    </p>

                    <?php echo $message; ?>

                    <form method="post" action="admin_panel.php">
                        <div class="form-group">
                            <label for="admin_user">Usuario:</label>
                            <input type="text" id="admin_user" name="admin_user" placeholder="Nombre de usuario" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_pass">Contraseña:</label>
                            <input type="password" id="admin_pass" name="admin_pass" placeholder="Contraseña" required>
                        </div>
                        
                        <input type="submit" value="Iniciar Sesión">
                    </form>
                </div>

            <?php endif; ?>
            </div> 
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2025 SecureTech Inc. Todos los derechos reservados.</p>
            <p style="font-size: 0.9em; color: var(--text-light); margin-top: 5px;">
                Servido por Apache/2.4.58 (Fedora)
            </p>
        </div>
    </footer>

</body>
</html>