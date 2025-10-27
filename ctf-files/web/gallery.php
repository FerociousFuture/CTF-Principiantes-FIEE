<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Galería de Imágenes - Pista Oculta</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .image-card {
            border: 1px solid #ced4da;
            border-radius: 5px;
            overflow: hidden;
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
        }
        .image-card img {
            width: 100%;
            height: auto;
            border-radius: 3px;
            max-height: 200px;
            object-fit: cover;
        }
        .image-card a {
            display: block;
            margin-top: 10px;
            color: #17a2b8;
            text-decoration: none;
            font-weight: bold;
        }
        .image-card a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Galería de Imágenes del Proyecto Fénix</h1>
        <p>Hemos subido algunas fotos recientes. Recuerda que la imagen debe ser descargada a tu máquina para analizarla.</p>
        
        <div class="nav">
            <a href="index.php">Inicio</a>
            <a href="blog.php">Blog de Noticias</a>
            <a href="login.php">Acceso de Usuarios</a>
        </div>
        
        <hr>
        
        <div class="image-grid">
            
            <div class="image-card">
                <img src="./images/foto_1.jpg" alt="Equipo de Desarrollo">
                <p>Sesión de planificación de la fase 1.</p>
                <a href="./images/foto_1.jpg" download>Descargar Original</a> 
            </div>

            <div class="image-card">
                <img src="./images/foto_2.jpg" alt="Infraestructura de Servidores">
                <p>Nuevos racks de servidores instalados.</p>
                <a href="./images/foto_2.jpg" download>Descargar Original</a> 
            </div>

            <div class="image-card">
                <img src="./images/Soy_ghost.jpg.jpg" alt="ERROR ERROR ERROR">
                <p>Whoami</p>
                <a href="./images/Soy_ghost.jpg.jpg" download>Descargar Original (CLAVE 4)</a> 
            </div>

        </div>

        <p style="margin-top: 30px;">Si no encuentras nada, quizás debas probar diferentes herramientas de esteganografía.</p>
    </div>
</body>
</html>