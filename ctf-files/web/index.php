<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureTech Inc. - Soluciones de Seguridad</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="main-header">
        <div class="container header-content">
            <a href="index.php" class="logo">SecureTech Inc.</a>
            
            <nav class="main-nav">
                <a href="index.php" class="active">Inicio</a>
                <a href="blog.php">Blog</a>
                <a href="gallery.php">Galería</a>
                <a href="login.php">Acceso Clientes</a>
            </nav>
        </div>
    </header>

    <main class="page-content">
        <div class="container">

            <div style="text-align: center; padding: 2.5rem 1rem; background: var(--bg-white); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                <h1>Portal de Evaluación de Seguridad</h1>
                <p style="font-size: 1.25rem; color: var(--text-light); margin-top: -10px;">
                    Bienvenido a la plataforma de evaluación de SecureTech.
                </p>
                <p>Este entorno controlado está diseñado para probar y mejorar sus habilidades en ciberseguridad.</p>
            </div>

            <div style="margin-top: 2.5rem; background: var(--bg-white); padding: 2rem; border-radius: 8px;">
                <h2>Sobre este Desafío (CTF)</h2>
                <p>Su misión es encontrar las <strong>5 claves (flags)</strong> ocultas dentro de nuestra infraestructura. Este desafío simula vulnerabilidades comunes encontradas en aplicaciones web corporativas del mundo real.</p>
                
                <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 1.5rem 0;">

                <h3>Puntos de Partida</h3>
                <p>Para comenzar su evaluación, le recomendamos explorar las secciones públicas de nuestro portal, accesibles desde la barra de navegación superior (Blog, Galería, Acceso Clientes).</p>
                <p>Sin embargo, tenga en cuenta que <strong>no todo lo que necesita está a simple vista.</strong> El reconocimiento inicial es clave. ¡Buena suerte!</p>
            </div>

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

    <a href="key_1_intro.html" class="hidden-link">clave-secreta-1</a>
    <a href="passwords.php" class="hidden-link">enlace-secreto-passwords</a>
    <a href="admin_panel.php" class="hidden-link">enlace-secreto-admin</a>

</body>
</html>