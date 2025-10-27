#!/bin/bash
# -------------------------------------------------------------
# Script de Configuración de Laboratorio CTF (setup.sh)
#
# Este script instala, configura y prepara todos los servicios 
# y vulnerabilidades en una MV de Fedora Server para el CTF.
# -------------------------------------------------------------

echo "Made by FerociousFuture"
echo "Iniciando configuración automática del laboratorio CTF en Fedora Server."

# Variables
CTF_FILES_DIR="./ctf-files"
# RUTA CORREGIDA: Apunta a ctf-files/ctf_db_setup.sql
SQL_SCRIPT="$CTF_FILES_DIR/ctf_db_setup.sql" 
DB_USER="ctf_user"
DB_PASS="ctf_pass"

# 1. Instalación de Servicios Esenciales
######################################

echo "Paso 1/7: Instalando servidor web (Apache), PHP, MariaDB y utilidades..."

# Instala Apache, PHP, MariaDB y herramientas de seguridad/configuración (semanage)
sudo dnf install httpd php php-mysqlnd mariadb-server policycoreutils-python-utils -y

if [ $? -ne 0 ]; then
    echo "Error al instalar paquetes. Revise su conexión o los repositorios."
    exit 1
fi

echo "Paquetes instalados."

# 2. Habilitación e Inicio de Servicios
#####################################

echo "Paso 2/7: Habilitando e iniciando servicios..."

sudo systemctl enable httpd
sudo systemctl start httpd
sudo systemctl enable mariadb
sudo systemctl start mariadb

# Dar un pequeño tiempo para que los servicios arranquen
sleep 5 

echo "Servicios iniciados."

# 3. Configuración del Firewall y SELinux (Apertura Robusta del Puerto 80)
#########################################################################

echo "Paso 3/7: Configurando Firewall y SELinux..."

# 3a. Configuración de Apache para asegurar que escuche en el puerto 80
sudo sed -i 's/^#Listen 12.34.56.78:80/Listen 80/' /etc/httpd/conf/httpd.conf

# 3b. Configuración del Firewall (abrir por número es más robusto)
sudo firewall-cmd --zone=public --add-port=80/tcp --permanent
sudo firewall-cmd --zone=public --add-port=22/tcp --permanent
sudo firewall-cmd --reload

# 3c. Configuración de SELinux (necesario en Fedora para el puerto 80)
# Usa -m (modificar) si ya existe la regla, o -a (añadir) si no existe.
sudo semanage port -a -t http_port_t -p tcp 80 2>/dev/null || sudo semanage port -m -t http_port_t -p tcp 80

sudo systemctl restart httpd # Reiniciar Apache para aplicar cambios de SELinux

echo "Firewall y SELinux configurados. Puerto 80 (HTTP) abierto."

# 4. Limpieza Inicial de MariaDB
################################

echo "Paso 4/7: Configurando seguridad inicial de MariaDB..."

sudo mysql -u root <<EOF
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\_%';
FLUSH PRIVILEGES;
EOF

# 5. Configuración de Base de Datos y Usuario de Aplicación
##########################################################

echo "Paso 5/7: Creando usuario de aplicación '$DB_USER' y otorgando permisos..."

# 5a. Eliminar usuario si existe (se usa 'EOF' con comillas simples para evitar conflicto de shell)
sudo mysql -u root <<'EOF'
DROP USER IF EXISTS 'ctf_user'@'localhost';
FLUSH PRIVILEGES;
EOF

# 5b. Crear el usuario de la aplicación y otorgar permisos en un solo paso
sudo mysql -u root <<'EOF'
GRANT SELECT, INSERT, UPDATE, DELETE ON ctf_lab.* TO 'ctf_user'@'localhost' IDENTIFIED BY 'ctf_pass';
FLUSH PRIVILEGES;
EOF

echo "Usuario '$DB_USER' de MariaDB creado con éxito."

# 6. Despliegue de Archivos del CTF y Creación de la Base de Datos
################################################################

echo "Paso 6/7: Desplegando archivos del CTF y creando la base de datos..."

# 6a. Despliegue de Archivos Web y permisos
if [ -d "$CTF_FILES_DIR/web" ]; then
    sudo rm -rf /var/www/html/* sudo cp -r "$CTF_FILES_DIR/web"/* /var/www/html/
    sudo cp -r "$CTF_FILES_DIR/images" /var/www/html/ # Copiar carpeta images
    
    sudo chown -R apache:apache /var/www/html
    sudo chmod -R 755 /var/www/html
    echo "Archivos web desplegados en /var/www/html/."
else
    echo "ADVERTENCIA: No se encontró la carpeta '$CTF_FILES_DIR/web'."
fi

# 6b. Creación y población de la Base de Datos
if [ -f "$SQL_SCRIPT" ]; then
    echo "Ejecutando script SQL de configuración desde $SQL_SCRIPT..."
    
    # El script SQL crea la DB y las tablas
    sudo mysql -u root < "$SQL_SCRIPT" 
    
    if [ $? -eq 0 ]; then
        echo "Base de datos 'ctf_lab' creada y poblada con éxito."
    else
        echo "Error al ejecutar el script SQL."
    fi
else
    echo "ADVERTENCIA: No se encontró el script SQL en '$SQL_SCRIPT'. La DB no se ha configurado."
fi

# 7. NOTIFICACIÓN FINAL
#####################

echo ""
echo "--------------------------------------------------------------------------------------"
echo "🎉 Configuración de Laboratorio CTF completada."
echo "¡El puerto 80 (HTTP) está abierto y listo para el ataque!"
echo "Accede desde tu máquina anfitriona usando la IP de tu MV."
echo "--------------------------------------------------------------------------------------"