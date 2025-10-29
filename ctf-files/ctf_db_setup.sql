-- Archivo: ctf_db_setup.sql

-- 1. Crear la Base de Datos para el CTF
CREATE DATABASE IF NOT EXISTS ctf_lab;

-- Usar la nueva base de datos
USE ctf_lab;

-- 2. Crear las tablas (si no existen)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    secret_key VARCHAR(100) -- Aquí se oculta la clave SQLi
);

CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    author VARCHAR(50) NOT NULL
);

-- 3. ¡CORRECCIÓN! Limpiar datos existentes para reejecución segura
-- Esto evita el error "Duplicate entry" si el script se corre de nuevo.
DELETE FROM users;
DELETE FROM blog_posts;

-- 4. Insertar el usuario administrador (adminpassword = 1234, hash MD5: 81dc9bdb52d04dc20036dbd8313ed055)
-- Este usuario es el objetivo de la inyección SQL para obtener la clave.
INSERT INTO users (username, password_hash, role, secret_key) VALUES (
    'admin', 
    '81dc9bdb52d04dc20036dbd8313ed055', 
    'administrator', 
    'KEY_3_SQLi_W0N' -- ¡Clave 3 del CTF!
);

-- 5. Insertar un usuario normal
INSERT INTO users (username, password_hash, role) VALUES (
    'guest', 
    '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', -- Clave: 'password' (SHA1)
    'user'
);


-- 6. Insertar posts (incluyendo la pista de la clave XSS)
INSERT INTO blog_posts (title, content, author) VALUES (
    'Bienvenido al Blog del CTF', 
    'Este blog ha estado desatendido.', 
    'SystemAdmin'
);
-- 7. Insertar el usuario para el desafío de Fuerza Bruta (Hydra)
-- Usuario: sysadmin, Contraseña: 4321 (MD5: 827ccb0eea8a706c4c34a16891f84e7b)
INSERT INTO users (username, password_hash, role) VALUES (
    'sysadmin', 
    '827ccb0eea8a706c4c34a16891f84e7b', 
    'user'
);

INSERT INTO blog_posts (title, content, author) VALUES (
    'ADMIN POST', 
    'El último ataque XSS expuso una clave. No se ha limpiado el comentario malicioso.', 
    'Anonymous'
);