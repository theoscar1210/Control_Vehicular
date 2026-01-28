# Configuración de Alertas Semanales por Email

## Funcionalidad Implementada

### 1. Alertas Desaparecen al Marcarlas como Leídas

- Las alertas marcadas como leídas (`leida = 1`) ya NO aparecen en el dashboard
- Solo se muestran alertas pendientes (no leídas)
- Al marcar una alerta como leída, desaparece automáticamente de la vista

**Archivos modificados:**

- `app/Http/Controllers/DashboardController.php` (línea 77)

### 2. Envío Semanal de Alertas por Email

- Se envía un resumen de alertas cada **Lunes a las 04:00 AM**
- El sistema usa la hora de la red (timezone configurado en el servidor)
- Los correos se envían SOLO a usuarios con rol **ADMIN** y **SST** que estén activos

**Archivos creados:**

- `app/Console/Commands/EnviarAlertasSemanales.php` - Comando para envío de emails
- `resources/views/emails/alertas-semanales.blade.php` - Plantilla HTML del email
- `app/Console/kernel.php` - Programación del comando (líneas 23-27)

---

## Configuración Requerida

### 1. Configurar Timezone (Hora de la Red)

Editar el archivo `config/app.php` y cambiar la línea 68:

```php
// Para Colombia (hora de Bogotá)
'timezone' => 'America/Bogota',

// O usar la zona horaria de tu región
```

**Zonas horarias comunes:**

- Colombia: `America/Bogota`
- Argentina: `America/Argentina/Buenos_Aires`
- México: `America/Mexico_City`
- España: `Europe/Madrid`

### 2. Configurar Correo Electrónico

Editar el archivo `.env` con la configuración de correo:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-contraseña-de-aplicación
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="Control Vehicular"
```

**Nota:** Para Gmail, debes crear una "Contraseña de Aplicación" desde:
https://myaccount.google.com/apppasswords

### 3. Asegurar que los Usuarios tengan Email

Verificar que los usuarios con rol ADMIN y SST tengan su email configurado en la base de datos:

```sql
UPDATE usuarios
SET email = 'admin@ejemplo.com'
WHERE rol = 'ADMIN' AND activo = 1;

UPDATE usuarios
SET email = 'sst@ejemplo.com'
WHERE rol = 'SST' AND activo = 1;
```

---

## Activar el Scheduler de Laravel

Para que los comandos programados se ejecuten automáticamente, debes configurar el **cron** en tu servidor.

### En Linux/macOS:

1. Abrir el crontab:

```bash
crontab -e
```

2. Agregar esta línea:

```bash
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

### En Windows (usando Programador de Tareas):

1. Abrir "Programador de tareas"
2. Crear nueva tarea básica
3. Configurar para ejecutarse cada minuto
4. Acción: Ejecutar programa
5. Programa: `php`
6. Argumentos: `artisan schedule:run`
7. Directorio inicial: `c:\laravel-projects\Control_Vehicular`

---

## Probar la Funcionalidad

### 1. Probar el Comando Manualmente

Ejecutar desde la terminal:

```bash
cd c:\laravel-projects\Control_Vehicular
php artisan alertas:enviar-semanales
```

Esto enviará los correos inmediatamente (sin esperar al lunes).

### 2. Ver los Comandos Programados

```bash
php artisan schedule:list
```

Deberías ver:

- `check:document-expirations` - Todos los días a las 08:00
- `alertas:enviar-semanales` - Lunes a las 01:00

### 3. Verificar que las Alertas Desaparecen

1. Ir al dashboard
2. Ver las alertas pendientes
3. Hacer clic en "Marcar leída"
4. La alerta debe desaparecer inmediatamente

---

## Contenido del Email

El correo electrónico incluye:

- Saludo personalizado con el nombre del usuario
- Total de alertas pendientes
- Fecha del reporte
- Listado agrupado por tipo de documento:
    - SOAT
    - Tecnomecánica
    - Licencia Conducción
    - Tarjeta Propiedad
- Mensaje de cada alerta
- Fecha de vencimiento
- Tipo de documento (Vehículo o Conductor)
- Botón para ir al sistema
- Diseño profesional con colores corporativos

---

## Destinatarios

Los correos se envían ÚNICAMENTE a:

- Usuarios con rol **ADMIN**
- Usuarios con rol **SST**
- Que estén **activos** (`activo = 1`)
- Que tengan un **email configurado** (no NULL)

---

## Frecuencia de Envío

- **Cuándo:** Todos los **Lunes**
- **Hora:** **01:00 AM** (según timezone configurado)
- **Qué se envía:** Solo alertas **NO LEÍDAS** (`leida = 0`)

Si no hay alertas pendientes, el comando termina sin enviar correos.

---

## Solución de Problemas

### No se envían los correos

1. Verificar configuración de `.env` (MAIL\_\*)
2. Probar manualmente: `php artisan alertas:enviar-semanales`
3. Revisar logs: `storage/logs/laravel.log`
4. Verificar que hay usuarios ADMIN/SST con email

### Los correos se envían pero no llegan

1. Verificar carpeta de SPAM
2. Si usas Gmail, asegúrate de usar "Contraseña de Aplicación"
3. Verificar que MAIL_ENCRYPTION sea `tls` o `ssl`

### El scheduler no se ejecuta

1. Verificar que el cron esté configurado correctamente
2. Ejecutar: `php artisan schedule:run` manualmente
3. En Windows, verificar el Programador de Tareas

---

## Modificar Destinatarios

Si quieres agregar más roles o cambiar los destinatarios, editar:

`app/Console/Commands/EnviarAlertasSemanales.php` (línea 50)

```php
// Ejemplo: Agregar PORTERIA
$destinatarios = Usuario::whereIn('rol', ['ADMIN', 'SST', 'PORTERIA'])
    ->where('activo', 1)
    ->whereNotNull('email')
    ->get();
```

---

## Cambiar Frecuencia de Envío

Editar `app/Console/kernel.php` (línea 25-27):

```php
// Enviar todos los días a las 01:00
$schedule->command('alertas:enviar-semanales')->dailyAt('01:00');

// Enviar los Viernes a las 18:00
$schedule->command('alertas:enviar-semanales')->weeklyOn(5, '18:00');

// Enviar el primer día de cada mes
$schedule->command('alertas:enviar-semanales')->monthlyOn(1, '01:00');
```

**Días de la semana:**

- 0 = Domingo
- 1 = Lunes
- 2 = Martes
- 3 = Miércoles
- 4 = Jueves
- 5 = Viernes
- 6 = Sábado
