<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Blog de Noticias - Vulnerable</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Blog de Noticias de la Empresa</h1>
        <p>¡Bienvenido! Puedes dejar tus propios comentarios, pero úsalos con responsabilidad.</p>
        
        <div class="nav">
            <a href="index.php">Inicio</a>
            <a href="login.php">Acceso de Usuarios</a>
            <a href="gallery.php">Galería de Imágenes</a>
        </div>
        
        <hr>
        
        <?php
        // CONFIGURACIÓN DE LA BASE DE DATOS (USUARIO CORREGIDO)
        $servername = "localhost";
        $username = "ctf_user"; // ¡USUARIO CORREGIDO!
        $password = "ctf_pass"; // ¡CLAVE CORREGIDA!
        $dbname = "ctf_lab";
        
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            echo "<div class='message error'>Error de conexión a la base de datos: " . $conn->connect_error . "</div>";
            exit();
        }

        // --- MANEJO DE PUBLICACIÓN DE NUEVOS COMENTARIOS ---
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // No sanitizamos los datos de entrada
            $new_title = $_POST['post_title'];
            $new_content = $_POST['post_content'];
            $new_author = "Participante CTF";

            $stmt = $conn->prepare("INSERT INTO blog_posts (title, content, author) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $new_title, $new_content, $new_author);

            if ($stmt->execute()) {
                echo "<div class='message success'>Comentario publicado con éxito. ¡Revísalo abajo!</div>";
            } else {
                echo "<div class='message error'>Error al publicar el comentario: " . $conn->error . "</div>";
            }
            $stmt->close();
        }
        
        // --- FORMULARIO PARA NUEVO COMENTARIO ---
        ?>
        
        <div style="text-align: left; background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 30px;">
            <h3>Publicar Nuevo Comentario</h3>
            <form method="post" action="blog.php">
                <label for="post_title" style="display: block; text-align: left;">Título:</label>
                <input type="text" id="post_title" name="post_title" required>
                
                <label for="post_content" style="display: block; text-align: left;">Contenido:</label>
                <textarea id="post_content" name="post_content" rows="4" required></textarea>
                
                <input type="submit" value="Publicar">
            </form>
        </div>

        <?php
        // --- MOSTRAR TODOS LOS POSTS (VULNERABILIDAD AQUÍ) ---
        $sql = "SELECT title, content, author FROM blog_posts ORDER BY id DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<h2>Publicaciones Recientes</h2>";
            while($row = $result->fetch_assoc()) {
                // VULNERABILIDAD CLAVE: NO SE SANEAN LAS SALIDAS
                $title = $row['title'];
                $content = $row['content']; 
                $author = $row['author'];
                
                echo "<div style='border: 1px solid #ced4da; padding: 15px; margin-top: 15px; text-align: left; background-color: #ffffff;'>";
                echo "<h3>" . $title . "</h3>";
                echo "<p><strong>Autor:</strong> " . $author . "</p>";
                // El contenido se imprime tal cual, permitiendo la ejecución de <script>
                echo "<div style='background-color: #e9ecef; padding: 10px; border-radius: 3px;'>";
                echo $content; // LA VULNERABILIDAD RESIDE EN ESTA LÍNEA
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No hay publicaciones aún.</p>";
        }

        $conn->close();
        ?>
        
        <p style="margin-top: 30px;"><a href="index.php">Volver al inicio</a></p>
    </div>
</body>
</html>