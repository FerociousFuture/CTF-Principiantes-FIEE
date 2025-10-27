<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Archivos Secretos - Pistas de Contraseñas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Archivos de Mantenimiento y Auditoría</h1>
        <p>Parece que has encontrado un archivo confidencial. ¡No se lo digas a nadie!</p>
        
        <div class="nav">
            <a href="index.php">Volver</a>
        </div>
        
        <hr>

        <h2>Contraseñas Hash de la Base de Datos</h2>
        <p>Hemos guardado los hashes de una cuenta de prueba en un archivo. Necesitas crackear la contraseña para obtener la Clave 5.</p>
        
        <div style="text-align: left; background-color: #fce4ec; padding: 15px; border-radius: 5px; border: 1px dashed #e91e63;">
            <p><strong>Ubicación del Hash (dentro de la MV):</strong> <code>/var/www/html/hashes.txt</code></p>
            <p>El formato del hash es MD5. La contraseña es la clave final del CTF.</p>
        </div>

        <p style="margin-top: 30px;">**Pista Adicional (Fuerza Bruta):**</p>
        <p>También hay una **página de administración oculta** en el puerto 80. La URL es <code>admin_panel.php</code>. El usuario es <code>sysadmin</code> y la contraseña es débil (de 4 dígitos numéricos).</p>
        <p>Esta es una buena oportunidad para usar **Hydra** con un diccionario numérico.</p>

    </div>
</body>
</html>