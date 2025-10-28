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
        <p>Hemos subido algunas fotos recientes.</p>
        
        <div class="nav">
            <a href="index.php">Inicio</a>
            <a href="blog.php">Blog de Noticias</a>
            <a href="login.php">Acceso de Usuarios</a>
        </div>
        
        <hr>
        
        <div class="image-grid">
            
            <div class="image-card">
                <img src="./images/foto1.jpg" alt="Equipo de Desarrollo">
                <p>Sesión de planificación de la fase 1.</p>
                <a href="./images/foto1.jpg" download="foto1.jpg">Descargar Original</a> 
            </div>

            <div class="image-card">
                <img src="./images/foto2.jpg" alt="Aprende mas sobre las redes de computadoras">
                <p>Seminario de Redes de Informatica.</p>
                <a href="./images/foto2.jpg" download="foto2.jpg">Descargar Original</a> 
            </div>

            <div class="image-card">
                <img src="./images/Soy_ghost.jpg" alt="ERROR ERROR ERROR">
                <p>whoami</p>
                
                <a href="./images/Soy_ghost.jpg" download="Soy_ghost.jpg">Descargar Original</a> 
            </div>

            <div class="image-card">
                <img src="./images/foto3.jpg" alt="Concurso de botargas UV">
                <p>Concurso de Botargas</p>
                
                <a href="./images/foto3.jpg" download="foto3.jpg">Descargar Original</a> 
            </div>

        </div>

        <p style="margin-top: 30