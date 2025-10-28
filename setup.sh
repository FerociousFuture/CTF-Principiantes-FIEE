#!/usr/bin/env bash
# --------------------------------------------------------------------
# 🧱 CTF Fedora Server Auto Setup Script - Versión Definitiva
# --------------------------------------------------------------------
# Instala y configura Apache, PHP, MariaDB, firewall y SELinux.
# Crea la base de datos del laboratorio CTF y despliega los archivos web.
# Resuelve conflictos con la página de bienvenida de Fedora y permisos de SELinux.
# --------------------------------------------------------------------

# Configuración estricta de errores
set -euo pipefail
IFS=$'\n\t'

LOG_FILE="/var/log/ctf_setup.log"
# Redirigir toda la salida (stdout y stderr) al log y a la consola
exec > >(tee -a "$LOG_FILE") 2>&1

# 🎨 Colores
GREEN="\e[32m"; RED="\e[31m"; YELLOW="\e[33m"; CYAN="\e[36m"; NC="\e[0m"

info()  { echo -e "${CYAN}[INFO]${NC} $*"; }
ok()    { echo -e "${GREEN}[OK]${NC} $*"; }
warn()  { echo -e "${YELLOW}[WARN]${NC} $*"; }
error() { echo -e "${RED}[ERROR]${NC} $*"; exit 1; }

# 🧩 Verificación de permisos
if [ "$EUID" -ne 0 ]; then
  error "Este script debe ejecutarse como root o con sudo."
fi

# --------------------------------------------------------
# 0. Determinar la ubicación absoluta del script
# --------------------------------------------------------
# ESTO ARREGLA EL ERROR DE COPIA:
SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
cd "$SCRIPT_DIR" || exit 1
info "Cambiando al directorio del script: $SCRIPT_DIR"
# --------------------------------------------------------

# 📦 Variables
CTF_FILES_DIR="${CTF_FILES_DIR:-./ctf-files}"
SQL_SCRIPT="$CTF_FILES_DIR/ctf_db_setup.sql"
DB_NAME="${DB_NAME:-ctf_lab}"
DB_USER="${DB_USER:-ctf_user}"
DB_PASS="${DB_PASS:-ctf_pass}"
APACHE_USER="apache"
APACHE_GROUP="apache"
HTTP_PORT=80

info "Iniciando configuración del laboratorio CTF..."
info "Logs completos se guardarán en: $LOG_FILE"
sleep 1

# --------------------------------------------------------
# 1. Instalación de servicios y dependencias
# --------------------------------------------------------
info "Instalando paquetes requeridos (httpd, PHP, MariaDB, firewalld)..."
dnf install -y httpd php php-mysqlnd mariadb-server policycoreutils-python-utils firewalld net-tools >/dev/null || \
  error "Error al instalar los paquetes requeridos."
ok "Paquetes instalados correctamente."

# --------------------------------------------------------
# 2. Limpieza y preparación del entorno de Apache
# --------------------------------------------------------
info "Eliminando configuraciones previas de Apache problemáticas..."

# CORRECCIÓN: Deshabilitar la página de bienvenida de Fedora
if [ -f /etc/httpd/conf.d/welcome.conf ]; then
  mv /etc/httpd/conf.d/welcome.conf /etc/httpd/conf.d/welcome.conf.bak
  ok "Página de bienvenida de Fedora deshabilitada."
fi

# Arreglar conflicto 'OPTIONS=' (drop-in vacío)
if [ -f /etc/systemd/system/httpd.service.d/php-fpm.conf ]; then
  rm -f /etc/systemd/system/httpd.service.d/php-fpm.conf
  ok "Archivo drop-in php-fpm.conf eliminado."
fi

# Asegurar que solo escuche en el puerto 80
sed -i '/^Listen /d' /etc/httpd/conf/httpd.conf
echo "Listen ${HTTP_PORT}" >> /etc/httpd/conf/httpd.conf

# --------------------------------------------------------
# 3. Habilitación e inicio de servicios base
# --------------------------------------------------------
info "Habilitando e iniciando MariaDB y firewalld..."
systemctl enable --now mariadb || error "No se pudo iniciar MariaDB."
systemctl enable --now firewalld || warn "No se pudo iniciar firewalld (continuando)."
ok "Servicios de base habilitados."

# --------------------------------------------------------
# 4. Firewall y SELinux
# --------------------------------------------------------
info "Configurando firewall y SELinux..."
if command -v firewall-cmd &>/dev/null; then
  firewall-cmd --permanent --add-port=${HTTP_PORT}/tcp || warn "Fallo al abrir puerto HTTP."
  firewall-cmd --permanent --add-service=ssh || firewall-cmd --permanent --add-port=22/tcp
  firewall-cmd --reload
  ok "Firewall configurado correctamente."
fi

if command -v semanage &>/dev/null; then
  semanage port -a -t http_port_t -p tcp ${HTTP_PORT} 2>/dev/null || \
  semanage port -m -t http_port_t -p tcp ${HTTP_PORT}
  ok "SELinux configurado para puerto ${HTTP_PORT}."
fi

# --------------------------------------------------------
# 5. Configuración de MariaDB
# --------------------------------------------------------
info "Configurando MariaDB (seguridad básica y creación de DB)..."
# Usamos 'EOF' con comillas para evitar la expansión de variables por el shell
mysql -u root <<'SQL' || error "Fallo en configuración inicial de MariaDB."
DELETE FROM mysql.user WHERE User='' OR (User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1'));
DROP DATABASE IF EXISTS test;
FLUSH PRIVILEGES;
CREATE DATABASE IF NOT EXISTS `ctf_lab` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'ctf_user'@'localhost' IDENTIFIED BY 'ctf_pass';
GRANT ALL PRIVILEGES ON `ctf_lab`.* TO 'ctf_user'@'localhost';
FLUSH PRIVILEGES;
SQL
ok "Base de datos '$DB_NAME' y usuario '$DB_USER' creados."

# --------------------------------------------------------
# 6. Despliegue de archivos web y base de datos
# --------------------------------------------------------
info "Desplegando archivos web del CTF..."
WEB_DIR="$CTF_FILES_DIR/web"
IMG_DIR="$CTF_FILES_DIR/images"
HTML_DIR="/var/www/html"

# Limpiar el destino
rm -rf "$HTML_DIR"/*

# 6a. Copiar archivos web (Sintaxis robusta)
if [ -d "$WEB_DIR" ]; then
  cp -r "$WEB_DIR/." "$HTML_DIR" || error "Error copiando archivos web (desde $WEB_DIR)."
else
  error "No se encontró el directorio fuente: '$WEB_DIR'."
fi

# 6b. Copiar imágenes (Sintaxis robusta)
if [ -d "$IMG_DIR" ]; then
  # Copia el *contenido* de ctf-files/images/ a /var/www/html/images/
  mkdir -p "$HTML_DIR/images"
  cp -r "$IMG_DIR/." "$HTML_DIR/images" || error "Error copiando archivos de imágenes."
else
  warn "No se encontró '$IMG_DIR'."
fi

# 6c. Verificación de la copia
info "Verificando la copia de archivos..."
if [ ! -f "$HTML_DIR/index.php" ]; then
    error "¡VERIFICACIÓN FALLIDA! El archivo 'index.php' no existe en $HTML_DIR. La copia falló."
fi
ok "Archivos web desplegados y validados en $HTML_DIR."

# 6d. Asignar permisos y contexto de SELinux
chown -R ${APACHE_USER}:${APACHE_GROUP} "$HTML_DIR"
chmod -R 755 "$HTML_DIR"
restorecon -Rv "$HTML_DIR" >/dev/null 2>&1 || true
ok "Permisos de archivos y contexto de SELinux aplicados."

# 6e. Ejecutar script SQL
if [ -f "$SQL_SCRIPT" ]; then
  info "Ejecutando script SQL: $SQL_SCRIPT"
  mysql -u root "$DB_NAME" < "$SQL_SCRIPT" && ok "Script SQL ejecutado correctamente." || warn "Error ejecutando script SQL (¿ya se ejecutó?)."
else
  warn "No se encontró '$SQL_SCRIPT'."
fi

# --------------------------------------------------------
# 7. Inicio final y verificación de Apache
# --------------------------------------------------------
info "Iniciando y verificando servidor Apache..."
apachectl configtest >/dev/null 2>&1 || error "Error en la configuración de Apache."
systemctl daemon-reload
systemctl enable httpd
systemctl restart httpd || error "Apache no pudo iniciarse."
ok "Servidor Apache iniciado correctamente."

# --------------------------------------------------------
# 8. Confirmación final
# --------------------------------------------------------
sleep 1
ok "✅ Configuración completa del laboratorio CTF."
echo -e "${GREEN}El servicio HTTP está activo en el puerto ${HTTP_PORT}.${NC}"
echo -e "Accede desde tu máquina anfitriona usando: ${CYAN}http://<IP_de_tu_VM>${NC}"