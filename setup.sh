#!/usr/bin/env bash
# --------------------------------------------------------------------
# Script de Autoconfiguración del Servidor CTF (Fedora)
# --------------------------------------------------------------------
# Aprovisiona un servidor LAMP (Apache, MariaDB, PHP) con reglas
# de firewall y SELinux para un laboratorio CTF.
# --------------------------------------------------------------------

# Modo estricto y seguro
set -euo pipefail
IFS=$'\n\t'

LOG_FILE="/var/log/ctf_setup.log"
# Log dual: salida a consola y a /var/log/ctf_setup.log
exec > >(tee -a "$LOG_FILE") 2>&1

# Utilidades de logging con colores
GREEN="\e[32m"; RED="\e[31m"; YELLOW="\e[33m"; CYAN="\e[36m"; NC="\e[0m"

info()  { echo -e "${CYAN}[INFO]${NC} $*"; }
ok()    { echo -e "${GREEN}[OK]${NC} $*"; }
warn()  { echo -e "${YELLOW}[WARN]${NC} $*" >&2; }
error() { echo -e "${RED}[ERROR]${NC} $*" >&2; exit 1; }

# Verificación de superusuario
if [ "$EUID" -ne 0 ]; then
  error "Este script debe ejecutarse como root o con sudo."
fi

# --------------------------------------------------------
# 0. Contexto de ejecución del script
# --------------------------------------------------------
# Asegura la ruta de copia (evita errores de 'cp' relativo)
SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
cd "$SCRIPT_DIR" || exit 1
info "Directorio de trabajo del script: $SCRIPT_DIR"
# --------------------------------------------------------

# Variables de configuración
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
# Fase 1: Instalación de dependencias
# --------------------------------------------------------
info "Instalando paquetes (httpd, php-mysqlnd, mariadb-server, firewalld)..."
dnf install -y httpd php php-mysqlnd mariadb-server policycoreutils-python-utils firewalld net-tools >/dev/null || \
  error "Error al instalar paquetes base."
ok "Dependencias instaladas."

# --------------------------------------------------------
# Fase 2: Saneamiento de Apache
# --------------------------------------------------------
info "Limpiando configuraciones previas de Apache..."

# Deshabilita la página de bienvenida de Fedora (evita conflictos)
if [ -f /etc/httpd/conf.d/welcome.conf ]; then
  mv /etc/httpd/conf.d/welcome.conf /etc/httpd/conf.d/welcome.conf.bak
  ok "Página de bienvenida de Fedora deshabilitada."
fi

# Limpia drop-ins de systemd conflictivos (ej. php-fpm)
if [ -f /etc/systemd/system/httpd.service.d/php-fpm.conf ]; then
  rm -f /etc/systemd/system/httpd.service.d/php-fpm.conf
  ok "Archivo drop-in php-fpm.conf eliminado."
fi

# Define el puerto de escucha (Limpia configs anteriores)
sed -i '/^Listen /d' /etc/httpd/conf/httpd.conf
echo "Listen ${HTTP_PORT}" >> /etc/httpd/conf/httpd.conf

# --------------------------------------------------------
# Fase 3: Habilitación de servicios base
# --------------------------------------------------------
info "Habilitando e iniciando MariaDB y firewalld..."
systemctl enable --now mariadb || error "No se pudo iniciar MariaDB."
systemctl enable --now firewalld || warn "No se pudo iniciar firewalld (continuando)."
ok "Servicios de base habilitados."

# --------------------------------------------------------
# Fase 4: Configuración de Seguridad (Firewall/SELinux)
# --------------------------------------------------------
info "Configurando firewall y SELinux..."
if command -v firewall-cmd &>/dev/null; then
  firewall-cmd --permanent --add-port=${HTTP_PORT}/tcp || warn "Fallo al abrir puerto HTTP."
  firewall-cmd --permanent --add-service=ssh || firewall-cmd --permanent --add-port=22/tcp
  firewall-cmd --reload
  ok "Firewall configurado."
fi

if command -v semanage &>/dev/null; then
  # Intenta añadir; si falla (ya existe), modifica.
  semanage port -a -t http_port_t -p tcp ${HTTP_PORT} 2>/dev/null || \
  semanage port -m -t http_port_t -p tcp ${HTTP_PORT}
  ok "SELinux configurado para puerto ${HTTP_PORT}."
fi

# --------------------------------------------------------
# Fase 5: Configuración de MariaDB
# --------------------------------------------------------
info "Configurando MariaDB (creación de DB y usuario)..."
# Script de configuración inicial de MariaDB (Heredoc)
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
# Fase 6: Despliegue de la aplicación CTF
# --------------------------------------------------------
info "Desplegando archivos web del CTF..."
WEB_DIR="$CTF_FILES_DIR/web"
IMG_DIR="$CTF_FILES_DIR/images"
HTML_DIR="/var/www/html"

# Limpia el directorio web
rm -rf "$HTML_DIR"/*

# 6a. Copia los archivos de la aplicación
if [ -d "$WEB_DIR" ]; then
  cp -r "$WEB_DIR/." "$HTML_DIR" || error "Error copiando archivos web (desde $WEB_DIR)."
else
  error "No se encontró el directorio fuente: '$WEB_DIR'."
fi

# 6b. Copia los assets (imágenes)
if [ -d "$IMG_DIR" ]; then
  mkdir -p "$HTML_DIR/images"
  cp -r "$IMG_DIR/." "$HTML_DIR/images" || error "Error copiando archivos de imágenes."
else
  warn "No se encontró '$IMG_DIR'."
fi

# 6c. Validación de despliegue
info "Verificando la copia de archivos..."
if [ ! -f "$HTML_DIR/index.php" ]; then
    error "¡VERIFICACIÓN FALLIDA! El archivo 'index.php' no existe en $HTML_DIR."
fi
ok "Archivos web desplegados y validados en $HTML_DIR."

# 6d. Aplica permisos (chown/chmod) y contexto (restorecon)
chown -R ${APACHE_USER}:${APACHE_GROUP} "$HTML_DIR"
chmod -R 755 "$HTML_DIR"
restorecon -Rv "$HTML_DIR" >/dev/null 2>&1 || true
ok "Permisos de archivos y contexto de SELinux aplicados."

# 6e. Importa la base de datos del CTF
if [ -f "$SQL_SCRIPT" ]; then
  info "Ejecutando script SQL: $SQL_SCRIPT"
  mysql -u root "$DB_NAME" < "$SQL_SCRIPT" && ok "Script SQL ejecutado." || warn "Error ejecutando script SQL (¿ya se ejecutó?)."
else
  warn "No se encontró '$SQL_SCRIPT'."
fi

# --------------------------------------------------------
# Fase 7: Inicio y validación de Apache
# --------------------------------------------------------
info "Iniciando y verificando servidor Apache..."
apachectl configtest >/dev/null 2>&1 || error "Error en la configuración de Apache."
systemctl daemon-reload
systemctl enable httpd
systemctl restart httpd || error "Apache no pudo iniciarse."
ok "Servidor Apache iniciado correctamente."

# --------------------------------------------------------
# Fase 8: Finalización
# --------------------------------------------------------
sleep 1
ok "Configuración completa del laboratorio CTF."
echo -e "${GREEN}El servicio HTTP está activo en el puerto ${HTTP_PORT}.${NC}"
echo -e "Accede desde tu máquina anfitriona usando: ${CYAN}http://<IP_de_tu_VM>${NC}"