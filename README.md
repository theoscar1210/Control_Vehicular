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

## Requisitos del Sistema

- PHP >= 8.2
- Composer
- Docker y Docker Compose (si usa Sail)
- MySQL 8.0 o MariaDB 10.x
- Node.js >= 18 (para assets)

## Instalacion

### Con Laravel Sail (Docker)

```bash
# Clonar el repositorio
git clone <url-repositorio>
cd Control_Vehicular

# Instalar dependencias
composer install

# Copiar archivo de entorno
cp .env.example .env

# Generar clave de aplicacion
./vendor/bin/sail artisan key:generate

# Levantar contenedores
./vendor/bin/sail up -d

# Ejecutar migraciones
./vendor/bin/sail artisan migrate

# (Opcional) Ejecutar seeders
./vendor/bin/sail artisan db:seed
```

### Instalacion Local

```bash
# Clonar el repositorio
git clone <url-repositorio>
cd Control_Vehicular

# Instalar dependencias
composer install

# Copiar archivo de entorno
cp .env.example .env

# Configurar base de datos en .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=control_vehicular
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_password

# Generar clave de aplicacion
php artisan key:generate

# Ejecutar migraciones
php artisan migrate

# (Opcional) Ejecutar seeders
php artisan db:seed

# Iniciar servidor de desarrollo
php artisan serve
```

## Configuracion

### Variables de Entorno Importantes

```env
APP_NAME="Control Vehicular"
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_CO

# Configuracion de correo para alertas (opcional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.ejemplo.com
MAIL_PORT=587
MAIL_USERNAME=correo@ejemplo.com
MAIL_PASSWORD=password
```

## Estructura del Proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AlertaController.php      # Gestion de alertas
│   │   ├── ConductorController.php   # Gestion de conductores
│   │   ├── DashboardController.php   # Dashboard principal
│   │   ├── PorteriaController.php    # Modulo porteria
│   │   ├── ReporteController.php     # Centro de reportes
│   │   └── VehiculoController.php    # Gestion de vehiculos
│   └── Middleware/
│       └── CheckRole.php             # Control de acceso por rol
├── Models/
│   ├── Alerta.php
│   ├── Conductor.php
│   ├── DocumentoConductor.php
│   ├── DocumentoVehiculo.php
│   ├── Propietario.php
│   ├── User.php
│   └── Vehiculo.php
└── Exports/                          # Exportaciones Excel
    └── ...

resources/views/
├── alertas/                          # Vistas de alertas
├── auth/                             # Vistas de autenticacion
├── conductores/                      # Vistas de conductores
├── dashboard.blade.php               # Dashboard principal
├── layouts/                          # Layouts principales
├── porteria/                         # Modulo porteria
├── reportes/                         # Centro de reportes
│   ├── alertas.blade.php
│   ├── centro.blade.php
│   ├── ficha.blade.php
│   ├── historico.blade.php
│   ├── propietarios.blade.php
│   └── vehiculos.blade.php
└── vehiculos/                        # Vistas de vehiculos
```

## Uso del Sistema

### Acceso Inicial

1. Acceder a la URL del sistema
2. Iniciar sesion con credenciales de administrador
3. Navegar por el menu lateral segun el rol asignado

### Flujo de Trabajo Tipico

1. **Registrar Propietario**: Agregar datos del propietario del vehiculo
2. **Registrar Vehiculo**: Crear registro con placa, marca, modelo
3. **Cargar Documentos**: Subir SOAT, Tecnomecanica, etc. con fechas de vencimiento
4. **Asignar Conductor**: Vincular conductor al vehiculo
5. **Monitorear Alertas**: Revisar alertas de vencimiento en el dashboard
6. **Generar Reportes**: Exportar informacion en PDF/Excel segun necesidad

## Tipos de Documentos

### Documentos de Vehiculo
- SOAT (Seguro Obligatorio)
- Tecnomecanica (Revision Tecnico-Mecanica)
- Tarjeta de Propiedad

### Documentos de Conductor
- Licencia de Conduccion

## Estados de Documentos

| Estado | Descripcion | Color |
|--------|-------------|-------|
| VIGENTE | Mas de 30 dias para vencer | Verde |
| POR_VENCER | Menos de 30 dias para vencer | Amarillo |
| VENCIDO | Fecha de vencimiento superada | Rojo |

## Soporte

Para reportar problemas o solicitar funcionalidades, contactar al equipo de desarrollo.

## Licencia

Todos los derechos reservados - Club Campestre Altos del Chicala 2025
