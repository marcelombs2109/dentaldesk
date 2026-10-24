# 05 · Plan de implementación (orden de trabajo sobre `dentaldesk-despues`)

**Cómo usar estos archivos:** copiar la carpeta `sdd/` dentro del proyecto del "después" (por ejemplo en `docs/sdd/`). Cada tarea cita la especificación que cumple y los criterios que la validan. Una tarea se da por terminada solo cuando sus criterios de aceptación se comprueban y se toma la captura de evidencia.

**Reglas de trabajo**
- No se modifica el proyecto del "antes".
- No se abre ni se versiona el archivo `.env`.
- Cada tarea se hace en un commit propio, con el identificador de la especificación en el mensaje (por ejemplo, `SDD-01: login y logout`).
- No se agrega funcionalidad que no esté en una especificación.

## Decisiones de diseño ya tomadas

| Decisión | Motivo |
|---|---|
| El rol se guarda como columna `rol` en `users` con tres valores fijos | Son tres roles estables; un paquete de permisos agrega complejidad sin beneficio |
| Valor por defecto de `rol`: `odontologo` (el de menor alcance) | Ante un olvido, el sistema falla hacia lo seguro |
| `rol` no está en `$fillable` | Evita la asignación masiva y el auto-ascenso de privilegios |
| `users.id` sigue siendo numérico; `dentista_id` es UUID | Cambiar la clave de usuarios obliga a tocar `sessions`; no aporta al tema |
| Los permisos viven en un único archivo `config/permisos.php` | La matriz queda en un solo lugar y es auditable |
| Los usuarios se crean con seeder; no hay pantalla de administración | Opción B de la SDD-02 |
| El webhook se protege con firma de Twilio, no con usuario y rol | Es una máquina y no puede iniciar sesión |

## Tareas

| N.º | Tarea | Especificación | Archivos principales | Se valida con |
|---|---|---|---|---|
| T1 | Migración que agrega `rol` y `dentista_id` a `users`; constantes y relación en `User` | SDD-01 R3, R4 | `database/migrations/*agregar_rol_y_dentista_a_users*`, `app/Models/User.php` | `php artisan migrate` sin errores; columnas visibles en la base |
| T2 | Seeder de usuarios de trabajo: un administrador, una recepcionista y un odontólogo por cada doctor existente (vinculado por nombre) | SDD-01 R7 | `database/seeders/UsuarioSeeder.php`, `DatabaseSeeder.php` | Los usuarios existen con el rol y el `dentista_id` correctos |
| T3 | Login y logout: controlador, vista, rutas, limitador de intentos, regeneración de sesión; menú con usuario y botón de salir | SDD-01 R1, R5, R6 | `app/Http/Controllers/AuthController.php`, `resources/views/auth/login.blade.php`, `routes/web.php`, `layouts/app.blade.php` | SDD-01 criterios 3, 4, 5 |
| T4 | Proteger todas las rutas con `auth`, excepto login y webhook | SDD-01 R2 | `routes/web.php` | SDD-01 criterios 1, 2, 6 |
| T5 | Archivo de permisos y verificación por ruta (Gates definidos desde `config/permisos.php`, middleware `can:`); respuesta 403 con vista propia | SDD-02 R2 a R5 | `config/permisos.php`, `app/Providers/AppServiceProvider.php`, `routes/web.php`, `resources/views/errors/403.blade.php` | SDD-02 criterios 1 a 4 y 6 |
| T6 | Menú y botones según el rol (`@can`) | SDD-02 R6 | `layouts/app.blade.php`, vistas de pacientes y citas | SDD-02 criterio 5 |
| T7 | Políticas de alcance del odontólogo y filtros por doctor en pacientes, agenda, panel y citas | SDD-03 R1 a R6 | `app/Policies/PacientePolicy.php`, `app/Policies/CitaPolicy.php`, controladores | SDD-03 criterios 1 a 6 |
| T8 | Webhook: validación de firma de Twilio, número exacto, respuestas exactas, registro en log | SDD-04 R1 a R6 | `app/Http/Middleware/ValidarFirmaTwilio.php`, `WebhookController.php`, `routes/web.php` | SDD-04 criterios 1 a 6 |
| T9 | Recorrido final por rol y capturas del "después" | Todas | `evidencia/despues/` | Tabla de comparación antes y después completa |

## Evidencia a capturar por tarea

| Tarea | Captura del "antes" | Captura del "después" |
|---|---|---|
| T3, T4 | `/pacientes` con la lista visible sin sesión | Redirección al login; bloqueo por intentos |
| T5, T6 | Botón de borrar disponible y borrado ejecutado | 403 de la recepcionista al borrar; menús distintos por rol |
| T7 | Agenda y pacientes completos para cualquiera | Dr. García solo ve lo suyo; 403 sobre un recurso ajeno |
| T8 | Petición sin firma que cambia una cita | La misma petición responde 403 y la cita no cambia |

## Orden y dependencias
T1 → T2 → T3 → T4 → T5 → T6 → T7. La tarea T8 es independiente y puede hacerse en cualquier momento. T9 va al final.
