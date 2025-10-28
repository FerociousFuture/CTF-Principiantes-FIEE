<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-R">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería - SecureTech Inc.</title>
    <link rel="stylesheet" href="style.css">
    </head>
<body>

    <header class="main-header">
        <div class="container header-content">
            <a href="index.php" class="logo">SecureTech Inc.</a>
            <nav class="main-nav">
                <a href="index.php">Inicio</a>
                <a href="blog.php">Blog</a>
                <a href="gallery.php" class="active">Galería</a> <a href="login.php">Acceso Clientes</a>
            </nav>
        </div>
    </header>

    <main class="page-content">
        <div class="container">
            
            <div style="text-align: center; margin-bottom: 2.5rem;">
                <h1>Galería de Eventos</h1>
                <p class="text-light" style="font-size: 1.1rem;">Un vistazo a nuestro equipo y actividades recientes.</p>
            </div>

            <div class="gallery-grid">
            
                <div class="gallery-item">
                    <img src="./images/foto1.jpg" alt="Equipo de Desarrollo en reunión">
                    <p>Sesión de planificación de la fase 1.</p>
                    <a href="./images/foto1.jpg" download="foto1.jpg">Descargar Original</a> 
                </div>

                <div class="gallery-item">
                    <img src="./images/foto2.jpg" alt="Seminario de Redes">
                    <p>Seminario de Redes de Informática.</p>
                    <a href="./images/foto2.jpg" download="foto2.jpg">Descargar Original</a> 
                </div>

                <div class="gallery-item">
                    <img src="./images/Soy_ghost.jpg" alt="Foto del equipo">
                    <p>whoami</p> <a href="./images/Soy_ghost.jpg" download="Soy_ghost.jpg">Descargar Original</a> 
                </div>

                <div class="gallery-item">
                    <img src="./images/foto3.jpg" alt="Evento de la mascota de la empresa">
                    <p>Concurso de Botargas</p>
                    <a href="./images/foto3.jpg" download="foto3.jpg">Descargar Original</a> 
                </div>

            </div> </div> </main>

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