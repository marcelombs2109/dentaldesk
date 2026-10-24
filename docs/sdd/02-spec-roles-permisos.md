# SDD-02 · Roles y permisos (matriz RBAC)

**Responde a:** H2, H3, H7. **Depende de:** SDD-01.

## Problema
No existe el concepto de rol: quien accede puede ver, modificar y borrar todo, sin distinguir funciones.

## Requisitos
- **R1.** El sistema define tres roles: administrador, recepcionista y odontólogo.
- **R2.** Los permisos de cada rol se declaran en un único archivo de configuración (la matriz hecha código), no dispersos en cada controlador.
- **R3.** Cada ruta verifica el permiso del rol antes de ejecutar la acción.
- **R4.** Ante la duda, el permiso se niega (mínimo privilegio).
- **R5.** Una acción no permitida responde 403 con un mensaje claro y no ejecuta nada.
- **R6.** Las opciones del menú y los botones no permitidos no se muestran al rol correspondiente.

## Matriz de permisos
| Recurso y acción | Administrador | Recepcionista | Odontólogo |
|---|---|---|---|
| Ver lista y ficha de pacientes | Sí | Sí | Solo sus pacientes* |
| Registrar paciente | Sí | Sí | No |
| Modificar datos de paciente | Sí | Sí | No |
| Borrar paciente | Sí | No | No |
| Ver agenda | Toda | Toda | Solo la suya* |
| Consultar disponibilidad | Sí | Sí | Sí |
| Crear cita | Sí | Sí | No |
| Ver cita | Sí | Sí | Solo las suyas* |
| Cambiar estado de cita | Sí | Sí | Solo las suyas* |
| Gestionar usuarios y roles | Sí | No | No |
| Configurar doctores, turnos y consultorios | Sí | No | No |

\* Permiso condicionado: ver SDD-03.

## Correspondencia con las rutas actuales
| Ruta | Permiso que la protege |
|---|---|
| `GET /` | Todos los roles autenticados (el odontólogo ve solo sus cifras) |
| `GET /agenda` | Ver agenda |
| `GET /pacientes/buscar` | Ver lista y ficha de pacientes |
| `GET /pacientes`, `GET /pacientes/{id}` | Ver lista y ficha de pacientes |
| `GET /pacientes/create`, `POST /pacientes` | Registrar paciente |
| `GET /pacientes/{id}/edit`, `PUT /pacientes/{id}` | Modificar datos de paciente |
| `DELETE /pacientes/{id}` | Borrar paciente |
| `GET /citas/disponibilidad` | Consultar disponibilidad |
| `GET /citas/create`, `POST /citas` | Crear cita |
| `GET /citas/{id}` | Ver cita |
| `POST /citas/{cita}/estado` | Cambiar estado de cita |

## Regla de acceso
Cada petición se evalúa con el rol del usuario autenticado contra la matriz. Sin permiso, la respuesta es 403.

## Criterios de aceptación
1. La recepcionista intenta `DELETE /pacientes/{id}` y recibe 403; el paciente sigue existiendo.
2. La recepcionista intenta abrir la gestión de usuarios y recibe 403.
3. El administrador borra un paciente y la acción se ejecuta.
4. El odontólogo intenta `POST /citas` y recibe 403.
5. El menú de la recepcionista no muestra opciones de administración.
6. Cambiar un permiso en el archivo de configuración cambia el comportamiento sin editar controladores.

## Evidencia
- Antes: captura del botón de borrar disponible para cualquiera y del borrado ejecutado.
- Después: capturas de los 403 por rol y de los menús distintos por rol.

## Decisión tomada (estudiante)
Las filas "Gestionar usuarios y roles" y "Configurar doctores, turnos y consultorios" no tienen hoy ninguna ruta ni pantalla en DentalDesk. Se eligió la **opción B**: se mantienen en el diseño de la matriz, los usuarios se crean con seeder, y la pantalla de administración queda documentada como trabajo futuro. Por eso los criterios de aceptación 2 y 3 se verifican sobre la matriz y las rutas existentes, no sobre una pantalla de usuarios.

## Trazabilidad
Hallazgos H2, H3, H7. STRIDE: E, T, I. ISO/IEC 27001:2022 A.5.15, A.5.18, A.8.2, A.8.3. OWASP A01:2021.
