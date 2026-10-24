# SDD-01 · Autenticación

**Responde a:** H1, H8 (parcial). **Depende de:** nada. **Alimenta:** SDD-02, SDD-03.

## Problema
Ninguna ruta exige identificación: cualquiera puede ver, modificar y borrar datos de pacientes.

## Requisitos
- **R1.** Existe una pantalla de inicio de sesión (correo y contraseña) y una acción de cierre de sesión visible en el menú.
- **R2.** Toda ruta exige un usuario autenticado, excepto el login y el webhook de Twilio (que se protege según SDD-04).
- **R3.** Cada usuario tiene exactamente un rol: `administrador`, `recepcionista` u `odontologo`.
- **R4.** El rol `odontologo` queda vinculado a un doctor de la tabla `dentistas` (`dentista_id`, UUID, obligatorio para ese rol y vacío para los demás).
- **R5.** Los intentos de inicio de sesión fallidos están limitados (por ejemplo, 5 por minuto por correo e IP).
- **R6.** Al iniciar sesión se regenera el identificador de sesión y al cerrarla se invalida.
- **R7.** Los usuarios de trabajo (uno por rol) se crean con un seeder, con contraseñas de prueba.

## Regla de acceso
Sin sesión iniciada, cualquier petición a una ruta protegida redirige al login y no devuelve datos.

## Criterios de aceptación
1. Abrir `/pacientes` sin sesión muestra el login y no la lista.
2. Un `POST /pacientes/{id}` con método DELETE sin sesión no borra nada y redirige al login.
3. Credenciales incorrectas muestran un mensaje genérico (no revela si falló el correo o la contraseña).
4. Tras 5 intentos fallidos seguidos, el sistema bloquea temporalmente nuevos intentos.
5. Tras cerrar sesión, el botón "atrás" del navegador no permite ver datos protegidos.
6. `POST /webhook/whatsapp` sigue accesible sin sesión.

## Evidencia
- Antes: captura de `/pacientes` con la lista visible, sin sesión.
- Después: captura de la redirección al login; captura del bloqueo por intentos; captura del menú con el usuario y el cierre de sesión.

## Artefactos a crear o modificar
Migración que agrega `rol` y `dentista_id` a `users`; modelo `User`; controlador y vistas de login; grupo de rutas con middleware `auth`; menú del layout; seeder de usuarios; limitador de intentos.

## Trazabilidad
Hallazgos H1, H8. STRIDE: S, R. ISO/IEC 27001:2022 A.5.16, A.8.5. OWASP A07:2021.
