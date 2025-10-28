# CTF: Laboratorio de Ciberseguridad
## By FerociousFuture

Este repositorio contiene la configuración completa para un laboratorio de Captura la Bandera (CTF) desplegado en una Máquina Virtual (MV) de Fedora Server. El objetivo es ofrecer un entorno controlado para el desarrollo de habilidades esenciales en ciberseguridad ofensiva.

---

## Objetivo General

Desarrollar habilidades de aprendizaje vistas en la materia de ciberseguridad.

## Objetivos Especificos

* Comprender el funcionamiento del direccionamiento, clases, máscaras, puertas de enlace de una IP.
* Entender el funcionamiento del protocolo HTTP, sus vulnerabilidades y sus métodos de inspección.
* Aprender técnicas de análisis forense y de reconocimiento dentro de un espacio controlado y seguro.

## Temática y Flujo del Laboratorio

El desafío consiste en encontrar **cinco (5) claves ocultas** en un período de 60 minutos, utilizando únicamente el protocolo HTTP y el reconocimiento de la MV.

El flujo de ataque se basa en el ciclo de reconocimiento y explotación de servicios web:

1.  **Reconocimiento y Scrapping:** Escaneo de puertos y descubrimiento de contenido oculto.
2.  **Explotación de Servicios Web:** Compromiso de aplicaciones por medio de fallos de diseño (**SQLi** y **XSS**).
3.  **Análisis Forense:** Extracción de datos ocultos en archivos (**Esteganografía**) y *cracking* de credenciales (**John the Ripper** y **Hydra**).

---

## Herramientas Necesarias (En la Máquina Anfitriona)

Para interactuar y explotar la MV, se requerirán las siguientes herramientas de código abierto. Se asume el uso de un sistema operativo de ataque.

* **Reconocimiento:** `Nmap`, `dirb` o `gobuster` (para descubrir directorios).
* **Explotación Web:** Navegador web, extensiones de *proxy* como **Burp Suite** (Community Edition).
* **Ataque a Credenciales:** **John the Ripper** (para *cracking* de hashes) y **Hydra** (para fuerza bruta en logins).
* **Análisis Forense:** Herramientas de Esteganografía, como `steghide`.

---

## Habilidades Clave a Desarrollar

Se recomienda que el participante tenga conocimientos fundamentales en las siguientes áreas antes de iniciar el desafío:

* **Fundamentos de Redes:** Entendimiento de direcciones IP, puertos y el uso de herramientas de escaneo.
* **Protocolo HTTP:** Comprensión de métodos, encabezados HTTP y la inspección del código fuente (Scrapping).
* **Inyección SQL (SQLi):** Conocimiento de la sintaxis básica de SQL y la explotación de *payloads* de inyección.
* **XSS (Cross-Site Scripting):** Capacidad para inyectar código JavaScript en una aplicación web vulnerable.
* **Cracking de Contraseñas:** Familiaridad con tipos de *hashes* (MD5) y comandos de **John the Ripper** y **Hydra**.
* **Esteganografía:** Capacidad para identificar y extraer información oculta en archivos de imagen.

---

## Pasos de Despliegue

1.  **MV Base:** Instalar una Máquina Virtual de VirtualBox con **Fedora Server 38+**.
2.  **Configuración de Red:** Asegurar que la MV utilice el modo **Adaptador Solo Anfitrión** (Host-Only Adapter).
3.  **Clonar el Repo:** Dentro de la MV de Fedora Server, clonar este repositorio.
    ```bash
    git clone https://github.com/FerociousFuture/CTF-Principiantes-FIEE.git
    ```
4.  **Ejecución:** Otorgar permisos y ejecutar el script de configuración, el cual instalará Apache, MariaDB y desplegará todos los archivos del CTF.
    ```bash
    chmod +x setup.sh
    sudo ./setup.sh
    ```
5.  **Inicio:** Una vez finalizado el script, el laboratorio está activo. Utilice la máquina anfitriona para escanear la IP de la MV e iniciar el CTF.