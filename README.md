# Control Vehicular - Club Campestre Altos del Chicala

Sistema de gestion y control documental vehicular desarrollado para el Club Campestre Altos del Chicala.

## Descripcion

Control Vehicular es una aplicacion web que permite gestionar el control documental de vehiculos y conductores, con alertas automaticas de vencimiento, generacion de reportes y control de acceso vehicular.

## Caracteristicas Principales

### Gestion de Vehiculos

- Registro completo de vehiculos (placa, marca, modelo, color, tipo)
- Asignacion de propietarios y conductores
- Control de documentos (SOAT, Tecnomecanica, Tarjeta de Propiedad)
- Semaforo de estados (Vigente, Por vencer, Vencido)

### Gestion de Conductores

- Registro de conductores con informacion personal
- Control de licencias de conduccion
- Historial de asignaciones a vehiculos

### Sistema de Alertas

- Alertas automaticas de documentos proximos a vencer
- Notificaciones en tiempo real
- Clasificacion por tipo de documento y urgencia
- Marcado de alertas como leidas

### Centro de Reportes

- Reporte general de vehiculos
- Reporte de alertas y vencimientos
- Reporte por propietario
- Reporte historico de renovaciones
- Ficha individual por vehiculo
- Exportacion a PDF y Excel

### Modulo Porteria

- Busqueda rapida de vehiculos por placa
- Verificacion de estado documental
- Registro de entradas y salidas

### Control de Acceso por Roles

- **ADMIN**: Acceso completo al sistema
- **SST**: Gestion de vehiculos, conductores y reportes
- **PORTERIA**: Acceso solo al modulo de porteria

## Tecnologias

- **Framework**: Laravel 11
- **Base de Datos**: MySQL
- **Frontend**: Blade, Bootstrap 5, Font Awesome
- **Contenedores**: Docker (Laravel Sail)
- **Reportes PDF**: DomPDF
- **Exportacion Excel**: Maatwebsite/Laravel-Excel

# MANUAL TÉCNICO DE INSTALACIÓN

## Sistema de Control Vehicular - Club Campestre Altos del Chicalá

**Versión:** 1.0
**Framework:** Laravel 12
**PHP:** 8.2+

---

## ÍNDICE

1. [Requisitos del Sistema](#1-requisitos-del-sistema)
2. [Instalación en Entorno Local](#2-instalación-en-entorno-local)
3. [Instalación en Hostinger](#3-instalación-en-hostinger)
4. [Configuración de Tareas Programadas](#4-configuración-de-tareas-programadas)
5. [Solución de Problemas](#5-solución-de-problemas)

---

## 1. REQUISITOS DEL SISTEMA

### 1.1 Requisitos de Software

| Componente | Versión Mínima | Recomendada |
| ---------- | -------------- | ----------- |
| PHP        | 8.2            | 8.3         |
| MySQL      | 5.7            | 8.0         |
| Node.js    | 18.x           | 20.x        |
| Composer   | 2.x            | 2.7+        |
| NPM        | 9.x            | 10.x        |

### 1.2 Extensiones PHP Requeridas

```
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- PDO_MySQL
- Tokenizer
- XML
- GD (para generación de PDFs)
- Zip (para exportación Excel)
```

### 1.3 Dependencias del Proyecto

**Backend (Composer):**

```
laravel/framework: ^12.0
barryvdh/laravel-dompdf: ^3.1    (Generación de PDFs)
maatwebsite/excel: ^3.1          (Exportación a Excel/CSV)
doctrine/dbal: ^4.3              (Migraciones de BD)
laravel/sanctum: ^4.0            (Autenticación API)
laravel/breeze: ^2.3             (Autenticación UI)
```

**Frontend (NPM):**

```
vite: ^7.0.7
tailwindcss: ^3.1.0
alpinejs: ^3.4.2
axios: ^1.11.0
```

---

## 2. INSTALACIÓN EN ENTORNO LOCAL

### 2.1 Windows (XAMPP/Laragon)

#### Opción A: Usando Laragon (Recomendado)

**Paso 1: Instalar Laragon**

```
1. Descargar Laragon Full desde: https://laragon.org/download/
2. Instalar con opciones por defecto
3. Laragon incluye: PHP 8.2, MySQL 8, Node.js, Composer
```

**Paso 2: Clonar el Proyecto**

```bash
# Abrir terminal de Laragon (clic derecho en icono → Terminal)
cd C:\laragon\www

# Clonar repositorio
git clone https://github.com/tu-usuario/Control_Vehicular.git

# Entrar al proyecto
cd Control_Vehicular
```

**Paso 3: Configurar Variables de Entorno**

```bash
# Copiar archivo de configuración
copy .env.example .env
```

**Editar archivo `.env` con Notepad:**

```env
APP_NAME="Control Vehicular"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://control-vehicular.test

# Configuración MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=control_vehicular
DB_USERNAME=root
DB_PASSWORD=

# Configuración de correo (opcional para desarrollo)
MAIL_MAILER=log
```

**Paso 4: Instalar Dependencias**

```bash
# Instalar dependencias PHP
composer install

# Generar clave de aplicación
php artisan key:generate

# Instalar dependencias Node.js
npm install
```

**Paso 5: Crear Base de Datos**

```bash
# Opción 1: Desde HeidiSQL (incluido en Laragon)
# - Abrir HeidiSQL desde Laragon
# - Crear nueva base de datos: control_vehicular
# - Cotejamiento: utf8mb4_unicode_ci

# Opción 2: Desde terminal MySQL
mysql -u root -e "CREATE DATABASE control_vehicular CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**Paso 6: Ejecutar Migraciones y Seeders**

```bash
# Crear tablas
php artisan migrate

# (Opcional) Cargar datos de prueba
php artisan db:seed
```

**Paso 7: Compilar Assets**

```bash
# Para desarrollo (con hot reload)
npm run dev

# Para producción
npm run build
```

**Paso 8: Iniciar Servidor**

```bash
# Opción 1: Usar virtual host de Laragon
# El proyecto estará disponible en: http://control-vehicular.test

# Opción 2: Servidor de desarrollo
php artisan serve
# Disponible en: http://127.0.0.1:8000
```

---

#### Opción B: Usando XAMPP

**Paso 1: Instalar XAMPP**

```
1. Descargar XAMPP con PHP 8.2+: https://www.apachefriends.org/
2. Instalar en C:\xampp
3. Iniciar Apache y MySQL desde el panel de control
```

**Paso 2: Instalar Composer**

```
1. Descargar: https://getcomposer.org/download/
2. Ejecutar instalador
3. Seleccionar php.exe de XAMPP: C:\xampp\php\php.exe
```

**Paso 3: Instalar Node.js**

```
1. Descargar: https://nodejs.org/
2. Instalar versión LTS (20.x)
3. Verificar: node --version && npm --version
```

**Paso 4: Clonar y Configurar**

```bash
cd C:\xampp\htdocs
git clone https://github.com/tu-usuario/Control_Vehicular.git
cd Control_Vehicular

# Configurar .env (ver Opción A, Paso 3)
copy .env.example .env
# Editar .env con los datos de MySQL
```

**Paso 5: Instalar y Ejecutar**

```bash
composer install
php artisan key:generate
npm install

# Crear base de datos desde phpMyAdmin
# URL: http://localhost/phpmyadmin
# Crear BD: control_vehicular, cotejamiento: utf8mb4_unicode_ci

php artisan migrate
npm run build
```

**Paso 6: Configurar Virtual Host (Opcional)**

Editar `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/Control_Vehicular/public"
    ServerName control-vehicular.local
    <Directory "C:/xampp/htdocs/Control_Vehicular/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Editar `C:\Windows\System32\drivers\etc\hosts`:

```
127.0.0.1   control-vehicular.local
```

Reiniciar Apache.

---

### 2.2 Linux (Ubuntu/Debian)

**Paso 1: Instalar Dependencias del Sistema**

```bash
# Actualizar repositorios
sudo apt update && sudo apt upgrade -y

# Instalar PHP 8.2 y extensiones
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd \
    php8.2-intl php8.2-readline

# Instalar MySQL
sudo apt install -y mysql-server

# Instalar Node.js 20.x
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

**Paso 2: Configurar MySQL**

```bash
# Iniciar MySQL
sudo systemctl start mysql
sudo systemctl enable mysql

# Configurar seguridad (opcional pero recomendado)
sudo mysql_secure_installation

# Crear base de datos y usuario
sudo mysql -u root -p
```

```sql
CREATE DATABASE control_vehicular CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'control_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
GRANT ALL PRIVILEGES ON control_vehicular.* TO 'control_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**Paso 3: Clonar y Configurar Proyecto**

```bash
# Clonar en directorio de proyectos
cd /var/www
sudo git clone https://github.com/tu-usuario/Control_Vehicular.git
cd Control_Vehicular

# Dar permisos al usuario actual
sudo chown -R $USER:www-data .
chmod -R 775 storage bootstrap/cache

# Configurar .env
cp .env.example .env
nano .env
```

**Contenido de `.env` para Linux:**

```env
APP_NAME="Control Vehicular"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=control_vehicular
DB_USERNAME=control_user
DB_PASSWORD=tu_password_seguro
```

**Paso 4: Instalar y Ejecutar**

```bash
# Instalar dependencias
composer install
npm install

# Generar clave
php artisan key:generate

# Migraciones
php artisan migrate

# Compilar assets
npm run build

# Servidor de desarrollo
php artisan serve
```

---

### 2.3 macOS

**Paso 1: Instalar Homebrew y Dependencias**

```bash
# Instalar Homebrew (si no está instalado)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Instalar PHP 8.2
brew install php@8.2
brew link php@8.2 --force

# Instalar MySQL
brew install mysql
brew services start mysql

# Instalar Node.js
brew install node@20

# Instalar Composer
brew install composer
```

**Paso 2: Configurar MySQL**

```bash
# Crear base de datos
mysql -u root -e "CREATE DATABASE control_vehicular CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**Paso 3: Clonar y Configurar**

```bash
cd ~/Projects  # o tu directorio preferido
git clone https://github.com/tu-usuario/Control_Vehicular.git
cd Control_Vehicular

cp .env.example .env
# Editar .env con configuración de MySQL

composer install
npm install
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

---

### 2.4 Usuarios por Defecto (Seeder)

Si ejecutas `php artisan db:seed`, se crean estos usuarios:

| Usuario       | Email                         | Contraseña | Rol      |
| ------------- | ----------------------------- | ---------- | -------- |
| Administrador | admin@controlvehicular.com    | password   | ADMIN    |
| SST           | sst@controlvehicular.com      | password   | SST      |
| Portería      | porteria@controlvehicular.com | password   | PORTERIA |

> **IMPORTANTE:** Cambiar contraseñas inmediatamente en producción.

---

## 3. INSTALACIÓN EN HOSTINGER

### 3.1 Requisitos del Hosting

Hostinger debe tener:

- Plan Premium o Business (PHP 8.2+)
- MySQL 8.0
- Acceso SSH (recomendado)
- Soporte para Composer
- Node.js (para compilar assets localmente)

### 3.2 Preparación Local

**Paso 1: Compilar Assets para Producción**

```bash
# En tu máquina local
cd Control_Vehicular
npm run build
```

Esto genera la carpeta `public/build/` con los assets compilados.

**Paso 2: Configurar .env para Producción**

Crear archivo `.env.production`:

```env
APP_NAME="Control Vehicular"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

# Estos valores se configurarán desde Hostinger
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_controlveh
DB_USERNAME=u123456789_admin
DB_PASSWORD=TuPasswordSeguro123!

# Sesiones y caché
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Correo (configurar con datos de Hostinger)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=correo@tudominio.com
MAIL_PASSWORD=tu_password_correo
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3.3 Subir Archivos a Hostinger

#### Método A: Usando Git + SSH (Recomendado)

**Paso 1: Conectar por SSH**

```bash
# Obtener credenciales SSH desde panel de Hostinger
# Websites → tu sitio → Avanzado → Acceso SSH

ssh u123456789@tudominio.com -p 65002
```

**Paso 2: Clonar Repositorio**

```bash
# En el servidor Hostinger
cd domains/tudominio.com
rm -rf public_html  # Respaldar si hay contenido

git clone https://github.com/tu-usuario/Control_Vehicular.git public_html
cd public_html
```

**Paso 3: Instalar Dependencias**

```bash
# Instalar Composer dependencies (sin dev)
composer install --no-dev --optimize-autoloader

# Configurar permisos
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

---

#### Método B: Usando File Manager / FTP

**Paso 1: Preparar Archivos Localmente**

```bash
# Excluir archivos innecesarios
# NO subir: node_modules/, .git/, tests/
```

**Paso 2: Crear archivo ZIP**

Incluir estas carpetas/archivos:

```
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── build/          ← Assets compilados
│   ├── images/
│   ├── .htaccess
│   └── index.php
├── resources/
├── routes/
├── storage/
├── vendor/             ← Generar con: composer install --no-dev
├── .env                ← Configurar para producción
├── artisan
└── composer.json
```

**Paso 3: Subir vía File Manager**

```
1. Ir a hPanel → Archivos → Administrador de archivos
2. Navegar a: domains/tudominio.com/public_html
3. Subir archivo ZIP
4. Extraer
```

### 3.4 Configurar Base de Datos en Hostinger

**Paso 1: Crear Base de Datos**

```
1. hPanel → Bases de datos → MySQL
2. Crear nueva base de datos:
   - Nombre: controlvehiculo
   - Usuario: admin_cv
   - Contraseña: [generar segura]
3. Anotar los datos completos:
   - Host: localhost
   - BD: u123456789_controlvehiculo
   - Usuario: u123456789_admin_cv
```

**Paso 2: Configurar .env en Servidor**

Usando File Manager o SSH, editar `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_controlvehiculo
DB_USERNAME=u123456789_admin_cv
DB_PASSWORD=TuPasswordSeguro
```

**Paso 3: Ejecutar Migraciones**

Vía SSH:

```bash
cd domains/tudominio.com/public_html
php artisan migrate --force
```

O vía URL temporal (crear y eliminar después):

Crear `public/setup.php`:

```php
<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";
echo "Ejecutando migraciones...\n";
$kernel->call('migrate', ['--force' => true]);
echo "Migraciones completadas.\n";

echo "\nEjecutando seeders...\n";
$kernel->call('db:seed', ['--force' => true]);
echo "Seeders completados.\n";
echo "</pre>";

// IMPORTANTE: Eliminar este archivo después de usar
```

Acceder a: `https://tudominio.com/setup.php`

**ELIMINAR `setup.php` INMEDIATAMENTE después de usarlo.**

### 3.5 Configurar Dominio y SSL

**Paso 1: Apuntar Dominio**

```
1. hPanel → Dominios
2. Verificar que apunta a public_html
```

**Paso 2: Instalar SSL**

```
1. hPanel → SSL
2. Instalar certificado gratuito (Let's Encrypt)
3. Forzar HTTPS (marcar opción)
```

**Paso 3: Actualizar APP_URL**

```env
APP_URL=https://tudominio.com
```

### 3.6 Configurar .htaccess

El archivo `public/.htaccess` debe contener:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Forzar HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Seguridad adicional
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Proteger archivos sensibles
<FilesMatch "\.(env|log|md)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Compresión GZIP
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/css application/json
    AddOutputFilterByType DEFLATE application/javascript text/xml application/xml
</IfModule>

# Caché de navegador
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### 3.7 Configurar Estructura de Carpetas

En algunos hostings, la estructura debe ser:

```
domains/tudominio.com/
├── public_html/              ← Solo contenido de /public
│   ├── build/
│   ├── images/
│   ├── .htaccess
│   └── index.php             ← Modificado
├── app/                      ← Fuera de public_html
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
└── artisan
```

Si usas esta estructura, modificar `public_html/index.php`:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Ajustar rutas al directorio padre
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

---

## 4. CONFIGURACIÓN DE TAREAS PROGRAMADAS

### 4.1 Tareas del Sistema

El sistema tiene 3 tareas programadas:

| Tarea                          | Frecuencia | Hora        | Descripción                              |
| ------------------------------ | ---------- | ----------- | ---------------------------------------- |
| `documentos:check-expirations` | Diario     | 06:00       | Verificar documentos por vencer/vencidos |
| `alertas:enviar-semanales`     | Semanal    | Lunes 08:00 | Enviar correos de alertas                |
| `registros:purgar`             | Diario     | 02:00       | Eliminar registros > 6 meses             |

### 4.2 Configurar Cron en Servidor Local (Linux/Mac)

```bash
# Abrir crontab
crontab -e

# Agregar línea (ajustar ruta)
* * * * * cd /var/www/Control_Vehicular && php artisan schedule:run >> /dev/null 2>&1
```

### 4.3 Configurar Cron en Hostinger

**Paso 1: Acceder a Cron Jobs**

```
1. hPanel → Avanzado → Trabajos Cron (Cron Jobs)
2. O ir a: SSH y editar crontab
```

**Paso 2: Agregar Tarea**

```
Comando: cd /home/u123456789/domains/tudominio.com/public_html && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
Frecuencia: Cada minuto (* * * * *)
```

O crear tareas individuales si el plan no permite cada minuto:

```
# Verificar vencimientos - Diario 6:00 AM
0 6 * * * cd /home/u123456789/domains/tudominio.com/public_html && /usr/bin/php artisan documentos:check-expirations

# Alertas semanales - Lunes 8:00 AM
0 8 * * 1 cd /home/u123456789/domains/tudominio.com/public_html && /usr/bin/php artisan alertas:enviar-semanales

# Purgar registros - Diario 2:00 AM
0 2 * * * cd /home/u123456789/domains/tudominio.com/public_html && /usr/bin/php artisan registros:purgar
```

### 4.4 Verificar Tareas Programadas

```bash
# Ver tareas configuradas
php artisan schedule:list

# Ejecutar manualmente para probar
php artisan schedule:run

# Ejecutar comando específico
php artisan documentos:check-expirations
```

---

## 5. SOLUCIÓN DE PROBLEMAS

### 5.1 Errores Comunes

#### Error: "500 Internal Server Error"

**Causas y Soluciones:**

1. **Falta clave de aplicación**

    ```bash
    php artisan key:generate
    ```

2. **Permisos incorrectos**

    ```bash
    chmod -R 775 storage bootstrap/cache
    chown -R www-data:www-data storage bootstrap/cache  # Linux
    ```

3. **Archivo .env no existe**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Ver logs para más detalles**
    ```bash
    tail -f storage/logs/laravel.log
    ```

---

#### Error: "SQLSTATE[HY000] Connection refused"

**Soluciones:**

1. Verificar que MySQL está ejecutándose
2. Verificar credenciales en `.env`
3. En Hostinger, usar `localhost` como DB_HOST

```bash
# Probar conexión
php artisan tinker
>>> DB::connection()->getPdo();
```

---

#### Error: "The Mix manifest does not exist"

**Causa:** Assets no compilados

**Solución:**

```bash
npm run build
```

---

#### Error: "Class not found"

**Solución:**

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

---

#### Error: "Permission denied" en storage

**Solución Linux:**

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**Solución Hostinger:**

```bash
chmod -R 755 storage bootstrap/cache
```

---

### 5.2 Comandos de Mantenimiento

```bash
# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Regenerar autoload
composer dump-autoload -o

# Verificar estado de migraciones
php artisan migrate:status

# Rollback última migración (CUIDADO)
php artisan migrate:rollback

# Refrescar base de datos (BORRA TODO)
php artisan migrate:fresh --seed
```

### 5.3 Verificación de Instalación

Ejecutar estos comandos para verificar:

```bash
# Verificar versión de Laravel
php artisan --version

# Verificar conexión a BD
php artisan tinker
>>> DB::connection()->getDatabaseName();

# Verificar rutas
php artisan route:list

# Verificar tareas programadas
php artisan schedule:list

# Verificar estado de la aplicación
php artisan about
```

### 5.4 Backup de Base de Datos

**Exportar:**

```bash
# Local
mysqldump -u root -p control_vehicular > backup_$(date +%Y%m%d).sql

# Hostinger (vía SSH)
mysqldump -u u123456789_admin -p u123456789_controlveh > backup.sql
```

**Importar:**

```bash
mysql -u root -p control_vehicular < backup.sql
```

---

## ANEXO A: ESTRUCTURA DEL PROYECTO

```
Control_Vehicular/
├── app/
│   ├── Console/Commands/        # Comandos Artisan personalizados
│   ├── Http/
│   │   ├── Controllers/         # Controladores
│   │   └── Middleware/          # Middleware (CheckRole, NoCacheHeaders)
│   └── Models/                  # Modelos Eloquent
├── bootstrap/
├── config/                      # Archivos de configuración
├── database/
│   ├── migrations/              # Migraciones de BD
│   └── seeders/                 # Datos de prueba
├── public/
│   ├── build/                   # Assets compilados (Vite)
│   ├── images/                  # Imágenes estáticas
│   └── index.php                # Punto de entrada
├── resources/
│   ├── css/                     # Estilos (Tailwind)
│   ├── js/                      # JavaScript (Alpine.js)
│   └── views/                   # Vistas Blade
├── routes/
│   ├── web.php                  # Rutas web
│   └── console.php              # Tareas programadas
├── storage/
│   ├── app/                     # Archivos de la aplicación
│   ├── framework/               # Caché, sesiones
│   └── logs/                    # Logs de Laravel
├── .env                         # Variables de entorno
├── composer.json                # Dependencias PHP
├── package.json                 # Dependencias Node.js
└── vite.config.js               # Configuración Vite
```

---

## ANEXO B: MIGRACIONES

Orden de ejecución de migraciones:

| #   | Archivo                             | Tabla                          |
| --- | ----------------------------------- | ------------------------------ |
| 1   | create_users_table                  | users                          |
| 2   | create_cache_table                  | cache, cache_locks             |
| 3   | create_jobs_table                   | jobs, job_batches, failed_jobs |
| 4   | create_propietarios_table           | propietarios                   |
| 5   | create_conductores_table            | conductores                    |
| 6   | create_vehiculos_table              | vehiculos                      |
| 7   | create_documentos_vehiculo_table    | documentos_vehiculo            |
| 8   | create_documentos_conductor_table   | documentos_conductor           |
| 9   | create_alertas_table                | alertas                        |
| 10  | create_personal_access_tokens_table | personal_access_tokens         |
| 11  | add_version_and_softdeletes         | Modifica tablas de documentos  |
| 12  | add_fecha_matricula_to_vehiculos    | Agrega fecha_matricula         |
| 13  | add_categoria_licencia              | Agrega categorías de licencia  |

---

## ANEXO C: CHECKLIST DE DESPLIEGUE

### Pre-despliegue

- [ ] Compilar assets: `npm run build`
- [ ] Ejecutar tests: `php artisan test`
- [ ] Verificar .env de producción
- [ ] Backup de base de datos actual

### Despliegue

- [ ] Subir archivos al servidor
- [ ] Configurar .env en servidor
- [ ] Instalar dependencias: `composer install --no-dev`
- [ ] Ejecutar migraciones: `php artisan migrate --force`
- [ ] Limpiar cachés: `php artisan optimize:clear`
- [ ] Optimizar: `php artisan optimize`

### Post-despliegue

- [ ] Verificar acceso al sistema
- [ ] Probar login con diferentes roles
- [ ] Verificar generación de PDFs
- [ ] Verificar exportación Excel
- [ ] Configurar tareas cron
- [ ] Cambiar contraseñas por defecto
- [ ] Verificar SSL activo

---

_Documento generado con base en el análisis del proyecto Laravel Control Vehicular._
