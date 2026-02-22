# MANUAL DE USUARIO PASO A PASO
## Sistema de Control Vehicular
### Club Campestre Altos del Chicalá

---

# GUÍA PRÁCTICA PARA PERSONAL ADMINISTRATIVO

Este manual le enseñará a usar el sistema de manera sencilla, con instrucciones paso a paso para cada tarea.

---

## TABLA DE CONTENIDO

1. [Cómo iniciar sesión](#1-cómo-iniciar-sesión)
2. [Conocer el Panel Principal](#2-conocer-el-panel-principal)
3. [Registrar un vehículo nuevo (con propietario y documentos)](#3-registrar-un-vehículo-nuevo)
4. [Registrar un conductor nuevo](#4-registrar-un-conductor-nuevo)
5. [Gestionar documentos de un vehículo](#5-gestionar-documentos-de-un-vehículo)
6. [Renovar documentos vencidos](#6-renovar-documentos-vencidos)
7. [Entender y gestionar alertas](#7-entender-y-gestionar-alertas)
8. [Buscar vehículos y conductores](#8-buscar-vehículos-y-conductores)
9. [Asignar un conductor a un vehículo](#9-asignar-un-conductor-a-un-vehículo)
10. [Sistema de Auditoría](#10-sistema-de-auditoría)

---

## 1. CÓMO INICIAR SESIÓN

### Paso 1: Abrir el sistema
Abra su navegador (Chrome, Firefox o Edge) e ingrese la dirección del sistema que le proporcionó el administrador.

### Paso 2: Ingresar credenciales
Verá la pantalla de inicio con el logo del Club y el título "Control Vehicular".

```
┌─────────────────────────────────────────┐
│           [Logo del Club]               │
│         Control Vehicular               │
│            Bienvenido                   │
│                                         │
│  ┌─────────────────────────────────┐   │
│  │ 👤  Ingrese su Usuario          │   │
│  └─────────────────────────────────┘   │
│                                         │
│  ┌─────────────────────────────────┐   │
│  │ 🔒  ********                     │   │
│  └─────────────────────────────────┘   │
│                                         │
│       [ Iniciar Sesión ]                │
└─────────────────────────────────────────┘
```

**Complete los campos:**
1. **Usuario:** Escriba su nombre de usuario (ejemplo: `jperez`)
2. **Contraseña:** Escriba su contraseña

### Paso 3: Hacer clic en "Iniciar Sesión"
Si los datos son correctos, entrará al Panel Principal.

> **Si ve un error:** Verifique que escribió correctamente su usuario y contraseña. Si el problema persiste, contacte al administrador.

### Paso 4: Cerrar sesión al terminar
Cuando termine de trabajar:
1. Busque su nombre en la esquina superior derecha
2. Haga clic sobre él
3. Seleccione **"Cerrar Sesión"**

---

## 2. CONOCER EL PANEL PRINCIPAL

Al iniciar sesión, verá el Panel Principal (Dashboard) con la siguiente información:

```
┌────────────────────────────────────────────────────────────────┐
│  Bienvenido a la página principal                              │
│  Resumen del estado del cumplimiento documental                │
│  Rol: ADMIN                                                    │
├────────────────────────────────────────────────────────────────┤
│                                                                │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐       │
│  │    25    │  │    12    │  │    5     │  │    3     │       │
│  │ Vehículos│  │Conductores│  │Por Vencer│  │ Vencidos │       │
│  │ Activos  │  │ Activos  │  │(20 días) │  │          │       │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘       │
│                                                                │
├────────────────────────────────────────────────────────────────┤
│  🔔 Alertas                    [Marcar todas como leídas]      │
│  ─────────────────────────────────────────────────────────────│
│  │🟡│ SOAT - 🚗 ABC123 - Juan Pérez                           │
│  │   │ El SOAT del vehículo ABC123 vence en 15 días           │
│  │   │                              [Ver] [Marcar leída]      │
│  ─────────────────────────────────────────────────────────────│
│  │🔴│ Licencia - 👤 María García                              │
│  │   │ La licencia del conductor está vencida                 │
│  │   │                              [Ver] [Marcar leída]      │
└────────────────────────────────────────────────────────────────┘
```

### ¿Qué significan los números?

| Tarjeta | Significado |
|---------|-------------|
| **Vehículos Activos** | Total de vehículos registrados en el sistema |
| **Conductores Activos** | Total de conductores que están trabajando |
| **Por Vencer** | Documentos que vencen en los próximos 20 días |
| **Vencidos** | Documentos que ya pasaron su fecha de vencimiento |

### ¿Qué son las alertas?

Las alertas son avisos automáticos que el sistema genera cuando un documento está por vencer o ya venció. Cada alerta muestra:
- **Tipo de documento** (SOAT, Licencia, Tecnomecánica)
- **Placa del vehículo** o nombre del conductor
- **Descripción** del problema
- **Botones** para ver detalles o marcar como leída

---

## 3. REGISTRAR UN VEHÍCULO NUEVO

El registro de un vehículo se hace en **4 pasos secuenciales**. El sistema le guiará con una barra de progreso.

### PASO 1: Ir a la pantalla de registro

1. En el menú lateral, haga clic en **"Vehículos"**
2. Haga clic en el botón verde **"+ Nuevo Vehículo"**

### PASO 2: Buscar o registrar el propietario

Primero debe verificar si el propietario ya existe en el sistema:

```
┌─────────────────────────────────────────────────────────────┐
│  ┌───────────────────────────────────────────────────────┐ │
│  │  👤 1. Registrar Propietario                          │ │
│  ├───────────────────────────────────────────────────────┤ │
│  │                                                       │ │
│  │  Tipo Doc *            Identificación *               │ │
│  │  ┌──────────────┐      ┌──────────────────────────┐   │ │
│  │  │ CC        ▼  │      │ 12345678        [🔍Buscar]│   │ │
│  │  └──────────────┘      └──────────────────────────┘   │ │
│  │                                                       │ │
│  │  💡 Busque primero si el propietario ya existe        │ │
│  │                                                       │ │
│  └───────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

**Paso 2a: Buscar propietario existente**

1. Digite el número de identificación del propietario
2. Haga clic en el botón **"Buscar"**

**Si el propietario YA existe**, verá sus datos:

```
┌─────────────────────────────────────────────────────────────┐
│  ✅ Propietario encontrado:                                  │
│                                                             │
│  Tipo Documento: CC                                         │
│  Identificación: 12345678                                   │
│  Nombre: Juan                                               │
│  Apellido: Pérez                                            │
│  Vehículos registrados: 2                                   │
│                                                             │
│      [Cancelar]    [→ Continuar con este propietario]       │
└─────────────────────────────────────────────────────────────┘
```

Haga clic en **"Continuar con este propietario"** para proceder al registro del vehículo.

**Si el propietario NO existe**, complete el formulario:

```
┌─────────────────────────────────────────────────────────────┐
│  ⚠️ No se encontró propietario con identificación 12345678   │
│     Complete los datos para crear uno nuevo.                │
│                                                             │
│  Nombre *              Apellido *                           │
│  ┌──────────────┐      ┌──────────────┐                     │
│  │ Juan         │      │ Pérez        │                     │
│  └──────────────┘      └──────────────┘                     │
│                                                             │
│            [ 👤 Crear Propietario ]                         │
└─────────────────────────────────────────────────────────────┘
```

**Complete los campos:**
1. **Nombre:** Nombre del propietario (ejemplo: Juan)
2. **Apellido:** Apellido del propietario (ejemplo: Pérez)
3. **Tipo Documento:** Seleccione CC (Cédula) o NIT (para empresas)
4. **Identificación:** Número del documento (ya está pre-llenado)

Haga clic en **"Crear Propietario"**

> **Mensaje de éxito:** "Propietario creado correctamente. Ahora puede registrar el vehículo."

### PASO 3: Registrar el vehículo

Después de crear el propietario, se habilita el formulario del vehículo:

```
┌───────────────────────────────────────────────────────────┐
│  🚗 2. Registrar Vehículo                                 │
├───────────────────────────────────────────────────────────┤
│                                                           │
│  Placa *           Tipo *              Marca *            │
│  ┌──────────┐      ┌──────────────┐    ┌──────────────┐   │
│  │ ABC123   │      │ Carro     ▼  │    │ Toyota       │   │
│  └──────────┘      └──────────────┘    └──────────────┘   │
│                                                           │
│  Modelo (Año) *    Color *                                │
│  ┌──────────┐      ┌──────────────┐                       │
│  │ 2022     │      │ Blanco       │                       │
│  └──────────┘      └──────────────┘                       │
│                                                           │
│  Propietario actual: Juan Pérez                           │
│                                                           │
│            [ 🚗 Crear Vehículo ]                          │
└───────────────────────────────────────────────────────────┘
```

**Complete los campos:**
1. **Placa:** Placa del vehículo (se convierte a mayúsculas automáticamente)
2. **Tipo:** Seleccione "Carro" o "Moto"
3. **Marca:** Fabricante (Toyota, Chevrolet, Yamaha, etc.)
4. **Modelo:** Año del vehículo (ejemplo: 2022)
5. **Color:** Color del vehículo

Haga clic en **"Crear Vehículo"**

### PASO 4: Registrar la Licencia de Tránsito (Tarjeta de Propiedad)

```
┌───────────────────────────────────────────────────────────┐
│  📄 2. Licencia de Tránsito                               │
├───────────────────────────────────────────────────────────┤
│                                                           │
│  Número Licencia *         Entidad Emisora                │
│  ┌──────────────────┐      ┌──────────────────────────┐   │
│  │ 123456789        │      │ Secretaría de Tránsito   │   │
│  └──────────────────┘      └──────────────────────────┘   │
│                                                           │
│  Fecha de Expedición *     Fecha de Matrícula * ⓘ         │
│  ┌──────────────────┐      ┌──────────────────────────┐   │
│  │ 15/03/2022       │      │ 10/03/2022               │   │
│  └──────────────────┘      └──────────────────────────┘   │
│                                                           │
│  💡 Esta fecha determina cuándo vence la primera          │
│     Tecnomecánica (Carros: 5 años, Motos: 2 años)         │
│                                                           │
│            [ 💾 Guardar Licencia ]                        │
└───────────────────────────────────────────────────────────┘
```

**⚠️ IMPORTANTE:** La **Fecha de Matrícula** es muy importante porque:
- Para **Carros:** La primera Tecnomecánica se requiere 5 años después
- Para **Motos:** La primera Tecnomecánica se requiere 2 años después

### PASO 5: Registrar el SOAT

```
┌───────────────────────────────────────────────────────────┐
│  🛡️ 3. Documento SOAT                                     │
├───────────────────────────────────────────────────────────┤
│                                                           │
│  Número *                  Entidad Emisora                │
│  ┌──────────────────┐      ┌──────────────────────────┐   │
│  │ SOAT-2024-12345  │      │ Seguros del Estado       │   │
│  └──────────────────┘      └──────────────────────────┘   │
│                                                           │
│  Fecha Emisión *           Fecha Vencimiento ✨            │
│  ┌──────────────────┐      ┌──────────────────────────┐   │
│  │ 01/01/2024       │      │ 01/01/2025 (automático)  │   │
│  └──────────────────┘      └──────────────────────────┘   │
│                                                           │
│  ✨ Se calcula automáticamente (+1 año)                   │
│                                                           │
│            [ 💾 Guardar SOAT ]                            │
└───────────────────────────────────────────────────────────┘
```

**Complete los campos:**
1. **Número:** Número de la póliza SOAT
2. **Entidad Emisora:** Aseguradora (Seguros del Estado, Liberty, etc.)
3. **Fecha Emisión:** Fecha en que se compró el SOAT

> **Nota:** La fecha de vencimiento se calcula automáticamente (1 año después de la emisión)

### PASO 6: Registrar la Tecnomecánica (si aplica)

**Si el vehículo es nuevo** (menos de 5 años para carros o 2 años para motos), verá este mensaje:

```
┌───────────────────────────────────────────────────────────┐
│  🔧 4. Documento Tecnomecánica                            │
├───────────────────────────────────────────────────────────┤
│                                                           │
│  ┌─────────────────────────────────────────────────────┐ │
│  │  ✅ EXENTO - Vehículo "Nuevo" (Exención por tiempo) │ │
│  │                                                     │ │
│  │  Este vehículo no requiere Tecnomecánica hasta el   │ │
│  │  10/03/2027 (5 años desde la matrícula)             │ │
│  │                                                     │ │
│  │  ⏰ Días restantes: 1,095 días                      │ │
│  └─────────────────────────────────────────────────────┘ │
└───────────────────────────────────────────────────────────┘
```

**Si el vehículo YA requiere Tecnomecánica**, complete el formulario similar al SOAT.

### ¡Registro completo!

```
┌─────────────────────────────────────────────────────────────┐
│  Progreso del registro                                      │
│  ██████████████████████████████████████████████████ 100%   │
│  ✓ Propietario creado | ✓ Vehículo creado | ✓ Documentos   │
└─────────────────────────────────────────────────────────────┘
```

---

## 4. REGISTRAR UN CONDUCTOR NUEVO

### Paso 1: Ir a la pantalla de conductores

1. En el menú lateral, haga clic en **"Conductores"**
2. Haga clic en el botón verde **"+ Nuevo Conductor"**

### Paso 2: Completar información personal

```
┌─────────────────────────────────────────────────────────────────┐
│  👤 Información del Conductor                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  DATOS PERSONALES                                               │
│  ─────────────────                                              │
│  Nombre                    Apellido                             │
│  ┌──────────────────┐      ┌──────────────────┐                 │
│  │ María            │      │ García           │                 │
│  └──────────────────┘      └──────────────────┘                 │
│                                                                 │
│  Tipo de Documento         Identificación                       │
│  ┌──────────────────┐      ┌──────────────────┐                 │
│  │ CC            ▼  │      │ 87654321         │                 │
│  └──────────────────┘      └──────────────────┘                 │
│                                                                 │
│  Teléfono                  Teléfono Emergencia                  │
│  ┌──────────────────┐      ┌──────────────────┐                 │
│  │ 3001234567       │      │ 3009876543       │                 │
│  └──────────────────┘      └──────────────────┘                 │
│                                                                 │
│  ☑️ Activo                                                      │
│                                                                 │
│  Asignar a Vehículo                                             │
│  ┌────────────────────────────────────────────────────────┐    │
│  │ 🔍 Buscar vehículo por placa, marca o propietario...   │    │
│  └────────────────────────────────────────────────────────┘    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

**Complete los campos:**
1. **Nombre:** Nombre del conductor
2. **Apellido:** Apellido del conductor
3. **Tipo de Documento:** CC (Cédula) o CE (Cédula de Extranjería)
4. **Identificación:** Número del documento
5. **Clasificación:** Seleccione **EMPLEADO** (personal de planta del club) o **EXTERNO** (contratistas, proveedores, visitantes frecuentes)
6. **Teléfono:** Número de contacto principal
7. **Teléfono Emergencia:** Número de un familiar o contacto de emergencia
8. **Activo:** Deje marcado si el conductor está trabajando actualmente
9. **Asignar a Vehículo:** Opcional - puede buscar y seleccionar un vehículo

> **¿Por qué es importante la Clasificación?** Solo los conductores **EMPLEADO** permiten adjuntar el archivo digital de la licencia al refrendar una categoría, ya que son personal bajo responsabilidad directa del Club.

### Paso 3: Registrar la licencia de conducción

```
┌─────────────────────────────────────────────────────────────────┐
│  📄 Documento Licencia de Conducción                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Tipo de Documento                                              │
│  ┌────────────────────────────────────────────────────────┐    │
│  │ Licencia Conducción                                 ▼  │    │
│  └────────────────────────────────────────────────────────┘    │
│                                                                 │
│  Categoría Principal                                            │
│  ┌────────────────────────────────────────────────────────┐    │
│  │ B1 - Automóviles, Camperos, Camionetas             ▼  │    │
│  └────────────────────────────────────────────────────────┘    │
│                                                                 │
│  Categorías Adicionales (opcional)                              │
│  ☐ A1    ☐ A2    ☑️ B1    ☐ B2    ☐ B3                         │
│  ☐ C1    ☐ C2    ☐ C3                                          │
│                                                                 │
│  Número Documento           F. Emisión                          │
│  ┌──────────────────┐       ┌──────────────────┐                │
│  │ 87654321         │       │ 15/06/2020       │                │
│  └──────────────────┘       └──────────────────┘                │
│                                                                 │
│  F. Vencimiento             Entidad Emisora                     │
│  ┌──────────────────┐       ┌──────────────────┐                │
│  │ 15/06/2030       │       │ Min. Transporte  │                │
│  └──────────────────┘       └──────────────────┘                │
│                                                                 │
│  ⓘ Ingrese la fecha de vencimiento según su licencia           │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Categorías de licencia explicadas

| Categoría | ¿Qué puede conducir? |
|-----------|---------------------|
| **A1** | Motos hasta 125cc |
| **A2** | Motos de más de 125cc |
| **B1** | Carros, camionetas, camperos (hasta 10 pasajeros) |
| **B2** | Camiones, buses |
| **B3** | Tractomulas, vehículos articulados |
| **C1** | Taxis |
| **C2** | Buses de servicio público |
| **C3** | Camiones de carga pública |

### Paso 4: Guardar el conductor

Haga clic en **"Crear Conductor"**

> **Mensaje de éxito:** "Conductor creado exitosamente"

---

## 5. GESTIONAR DOCUMENTOS DE UN VEHÍCULO

### Cómo ver los documentos de un vehículo

1. Vaya a **"Vehículos"** en el menú
2. Busque el vehículo en la lista
3. Haga clic en el ícono de **documento** 📄 (columna "Acciones")

### Pantalla de documentos

```
┌─────────────────────────────────────────────────────────────────┐
│  📄 Documentos del Vehículo                                     │
│  ABC123 — Toyota Corolla                                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐            │
│  │    2    │  │    1    │  │    0    │  │    3    │            │
│  │ Vigentes│  │Por Vencer│  │ Vencidos│  │  Total  │            │
│  │   🟢    │  │   🟡    │  │   🔴    │  │         │            │
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘            │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│  📄 Documentos Activos                                          │
│                                                                 │
│  ┌────────────────────┐  ┌────────────────────┐                 │
│  │ 🛡️ SOAT            │  │ 🔧 Tecnomecánica   │                 │
│  │ ────────────────── │  │ ────────────────── │                 │
│  │ Número: SOAT-12345 │  │ Número: TM-67890   │                 │
│  │ Emisión: 01/01/24  │  │ Emisión: 15/06/24  │                 │
│  │ Vence: 01/01/25    │  │ Vence: 15/06/25    │                 │
│  │ Días: 180 🟢       │  │ Días: 10 🟡        │                 │
│  │                    │  │                    │                 │
│  │ [Renovar]          │  │ [Renovar]          │                 │
│  └────────────────────┘  └────────────────────┘                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Significado de los colores

| Color | Estado | Días restantes | Acción |
|-------|--------|----------------|--------|
| 🟢 Verde | Vigente | Más de 20 días | Ninguna |
| 🟡 Amarillo | Por vencer | 6 a 20 días | Planificar renovación |
| 🔴 Rojo | Crítico/Vencido | 0-5 días o vencido | ¡Renovar ya! |

---

## 6. RENOVAR DOCUMENTOS VENCIDOS

Cuando un documento está por vencer o ya venció, aparece el botón **"Renovar"**.

### Paso 1: Identificar el documento a renovar

En la lista de vehículos o en la pantalla de documentos, los documentos que necesitan atención se muestran con un indicador de color:

```
┌────────────────────────────────────────┐
│ 🛡️ SOAT                    🔴 VENCIDO  │
│ ────────────────────────────────────── │
│ Número: SOAT-2023-12345               │
│ Venció: 01/01/2024                    │
│ Hace 15 días                          │
│                                       │
│        [ 🔄 Renovar SOAT ]            │
└────────────────────────────────────────┘
```

### Paso 2: Hacer clic en "Renovar"

Se abrirá una ventana emergente (modal) con el formulario de renovación:

```
┌─────────────────────────────────────────────────────────────┐
│  🔄 Renovar SOAT                                     [X]    │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ℹ️ ABC123 - Toyota Corolla                                 │
│                                                             │
│  Número de Póliza *                                         │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ SOAT-2024-67890                                     │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Entidad Emisora                                            │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Seguros del Estado                                  │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Fecha de Compra *                                          │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ 20/01/2024                                          │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  📅 La fecha de vencimiento se calcula automáticamente     │
│     (+1 año desde la fecha de compra)                      │
│                                                             │
│  Nota (Opcional)                                            │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Renovación realizada en línea                       │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│            [ Cancelar ]  [ 💾 Guardar Renovación ]          │
└─────────────────────────────────────────────────────────────┘
```

### Paso 3: Completar los datos del nuevo documento

1. **Número de Póliza:** Ingrese el número del nuevo SOAT
2. **Entidad Emisora:** Nombre de la aseguradora
3. **Fecha de Compra:** Fecha en que adquirió el nuevo documento
4. **Adjuntar documento:** Opcional — los usuarios ADMIN y SST pueden adjuntar el archivo digital (PDF, imagen, Word o Excel, máx. 10MB). El archivo se guarda automáticamente en Google Drive.
5. **Nota:** Opcional, para agregar comentarios

### Paso 4: Guardar la renovación

Haga clic en **"Guardar Renovación"**

> **¿Qué pasa con el documento anterior?**
> El sistema automáticamente:
> - Marca el documento anterior como "Reemplazado"
> - Crea el nuevo documento como "Activo"
> - Guarda todo el historial de versiones

---

## 7. ENTENDER Y GESTIONAR ALERTAS

### ¿De dónde vienen las alertas?

El sistema revisa **automáticamente todos los días a las 3:00 AM** el estado de los documentos y genera alertas cuando:
- Un documento **vencerá en los próximos 20 días**
- Un documento **ya está vencido**

### Tipos de alertas

```
┌─────────────────────────────────────────────────────────────────┐
│  🔔 Centro de Alertas                [Marcar todas como leídas] │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  │🟡│ Alerta de documento por vencer                           │
│  │   │ ──────────────────────────────────────────────────────  │
│  │   │ 🛡️ SOAT                                    Nueva        │
│  │   │ 🚗 ABC123  👤 Juan Pérez                                │
│  │   │ El SOAT del vehículo ABC123 vence en 10 días            │
│  │   │ 📅 15/01/2024                                           │
│  │   │                                                         │
│  │   │ 📄 Doc. Vehículo  ⚠️ Próximo a vencer                   │
│  │   │                               [✓ Marcar leída]          │
│  │   │                                                         │
│  ─────────────────────────────────────────────────────────────│
│  │🔴│ Alerta de documento vencido                              │
│  │   │ ──────────────────────────────────────────────────────  │
│  │   │ 📄 Licencia Conducción                                  │
│  │   │ 👤 María García                                         │
│  │   │ La licencia de conducción está vencida hace 5 días      │
│  │   │ 📅 10/01/2024                                           │
│  │   │                                                         │
│  │   │ 📄 Doc. Conductor  🔴 Vencido                           │
│  │   │                               ✓ Leída                   │
│  │   │                                                         │
└─────────────────────────────────────────────────────────────────┘
```

### Cómo gestionar las alertas

**Marcar una alerta como leída:**
1. Haga clic en **"Marcar leída"** en la alerta específica

**Marcar todas como leídas:**
1. Haga clic en el botón **"Marcar todas como leídas"** en la parte superior

### Correos automáticos

El sistema envía un **resumen semanal por correo** todos los **lunes a las 4:00 AM** a los usuarios SST con:
- Lista de documentos vencidos
- Lista de documentos por vencer
- Enlace directo al sistema

---

## 8. BUSCAR VEHÍCULOS Y CONDUCTORES

### Buscar un vehículo

1. Vaya a **"Vehículos"** en el menú
2. En el campo de búsqueda, escriba:
   - La **placa** del vehículo (ejemplo: ABC123)
   - La **marca** (ejemplo: Toyota)
   - El **modelo** (ejemplo: Corolla)
   - El nombre del **propietario** (ejemplo: Juan)
3. Haga clic en **"Buscar"**

```
┌─────────────────────────────────────────────────────────────────┐
│  🔍 Buscar por placa, marca, modelo o propietario...            │
│  ┌─────────────────────────────────────────────────────┐ [🔍]  │
│  │ Toyota                                              │        │
│  └─────────────────────────────────────────────────────┘        │
├─────────────────────────────────────────────────────────────────┤
│  Resultados: 3 vehículo(s)                                      │
│                                                                 │
│  │ ABC123 │ Toyota Corolla  │ Juan Pérez │ 🟢 │ 🟢 │ Activo │  │
│  │ XYZ789 │ Toyota Hilux    │ María Gómez│ 🟡 │ 🟢 │ Activo │  │
│  │ DEF456 │ Toyota Yaris    │ Pedro López│ 🔴 │ 🟡 │ Activo │  │
└─────────────────────────────────────────────────────────────────┘
```

### Buscar un conductor

1. Vaya a **"Conductores"** en el menú
2. En el campo de búsqueda, escriba:
   - El **nombre** del conductor
   - El **apellido**
   - El número de **identificación**
3. Haga clic en **"Buscar"**

---

## 9. ASIGNAR UN CONDUCTOR A UN VEHÍCULO

### Opción 1: Desde el registro del conductor

Al crear un conductor nuevo, puede asignarlo directamente a un vehículo usando el campo "Asignar a Vehículo".

### Opción 2: Desde la edición del vehículo

1. Vaya a **"Vehículos"**
2. Busque el vehículo
3. Haga clic en el ícono de **lápiz** ✏️ (Editar)
4. En el campo **"Conductor"**, seleccione el conductor de la lista
5. Haga clic en **"Guardar Cambios"**

```
┌─────────────────────────────────────────────────────────────────┐
│  ✏️ Editar Vehículo - ABC123                                    │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Conductor Asignado                                             │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ 🔍 Buscar conductor...                               ▼  │   │
│  ├─────────────────────────────────────────────────────────┤   │
│  │ 👤 María García - CC 87654321                           │   │
│  │ 👤 Juan Pérez - CC 12345678                             │   │
│  │ 👤 Pedro López - CC 11223344                            │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│                      [ 💾 Guardar Cambios ]                     │
└─────────────────────────────────────────────────────────────────┘
```

> **Nota:** Un conductor puede estar asignado a múltiples vehículos si es necesario.

---

## RESUMEN RÁPIDO DE NAVEGACIÓN

| Quiero... | Dónde ir |
|-----------|----------|
| Ver resumen general | Panel Principal (Dashboard) |
| Registrar vehículo nuevo | Vehículos → Nuevo Vehículo |
| Buscar propietario existente | Nuevo Vehículo → Buscar por identificación |
| Registrar conductor nuevo | Conductores → Nuevo Conductor |
| Ver documentos de un vehículo | Vehículos → Ícono 📄 |
| Renovar un documento | Documentos del vehículo → Renovar |
| Ver todas las alertas | Alertas (en el menú) |
| Buscar un vehículo | Vehículos → Campo de búsqueda |
| Editar un vehículo | Vehículos → Ícono ✏️ |
| Eliminar un vehículo | Vehículos → Ícono 🗑️ |

---

## 10. SISTEMA DE AUDITORÍA

El sistema registra automáticamente todos los cambios realizados en los registros principales.

### ¿Qué se registra?

Cada vez que se **crea**, **edita** o **elimina** un registro de:
- Propietarios
- Vehículos
- Conductores
- Documentos de vehículos
- Documentos de conductores

El sistema guarda:
- **Quién** hizo el cambio (usuario)
- **Cuándo** se hizo el cambio (fecha y hora)
- **Qué** cambió (valores anteriores y nuevos)

### Eliminación segura (Soft Delete)

Cuando se elimina un registro:

```
┌─────────────────────────────────────────────────────────────┐
│  ⚠️ ¿Está seguro de eliminar este vehículo?                 │
│                                                             │
│  El vehículo ABC123 será marcado como eliminado.            │
│  Esta acción puede ser revertida por el administrador.      │
│                                                             │
│              [Cancelar]    [Eliminar]                       │
└─────────────────────────────────────────────────────────────┘
```

- El registro **no se borra permanentemente**
- Queda marcado como "eliminado" pero puede recuperarse
- El administrador puede restaurar registros eliminados si es necesario

> **Beneficio:** Si elimina algo por error, puede solicitar al administrador que lo recupere.

---

## CONTACTO Y SOPORTE

Si tiene preguntas o problemas con el sistema:
1. Contacte al administrador del Club
2. Describa el problema con el mayor detalle posible
3. Si es posible, indique qué pantalla estaba usando

---

*Manual elaborado para el Sistema de Control Vehicular v1.1*
*Club Campestre Altos del Chicalá - 2026*
