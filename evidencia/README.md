# Evidencia — control de acceso en DentalDesk

Este directorio contiene la materia prima de la sección **Resultados**: las
capturas y transcripciones del sistema **antes** y **después** de implementar
el paquete SDD (`docs/sdd/`).

- **Antes:** estado original del proyecto, sin autenticación ni autorización.
- **Después:** misma aplicación con SDD-01 a SDD-04 implementadas.

## Entorno de la evidencia

| Elemento | Valor |
|---|---|
| Aplicación | Laravel 11 · PHP 8.3 |
| URL | `http://dentaldesk.sitios.mb` (Apache + PostgreSQL `dentaldesk_db`) |
| Navegador | Google Chrome 147, modo headless, ventana 1440×900 |
| Fecha de captura | 24 de octubre de 2026 |

Las capturas se tomaron con Chrome headless y las peticiones HTTP con `curl`.
Los archivos `transcripcion-*.txt` guardan el código de respuesta real de cada
prueba para que las imágenes no sean la única fuente.

## Usuarios de prueba

Creados por `UsuarioSeeder`. Contraseña para todos: `password`.

| Correo | Rol | Vínculo |
|---|---|---|
| `admin@dentaldesk.test` | administrador | — |
| `recepcion@dentaldesk.test` | recepcionista | — |
| `garcia@dentaldesk.test` | odontólogo | Dr. García |
| `perez@dentaldesk.test` | odontólogo | Dr. Perez |
| `mayta@dentaldesk.test` | odontólogo | Dra. Mayta |

Para regenerarlos: `php artisan db:seed --class=UsuarioSeeder`
(el seeder es idempotente; no ejecutar `db:seed` completo porque `DentistaSeeder`
y `ConsultorioSeeder` insertan filas nuevas en cada corrida).

## Índice de capturas

### Antes (`evidencia/antes/`)

| Archivo | Qué muestra |
|---|---|
| `01-pacientes-sin-sesion.png` | Listado completo de pacientes sin iniciar sesión |
| `02-agenda-sin-sesion.png` | Agenda completa sin iniciar sesión |
| `03-formulario-creacion-sin-sesion.png` | Formulario de alta de paciente abierto sin sesión |
| `04-paciente-creado-sin-sesion.png` | Paciente «Prueba Evidencia» creado sin sesión |
| `05-paciente-en-listado-sin-sesion.png` | El paciente creado aparece en el listado |
| `06-paciente-borrado-sin-sesion.png` | El paciente fue borrado sin sesión |
| `07-agenda-webhook-antes.png` | Cita de Irene Mayta (11:00) en estado *Pendiente* |
| `08-agenda-webhook-sin-firma.png` | La misma cita en *Cancelada* tras un POST sin firma |
| `transcripcion-webhook.txt` | `POST /webhook/whatsapp` sin firma → **200** |

### Después (`evidencia/despues/`)

| Archivo | Qué muestra |
|---|---|
| `01-login.png` | Pantalla de inicio de sesión |
| `02-redirect-pacientes-sin-sesion.png` | `/pacientes` sin sesión redirige al login |
| `03-bloqueo-intentos.png` | Bloqueo tras cinco intentos fallidos |
| `04-menu-usuario.png` | Menú con usuario, rol y «Cerrar sesión» |
| `05-tras-logout.png` | Pantalla de login tras cerrar sesión |
| `06-403-recepcionista.png` | 403 al intentar borrar un paciente como recepcionista |
| `07-menu-recepcionista.png` | Menú y acciones de la recepcionista (sin borrar) |
| `08-menu-odontologo.png` | Menú y acciones del odontólogo (solo consulta) |
| `09-menu-administrador.png` | Menú y acciones del administrador (todas) |
| `10-pacientes-odontologo.png` | Dr. García ve solo sus cuatro pacientes |
| `11-agenda-odontologo.png` | Dr. García ve solo su cita del 10/05 |
| `12-agenda-recepcionista.png` | La recepcionista ve la agenda completa |
| `13-403-paciente-ajeno.png` | 403 al abrir la ficha de un paciente ajeno |
| `14-webhook-sin-firma-403.png` | El POST sin firma responde 403 |
| `15-webhook-sin-firma-sin-cambios.png` | La cita sigue *Pendiente* tras el intento |
| `16-webhook-firmado-confirma.png` | Un «Sí» firmado confirma la cita correcta |
| `17-dashboard-administrador.png` | Panel del administrador |
| `18-tras-logout-boton-atras.png` | El botón «atrás» ya no muestra datos protegidos |
| `19-credenciales-incorrectas.png` | Mensaje genérico ante credenciales inválidas |

## Comparación antes / después por hallazgo

| ID | Antes | Después | Evidencia |
|---|---|---|---|
| **H1** | Cualquiera con la URL usa el sistema | Toda ruta exige sesión; `/pacientes` redirige al login | `antes/01`, `despues/02`, `transcripcion-t3-t4.txt` |
| **H2** | No existe rol; nadie está limitado | Tres roles y matriz RBAC en `config/permisos.php` aplicada con Gates y `can:` | `despues/07`, `08`, `09`, `transcripcion-t5.txt` |
| **H3** | Borrar/modificar abierto a cualquiera | Recepcionista recibe 403 al borrar; solo el administrador borra | `antes/06`, `despues/06`, `transcripcion-t5.txt` |
| **H4** | El webhook sin firma altera citas | Sin `X-Twilio-Signature` válida responde 403 y no toca datos | `antes/08`, `despues/14`, `15`, `transcripcion-t8.txt` |
| **H5** | `From` vacío coincide con cualquier paciente (`LIKE '%%'`) | Solo se procesa un número de 8 a 15 dígitos con coincidencia exacta | `transcripcion-t8.txt` (caso 3) |
| **H6** | «buenos días» cancela porque contiene `no` | Solo «sí» y «no» exactos (sin distinguir mayúsculas ni tildes) | `transcripcion-t8.txt` (caso 4) |
| **H7** | CI, teléfono y observaciones de todos visibles | El odontólogo solo accede a sus pacientes y citas; el resto responde 403 | `despues/10`–`13`, `transcripcion-t7.txt` |
| **H8** | Sin trazabilidad | Cada cambio del webhook queda en el log con número, respuesta y cita; las acciones humanas quedan asociadas a un usuario autenticado. La auditoría completa de CRUD sigue fuera de alcance (cobertura parcial, como anticipa SDD-01) | `transcripcion-t8.txt` |

## Criterios de aceptación

### SDD-01 · Autenticación

| # | Criterio | Estado | Evidencia |
|---|---|---|---|
| 1 | `/pacientes` sin sesión muestra el login | Cumple | `despues/02`, `transcripcion-t3-t4.txt` |
| 2 | DELETE sin sesión no borra y redirige | Cumple | `transcripcion-t3-t4.txt` |
| 3 | Credenciales incorrectas dan mensaje genérico | Cumple | `despues/19` |
| 4 | Bloqueo tras cinco intentos | Cumple | `despues/03` |
| 5 | El botón «atrás» no muestra datos tras salir | Cumple | `despues/18` |
| 6 | El webhook sigue accesible sin sesión | Cumple | `transcripcion-t8.txt` (403 por firma, no redirección) |

### SDD-02 · Roles y permisos

| # | Criterio | Estado | Evidencia |
|---|---|---|---|
| 1 | Recepcionista recibe 403 al borrar y el paciente sigue existiendo | Cumple | `despues/06`, `transcripcion-t5.txt` |
| 2 | Recepcionista no accede a gestión de usuarios | Cumple (opción B) | `gestionar_usuarios` solo incluye a `administrador`; no existe pantalla, según la decisión documentada |
| 3 | El administrador borra un paciente | Cumple | `transcripcion-t9.txt` |
| 4 | El odontólogo recibe 403 al crear cita | Cumple | `transcripcion-t5.txt` |
| 5 | El menú de la recepcionista no muestra administración | Cumple | `despues/07`, `08`, `09` |
| 6 | Cambiar la matriz cambia el comportamiento sin tocar controladores | Cumple | `transcripcion-t9.txt`: `ver_agenda` restringido a administrador → recepcionista 403; al revertir → 200 |

### SDD-03 · Alcance del odontólogo

| # | Criterio | Estado | Evidencia |
|---|---|---|---|
| 1 | `/pacientes` de García no lista pacientes solo de otro doctor | Cumple | `despues/10` |
| 2 | Ficha de paciente ajeno responde 403 | Cumple | `despues/13` |
| 3 | `/agenda` de García solo muestra sus citas | Cumple | `despues/11` |
| 4 | Cambiar el estado de una cita ajena responde 403 | Cumple | `transcripcion-t7.txt` |
| 5 | `/pacientes/buscar` no devuelve pacientes ajenos | Cumple | `transcripcion-t7.txt` |
| 6 | La recepcionista ve la agenda completa | Cumple | `despues/12` |

### SDD-04 · Webhook de Twilio

| # | Criterio | Estado | Evidencia |
|---|---|---|---|
| 1 | POST sin firma responde 403 y no cambia nada | Cumple | `despues/14`, `15` |
| 2 | Firma inválida responde 403 | Cumple | `transcripcion-t8.txt` (caso 2) |
| 3 | `From` vacío no cambia nada | Cumple | `transcripcion-t8.txt` (caso 3) |
| 4 | «buenos días» no cambia el estado | Cumple | `transcripcion-t8.txt` (caso 4) |
| 5 | Un «Sí» firmado confirma la cita correcta | Cumple | `despues/16` |
| 6 | El cambio queda en el log | Cumple | `transcripcion-t8.txt` (extracto del log) |

## Pruebas automatizadas

```
php artisan test
```

`tests/Feature/ControlDeAccesoTest.php` verifica la redirección al login, la
protección de rutas, el formulario de login y el rechazo del webhook sin firma.

## Notas

- El paciente «Prueba Evidencia» (creado y borrado sin sesión en el «antes») y
  el paciente «Temporal Borrado» (prueba del administrador) quedaron con borrado
  lógico, como exige el flujo normal de la aplicación.
- La cita de Irene Mayta del 10/05 se usó para comparar el ataque del «antes»
  con el resultado del «después»; terminó **confirmada** por una respuesta
  legítima firmada.
- El token de Twilio no se imprime en ningún archivo de evidencia: las firmas se
  calcularon con `config('services.twilio.token')` dentro de la aplicación.
