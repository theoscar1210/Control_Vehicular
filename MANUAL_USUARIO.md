# MANUAL DE USUARIO
## Sistema de Control Vehicular
### Club Campestre Altos del Chicalá

---

## CONTENIDO

1. [Introducción](#1-introducción)
2. [Perfiles de Usuario](#2-perfiles-de-usuario)
3. [Acceso al Sistema](#3-acceso-al-sistema)
4. [Panel Principal (Dashboard)](#4-panel-principal-dashboard)
5. [Gestión de Vehículos](#5-gestión-de-vehículos)
6. [Gestión de Conductores](#6-gestión-de-conductores)
7. [Gestión de Documentos](#7-gestión-de-documentos)
8. [Módulo de Reportes](#8-módulo-de-reportes)
9. [Módulo de Portería](#9-módulo-de-portería)
10. [Sistema de Alertas](#10-sistema-de-alertas)
11. [Administración de Usuarios](#11-administración-de-usuarios)
12. [Sistema de Auditoría](#12-sistema-de-auditoría)
13. [Preguntas Frecuentes](#13-preguntas-frecuentes)

---

## 1. INTRODUCCIÓN

El **Sistema de Control Vehicular** es una herramienta diseñada para gestionar y monitorear la documentación de los vehículos y conductores del Club Campestre Altos del Chicalá.

### ¿Para qué sirve este sistema?

- Llevar un registro organizado de todos los vehículos y sus documentos
- Controlar que los documentos estén siempre vigentes (SOAT, Tecnomecánica, Licencias)
- Recibir alertas automáticas cuando los documentos estén próximos a vencer
- Verificar rápidamente el estado documental de cualquier vehículo en portería
- Generar reportes para la toma de decisiones

### Códigos de colores del sistema

El sistema utiliza un semáforo visual para indicar el estado de los documentos:

| Color | Significado | Acción requerida |
|-------|-------------|------------------|
| 🟢 **Verde** | Vigente (más de 20 días) | Ninguna |
| 🟡 **Amarillo** | Por vencer (6-20 días) | Planificar renovación |
| 🔴 **Rojo** | Crítico (0-5 días) o Vencido | Renovar inmediatamente |
| ⚪ **Gris** | Sin registro | Registrar documento |

---

## 2. PERFILES DE USUARIO

El sistema cuenta con tres tipos de usuarios, cada uno con diferentes permisos:

### 2.1 Administrador (ADMIN)

**Acceso completo al sistema**

| Puede hacer | No puede hacer |
|-------------|----------------|
| ✅ Gestionar vehículos y conductores | - |
| ✅ Registrar y actualizar documentos | - |
| ✅ Ver y generar todos los reportes | - |
| ✅ Acceder al módulo de portería | - |
| ✅ Crear, editar y eliminar usuarios | - |
| ✅ Ver todas las alertas del sistema | - |

### 2.2 SST (Seguridad y Salud en el Trabajo)

**Gestión documental completa, sin administración de usuarios**

| Puede hacer | No puede hacer |
|-------------|----------------|
| ✅ Gestionar vehículos y conductores | ❌ Crear o eliminar usuarios |
| ✅ Registrar y actualizar documentos | ❌ Acceder al módulo de portería |
| ✅ Ver y generar todos los reportes | - |
| ✅ Ver alertas del sistema | - |

### 2.3 Portería (PORTERIA)

**Solo consulta rápida para control de acceso**

| Puede hacer | No puede hacer |
|-------------|----------------|
| ✅ Buscar vehículos por placa | ❌ Crear o editar vehículos |
| ✅ Ver estado de documentos | ❌ Crear o editar conductores |
| ✅ Ver alertas de documentos | ❌ Modificar documentos |
| - | ❌ Generar reportes |
| - | ❌ Administrar usuarios |

---

## 3. ACCESO AL SISTEMA

### 3.1 Iniciar sesión

1. Abra su navegador web
2. Ingrese la dirección del sistema
3. En la pantalla de inicio de sesión, ingrese:
   - **Usuario:** Su nombre de usuario asignado
   - **Contraseña:** Su contraseña personal
4. Haga clic en **"Iniciar Sesión"**

### 3.2 Cerrar sesión

1. Haga clic en su nombre de usuario (esquina superior derecha)
2. Seleccione **"Cerrar Sesión"**

> **Nota de seguridad:** Siempre cierre sesión al terminar de usar el sistema, especialmente en computadores compartidos.

### 3.3 ¿Olvidó su contraseña?

Contacte al administrador del sistema para restablecer su contraseña.

---

## 4. PANEL PRINCIPAL (DASHBOARD)

*Disponible para: ADMIN y SST*

Al iniciar sesión, verá el panel principal con un resumen del estado general del sistema.

### 4.1 Estadísticas principales

El panel muestra cuatro indicadores clave:

| Indicador | Descripción |
|-----------|-------------|
| **Vehículos Activos** | Total de vehículos registrados en el sistema |
| **Conductores Activos** | Total de conductores con estado activo |
| **Documentos por Vencer** | Documentos que vencen en los próximos 20 días |
| **Documentos Vencidos** | Documentos que ya pasaron su fecha de vencimiento |

### 4.2 Alertas recientes

Debajo de las estadísticas encontrará las **alertas más recientes** que no han sido leídas. Cada alerta indica:

- Tipo de alerta (vehículo o conductor)
- Documento afectado
- Estado (vencido o por vencer)
- Fecha de la alerta

**Para ver más detalles:** Haga clic sobre la alerta para ir directamente al registro correspondiente.

---

## 5. GESTIÓN DE VEHÍCULOS

*Disponible para: ADMIN y SST*

### 5.1 Ver listado de vehículos

1. En el menú principal, seleccione **"Vehículos"**
2. Verá una tabla con todos los vehículos registrados

La tabla muestra:
- **Placa** del vehículo
- **Marca y modelo**
- **Propietario**
- **Estado del SOAT** (semáforo)
- **Estado de Tecnomecánica** (semáforo)
- **Estado general** (Activo/Inactivo)

### 5.2 Buscar un vehículo

1. En la parte superior de la tabla, encontrará un campo de búsqueda
2. Escriba la placa, marca, modelo o nombre del propietario
3. Haga clic en **"Buscar"**

### 5.3 Registrar un nuevo vehículo

El registro de vehículos se realiza en pasos secuenciales, comenzando por el propietario.

#### Paso 1: Buscar o crear propietario

1. Haga clic en el botón **"Nuevo Vehículo"**
2. En la sección "Registrar Propietario":
   - Digite el número de identificación del propietario
   - Haga clic en **"Buscar"**

**Si el propietario ya existe:**
- El sistema mostrará los datos del propietario encontrado
- Haga clic en **"Continuar con este propietario"**

**Si el propietario no existe:**
- Complete los datos: Nombre, Apellido, Tipo Doc, Identificación
- Haga clic en **"Crear Propietario"**

#### Paso 2: Registrar datos del vehículo

Una vez seleccionado el propietario, complete el formulario del vehículo:

| Campo | Descripción | Ejemplo |
|-------|-------------|---------|
| Placa | Placa del vehículo (se convierte a mayúsculas automáticamente) | ABC123 |
| Tipo | Seleccione Carro o Moto | Carro |
| Marca | Fabricante del vehículo | Toyota |
| Modelo | Modelo específico | Corolla |
| Color | Color del vehículo | Blanco |
| Fecha de Matrícula | Fecha en que se matriculó (importante para Tecnomecánica) | 15/03/2022 |
| Conductor | Opcional - Asigne un conductor | - |

#### Paso 3: Registrar documentos

Complete los formularios de SOAT y Tecnomecánica (si aplica).

> **Importante:** La fecha de matrícula es fundamental para calcular correctamente cuándo el vehículo debe realizar su primera Tecnomecánica.

### 5.4 Editar un vehículo

1. En el listado, ubique el vehículo
2. Haga clic en el ícono de **lápiz** (Editar)
3. Modifique los campos necesarios
4. Haga clic en **"Guardar Cambios"**

### 5.5 Eliminar un vehículo

1. En el listado, ubique el vehículo
2. Haga clic en el ícono de **papelera** (Eliminar)
3. Confirme la eliminación

> **Nota:** Los vehículos eliminados se conservan en el sistema durante 6 meses antes de ser eliminados permanentemente. Durante este período pueden ser recuperados por el administrador.

### 5.6 Reglas especiales para Tecnomecánica

El sistema calcula automáticamente cuándo un vehículo necesita su primera revisión técnico-mecánica:

| Tipo de vehículo | Primera revisión obligatoria |
|------------------|------------------------------|
| **Carros** | 5 años después de la fecha de matrícula |
| **Motos** | 2 años después de la fecha de matrícula |

**Vehículos nuevos:** Si un vehículo aún no cumple el tiempo para su primera revisión, el sistema mostrará el estado **"Nuevo - Exento"** en color verde, junto con la fecha en que debe realizar su primera Tecnomecánica.

---

## 6. GESTIÓN DE CONDUCTORES

*Disponible para: ADMIN y SST*

### 6.1 Ver listado de conductores

1. En el menú principal, seleccione **"Conductores"**
2. Verá una tabla con todos los conductores registrados

La tabla muestra:
- **Nombre completo** del conductor
- **Identificación** (tipo y número)
- **Teléfono** de contacto
- **Vehículo asignado** (si tiene)
- **Licencia** (categoría y fecha de vencimiento)
- **Estado** (Activo/Inactivo)

### 6.2 Registrar un nuevo conductor

1. Haga clic en el botón **"Nuevo Conductor"**
2. Complete el formulario:

| Campo | Descripción | Obligatorio |
|-------|-------------|-------------|
| Nombre | Nombres del conductor | Sí |
| Apellido | Apellidos del conductor | Sí |
| Tipo de documento | CC (Cédula) o CE (Cédula Extranjería) | Sí |
| Número de identificación | Número del documento | Sí |
| Clasificación | **EMPLEADO** (personal de planta) o **EXTERNO** (contratistas, visitantes) | Sí |
| Teléfono | Número de contacto | No |
| Teléfono de emergencia | Contacto en caso de emergencia | No |

> **Nota:** La clasificación es importante. Solo los conductores marcados como **EMPLEADO** permiten adjuntar documentos físicos a Google Drive al refrendar categorías de licencia.

3. Haga clic en **"Guardar"**

### 6.3 Categorías de licencia de conducción

El sistema maneja las categorías de licencia según la normativa colombiana:

| Categoría | Descripción |
|-----------|-------------|
| **A1** | Motocicletas hasta 125 cc |
| **A2** | Motocicletas de más de 125 cc |
| **B1** | Automóviles, camperos, camionetas |
| **B2** | Camiones, buses |
| **B3** | Vehículos articulados |
| **C1** | Servicio público (taxi) |
| **C2** | Servicio público (bus) |
| **C3** | Servicio público (carga) |

Un conductor puede tener múltiples categorías registradas.

### 6.4 Asignar conductor a un vehículo

1. Edite el vehículo correspondiente
2. En el campo **"Conductor"**, seleccione el conductor
3. Guarde los cambios

> **Nota:** Un conductor puede estar asignado a múltiples vehículos.

---

## 7. GESTIÓN DE DOCUMENTOS

*Disponible para: ADMIN y SST*

### 7.1 Tipos de documentos de vehículos

| Documento | Descripción | Vencimiento |
|-----------|-------------|-------------|
| **SOAT** | Seguro Obligatorio de Accidentes de Tránsito | Anual |
| **Tecnomecánica** | Revisión técnico-mecánica y de emisiones | Anual (después de primera revisión) |
| **Tarjeta de Propiedad** | Documento de propiedad del vehículo | No vence |
| **Póliza** | Seguro adicional (opcional) | Variable |

### 7.2 Tipos de documentos de conductores

| Documento | Descripción | Vencimiento |
|-----------|-------------|-------------|
| **Licencia de Conducción** | Permiso para conducir | Según edad y tipo |
| **EPS** | Afiliación a salud | Variable |
| **ARL** | Afiliación a riesgos laborales | Variable |
| **Certificado Médico** | Aptitud para conducir | Variable |

### 7.3 Registrar un documento de vehículo

1. Vaya al detalle del vehículo o al historial de documentos
2. Haga clic en **"Agregar Documento"**
3. Complete el formulario:

| Campo | Descripción |
|-------|-------------|
| Tipo de documento | Seleccione: SOAT, Tecnomecánica, etc. |
| Número del documento | Número o código del documento |
| Entidad emisora | Aseguradora o entidad que expidió el documento |
| Fecha de emisión | Fecha en que se expidió |
| Fecha de vencimiento | Fecha en que vence (no aplica para Tarjeta de Propiedad) |
| Adjuntar documento | Archivo PDF, imagen o Word del documento físico (opcional) |
| Observaciones | Notas adicionales (opcional) |

4. Haga clic en **"Guardar"**

> **Adjuntar archivo:** Los usuarios ADMIN y SST pueden adjuntar el archivo digital del documento (PDF, imagen, Word o Excel, máx. 10MB). El archivo se almacena en Google Drive automáticamente y queda vinculado al registro.

### 7.4 Renovar un documento

Cuando un documento vence y se obtiene uno nuevo:

1. En el historial de documentos del vehículo o conductor, haga clic en **"Renovar"** sobre el documento vencido
2. Se abrirá un formulario con los datos del nuevo documento
3. Complete la información actualizada y, opcionalmente, adjunte el nuevo archivo digital
4. El sistema automáticamente:
   - Marca el documento anterior como **"Reemplazado"**
   - Incrementa el número de versión
   - Mantiene el historial completo
   - Resuelve las alertas relacionadas con el documento anterior

### 7.5 Ver historial de documentos

1. En el listado de vehículos, haga clic en el ícono de **documentos**
2. Verá todos los documentos actuales e históricos
3. Los documentos reemplazados aparecen en color gris

---

## 8. MÓDULO DE REPORTES

*Disponible para: ADMIN y SST*

### 8.1 Acceder a reportes

1. En el menú principal, seleccione **"Reportes"**
2. Verá el centro de reportes con todas las opciones disponibles

### 8.2 Tipos de reportes disponibles

#### 8.2.1 Reporte de Vehículos

Muestra el estado general de todos los vehículos con sus documentos.

**Filtros disponibles:**
- Por tipo de vehículo (Carro/Moto)
- Por propietario
- Por placa

**Información incluida:**
- Datos del vehículo y propietario
- Estado de cada documento (SOAT, Tecnomecánica, etc.)
- Resumen estadístico

**Exportar:** PDF o Excel

#### 8.2.2 Reporte de Alertas

Lista los documentos próximos a vencer o ya vencidos.

**Filtros disponibles:**
- Rango de días para alertas
- Tipo de documento
- Estado (Por vencer / Vencido)

**Información incluida:**
- Documento y vehículo/conductor afectado
- Días para vencimiento o días vencido
- Nivel de urgencia

**Exportar:** PDF o Excel

#### 8.2.3 Ficha de Vehículo

Genera una ficha detallada de un vehículo específico.

**Información incluida:**
- Datos completos del vehículo
- Información del propietario
- Información del conductor asignado
- Estado de todos los documentos con semáforo visual
- Historial de documentos recientes

**Uso recomendado:** Imprimir para archivo físico o verificaciones

**Exportar:** PDF o Imprimir directamente

#### 8.2.4 Reporte por Propietario

Agrupa los vehículos por propietario.

**Información incluida:**
- Lista de propietarios
- Vehículos de cada propietario
- Estado documental de cada vehículo
- Estadísticas por propietario

**Exportar:** PDF o Excel

#### 8.2.5 Reporte Histórico

Muestra la actividad de documentos en un período de tiempo.

**Filtros disponibles:**
- Rango de fechas (por defecto: últimos 6 meses)
- Tipo de documento (SOAT, Tecnomecánica, Licencia, etc.)
- Placa del vehículo (búsqueda parcial)

**Información incluida:**
- Documentos registrados y renovados
- Fechas de registro
- Tipo de operación (nuevo/renovación)

**Exportar:** PDF o Excel

> **Nota:** Al exportar a PDF, el reporte respetará todos los filtros aplicados. Solo se incluirán los documentos que coincidan con los criterios de búsqueda seleccionados.

### 8.3 Cómo exportar un reporte

1. Genere el reporte con los filtros deseados
2. En la parte superior, encontrará botones:
   - **PDF:** Genera documento en formato PDF
   - **Excel:** Genera archivo compatible con hojas de cálculo
   - **Imprimir:** Envía directamente a la impresora

---

## 9. MÓDULO DE PORTERÍA

*Disponible para: ADMIN y PORTERIA*

Este módulo está diseñado para que el personal de portería pueda verificar rápidamente si un vehículo tiene sus documentos en regla.

### 9.1 Buscar un vehículo

1. Ingrese la **placa** del vehículo en el campo de búsqueda
2. Haga clic en **"Buscar"** o presione Enter
3. El sistema mostrará inmediatamente:

### 9.2 Información mostrada

Al encontrar un vehículo, verá:

**Datos del vehículo:**
- Placa, marca, modelo, color
- Nombre del propietario
- Nombre del conductor asignado

**Estado de documentos (con semáforo):**

| Documento | Estado posible |
|-----------|---------------|
| SOAT | 🟢 Vigente / 🟡 Por vencer (X días) / 🔴 Vencido / ⚪ Sin registro |
| Tecnomecánica | 🟢 Vigente / 🟡 Por vencer / 🔴 Vencido / 🟢 Nuevo-Exento / ⚪ Sin registro |
| Tarjeta Propiedad | 🟢 Registrada / ⚪ Sin registro |
| Licencia del conductor | 🟢 Vigente / 🟡 Por vencer / 🔴 Vencida / ⚪ Sin registro |

### 9.3 Criterios de decisión

| Si ve... | Recomendación |
|----------|---------------|
| Todo en **verde** | ✅ Puede ingresar |
| Algún documento en **amarillo** | ⚠️ Puede ingresar, pero informar que debe renovar pronto |
| Algún documento en **rojo** | ❌ No debería ingresar hasta regularizar |
| Documento **sin registro** | ❌ Verificar con administración |

### 9.4 Alertas en portería

En la pantalla de portería también se muestran las alertas recientes de documentos vencidos o por vencer, para que el personal esté informado de la situación general.

---

## 10. SISTEMA DE ALERTAS

### 10.1 ¿Cómo funcionan las alertas?

El sistema revisa automáticamente **todos los días a las 3:00 AM** el estado de los documentos y genera alertas cuando:

- Un documento **vencerá en los próximos 20 días**
- Un documento **ya está vencido**

### 10.2 ¿Dónde ver las alertas?

Las alertas aparecen en:
- El **Panel Principal** (Dashboard)
- El módulo de **Portería**
- El **Reporte de Alertas**

### 10.3 Notificaciones por correo electrónico

El sistema envía automáticamente un **resumen semanal por correo** (los lunes a las 4:00 AM) a todos los usuarios SST que tengan correo configurado.

El correo incluye:
- Cantidad total de alertas pendientes
- Lista de documentos vencidos
- Lista de documentos por vencer
- Enlace directo al sistema

### 10.4 Marcar alertas como leídas

1. Haga clic sobre la alerta
2. Al visualizar el detalle, la alerta se marca como leída
3. Las alertas leídas no desaparecen, pero ya no se muestran en el panel principal

---

## 11. ADMINISTRACIÓN DE USUARIOS

*Disponible únicamente para: ADMIN*

### 11.1 Ver usuarios del sistema

1. En el menú principal, seleccione **"Usuarios"**
2. Verá la lista de todos los usuarios registrados

### 11.2 Crear un nuevo usuario

1. Haga clic en **"Nuevo Usuario"**
2. Complete el formulario:

| Campo | Descripción |
|-------|-------------|
| Nombre | Nombres del usuario |
| Apellido | Apellidos del usuario |
| Usuario | Nombre de usuario para iniciar sesión (único) |
| Correo electrónico | Email del usuario (único) |
| Contraseña | Contraseña inicial |
| Confirmar contraseña | Repetir la contraseña |
| Rol | ADMIN, SST o PORTERIA |

3. Haga clic en **"Guardar"**

### 11.3 Modificar un usuario

1. Ubique el usuario en la lista
2. Haga clic en **"Editar"**
3. Modifique los campos necesarios
4. Haga clic en **"Guardar Cambios"**

### 11.4 Desactivar un usuario

En lugar de eliminar usuarios, se recomienda **desactivarlos**:

1. Edite el usuario
2. Cambie el estado a **"Inactivo"**
3. El usuario no podrá iniciar sesión pero su registro se conserva

### 11.5 Eliminar un usuario

1. Ubique el usuario en la lista
2. Haga clic en **"Eliminar"**
3. Confirme la eliminación

> **Advertencia:** Esta acción es permanente. Se recomienda desactivar en lugar de eliminar.

---

## 12. SISTEMA DE AUDITORÍA

*Disponible para: ADMIN*

El sistema cuenta con un registro automático de auditoría que permite rastrear todos los cambios realizados en los registros principales.

### 12.1 ¿Qué se registra?

El sistema registra automáticamente las siguientes acciones:

| Acción | Descripción |
|--------|-------------|
| **Crear** | Cuando se crea un nuevo registro |
| **Actualizar** | Cuando se modifica un registro existente |
| **Eliminar** | Cuando se elimina un registro (soft delete) |

### 12.2 Modelos auditados

Los siguientes registros tienen auditoría habilitada:

| Modelo | Campos auditados |
|--------|------------------|
| **Propietario** | nombre, apellido, tipo_doc, identificacion |
| **Vehículo** | placa, marca, modelo, color, tipo, propietario, conductor, estado |
| **Conductor** | nombre, apellido, tipo_doc, identificacion, telefono, estado |
| **Documento Vehículo** | tipo_documento, numero_documento, fecha_vencimiento, activo, version, estado |
| **Documento Conductor** | tipo_documento, numero_documento, categoria_licencia, fecha_vencimiento, activo, version |

### 12.3 Información registrada

Para cada cambio se guarda:

- **Quién:** Usuario que realizó el cambio
- **Cuándo:** Fecha y hora exacta del cambio
- **Qué:** Registro afectado (tipo y ID)
- **Valores anteriores:** Estado del registro antes del cambio
- **Valores nuevos:** Estado del registro después del cambio

### 12.4 Soft Deletes (Eliminación suave)

Los registros eliminados no se borran permanentemente de la base de datos. En su lugar:

- Se marcan como "eliminados" con una fecha
- No aparecen en las listas normales del sistema
- Pueden ser recuperados por el administrador si es necesario
- Se conserva todo el historial de cambios

> **Nota:** Esta funcionalidad permite recuperar información eliminada por error y mantener trazabilidad completa de las operaciones del sistema.

---

## 13. PREGUNTAS FRECUENTES

### ¿Por qué un vehículo nuevo aparece como "Exento" en Tecnomecánica?

Según la normativa colombiana, los vehículos nuevos no requieren revisión técnico-mecánica inmediatamente:
- **Carros:** Primera revisión a los 5 años de la fecha de matrícula
- **Motos:** Primera revisión a los 2 años de la fecha de matrícula

El sistema calcula esto automáticamente y muestra "Nuevo - Exento" mientras el vehículo esté dentro de este período.

### ¿Qué significa cada color en el estado de documentos?

- 🟢 **Verde:** El documento está vigente por más de 20 días
- 🟡 **Amarillo:** El documento vence entre 6 y 20 días
- 🔴 **Rojo:** El documento vence en menos de 5 días o ya está vencido
- ⚪ **Gris:** No hay documento registrado

### ¿Por qué no puedo ver cierto módulo?

Su acceso depende de su rol:
- **ADMIN:** Acceso total
- **SST:** Todo excepto usuarios y portería
- **PORTERIA:** Solo búsqueda de vehículos y alertas

Contacte al administrador si necesita acceso adicional.

### ¿Cómo actualizo un documento vencido?

1. Vaya al vehículo o conductor correspondiente
2. Haga clic en "Agregar Documento"
3. Ingrese los datos del nuevo documento
4. El sistema marcará el anterior como "Reemplazado" automáticamente

### ¿Cada cuánto se envían los correos de alerta?

Los correos se envían automáticamente **todos los lunes** a las 4:00 AM a los usuarios SST.

### ¿Qué pasa si elimino un vehículo por error?

Los vehículos eliminados se conservan durante 6 meses. Contacte al administrador para recuperarlo.

### ¿Cómo puedo cambiar mi contraseña?

Contacte al administrador del sistema para realizar el cambio de contraseña.

### La Tarjeta de Propiedad no me pide fecha de vencimiento, ¿es correcto?

Sí, es correcto. La Tarjeta de Propiedad es un documento que **no vence**, por lo que el sistema no solicita fecha de vencimiento para este tipo de documento.

---

## SOPORTE TÉCNICO

Para reportar problemas o solicitar ayuda con el sistema, contacte al administrador del Club Campestre Altos del Chicalá.

---

*Manual generado para el Sistema de Control Vehicular v1.1*
*Club Campestre Altos del Chicalá - 2026*
