<?php
// ------------------------------------------------------------------
// blog.php: Maneja la lógica del blog, vulnerable a XSS.
// ------------------------------------------------------------------

// Establece un cookie de depuración no-HttpOnly, visible para JavaScript.
$cookie_name = "debug_session_id";
$cookie_value = "KEY_4_XSS_C00KIE_FTW";
setcookie($cookie_name, $cookie_value, time() + 3600, "/");

// Incluye la configuración de la base de datos.
require_once 'config.php';

$message = "";
$posts = []; // Almacena los posts para mostrarlos.

// Inicia la conexión con la base de datos.
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    $message = "<div class='message error'>Error de conexión a la base de datos: " . $conn->connect_error . "</div>";
} else {

    // Procesa la solicitud de borrado si 'delete_id' está presente.
    if (isset($_GET['delete_id'])) {
        $delete_id = (int)$_GET['delete_id'];
        // Solo permite borrar posts del autor 'Participante CTF'.
        $author_to_delete = "Participante CTF"; 
        
        $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ? AND author = ?");
        $stmt->bind_param("is", $delete_id, $author_to_delete);
        $stmt->execute();
        $stmt->close();
        
        // Redirige para limpiar la URL y evitar re-borrado al recargar.
        header("Location: blog.php");
        exit;
    }

    // Procesa la publicación de un nuevo comentario si es un método POST.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_title = $_POST['post_title'] ?? 'Sin Título';
        $new_content = $_POST['post_content'] ?? '';
        $new_author = "Participante CTF"; // Autor fijo para posts de usuarios.

        $stmt = $conn->prepare("INSERT INTO blog_posts (title, content, author) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $new_title, $new_content, $new_author);

        if ($stmt->execute()) {
            $message = "<div class='message success'>Comentario publicado con éxito. ¡Revísalo abajo!</div>";
        } else {
            $message = "<div class='message error'>Error al publicar el comentario: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
    
    // Obtiene todos los posts de la base de datos para mostrarlos.
    $sql = "SELECT id, title, content, author FROM blog_posts ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $posts[] = $row; 
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale1.0">
    <title>Blog - SecureTech Inc.</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="main-header">
        <div class="container header-content">
            <a href="index.php" class="logo">SecureTech Inc.</a>
            <nav class="main-nav">
                <a href="index.php">Inicio</a>
                <a href="blog.php" class="active">Blog</a> <a href="gallery.php">Galería</a>
                <a href="login.php">Acceso Clientes</a>
            </nav>
        </div>
    </header>

    <main class="page-content">
        <div class="container">
            
            <h1>Blog de Noticias de SecureTech</h1>
            <p style="text-align: center; font-size: 1.1rem; color: var(--text-light); margin-top: -10px; margin-bottom: 2rem;">
                Bienvenido a nuestro portal. Siéntete libre de dejar tus comentarios.
            </p>

            <div class="content-box" style="margin-bottom: 2.5rem;">
                <h3 style="text-align: center;">Publicar Nuevo Comentario</h3>
                
                <?php echo $message; ?>

                <form method="post" action="blog.php">
                    <div class="form-group">
                        <label for="post_title">Título:</label>
                        <input type="text" id="post_title" name="post_title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="post_content">Contenido:</label>
                        <textarea id="post_content" name="post_content" rows="4" required></textarea>
                    </div>
                    
                    <input type="submit" value="Publicar Comentario">
                </form>
            </div>

            <h2 style="text-align: center;">Publicaciones Recientes</h2>
            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 1rem 0 2rem 0;">

            <?php
            if (empty($posts) && empty($message)) { 
                echo "<p style='text-align: center;'>No hay publicaciones aún. ¡Sé el primero en comentar!</p>";
            } else {
                // Itera y muestra cada post.
                foreach ($posts as $post) {
                    echo "<div class='blog-post'>";
                    
                    // Muestra el botón de borrado solo para posts del usuario.
                    if ($post['author'] === 'Participante CTF') {
                        echo "<a href='blog.php?delete_id=" . $post['id'] . "' 
                               style='float: right; color: #dc3545; text-decoration: none; font-weight: bold; margin-left: 10px;' 
                               title='Borrar este post'
                               onclick=\"return confirm('¿Estás seguro de que quieres borrar este post?');\">
                               [X] Borrar
                             </a>";
                    }
                    
                    // Imprime los datos del post sin sanitización (VULNERABILIDAD XSS).
                    echo "<h2>" . $post['title'] . "</h2>"; 
                    echo "<p class='post-meta'><strong>Autor:</strong> " . $post['author'] . "</p>";
                    echo "<div>" . $post['content'] . "</div>";
                    echo "</div>";
                }
            }
            ?>
            
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