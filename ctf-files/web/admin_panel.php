<?php
// ------------------------------------------------------------------
// LÓGICA DE LOGIN (VULNERABLE A FUERZA BRUTA)
// ------------------------------------------------------------------
$message = "";
$correct_password = "4321"; // Contraseña de 4 dígitos para Hydra

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Usamos '??' para evitar warnings si las claves no están definidas
    $user = $_POST['admin_user'] ?? '';
    $pass = $_POST['admin_pass'] ?? '';

    // Lógica de autenticación simple y vulnerable a fuerza bruta
    if ($user === "sysadmin" && $pass === $correct_password) {
        $message = "<div class='message success'><strong>¡ACCESO CONCEDIDO!</strong><br>Has encontrado la contraseña por fuerza bruta. Esto completa el desafío de Hydra.</div>";
    } else {
        $message = "<div class='message error'>Acceso Denegado. Credenciales incorrectas.</div>";
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
            <a href="index.html" class="logo">SecureTech Inc.</a>
            <nav class="main-nav">
                <a href="index.html">Inicio</a>
                <a href="blog.php">Blog</a>
                <a href="gallery.php">Galería</a>
                <a href="login.php">Acceso Clientes</a>
            </nav>
        </div>
    </header>

    <main class="page-content">
        <div class="container">
            
            <div class="content-box">
                <h2 style="text-align: center;">Acceso de Administrador del Sistema</h2>
                <p style="text-align: center; color: var(--text-light); margin-top: -10px;">
                    Este panel es solo para personal autorizado.
                </p>

                <?php echo $message; ?>

                <form method="post" action="admin_panel.php">
                    <div class="form-group">
                        <label for="admin_user">Usuario:</label>
                        <input type="text" id="admin_user" name="admin_user" value="sysadmin" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_pass">Contraseña:</label>
                        <input type="password" id="admin_pass" name="admin_pass" placeholder="PIN de 4 dígitos" required>
                    </div>
                    
                    <input type="submit" value="Iniciar Sesión">
                </form>
            </div>

        </div> </main>

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