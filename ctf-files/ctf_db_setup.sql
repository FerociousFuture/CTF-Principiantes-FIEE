-- Archivo: ctf_db_setup.sql

-- 1. Crear la Base de Datos para el CTF
CREATE DATABASE IF NOT EXISTS ctf_lab;

-- Usar la nueva base de datos
USE ctf_lab;

-- 2. Crear la tabla de usuarios para el login vulnerable
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    secret_key VARCHAR(100) -- Aquí se ocultará la clave
);

-- 3. Insertar el usuario administrador con un hash DEBIL (para Hydra/John, aunque lo usaremos para SQLi)
-- Hash de 'adminpassword' usando MD5 (sólo para este propósito de CTF)
INSERT INTO users (username, password_hash, role, secret_key) VALUES (
    'admin', 
    '81dc9bdb52d04dc20036dbd8313ed055', -- Clave: '1234' (para que John/Hydra pueda crackearla si fuera necesario)
    'administrator', 
    'KEY_3_SQLi_W0N' -- ¡La tercera clave del CTF!
);

-- 4. Insertar un usuario normal (con una clave diferente, quizás un hash más fuerte)
INSERT INTO users (username, password_hash, role) VALUES (
    'guest', 
    '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', -- Clave: 'password' (SHA1)
    'user'
);

-- 5. Crear una tabla de un blog simple para el XSS
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    author VARCHAR(50) NOT NULL
);

-- 6. Insertar posts (uno que sirva de pista, otro con la clave XSS)
INSERT INTO blog_posts (title, content, author) VALUES (
    'Bienvenido al Blog del CTF', 
    'Este blog ha estado desatendido por mucho tiempo. No confíes en lo que la gente publica aquí...', 
    'SystemAdmin'
);

INSERT INTO blog_posts (title, content, author) VALUES (
    '¿Viste algo raro?', 
    'Ayer alguien publicó algo que no me gustó. Creo que usaron una etiqueta `<script>` maliciosa. ¡Ten cuidado! La clave está en el mensaje de error o en la sesión de la víctima. PISTA: Usa un payload que filtre el contenido de la cookie de sesión a tu máquina. La clave del XSS es: KEY_2_XSS_F0UND', 
    'Anonymous'
);