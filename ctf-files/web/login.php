<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso de Usuarios</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Ajuste específico para centrar el login en el container */
        .login-box { 
            background-color: white; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2); 
            width: 300px;
            margin: auto;
        }
    </style>
</head>
<body>
    <?php
    // CONFIGURACIÓN DE LA BASE DE DATOS (¡CORREGIDO!)
    $servername = "localhost";
    $username = "ctf_user"; // ¡USUARIO CORREGIDO!
    $password = "ctf_pass"; // ¡CLAVE CORREGIDA!
    $dbname = "ctf_lab";
    $message = "";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        $message = "<div class='message error'>Error de conexión a la base de datos: " . $conn->connect_error . "</div>";
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Consulta SQL VULNERABLE
        $user = $_POST['username'];
        $pass = $_POST['password'];

        $sql = "SELECT username, secret_key, role FROM users WHERE username = '$user' AND password_hash = MD5('$pass')";
        
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $role = $row['role'];
            $secret_key = $row['secret_key'];
            
            if ($role === 'administrator' && !empty($secret_key)) {
                $message = "<div class='message success'>ACCESO ADMINISTRADOR CON ÉXITO.<br>Bienvenido, $user.<br>Tu clave secreta (KEY_3) es: <h3>$secret_key</h3><p>Continúa la búsqueda.</p></div>";
            } else {
                $message = "<div class='message success'>ACCESO DE USUARIO CON ÉXITO.<br>Bienvenido, $user. Acceso limitado, no hay clave aquí para ti.</div>";
            }
        } else {
            $message = "<div class='message error'>Usuario o contraseña incorrectos.</div>";
        }
    }

    $conn->close();
    ?>

    <div class="container">
        <div class="login-box">
            <h2>Acceso al Panel de Control</h2>
            <?php echo $message; ?>
            <form method="post" action="login.php">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" placeholder="Ingresa tu usuario" required>
                
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                
                <input type="submit" value="Iniciar Sesión">
            </form>
        </div>
    </div>
</body>
</html>