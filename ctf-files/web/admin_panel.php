<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración (Fuerza Bruta)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Acceso al Panel de Control</h1>
        <p>Esta página requiere el usuario: <code>sysadmin</code>.</p>
        <p>La contraseña es débil, de 4 dígitos numéricos. ¡Usa una herramienta de fuerza bruta!</p>
        
        <?php
        $message = "";
        $correct_password = "4321"; // Contraseña de 4 dígitos para Hydra

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user = $_POST['admin_user'];
            $pass = $_POST['admin_pass'];

            // Lógica de autenticación simple y vulnerable a fuerza bruta
            if ($user === "sysadmin" && $pass === $correct_password) {
                $message = "<div class='message success'>¡ACCESO CONCEDIDO!<br>Has encontrado la contraseña por fuerza bruta. Esto completa el desafío de Hydra/John.</div>";
            } else {
                $message = "<div class='message error'>Acceso Denegado. Credenciales incorrectas.</div>";
            }
        }
        ?>

        <div style="text-align: center; background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-top: 20px;">
            <h2>Login de Administrador</h2>
            <?php echo $message; ?>
            <form method="post" action="admin_panel.php">
                <label for="admin_user">Usuario:</label>
                <input type="text" id="admin_user" name="admin_user" value="sysadmin" required>
                
                <label for="admin_pass">Contraseña:</label>
                <input type="password" id="admin_pass" name="admin_pass" required>
                
                <input type="submit" value="Iniciar Sesión">
            </form>
        </div>
    </div>
</body>
</html>