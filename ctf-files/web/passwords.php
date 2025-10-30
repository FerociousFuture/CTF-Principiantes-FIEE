<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <title>Auditoría Interna - SecureTech Inc.</title>
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

            <div class="content-box">
                <h2 style="text-align: center;">Portal de Auditoría de Seguridad</h2>
                <p style="text-align: center; color: var(--text-light);">Ha accedido a un recurso de mantenimiento restringido.</p>

                <div class="message error" style="text-align: left;">
                    <strong>ALERTA:</strong> Este acceso ha sido registrado. Esta herramienta es solo para personal de TI autorizado.
                </div>

                <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 2rem 0;">

                <h3>Resultados de la Auditoría</h3>
                <p>Se han identificado las siguientes vulnerabilidades pendientes de revisión:</p>

                <div class="clave-box">
                    <h3>Vulnerabilidad #1: Archivo de Hash Expuesto</h3>
                    <p>Se ha detectado un archivo de prueba con credenciales hasheadas en una ubicación pública.</p>
                    <ul>
                        <li><strong>Archivo:</strong> <code>/var/www/html/hashes.txt</code></li>
                        <li><strong>Formato:</strong> MD5</li>
                        <li><strong>Acción Requerida:</strong> Crackear el hash para obtener la <strong>Clave 5</strong> y reportarla.</li>
                    </ul>
                </div>

                <div class="clave-box" style="margin-top: 1.5rem;">
                    <h3>Vulnerabilidad #2: Acceso Interno con Credenciales Débiles</h3>
                    <p>Se ha identificado que un panel de administración interno (<code>admin_panel.php</code>) carece de una política de bloqueo de intentos fallidos, lo que permite la <strong>adivinación automatizada de contraseñas</strong> (fuerza bruta).</p>
                    <p>Se aconseja a los administradores que revisen los archivos expuestos en <code>robots.txt</code>, ya que se han encontrado listas de contraseñas de texto plano (<code>*.txt</code>) dejadas por desarrolladores que facilitaron esta auditoría.</p>
                    <ul>
                        <li><strong>Acción Requerida:</strong> Implementar un límite de intentos y sanear los archivos de respaldo del directorio web.</li>
                    </ul>
                </div>

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

</body>
</html>