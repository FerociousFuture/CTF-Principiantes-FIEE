<?php
// ------------------------------------------------------------------
// LÓGICA DE LOGIN (VULNERABLE A SQL INJECTION)
// ------------------------------------------------------------------

// ¡IMPORTANTE! Iniciar la sesión al principio de todo.
session_start();

require_once 'config.php';
$message = "";

// Redirigir si el usuario ya está logueado como admin
if (isset($_SESSION['role']) && $_SESSION['role'] === 'administrator') {
    header("Location: dashboard.php");
    exit;
}

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    $message = "<div class='message error'>Error de conexión a la base de datos.</div>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$conn->connect_error) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // VULNERABILIDAD SQLi
    // La consulta ahora pide el 'id' para guardarlo en la sesión
    $sql = "SELECT id, username, secret_key, role FROM users WHERE username = '$user' AND password_hash = MD5('$pass')";
    
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $role = $row['role'];

        if ($role === 'administrator') {
            // ¡ÉXITO!
            // Guardamos los datos del admin en la sesión
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            
            // Redirigimos al panel de control
            header("Location: dashboard.php");
            $conn->close();
            exit; // Importante salir después de redirigir
            
        } else {
            // Usuario normal (no nos interesa para este CTF)
            $message = "<div class='message error'>Acceso de usuario estándar no permitido en este panel.</div>";
        }
    } else {
        // Error de login
        $message = "<div class='message error'>Usuario o contraseña incorrectos.</div>";
    }
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
        </div>
    </main>

    <footer class="main-footer">
        </footer>

</body>
</html>