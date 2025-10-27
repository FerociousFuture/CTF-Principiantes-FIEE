<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso de Usuarios</title>
    <link rel="stylesheet" href="style.css"> <style>
        /* Ajuste específico para que el login esté en el centro y no ocupe todo el ancho del container */
        .login-box { 
            background-color: white; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2); 
            width: 300px;
            margin: auto; /* Centrar */
        }
    </style>
</head>
<body>
    <?php
    // ... (El código PHP y la lógica de conexión/SQLi es la misma que la versión anterior) ...
    $servername = "localhost";
    $username = "root";
    $password = ""; 
    $dbname = "ctf_lab";
    $message = "";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        $message = "<div class='message error'>Error de conexión a la base de datos: " . $conn->connect_error . "</div>";
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = $_POST['username'];
        $pass = $_POST['password'];

        // Consulta SQL VULNERABLE
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
            // Este error puede ser aprovechado para detectar el tipo de base de datos.
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