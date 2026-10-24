# 00 · Diagnóstico del estado inicial de DentalDesk ("antes")

**Sistema analizado:** DentalDesk, sistema de gestión de citas odontológicas (Laravel 11, PostgreSQL, vistas Blade).
**Método:** revisión estática del código (rutas, controladores, modelos, servicios, migraciones, vistas y configuración) y comprobación en el sistema en ejecución (capturas en `evidencia/antes/`).
**Alcance:** control de acceso (autenticación, autorización y trazabilidad). Los demás aspectos de seguridad quedan fuera del trabajo.

## Superficie de exposición

| N.º | Endpoint | Controlador | ¿Pide autenticación? | Datos o acción |
|---|---|---|---|---|
| 1 | `GET /` | DashboardController@index | No | Conteo de citas del día por estado |
| 2 | `GET /agenda` | AgendaController@index | No | Citas del día con paciente, doctor y consultorio |
| 3 | `GET /pacientes/buscar` | PacienteController@buscar | No | JSON con nombre, apellido, CI y teléfono |
| 4 | `GET /pacientes`, `GET /pacientes/{id}` | PacienteController | No | Lista y ficha completa (incluye observaciones) |
| 5 | `POST /pacientes`, `PUT /pacientes/{id}` | PacienteController | No | Crea y modifica datos personales |
| 6 | `DELETE /pacientes/{id}` | PacienteController@destroy | No | Eliminación lógica (SoftDeletes) |
| 7 | `GET /citas/disponibilidad` | CitaController@disponibilidad | No | JSON con doctores y consultorios libres |
| 8 | `GET /citas/create`, `POST /citas`, `GET /citas/{id}` | CitaController | No | Crea y consulta citas |
| 9 | `POST /citas/{cita}/estado` | CitaController@cambiarEstado | No | Cambia el estado de cualquier cita |
| 10 | `POST /webhook/whatsapp` | WebhookController@whatsapp | No (CSRF desactivado) | Confirma o cancela citas según el mensaje recibido |

## Hallazgos

| ID | Hallazgo | Evidencia en el código | Severidad | STRIDE | Referencia |
|---|---|---|---|---|---|
| H1 | Ninguna ruta exige identificación. Cualquier persona con acceso a la dirección usa todo el sistema. | `routes/web.php` sin middleware `auth`; `bootstrap/app.php` con `withMiddleware` vacío; no existen rutas ni vistas de login. | Alta | S, T, I | OWASP A07:2021; ISO/IEC 27001:2022 A.8.5 |
| H2 | No existe el concepto de rol. Todo usuario (si lo hubiera) podría hacerlo todo. | Tabla `users` sin campo de rol ni vínculo con `dentistas`; no hay Policies ni Gates; `AppServiceProvider` no define autorización. | Alta | E | OWASP A01:2021; ISO/IEC 27001:2022 A.5.15, A.5.18 |
| H3 | Acciones destructivas y de modificación abiertas a cualquiera. | `PacienteController@destroy` y `@update`; `CitaController@cambiarEstado`. El borrado es lógico (SoftDeletes), pero no hay interfaz para restaurar ni registro de quién lo hizo. | Alta | T, R | OWASP A01:2021; ISO/IEC 27001:2022 A.8.3 |
| H4 | El webhook de WhatsApp no valida que la petición venga de Twilio. | `WebhookController@whatsapp` no usa la firma `X-Twilio-Signature`; la ruta quita el CSRF (correcto) pero no lo reemplaza. | Alta | S, T | OWASP A07:2021; ISO/IEC 27001:2022 A.8.5 |
| H5 | El webhook puede alterar citas sin teléfono válido: si `From` llega vacío, el filtro `LIKE '%%'` coincide con cualquier paciente y se actualiza el recordatorio más reciente. | `WebhookController@whatsapp`, consulta con `regexp_replace(...) LIKE %{$telefono}%`. | Alta | T | OWASP A01:2021 |
| H6 | La interpretación de la respuesta es ambigua: `str_contains($mensaje, 'no')` o `'si'` coincide con palabras como "buenos" o "sin". | `WebhookController@whatsapp`. | Media | T | Calidad de entrada (fuera del eje RBAC; se corrige junto con H4) |
| H7 | Exposición de datos personales sin necesidad de rol: CI, teléfono y observaciones. | `PacienteController@buscar` devuelve `ci` y `telefono_whatsapp`; `pacientes/show` muestra observaciones. | Media | I | OWASP A01:2021; ISO/IEC 27001:2022 A.8.3 |
| H8 | No hay trazabilidad: no se registra quién crea, modifica, cancela o borra. | Ningún `Log` ni auditoría en controladores ni servicios; sin usuarios no es posible. | Media | R | ISO/IEC 27001:2022 A.8.15 |

## Controles ya presentes (punto de partida)

- Las contraseñas se almacenan con hash (`'password' => 'hashed'` en `User.php`).
- Existen las tablas `users` y `sessions`, y el guard `web` por sesión está configurado en `config/auth.php`.
- Todos los formularios que modifican datos incluyen `@csrf`.
- Las entradas se validan con `$request->validate(...)` en pacientes y citas.
- Cookies de sesión con `http_only` activo y `same_site=lax` (`config/session.php`).
- Pacientes, citas y demás tablas usan UUID como clave, lo que dificulta adivinar identificadores (no reemplaza a la autorización).

## Nota sobre la referencia a normas

Las referencias a OWASP Top 10 (2021) e ISO/IEC 27001:2022 (Anexo A) son orientativas. Antes de citarlas en el informe, confirmar la numeración y el texto en las fuentes oficiales y citarlas en APA 7.
