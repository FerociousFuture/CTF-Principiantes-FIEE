#!/usr/bin/env bash
# --------------------------------------------------------------------
# 🧱 CTF Fedora Server Auto Setup Script - Versión a prueba de fallos
# --------------------------------------------------------------------
# Instala y configura Apache, PHP, MariaDB, firewall y SELinux.
# Crea la base de datos del laboratorio CTF y despliega los archivos web.
# --------------------------------------------------------------------

set -euo pipefail
IFS=$'\n\t'

LOG_FILE="/var/log/ctf_setup.log"
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

# 📦 Variables de entorno configurables
CTF_FILES_DIR="${CTF_FILES_DIR:-./ctf-files}"
SQL_SCRIPT="$CTF_FILES_DIR/ctf_db_setup.sql"
DB_NAME="${DB_NAME:-ctf_lab}"
DB_USER="${DB_USER:-ctf_user}"
DB_PASS="${DB_PASS:-ctf_pass}"
APACHE_USER="apache"
APACHE_GROUP="apache"
HTTP_PORT=80

info "Iniciando configuración del laboratorio CTF..."
sleep 1

# --------------------------------------------------------
# 1. Instalación de servicios y dependencias
# --------------------------------------------------------
info "Instalando paquetes requeridos (httpd, PHP, MariaDB, firewalld)..."

dnf install -y httpd php php-mysqlnd mariadb-server policycoreutils-python-utils firewalld net-tools >/dev/null || \
  error "Error al instalar los paquetes requeridos."

ok "Paquetes instalados correctamente."

# --------------------------------------------------------
# 2. Limpieza y preparación del entorno
# --------------------------------------------------------
info "Eliminando configuraciones previas de Apache problemáticas..."

# Arreglar conflicto 'OPTIONS=' (drop-in vacío)
if [ -f /etc/systemd/system/httpd.service.d/php-fpm.conf ]; then
  rm -f /etc/systemd/system/httpd.service.d/php-fpm.conf
  ok "Archivo drop-in php-fpm.conf eliminado."
fi

# Eliminar múltiples líneas Listen
sed -i '/^Listen /d' /etc/httpd/conf/httpd.conf
echo "Listen ${HTTP_PORT}" >> /etc/httpd/conf/httpd.conf

# Validar configuración de Apache antes de iniciar
apachectl configtest >/dev/null 2>&1 || true

# --------------------------------------------------------
# 3. Habilitación e inicio de servicios
# --------------------------------------------------------
info "Habilitando e iniciando Apache, MariaDB y firewalld..."

systemctl enable --now mariadb || error "No se pudo iniciar MariaDB."
systemctl enable --now firewalld || warn "No se pudo iniciar firewalld (continuando)."

# Apache aún no se inicia: lo probaremos tras validar configuración
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

# SELinux: asignar puerto HTTP si es necesario
if command -v semanage &>/dev/null; then
  semanage port -a -t http_port_t -p tcp ${HTTP_PORT} 2>/dev/null || \
  semanage port -m -t http_port_t -p tcp ${HTTP_PORT}
  ok "SELinux configurado para puerto ${HTTP_PORT}."
fi

# --------------------------------------------------------
# 5. Configuración de MariaDB
# --------------------------------------------------------
info "Configurando MariaDB (seguridad básica y creación de DB)..."

mysql -u root <<SQL || error "Fallo en configuración inicial de MariaDB."
DELETE FROM mysql.user WHERE User='' OR (User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1'));
DROP DATABASE IF EXISTS test;
FLUSH PRIVILEGES;
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL

ok "Base de datos '$DB_NAME' y usuario '$DB_USER' creados."

# --------------------------------------------------------
# 6. Despliegue de archivos web
# --------------------------------------------------------
info "Desplegando archivos web del CTF..."

if [ -d "$CTF_FILES_DIR/web" ]; then
  rm -rf /var/www/html/*
  cp -r "$CTF_FILES_DIR/web/." /var/www/html/ || error "Error copiando archivos web."
  [ -d "$CTF_FILES_DIR/images" ] && cp -r "$CTF_FILES_DIR/images" /var/www/html/

  chown -R ${APACHE_USER}:${APACHE_GROUP} /var/www/html
  chmod -R 755 /var/www/html
  restorecon -Rv /var/www/html >/dev/null 2>&1 || true
  ok "Archivos web desplegados correctamente."
else
  warn "No se encontró '$CTF_FILES_DIR/web'."
fi

# Ejecutar script SQL si existe
if [ -f "$SQL_SCRIPT" ]; then
  info "Ejecutando script SQL: $SQL_SCRIPT"
  mysql -u root "$DB_NAME" < "$SQL_SCRIPT" && ok "Script SQL ejecutado correctamente." || warn "Error ejecutando script SQL."
else
  warn "No se encontró '$SQL_SCRIPT'."
fi

# --------------------------------------------------------
# 7. Verificación de configuración de Apache
# --------------------------------------------------------
info "Verificando configuración de Apache..."
if apachectl configtest >/dev/null 2>&1; then
  ok "Configuración de Apache válida."
else
  error "Error en la configuración de Apache. Revisa /etc/httpd/conf/httpd.conf"
fi

# --------------------------------------------------------
# 8. Inicio del servidor web
# --------------------------------------------------------
info "Iniciando servidor Apache..."
systemctl daemon-reload
systemctl enable httpd
systemctl restart httpd || error "Apache no pudo iniciarse."

# --------------------------------------------------------
# 9. Confirmación final
# --------------------------------------------------------
sleep 1
ok "✅ Configuración completa del laboratorio CTF."
echo -e "${GREEN}El servicio HTTP está activo en el puerto ${HTTP_PORT}.${NC}"
echo -e "Accede desde tu máquina anfitriona usando: ${CYAN}http://<IP_de_tu_VM>${NC}"
echo -e "Log del proceso: ${YELLOW}${LOG_FILE}${NC}"
