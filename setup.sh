#!/bin/bash
# -------------------------------------------------------------
# Script de Configuración de Laboratorio CTF (setup.sh)
#
# Este script está diseñado para instalar y configurar todos 
# los servicios necesarios en una máquina virtual de Fedora Server 
# para el laboratorio de ciberseguridad (CTF).
# -------------------------------------------------------------

echo "Made by FerociousFuture"
echo "Iniciando configuración del laboratorio CTF en Fedora Server."

# 1. Instalación de Servicios Esenciales
######################################

echo "Paso 1/5: Instalando servidor web (Apache) y base de datos (MariaDB)..."

# Instala Apache HTTP Server, PHP y MariaDB Server
sudo dnf install httpd php php-mysqlnd mariadb-server -y

if [ $? -ne 0 ]; then
    echo "Error al instalar paquetes. Revise su conexión o los repositorios."
    exit 1
fi

echo "Paquetes instalados."

# 2. Habilitación e Inicio de Servicios
#####################################

echo "Paso 2/5: Habilitando e iniciando servicios..."

# Habilitar e iniciar Apache
sudo systemctl enable httpd
sudo systemctl start httpd

# Habilitar e iniciar MariaDB
sudo systemctl enable mariadb
sudo systemctl start mariadb

# Esperar un momento para asegurar que MariaDB esté listo
sleep 5

echo "Servicios iniciados."

# 3. Configuración de Base de Datos (Seguridad y Usuarios)
#######################################################

echo "Paso 3/5: Configurando seguridad de MariaDB..."

# Ejecutar comandos de seguridad iniciales para MariaDB:
# Limpia usuarios anónimos y la base de datos de prueba
sudo mysql -u root <<EOF
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\_%';
FLUSH PRIVILEGES;
EOF

echo "Seguridad básica de MariaDB configurada. (La contraseña de root de MariaDB sigue vacía por defecto)."

# 4. Configuración del Firewall
#################################################################

echo "Paso 4/5: Configurando Firewall (Permitir HTTP/80 y SSH/22 para escaneo)..."

# Abrir puertos HTTP (80) y SSH (22) en el firewall
sudo firewall-cmd --zone=public --add-service=http --permanent
sudo firewall-cmd --zone=public --add-service=ssh --permanent 
sudo firewall-cmd --reload

echo "Firewall configurado. Puertos 80 (HTTP) y 22 (SSH) abiertos."

# 5. Preparación del Entorno CTF (Contenido y Vulnerabilidades)
#############################################################

echo "Paso 5/5: Desplegando archivos del CTF y configurando la base de datos..."

CTF_FILES_DIR="./ctf-files"
SQL_SCRIPT="./ctf_db_setup.sql" # Asumimos que el script SQL está en la raíz del repo

# Copia los archivos del CTF a la carpeta web de Apache
if [ -d "$CTF_FILES_DIR/web" ]; then
    echo "Desplegando contenido web..."
    sudo rm -rf /var/www/html/* # Limpiamos lo que Apache trae por defecto
    # Copiar contenido de 'web' y 'images'
    sudo cp -r "$CTF_FILES_DIR/web"/* /var/www/html/
    sudo cp -r "$CTF_FILES_DIR/images" /var/www/html/
    
    # Asignar permisos correctos al usuario 'apache'
    sudo chown -R apache:apache /var/www/html
    sudo chmod -R 755 /var/www/html
    echo "Archivos web desplegados en /var/www/html/."
else
    echo "ADVERTENCIA: No se encontró la carpeta '$CTF_FILES_DIR/web'. El contenido web no se ha desplegado."
fi

# AHORA, EJECUTAMOS EL SCRIPT SQL
if [ -f "$SQL_SCRIPT" ]; then
    echo "Configurando y llenando la base de datos 'ctf_lab'..."
    
    # Ejecuta el script SQL en MariaDB, usando root sin contraseña
    sudo mysql -u root < "$SQL_SCRIPT"
    
    if [ $? -eq 0 ]; then
        echo "Base de datos 'ctf_lab' creada y poblada con éxito. Las claves SQLi y XSS están listas."
    else
        echo "Error al ejecutar el script SQL. Por favor, revisa el archivo '$SQL_SCRIPT'."
    fi
else
    echo "ADVERTENCIA: No se encontró el script SQL en '$SQL_SCRIPT'. La base de datos no se ha configurado."
fi

echo ""
echo "--------------------------------------------------------------------------------------"
echo "🎉 Configuración del Laboratorio CTF completada."
echo "La máquina virtual es accesible vía HTTP en su IP local."
echo "Para empezar, escanea los puertos para encontrar la IP."
echo "--------------------------------------------------------------------------------------"