# PLAN DE PRUEBAS FUNCIONALES
## Sistema de Control Vehicular - Club Campestre Altos del Chicalá

**Versión:** 1.0
**Fecha:** Enero 2026
**Basado en:** Código fuente del sistema

---

## ÍNDICE

1. [Introducción](#1-introducción)
2. [Alcance](#2-alcance)
3. [Perfiles de Usuario](#3-perfiles-de-usuario)
4. [Módulo de Autenticación](#4-módulo-de-autenticación)
5. [Módulo de Vehículos](#5-módulo-de-vehículos)
6. [Módulo de Conductores](#6-módulo-de-conductores)
7. [Módulo de Documentos](#7-módulo-de-documentos)
8. [Módulo de Alertas](#8-módulo-de-alertas)
9. [Módulo de Reportes](#9-módulo-de-reportes)
10. [Módulo de Portería](#10-módulo-de-portería)
11. [Módulo de Usuarios](#11-módulo-de-usuarios)
12. [Pruebas de Seguridad y Permisos](#12-pruebas-de-seguridad-y-permisos)
13. [Casos Límite y Errores](#13-casos-límite-y-errores)

---

## 1. INTRODUCCIÓN

Este documento describe el plan de pruebas funcionales para el Sistema de Control Vehicular. Cada caso de prueba incluye:

- **ID:** Identificador único del caso
- **Descripción:** Qué se está probando
- **Precondiciones:** Estado inicial requerido
- **Datos de Entrada:** Valores específicos a usar
- **Pasos:** Acciones a ejecutar
- **Resultado Esperado:** Comportamiento correcto
- **Perfil:** Usuario que ejecuta la prueba

---

## 2. ALCANCE

### Módulos a Probar
| Módulo | Funcionalidades |
|--------|-----------------|
| Autenticación | Login, Logout, Sesiones |
| Vehículos | CRUD, Búsqueda, Soft Delete |
| Conductores | CRUD, Asignación vehículos, Soft Delete |
| Documentos | CRUD, Renovación, Estados, Historial |
| Alertas | Creación automática, Lectura, Marcado |
| Reportes | Generación, Filtros, Exportación |
| Portería | Búsqueda, Visualización |
| Usuarios | CRUD (solo ADMIN) |

### Estados de Documentos
| Estado | Condición |
|--------|-----------|
| VIGENTE | Más de 20 días para vencer |
| POR_VENCER | Entre 0 y 20 días para vencer |
| VENCIDO | Fecha de vencimiento pasada |
| REEMPLAZADO | Existe versión más reciente |

---

## 3. PERFILES DE USUARIO

| Perfil | Descripción | Permisos Principales |
|--------|-------------|---------------------|
| ADMIN | Administrador total | Acceso completo a todos los módulos |
| SST | Seguridad y Salud | Gestión de vehículos, conductores, alertas, reportes |
| PORTERIA | Control de acceso | Solo búsqueda de vehículos y alertas |

---

## 4. MÓDULO DE AUTENTICACIÓN

### CP-AUTH-001: Login exitoso
| Campo | Valor |
|-------|-------|
| **ID** | CP-AUTH-001 |
| **Descripción** | Verificar inicio de sesión con credenciales válidas |
| **Precondiciones** | Usuario registrado y activo en el sistema |
| **Datos de Entrada** | Usuario: `admin@club.com` / Contraseña: `Admin123*` |
| **Pasos** | 1. Acceder a `/login` <br> 2. Ingresar usuario <br> 3. Ingresar contraseña <br> 4. Clic en "Iniciar Sesión" |
| **Resultado Esperado** | Redirección al Dashboard. Mensaje de bienvenida visible. |
| **Perfil** | ADMIN |

---

### CP-AUTH-002: Login fallido - Credenciales incorrectas
| Campo | Valor |
|-------|-------|
| **ID** | CP-AUTH-002 |
| **Descripción** | Verificar rechazo con credenciales inválidas |
| **Precondiciones** | Ninguna |
| **Datos de Entrada** | Usuario: `admin@club.com` / Contraseña: `ContraseñaIncorrecta` |
| **Pasos** | 1. Acceder a `/login` <br> 2. Ingresar usuario <br> 3. Ingresar contraseña incorrecta <br> 4. Clic en "Iniciar Sesión" |
| **Resultado Esperado** | Permanece en login. Mensaje de error: "Credenciales incorrectas" |
| **Perfil** | Cualquiera |

---

### CP-AUTH-003: Login fallido - Usuario inactivo
| Campo | Valor |
|-------|-------|
| **ID** | CP-AUTH-003 |
| **Descripción** | Verificar que usuarios inactivos no pueden acceder |
| **Precondiciones** | Usuario existe pero tiene `activo = 0` |
| **Datos de Entrada** | Usuario: `inactivo@club.com` / Contraseña: `Password123` |
| **Pasos** | 1. Acceder a `/login` <br> 2. Ingresar credenciales del usuario inactivo <br> 3. Clic en "Iniciar Sesión" |
| **Resultado Esperado** | Acceso denegado. Mensaje de error apropiado. |
| **Perfil** | N/A |

---

### CP-AUTH-004: Logout exitoso
| Campo | Valor |
|-------|-------|
| **ID** | CP-AUTH-004 |
| **Descripción** | Verificar cierre de sesión correcto |
| **Precondiciones** | Usuario autenticado en el sistema |
| **Datos de Entrada** | N/A |
| **Pasos** | 1. Estar logueado <br> 2. Clic en nombre de usuario <br> 3. Clic en "Cerrar Sesión" |
| **Resultado Esperado** | Redirección a página de login. Sesión invalidada. |
| **Perfil** | ADMIN, SST, PORTERIA |

---

### CP-AUTH-005: Acceso sin autenticación
| Campo | Valor |
|-------|-------|
| **ID** | CP-AUTH-005 |
| **Descripción** | Verificar protección de rutas sin login |
| **Precondiciones** | Sin sesión activa |
| **Datos de Entrada** | URL: `/dashboard` |
| **Pasos** | 1. Cerrar sesión <br> 2. Acceder directamente a `/dashboard` |
| **Resultado Esperado** | Redirección automática a `/login` |
| **Perfil** | N/A |

---

## 5. MÓDULO DE VEHÍCULOS

### CP-VEH-001: Crear vehículo completo
| Campo | Valor |
|-------|-------|
| **ID** | CP-VEH-001 |
| **Descripción** | Registro de vehículo con todos los datos |
| **Precondiciones** | Usuario ADMIN o SST autenticado |
| **Datos de Entrada** | |
| | **Propietario:** Nombre: `Juan Carlos`, Apellido: `Pérez López`, Tipo Doc: `CC`, Identificación: `1098765432`, Teléfono: `3001234567`, Email: `juan.perez@email.com` |
| | **Vehículo:** Placa: `ABC123`, Marca: `Mazda`, Modelo: `CX-5`, Color: `Blanco`, Tipo: `Carro` |
| | **Tarjeta Propiedad:** Número: `TP-123456`, Fecha Matrícula: `2024-06-15` |
| | **SOAT:** Número: `SOAT-789012`, Entidad: `Seguros Bolívar`, Fecha Emisión: `2025-01-10`, Fecha Vencimiento: `2026-01-10` |
| | **Tecnomecánica:** Número: `TM-456789`, Entidad: `CDA Centro`, Fecha Emisión: `2025-02-01`, Fecha Vencimiento: `2026-02-01` |
| **Pasos** | 1. Ir a Vehículos → Nuevo Vehículo <br> 2. Completar datos de Propietario <br> 3. Completar datos de Vehículo <br> 4. Completar Tarjeta de Propiedad <br> 5. Completar SOAT <br> 6. Completar Tecnomecánica <br> 7. Clic en "Guardar" |
| **Resultado Esperado** | Vehículo creado. Mensaje de éxito. Aparece en listado con estado VIGENTE. |
| **Perfil** | ADMIN, SST |

---

### CP-VEH-002: Crear vehículo - Placa duplicada
| Campo | Valor |
|-------|-------|
| **ID** | CP-VEH-002 |
| **Descripción** | Verificar que no se permiten placas duplicadas |
| **Precondiciones** | Existe vehículo con placa `ABC123` |
| **Datos de Entrada** | Placa: `ABC123` (duplicada), resto de datos válidos |
| **Pasos** | 1. Ir a Vehículos → Nuevo Vehículo <br> 2. Ingresar placa existente <br> 3. Completar resto de datos <br> 4. Intentar guardar |
| **Resultado Esperado** | Error de validación: "La placa ya está registrada" |
| **Perfil** | ADMIN, SST |

---

### CP-VEH-003: Crear vehículo nuevo (Tecnomecánica exenta - Carro)
| Campo | Valor |
|-------|-------|
| **ID** | CP-VEH-003 |
| **Descripción** | Verificar exención de tecnomecánica para carro nuevo |
| **Precondiciones** | Usuario ADMIN o SST autenticado |
| **Datos de Entrada** | |
| | Tipo: `Carro`, Fecha Matrícula: `2024-03-15` (menos de 5 años) |
| | Tecnomecánica: Marcar "Vehículo exento" |
| **Pasos** | 1. Ir a Vehículos → Nuevo Vehículo <br> 2. Seleccionar Tipo: Carro <br> 3. En Tarjeta Propiedad, ingresar fecha matrícula reciente <br> 4. En Tecnomecánica, verificar que aparece opción "Exento" <br> 5. Marcar como exento <br> 6. Guardar |
| **Resultado Esperado** | Vehículo creado. Tecnomecánica muestra "Exento hasta [fecha matrícula + 5 años]" |
| **Perfil** | ADMIN, SST |

---

### CP-VEH-004: Crear vehículo nuevo (Tecnomecánica exenta - Moto)
| Campo | Valor |
|-------|-------|
| **ID** | CP-VEH-004 |
| **Descripción** | Verificar exención de tecnomecánica para moto nueva |
| **Precondiciones** | Usuario ADMIN o SST autenticado |
| **Datos de Entrada** | |
| | Tipo: `Moto`, Fecha Matrícula: `2025-01-01` (menos de 2 años) |
| | Tecnomecánica: Marcar "Vehículo exento" |
| **Pasos** | 1. Ir a Vehículos → Nuevo Vehículo <br> 2. Seleccionar Tipo: Moto <br> 3. En Tarjeta Propiedad, ingresar fecha matrícula reciente <br> 4. En Tecnomecánica, verificar que aparece opción "Exento" <br> 5. Marcar como exento <br> 6. Guardar |
| **Resultado Esperado** | Vehículo creado. Tecnomecánica muestra "Exento hasta [fecha matrícula + 2 años]" |
| **Perfil** | ADMIN, SST |

---

### CP-VEH-005: Búsqueda de vehículo por placa
| Campo | Valor |
|-------|-------|
| **ID** | CP-VEH-005 |
| **Descripción** | Buscar vehículo usando número de placa |
| **Precondiciones** | Existe vehículo con placa `ABC123` |
| **Datos de Entrada** | Búsqueda: `ABC123` |
| **Pasos** | 1. Ir a Vehículos <br> 2. En campo de búsqueda escribir `ABC123` <br> 3. Presionar Enter o clic en buscar |
| **Resultado Esperado** | Listado muestra solo vehículo con placa ABC123 |
| **Perfil** | ADMIN, SST |

---

### CP-VEH-006: Búsqueda de vehículo por propietario
| Campo | Valor |
|-------|-------|
| **ID** | CP-VEH-006 |
| **Descripción** | Buscar vehículos por nombre de propietario |
| **Precondiciones** | Existen vehículos del propietario "Juan Pérez" |
| **Datos de Entrada** | Búsqueda: `Pérez` |
| **Pasos** | 1. Ir a Vehículos <br> 2. En campo de búsqueda escribir `Pérez` <br> 3. Presionar Enter |
| **Resultado Esperado** | Listado muestra todos los vehículos cuyo propietario contenga "Pérez" |
| **Perfil** | ADMIN, SST |

---

### CP-VEH-007: Editar vehículo
| Campo | Valor |
|-------|-------|
| **ID** | CP-VEH-007 |
| **Descripción** | Modificar información de vehículo existente |
| **Precondiciones** | Existe vehículo con placa `ABC123` |
| **Datos de Entrada** | Color: `Rojo` (cambiar de Blanco a Rojo) |
| **Pasos** | 1. Ir a Vehículos <br> 2. Buscar vehículo `ABC123` <br> 3. Clic en botón Editar <br> 4. Cambiar color a "Rojo" <br> 5. Clic en Guardar |
| **Resultado Esperado** | Vehículo actualizado. Color muestra "Rojo". Mensaje de éxito. |
| **Perfil** | ADMIN, SST |

---

### CP-VEH-008: Eliminar vehículo (Soft Delete)
| Campo | Valor |
|-------|-------|
| **ID** | CP-VEH-008 |
| **Descripción** | Eliminar vehículo (eliminación lógica) |
| **Precondiciones** | Existe vehículo con placa `XYZ789` |
| **Datos de Entrada** | Vehículo: `XYZ789` |
| **Pasos** | 1. Ir a Vehículos <br> 2. Buscar vehículo `XYZ789` <br> 3. Clic en botón Eliminar <br> 4. Confirmar eliminación |
| **Resultado Esperado** | Vehículo ya no aparece en listado principal. Mensaje de éxito. Datos se mantienen en BD (soft delete). |
| **Perfil** | ADMIN, SST |

---

### CP-VEH-009: Restaurar vehículo eliminado
| Campo | Valor |
|-------|-------|
| **ID** | CP-VEH-009 |
| **Descripción** | Recuperar vehículo previamente eliminado |
| **Precondiciones** | Vehículo `XYZ789` está eliminado (soft delete) |
| **Datos de Entrada** | Vehículo: `XYZ789` |
| **Pasos** | 1. Ir a Vehículos → Eliminados <br> 2. Buscar vehículo `XYZ789` <br> 3. Clic en botón Restaurar <br> 4. Confirmar restauración |
| **Resultado Esperado** | Vehículo reaparece en listado principal. Documentos asociados restaurados. |
| **Perfil** | ADMIN |

---

### CP-VEH-010: Paginación de vehículos
| Campo | Valor |
|-------|-------|
| **ID** | CP-VEH-010 |
| **Descripción** | Verificar paginación en listado de vehículos |
| **Precondiciones** | Más de 15 vehículos registrados |
| **Datos de Entrada** | N/A |
| **Pasos** | 1. Ir a Vehículos <br> 2. Verificar que se muestran máximo 15 registros <br> 3. Navegar a página 2 |
| **Resultado Esperado** | Página 1 muestra 15 registros. Controles de paginación visibles. Página 2 muestra siguientes registros. |
| **Perfil** | ADMIN, SST |

---

## 6. MÓDULO DE CONDUCTORES

### CP-CON-001: Crear conductor completo
| Campo | Valor |
|-------|-------|
| **ID** | CP-CON-001 |
| **Descripción** | Registro de conductor con todos los datos |
| **Precondiciones** | Usuario ADMIN o SST autenticado. Existe vehículo sin conductor asignado. |
| **Datos de Entrada** | |
| | Nombre: `María Elena`, Apellido: `García Rodríguez` |
| | Tipo Doc: `CC`, Identificación: `52345678` |
| | Teléfono: `3109876543`, Tel. Emergencia: `3201234567` |
| | Vehículo: `ABC123` (seleccionar del listado) |
| | **Licencia:** Número: `LIC-52345678`, Categoría: `B1`, Categorías Adicionales: `A2`, Fecha Emisión: `2023-05-10`, Fecha Vencimiento: `2033-05-10` |
| **Pasos** | 1. Ir a Conductores → Nuevo Conductor <br> 2. Completar datos personales <br> 3. Seleccionar vehículo a asignar <br> 4. Completar datos de licencia <br> 5. Clic en Guardar |
| **Resultado Esperado** | Conductor creado. Vehículo asignado. Licencia registrada con estado VIGENTE. |
| **Perfil** | ADMIN, SST |

---

### CP-CON-002: Crear conductor - Identificación duplicada
| Campo | Valor |
|-------|-------|
| **ID** | CP-CON-002 |
| **Descripción** | Verificar que no se permiten identificaciones duplicadas |
| **Precondiciones** | Existe conductor con identificación `52345678` |
| **Datos de Entrada** | Identificación: `52345678` (duplicada) |
| **Pasos** | 1. Ir a Conductores → Nuevo Conductor <br> 2. Ingresar identificación existente <br> 3. Intentar guardar |
| **Resultado Esperado** | Error de validación: "La identificación ya está registrada" |
| **Perfil** | ADMIN, SST |

---

### CP-CON-003: Asignar vehículo a conductor existente
| Campo | Valor |
|-------|-------|
| **ID** | CP-CON-003 |
| **Descripción** | Asignar vehículo a conductor que no tenía asignación |
| **Precondiciones** | Conductor existe sin vehículo. Vehículo `DEF456` está disponible (sin conductor). |
| **Datos de Entrada** | Conductor: `María García`, Vehículo: `DEF456` |
| **Pasos** | 1. Ir a Conductores <br> 2. Buscar conductor "María García" <br> 3. Clic en Editar <br> 4. Seleccionar vehículo `DEF456` <br> 5. Guardar |
| **Resultado Esperado** | Conductor actualizado. Vehículo muestra conductor asignado. |
| **Perfil** | ADMIN, SST |

---

### CP-CON-004: Cambiar vehículo de conductor
| Campo | Valor |
|-------|-------|
| **ID** | CP-CON-004 |
| **Descripción** | Cambiar asignación de vehículo a otro conductor |
| **Precondiciones** | Conductor tiene vehículo `ABC123`. Vehículo `GHI789` está disponible. |
| **Datos de Entrada** | Nuevo vehículo: `GHI789` |
| **Pasos** | 1. Ir a Conductores <br> 2. Editar conductor <br> 3. Cambiar vehículo de `ABC123` a `GHI789` <br> 4. Guardar |
| **Resultado Esperado** | Vehículo anterior (`ABC123`) queda sin conductor. Nuevo vehículo (`GHI789`) asignado. |
| **Perfil** | ADMIN, SST |

---

### CP-CON-005: Eliminar conductor (Soft Delete)
| Campo | Valor |
|-------|-------|
| **ID** | CP-CON-005 |
| **Descripción** | Eliminar conductor con eliminación lógica |
| **Precondiciones** | Conductor existe con vehículo asignado |
| **Datos de Entrada** | Conductor: `Pedro Martínez` |
| **Pasos** | 1. Ir a Conductores <br> 2. Buscar "Pedro Martínez" <br> 3. Clic en Eliminar <br> 4. Confirmar |
| **Resultado Esperado** | Conductor desaparece de listado. Vehículo queda sin conductor. Documentos del conductor se eliminan (soft delete). |
| **Perfil** | ADMIN, SST |

---

### CP-CON-006: Restaurar conductor eliminado
| Campo | Valor |
|-------|-------|
| **ID** | CP-CON-006 |
| **Descripción** | Recuperar conductor previamente eliminado |
| **Precondiciones** | Conductor está en estado eliminado (soft delete) |
| **Datos de Entrada** | Conductor: `Pedro Martínez` |
| **Pasos** | 1. Ir a Conductores → Eliminados <br> 2. Buscar conductor <br> 3. Clic en Restaurar |
| **Resultado Esperado** | Conductor reaparece en listado. Documentos restaurados. Vehículo NO se reasigna automáticamente. |
| **Perfil** | ADMIN |

---

### CP-CON-007: Categorías de licencia múltiples
| Campo | Valor |
|-------|-------|
| **ID** | CP-CON-007 |
| **Descripción** | Registrar licencia con múltiples categorías |
| **Precondiciones** | Conductor nuevo a registrar |
| **Datos de Entrada** | |
| | Categoría Principal: `C2` |
| | Categorías Adicionales: `B1`, `A2` |
| **Pasos** | 1. Crear nuevo conductor <br> 2. En sección licencia, seleccionar categoría `C2` <br> 3. Agregar categorías adicionales `B1` y `A2` <br> 4. Guardar |
| **Resultado Esperado** | Licencia muestra categoría principal `C2` y adicionales `B1, A2` |
| **Perfil** | ADMIN, SST |

---

## 7. MÓDULO DE DOCUMENTOS

### CP-DOC-001: Renovar SOAT
| Campo | Valor |
|-------|-------|
| **ID** | CP-DOC-001 |
| **Descripción** | Renovar documento SOAT de vehículo |
| **Precondiciones** | Vehículo tiene SOAT próximo a vencer o vencido |
| **Datos de Entrada** | |
| | Número: `SOAT-NEW-2026` |
| | Entidad: `Seguros Bolívar` |
| | Fecha Emisión: `2026-01-20` |
| | Fecha Vencimiento: `2027-01-20` |
| **Pasos** | 1. Ir a Vehículos <br> 2. Seleccionar vehículo <br> 3. Ir a pestaña Documentos <br> 4. En SOAT, clic en "Renovar" <br> 5. Completar nuevos datos <br> 6. Guardar |
| **Resultado Esperado** | Nuevo SOAT con estado VIGENTE. SOAT anterior cambia a estado REEMPLAZADO. Versión incrementa. |
| **Perfil** | ADMIN, SST |

---

### CP-DOC-002: Renovar Tecnomecánica
| Campo | Valor |
|-------|-------|
| **ID** | CP-DOC-002 |
| **Descripción** | Renovar certificado de tecnomecánica |
| **Precondiciones** | Vehículo tiene tecnomecánica vencida o por vencer |
| **Datos de Entrada** | |
| | Número: `TM-2026-001` |
| | Entidad: `CDA Automotriz` |
| | Fecha Emisión: `2026-01-15` |
| | (Vencimiento: calculado automáticamente a 1 año) |
| **Pasos** | 1. Ir al vehículo <br> 2. Sección Documentos → Tecnomecánica <br> 3. Clic en Renovar <br> 4. Ingresar datos <br> 5. Guardar |
| **Resultado Esperado** | Nueva tecnomecánica. Vencimiento automático: `2027-01-15` (1 año). Estado VIGENTE. |
| **Perfil** | ADMIN, SST |

---

### CP-DOC-003: Ver historial de documentos
| Campo | Valor |
|-------|-------|
| **ID** | CP-DOC-003 |
| **Descripción** | Consultar historial de renovaciones de un documento |
| **Precondiciones** | Vehículo con múltiples versiones de SOAT |
| **Datos de Entrada** | Vehículo: `ABC123`, Documento: SOAT |
| **Pasos** | 1. Ir al vehículo `ABC123` <br> 2. Sección Documentos <br> 3. En SOAT, clic en "Ver Historial" |
| **Resultado Esperado** | Modal muestra todas las versiones del SOAT ordenadas por fecha (más reciente primero). Cada versión muestra número, fechas, entidad y estado. |
| **Perfil** | ADMIN, SST |

---

### CP-DOC-004: Estado automático - VIGENTE
| Campo | Valor |
|-------|-------|
| **ID** | CP-DOC-004 |
| **Descripción** | Verificar cálculo automático de estado VIGENTE |
| **Precondiciones** | Ninguna |
| **Datos de Entrada** | Fecha Vencimiento: `[Fecha actual + 30 días]` |
| **Pasos** | 1. Crear documento con vencimiento a 30 días <br> 2. Guardar <br> 3. Verificar estado |
| **Resultado Esperado** | Estado = VIGENTE. Color verde. Más de 20 días para vencer. |
| **Perfil** | ADMIN, SST |

---

### CP-DOC-005: Estado automático - POR_VENCER
| Campo | Valor |
|-------|-------|
| **ID** | CP-DOC-005 |
| **Descripción** | Verificar cálculo automático de estado POR_VENCER |
| **Precondiciones** | Ninguna |
| **Datos de Entrada** | Fecha Vencimiento: `[Fecha actual + 15 días]` |
| **Pasos** | 1. Crear documento con vencimiento a 15 días <br> 2. Guardar <br> 3. Verificar estado |
| **Resultado Esperado** | Estado = POR_VENCER. Color amarillo. Entre 0 y 20 días para vencer. Alerta generada. |
| **Perfil** | ADMIN, SST |

---

### CP-DOC-006: Estado automático - VENCIDO
| Campo | Valor |
|-------|-------|
| **ID** | CP-DOC-006 |
| **Descripción** | Verificar cálculo automático de estado VENCIDO |
| **Precondiciones** | Ninguna |
| **Datos de Entrada** | Fecha Vencimiento: `[Fecha actual - 5 días]` |
| **Pasos** | 1. Verificar documento con vencimiento pasado <br> 2. Ver estado en listado |
| **Resultado Esperado** | Estado = VENCIDO. Color rojo. Alerta visible en Dashboard. |
| **Perfil** | ADMIN, SST |

---

### CP-DOC-007: Renovar licencia de conductor
| Campo | Valor |
|-------|-------|
| **ID** | CP-DOC-007 |
| **Descripción** | Renovar licencia de conducción |
| **Precondiciones** | Conductor con licencia por vencer |
| **Datos de Entrada** | |
| | Número: `LIC-REN-2026` |
| | Categoría: `B1` |
| | Fecha Emisión: `2026-01-20` |
| | Fecha Vencimiento: `2036-01-20` |
| **Pasos** | 1. Ir a Conductores <br> 2. Seleccionar conductor <br> 3. En Documentos → Licencia <br> 4. Clic en Renovar <br> 5. Completar datos <br> 6. Guardar |
| **Resultado Esperado** | Nueva licencia VIGENTE. Licencia anterior REEMPLAZADA. Versión incrementada. |
| **Perfil** | ADMIN, SST |

---

### CP-DOC-008: Validación fecha emisión/vencimiento
| Campo | Valor |
|-------|-------|
| **ID** | CP-DOC-008 |
| **Descripción** | Verificar que fecha emisión no sea posterior a vencimiento |
| **Precondiciones** | Ninguna |
| **Datos de Entrada** | |
| | Fecha Emisión: `2026-06-01` |
| | Fecha Vencimiento: `2026-01-01` (anterior) |
| **Pasos** | 1. Intentar crear documento <br> 2. Ingresar fecha vencimiento anterior a emisión <br> 3. Guardar |
| **Resultado Esperado** | Error de validación: "La fecha de vencimiento debe ser posterior a la fecha de emisión" |
| **Perfil** | ADMIN, SST |

---

## 8. MÓDULO DE ALERTAS

### CP-ALE-001: Generación automática de alerta por documento por vencer
| Campo | Valor |
|-------|-------|
| **ID** | CP-ALE-001 |
| **Descripción** | Verificar creación automática de alerta cuando documento entra en estado POR_VENCER |
| **Precondiciones** | Documento con vencimiento dentro de 20 días |
| **Datos de Entrada** | Documento SOAT vence en 10 días |
| **Pasos** | 1. Verificar que existe documento por vencer <br> 2. Ir a Dashboard o Centro de Alertas <br> 3. Buscar alerta correspondiente |
| **Resultado Esperado** | Alerta visible con tipo "SOAT", mensaje indicando días restantes, badge "POR_VENCER" en amarillo. |
| **Perfil** | ADMIN, SST |

---

### CP-ALE-002: Generación automática de alerta por documento vencido
| Campo | Valor |
|-------|-------|
| **ID** | CP-ALE-002 |
| **Descripción** | Verificar creación automática de alerta cuando documento vence |
| **Precondiciones** | Documento con fecha vencimiento pasada |
| **Datos de Entrada** | Documento Tecnomecánica venció hace 3 días |
| **Pasos** | 1. Verificar documento vencido <br> 2. Ir a Centro de Alertas <br> 3. Buscar alerta |
| **Resultado Esperado** | Alerta con badge "VENCIDO" en rojo. Mensaje indica documento vencido. |
| **Perfil** | ADMIN, SST |

---

### CP-ALE-003: Marcar alerta como leída
| Campo | Valor |
|-------|-------|
| **ID** | CP-ALE-003 |
| **Descripción** | Marcar una alerta individual como leída |
| **Precondiciones** | Existe alerta no leída |
| **Datos de Entrada** | Alerta seleccionada |
| **Pasos** | 1. Ir a Centro de Alertas <br> 2. Identificar alerta no leída (tiene badge "Nueva") <br> 3. Clic en "Marcar leída" |
| **Resultado Esperado** | Badge "Nueva" desaparece. Alerta cambia a estilo de leída (fondo más claro). Muestra ícono de check. |
| **Perfil** | ADMIN, SST, PORTERIA |

---

### CP-ALE-004: Marcar todas las alertas como leídas
| Campo | Valor |
|-------|-------|
| **ID** | CP-ALE-004 |
| **Descripción** | Marcar todas las alertas como leídas en lote |
| **Precondiciones** | Existen múltiples alertas no leídas |
| **Datos de Entrada** | N/A |
| **Pasos** | 1. Ir a Centro de Alertas o Dashboard <br> 2. Clic en "Marcar todas como leídas" |
| **Resultado Esperado** | Todas las alertas cambian a estado leída. Contador de alertas no leídas = 0. |
| **Perfil** | ADMIN, SST, PORTERIA |

---

### CP-ALE-005: Contador de alertas no leídas
| Campo | Valor |
|-------|-------|
| **ID** | CP-ALE-005 |
| **Descripción** | Verificar badge de contador en menú |
| **Precondiciones** | Existen 5 alertas no leídas |
| **Datos de Entrada** | N/A |
| **Pasos** | 1. Observar menú de navegación <br> 2. Verificar ícono de campana con número |
| **Resultado Esperado** | Badge muestra "5" junto al ícono de alertas. Se actualiza al marcar alertas como leídas. |
| **Perfil** | ADMIN, SST, PORTERIA |

---

### CP-ALE-006: Alerta muestra placa y conductor
| Campo | Valor |
|-------|-------|
| **ID** | CP-ALE-006 |
| **Descripción** | Verificar que alerta de vehículo muestra placa y conductor asignado |
| **Precondiciones** | Vehículo con conductor y documento por vencer |
| **Datos de Entrada** | Vehículo `ABC123` con conductor `María García` |
| **Pasos** | 1. Ir a Centro de Alertas <br> 2. Buscar alerta del vehículo |
| **Resultado Esperado** | Alerta muestra badge con placa `ABC123` y nombre del conductor `María García`. |
| **Perfil** | ADMIN, SST |

---

## 9. MÓDULO DE REPORTES

### CP-REP-001: Reporte de vehículos - Filtro por tipo
| Campo | Valor |
|-------|-------|
| **ID** | CP-REP-001 |
| **Descripción** | Filtrar reporte de vehículos por tipo (Carro/Moto) |
| **Precondiciones** | Existen vehículos de ambos tipos |
| **Datos de Entrada** | Filtro Tipo: `Moto` |
| **Pasos** | 1. Ir a Reportes → Vehículos <br> 2. Seleccionar filtro Tipo = Moto <br> 3. Aplicar filtro |
| **Resultado Esperado** | Listado muestra solo vehículos tipo Moto. Total actualizado. |
| **Perfil** | ADMIN, SST |

---

### CP-REP-002: Reporte de vehículos - Filtro por estado documentos
| Campo | Valor |
|-------|-------|
| **ID** | CP-REP-002 |
| **Descripción** | Filtrar vehículos por estado de documentación |
| **Precondiciones** | Existen vehículos con diferentes estados de documentos |
| **Datos de Entrada** | Filtro Estado: `VENCIDO` |
| **Pasos** | 1. Ir a Reportes → Vehículos <br> 2. Seleccionar filtro Estado Docs = VENCIDO <br> 3. Aplicar |
| **Resultado Esperado** | Solo vehículos con al menos un documento vencido. |
| **Perfil** | ADMIN, SST |

---

### CP-REP-003: Exportar reporte a PDF
| Campo | Valor |
|-------|-------|
| **ID** | CP-REP-003 |
| **Descripción** | Exportar reporte de vehículos a formato PDF |
| **Precondiciones** | Reporte con datos visibles |
| **Datos de Entrada** | Reporte de vehículos actual |
| **Pasos** | 1. En Reportes → Vehículos <br> 2. Clic en botón "Exportar PDF" |
| **Resultado Esperado** | Descarga archivo PDF. Contiene tabla con todos los datos visibles. Logo y fecha de generación incluidos. |
| **Perfil** | ADMIN, SST |

---

### CP-REP-004: Exportar reporte a Excel/CSV
| Campo | Valor |
|-------|-------|
| **ID** | CP-REP-004 |
| **Descripción** | Exportar reporte a formato CSV |
| **Precondiciones** | Reporte con datos visibles |
| **Datos de Entrada** | Reporte de vehículos actual |
| **Pasos** | 1. En Reportes → Vehículos <br> 2. Clic en botón "Exportar Excel" |
| **Resultado Esperado** | Descarga archivo CSV. Abre correctamente en Excel. Datos con formato UTF-8 (tildes correctas). |
| **Perfil** | ADMIN, SST |

---

### CP-REP-005: Ficha de vehículo
| Campo | Valor |
|-------|-------|
| **ID** | CP-REP-005 |
| **Descripción** | Generar ficha completa de un vehículo |
| **Precondiciones** | Vehículo con propietario, conductor y documentos |
| **Datos de Entrada** | Vehículo: `ABC123` |
| **Pasos** | 1. Ir a Reportes → Vehículos <br> 2. Clic en "Ver Ficha" del vehículo `ABC123` |
| **Resultado Esperado** | Página muestra: Datos del vehículo, Datos del propietario, Datos del conductor (si existe), Estado de todos los documentos con colores. |
| **Perfil** | ADMIN, SST |

---

### CP-REP-006: Reporte de alertas
| Campo | Valor |
|-------|-------|
| **ID** | CP-REP-006 |
| **Descripción** | Ver reporte de documentos por vencer/vencidos |
| **Precondiciones** | Existen documentos en diferentes estados |
| **Datos de Entrada** | Filtro: Próximos 20 días |
| **Pasos** | 1. Ir a Reportes → Alertas <br> 2. Verificar listado de documentos |
| **Resultado Esperado** | Listado dividido en: Por Vencer (amarillo) y Vencidos (rojo). Estadísticas en tarjetas superiores. |
| **Perfil** | ADMIN, SST |

---

### CP-REP-007: Reporte histórico
| Campo | Valor |
|-------|-------|
| **ID** | CP-REP-007 |
| **Descripción** | Ver historial de renovaciones en período |
| **Precondiciones** | Existen renovaciones en el último mes |
| **Datos de Entrada** | Período: Último mes |
| **Pasos** | 1. Ir a Reportes → Histórico <br> 2. Seleccionar rango de fechas <br> 3. Aplicar |
| **Resultado Esperado** | Listado de todas las renovaciones. Diferencia entre "Nuevo" y "Renovación". Agrupación por mes. |
| **Perfil** | ADMIN, SST |

---

## 10. MÓDULO DE PORTERÍA

### CP-POR-001: Búsqueda de vehículo por placa
| Campo | Valor |
|-------|-------|
| **ID** | CP-POR-001 |
| **Descripción** | Buscar vehículo en módulo de portería |
| **Precondiciones** | Usuario PORTERIA autenticado. Vehículo `ABC123` existe. |
| **Datos de Entrada** | Placa: `ABC123` |
| **Pasos** | 1. En módulo Portería <br> 2. Escribir placa en campo de búsqueda <br> 3. Clic en Buscar |
| **Resultado Esperado** | Muestra información del vehículo: Placa, Marca, Modelo, Color, Propietario, Conductor (si existe), Estado de documentos con colores. |
| **Perfil** | PORTERIA |

---

### CP-POR-002: Búsqueda vehículo inexistente
| Campo | Valor |
|-------|-------|
| **ID** | CP-POR-002 |
| **Descripción** | Buscar placa que no existe |
| **Precondiciones** | Placa `ZZZ999` no registrada |
| **Datos de Entrada** | Placa: `ZZZ999` |
| **Pasos** | 1. En módulo Portería <br> 2. Buscar placa inexistente |
| **Resultado Esperado** | Mensaje: "No se encontró vehículo con esa placa" |
| **Perfil** | PORTERIA |

---

### CP-POR-003: Visualización de colores de estado
| Campo | Valor |
|-------|-------|
| **ID** | CP-POR-003 |
| **Descripción** | Verificar código de colores en documentos |
| **Precondiciones** | Vehículo con documentos en diferentes estados |
| **Datos de Entrada** | Vehículo con SOAT vencido, Tecnomecánica por vencer, Tarjeta vigente |
| **Pasos** | 1. Buscar vehículo en Portería <br> 2. Observar colores de cada documento |
| **Resultado Esperado** | SOAT: Rojo (vencido). Tecnomecánica: Amarillo (por vencer). Tarjeta: Verde (vigente). |
| **Perfil** | PORTERIA |

---

### CP-POR-004: Ver alertas desde Portería
| Campo | Valor |
|-------|-------|
| **ID** | CP-POR-004 |
| **Descripción** | Verificar acceso a alertas desde módulo Portería |
| **Precondiciones** | Existen alertas no leídas |
| **Datos de Entrada** | N/A |
| **Pasos** | 1. En módulo Portería <br> 2. Verificar sección de alertas |
| **Resultado Esperado** | Se muestran alertas recientes. Permite marcar como leídas. |
| **Perfil** | PORTERIA |

---

### CP-POR-005: Búsqueda insensible a mayúsculas
| Campo | Valor |
|-------|-------|
| **ID** | CP-POR-005 |
| **Descripción** | Verificar que búsqueda funciona sin importar mayúsculas |
| **Precondiciones** | Vehículo con placa `ABC123` existe |
| **Datos de Entrada** | Búsqueda: `abc123` (minúsculas) |
| **Pasos** | 1. En Portería <br> 2. Buscar con minúsculas <br> 3. Verificar resultado |
| **Resultado Esperado** | Encuentra vehículo `ABC123` independientemente de mayúsculas/minúsculas. |
| **Perfil** | PORTERIA |

---

## 11. MÓDULO DE USUARIOS

### CP-USR-001: Crear usuario nuevo
| Campo | Valor |
|-------|-------|
| **ID** | CP-USR-001 |
| **Descripción** | Crear nuevo usuario del sistema |
| **Precondiciones** | Usuario ADMIN autenticado |
| **Datos de Entrada** | |
| | Nombre: `Carlos`, Apellido: `Rodríguez` |
| | Usuario: `crodriguez` |
| | Email: `carlos.rodriguez@club.com` |
| | Contraseña: `Segura123*` |
| | Confirmar Contraseña: `Segura123*` |
| | Rol: `SST` |
| **Pasos** | 1. Ir a Usuarios → Nuevo Usuario <br> 2. Completar todos los campos <br> 3. Guardar |
| **Resultado Esperado** | Usuario creado. Puede iniciar sesión. Rol SST asignado correctamente. |
| **Perfil** | ADMIN |

---

### CP-USR-002: Usuario duplicado
| Campo | Valor |
|-------|-------|
| **ID** | CP-USR-002 |
| **Descripción** | Verificar que no se permiten usuarios duplicados |
| **Precondiciones** | Usuario `crodriguez` ya existe |
| **Datos de Entrada** | Usuario: `crodriguez` (duplicado) |
| **Pasos** | 1. Intentar crear usuario con nombre de usuario existente <br> 2. Guardar |
| **Resultado Esperado** | Error: "El usuario ya existe" |
| **Perfil** | ADMIN |

---

### CP-USR-003: Email duplicado
| Campo | Valor |
|-------|-------|
| **ID** | CP-USR-003 |
| **Descripción** | Verificar que no se permiten emails duplicados |
| **Precondiciones** | Email `carlos.rodriguez@club.com` ya registrado |
| **Datos de Entrada** | Email: `carlos.rodriguez@club.com` |
| **Pasos** | 1. Intentar crear usuario con email existente <br> 2. Guardar |
| **Resultado Esperado** | Error: "El email ya está registrado" |
| **Perfil** | ADMIN |

---

### CP-USR-004: Desactivar usuario
| Campo | Valor |
|-------|-------|
| **ID** | CP-USR-004 |
| **Descripción** | Desactivar cuenta de usuario |
| **Precondiciones** | Usuario `crodriguez` existe y está activo |
| **Datos de Entrada** | Usuario: `crodriguez`, Activo: `No` |
| **Pasos** | 1. Ir a Usuarios <br> 2. Editar usuario `crodriguez` <br> 3. Cambiar estado a Inactivo <br> 4. Guardar |
| **Resultado Esperado** | Usuario desactivado. No puede iniciar sesión. |
| **Perfil** | ADMIN |

---

### CP-USR-005: Cambiar contraseña de usuario
| Campo | Valor |
|-------|-------|
| **ID** | CP-USR-005 |
| **Descripción** | Cambiar contraseña de otro usuario |
| **Precondiciones** | Usuario ADMIN autenticado |
| **Datos de Entrada** | Nueva contraseña: `NuevaPass456*` |
| **Pasos** | 1. Ir a Usuarios <br> 2. Editar usuario <br> 3. Ingresar nueva contraseña <br> 4. Confirmar contraseña <br> 5. Guardar |
| **Resultado Esperado** | Contraseña actualizada. Usuario puede acceder con nueva contraseña. |
| **Perfil** | ADMIN |

---

### CP-USR-006: Cambiar rol de usuario
| Campo | Valor |
|-------|-------|
| **ID** | CP-USR-006 |
| **Descripción** | Cambiar rol de SST a PORTERIA |
| **Precondiciones** | Usuario con rol SST existe |
| **Datos de Entrada** | Nuevo rol: `PORTERIA` |
| **Pasos** | 1. Editar usuario con rol SST <br> 2. Cambiar rol a PORTERIA <br> 3. Guardar <br> 4. Verificar que el usuario ve solo módulo Portería |
| **Resultado Esperado** | Rol actualizado. Permisos cambian inmediatamente. |
| **Perfil** | ADMIN |

---

## 12. PRUEBAS DE SEGURIDAD Y PERMISOS

### CP-SEG-001: Acceso no autorizado - SST a Usuarios
| Campo | Valor |
|-------|-------|
| **ID** | CP-SEG-001 |
| **Descripción** | Verificar que SST no puede acceder a gestión de usuarios |
| **Precondiciones** | Usuario SST autenticado |
| **Datos de Entrada** | URL: `/usuarios` |
| **Pasos** | 1. Autenticarse como SST <br> 2. Intentar acceder directamente a `/usuarios` |
| **Resultado Esperado** | Error 403 - Acceso denegado. Redirección a Dashboard. |
| **Perfil** | SST |

---

### CP-SEG-002: Acceso no autorizado - PORTERIA a Dashboard
| Campo | Valor |
|-------|-------|
| **ID** | CP-SEG-002 |
| **Descripción** | Verificar que PORTERIA no puede acceder al Dashboard general |
| **Precondiciones** | Usuario PORTERIA autenticado |
| **Datos de Entrada** | URL: `/dashboard` |
| **Pasos** | 1. Autenticarse como PORTERIA <br> 2. Intentar acceder a `/dashboard` |
| **Resultado Esperado** | Redirección a módulo de Portería. O error 403. |
| **Perfil** | PORTERIA |

---

### CP-SEG-003: Acceso no autorizado - PORTERIA a Vehículos
| Campo | Valor |
|-------|-------|
| **ID** | CP-SEG-003 |
| **Descripción** | Verificar que PORTERIA no puede gestionar vehículos |
| **Precondiciones** | Usuario PORTERIA autenticado |
| **Datos de Entrada** | URL: `/vehiculos` |
| **Pasos** | 1. Autenticarse como PORTERIA <br> 2. Intentar acceder a gestión de vehículos |
| **Resultado Esperado** | Error 403 - Acceso denegado. |
| **Perfil** | PORTERIA |

---

### CP-SEG-004: Acceso no autorizado - PORTERIA a Reportes
| Campo | Valor |
|-------|-------|
| **ID** | CP-SEG-004 |
| **Descripción** | Verificar que PORTERIA no puede acceder a reportes |
| **Precondiciones** | Usuario PORTERIA autenticado |
| **Datos de Entrada** | URL: `/reportes/vehiculos` |
| **Pasos** | 1. Autenticarse como PORTERIA <br> 2. Intentar acceder a reportes |
| **Resultado Esperado** | Error 403 - Acceso denegado. |
| **Perfil** | PORTERIA |

---

### CP-SEG-005: Menú según rol - ADMIN
| Campo | Valor |
|-------|-------|
| **ID** | CP-SEG-005 |
| **Descripción** | Verificar opciones de menú para ADMIN |
| **Precondiciones** | Usuario ADMIN autenticado |
| **Datos de Entrada** | N/A |
| **Pasos** | 1. Autenticarse como ADMIN <br> 2. Observar menú lateral |
| **Resultado Esperado** | Menú muestra: Dashboard, Vehículos, Conductores, Alertas, Reportes, Portería, Usuarios. |
| **Perfil** | ADMIN |

---

### CP-SEG-006: Menú según rol - SST
| Campo | Valor |
|-------|-------|
| **ID** | CP-SEG-006 |
| **Descripción** | Verificar opciones de menú para SST |
| **Precondiciones** | Usuario SST autenticado |
| **Datos de Entrada** | N/A |
| **Pasos** | 1. Autenticarse como SST <br> 2. Observar menú lateral |
| **Resultado Esperado** | Menú muestra: Dashboard, Vehículos, Conductores, Alertas, Reportes. NO muestra: Usuarios, Portería. |
| **Perfil** | SST |

---

### CP-SEG-007: Menú según rol - PORTERIA
| Campo | Valor |
|-------|-------|
| **ID** | CP-SEG-007 |
| **Descripción** | Verificar opciones de menú para PORTERIA |
| **Precondiciones** | Usuario PORTERIA autenticado |
| **Datos de Entrada** | N/A |
| **Pasos** | 1. Autenticarse como PORTERIA <br> 2. Observar menú |
| **Resultado Esperado** | Solo muestra: Portería (búsqueda de vehículos) y Alertas (solo ver). |
| **Perfil** | PORTERIA |

---

## 13. CASOS LÍMITE Y ERRORES

### CP-LIM-001: Vehículo sin documentos
| Campo | Valor |
|-------|-------|
| **ID** | CP-LIM-001 |
| **Descripción** | Comportamiento de vehículo sin documentos registrados |
| **Precondiciones** | Vehículo creado sin documentos |
| **Datos de Entrada** | Vehículo nuevo sin documentos |
| **Pasos** | 1. Crear vehículo solo con datos básicos <br> 2. Ver en listado <br> 3. Ver en Portería |
| **Resultado Esperado** | Muestra estado "SIN_DOCUMENTOS" o indicadores vacíos. No genera error. |
| **Perfil** | ADMIN, SST |

---

### CP-LIM-002: Conductor sin vehículo
| Campo | Valor |
|-------|-------|
| **ID** | CP-LIM-002 |
| **Descripción** | Comportamiento de conductor sin vehículo asignado |
| **Precondiciones** | Conductor existe sin vehículo |
| **Datos de Entrada** | Conductor sin asignación |
| **Pasos** | 1. Crear conductor sin asignar vehículo <br> 2. Verificar en listado |
| **Resultado Esperado** | Muestra "Sin vehículo asignado" en campo correspondiente. No genera error. |
| **Perfil** | ADMIN, SST |

---

### CP-LIM-003: Búsqueda sin resultados
| Campo | Valor |
|-------|-------|
| **ID** | CP-LIM-003 |
| **Descripción** | Comportamiento cuando búsqueda no encuentra resultados |
| **Precondiciones** | Ninguna |
| **Datos de Entrada** | Búsqueda: `XXXNOEXISTE999` |
| **Pasos** | 1. En cualquier listado <br> 2. Buscar texto inexistente |
| **Resultado Esperado** | Mensaje: "No se encontraron resultados". Tabla vacía. No genera error. |
| **Perfil** | ADMIN, SST, PORTERIA |

---

### CP-LIM-004: Paginación última página
| Campo | Valor |
|-------|-------|
| **ID** | CP-LIM-004 |
| **Descripción** | Navegación a última página de resultados |
| **Precondiciones** | Más de 30 registros (3 páginas) |
| **Datos de Entrada** | N/A |
| **Pasos** | 1. Ir a listado de vehículos <br> 2. Navegar a última página |
| **Resultado Esperado** | Muestra registros restantes. Botón "Siguiente" deshabilitado. |
| **Perfil** | ADMIN, SST |

---

### CP-LIM-005: Caracteres especiales en búsqueda
| Campo | Valor |
|-------|-------|
| **ID** | CP-LIM-005 |
| **Descripción** | Búsqueda con caracteres especiales |
| **Precondiciones** | Ninguna |
| **Datos de Entrada** | Búsqueda: `<script>alert('XSS')</script>` |
| **Pasos** | 1. Ingresar texto con caracteres especiales en búsqueda <br> 2. Ejecutar búsqueda |
| **Resultado Esperado** | No ejecuta script. Muestra "Sin resultados" o escapa caracteres. Sin errores de seguridad. |
| **Perfil** | Cualquiera |

---

### CP-LIM-006: Campos requeridos vacíos
| Campo | Valor |
|-------|-------|
| **ID** | CP-LIM-006 |
| **Descripción** | Intentar guardar formulario con campos requeridos vacíos |
| **Precondiciones** | Ninguna |
| **Datos de Entrada** | Formulario de vehículo con placa vacía |
| **Pasos** | 1. Ir a crear vehículo <br> 2. Dejar campo Placa vacío <br> 3. Intentar guardar |
| **Resultado Esperado** | Validación del lado del cliente y/o servidor. Mensaje: "El campo placa es requerido". No se guarda. |
| **Perfil** | ADMIN, SST |

---

### CP-LIM-007: Fecha matrícula futura
| Campo | Valor |
|-------|-------|
| **ID** | CP-LIM-007 |
| **Descripción** | Intentar registrar fecha de matrícula futura |
| **Precondiciones** | Ninguna |
| **Datos de Entrada** | Fecha Matrícula: `2030-01-01` |
| **Pasos** | 1. En Tarjeta de Propiedad <br> 2. Ingresar fecha futura <br> 3. Guardar |
| **Resultado Esperado** | Error de validación: "La fecha de matrícula no puede ser futura" |
| **Perfil** | ADMIN, SST |

---

### CP-LIM-008: Transacción fallida - Rollback
| Campo | Valor |
|-------|-------|
| **ID** | CP-LIM-008 |
| **Descripción** | Verificar rollback cuando falla parte de la transacción |
| **Precondiciones** | Simular error en creación de documento |
| **Datos de Entrada** | Datos de vehículo válidos, documento con error |
| **Pasos** | 1. Crear vehículo con propietario <br> 2. En paso de documentos, provocar error <br> 3. Verificar que vehículo NO se creó |
| **Resultado Esperado** | Si documento falla, vehículo no queda guardado parcialmente. Transacción completa o nada. |
| **Perfil** | ADMIN (técnico) |

---

### CP-LIM-009: Sesión expirada
| Campo | Valor |
|-------|-------|
| **ID** | CP-LIM-009 |
| **Descripción** | Comportamiento con sesión expirada |
| **Precondiciones** | Sesión activa |
| **Datos de Entrada** | N/A |
| **Pasos** | 1. Autenticarse <br> 2. Esperar tiempo de expiración de sesión <br> 3. Intentar navegar |
| **Resultado Esperado** | Redirección a login. Mensaje: "Sesión expirada, por favor inicie sesión nuevamente" |
| **Perfil** | Cualquiera |

---

### CP-LIM-010: Eliminación con datos relacionados
| Campo | Valor |
|-------|-------|
| **ID** | CP-LIM-010 |
| **Descripción** | Eliminar vehículo que tiene conductor y documentos |
| **Precondiciones** | Vehículo con conductor asignado y documentos |
| **Datos de Entrada** | Vehículo con relaciones |
| **Pasos** | 1. Intentar eliminar vehículo con datos relacionados |
| **Resultado Esperado** | Soft delete exitoso. Conductor queda sin vehículo asignado. Documentos se eliminan (soft delete). |
| **Perfil** | ADMIN, SST |

---

## RESUMEN DE CASOS DE PRUEBA

| Módulo | Cantidad | IDs |
|--------|----------|-----|
| Autenticación | 5 | CP-AUTH-001 a CP-AUTH-005 |
| Vehículos | 10 | CP-VEH-001 a CP-VEH-010 |
| Conductores | 7 | CP-CON-001 a CP-CON-007 |
| Documentos | 8 | CP-DOC-001 a CP-DOC-008 |
| Alertas | 6 | CP-ALE-001 a CP-ALE-006 |
| Reportes | 7 | CP-REP-001 a CP-REP-007 |
| Portería | 5 | CP-POR-001 a CP-POR-005 |
| Usuarios | 6 | CP-USR-001 a CP-USR-006 |
| Seguridad | 7 | CP-SEG-001 a CP-SEG-007 |
| Casos Límite | 10 | CP-LIM-001 a CP-LIM-010 |
| **TOTAL** | **71** | |

---

## ANEXO: DATOS DE PRUEBA SUGERIDOS

### Usuarios de Prueba
| Usuario | Contraseña | Rol | Estado |
|---------|-----------|------|--------|
| admin@club.com | Admin123* | ADMIN | Activo |
| sst@club.com | SST123* | SST | Activo |
| porteria@club.com | Port123* | PORTERIA | Activo |
| inactivo@club.com | Pass123* | SST | Inactivo |

### Vehículos de Prueba
| Placa | Tipo | Propietario | Estado Docs |
|-------|------|-------------|-------------|
| ABC123 | Carro | Juan Pérez | VIGENTE |
| DEF456 | Moto | María García | POR_VENCER |
| GHI789 | Carro | Carlos López | VENCIDO |
| JKL012 | Moto | Ana Martínez | SIN_DOCS |

### Conductores de Prueba
| Nombre | Identificación | Vehículo | Licencia Estado |
|--------|---------------|----------|-----------------|
| Pedro Ramírez | 1098765432 | ABC123 | VIGENTE |
| Laura Sánchez | 52345678 | DEF456 | POR_VENCER |
| Diego Torres | 80123456 | - | VENCIDO |

---

## HISTORIAL DE VERSIONES

| Versión | Fecha | Autor | Cambios |
|---------|-------|-------|---------|
| 1.0 | Enero 2026 | Sistema | Versión inicial del plan de pruebas |

---

*Documento generado con base en el análisis del código fuente del Sistema de Control Vehicular.*
