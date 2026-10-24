# SDD-03 · Alcance de datos del odontólogo

**Responde a:** H7 (parcial). **Depende de:** SDD-01, SDD-02.

## Problema
Aunque exista un rol, el odontólogo vería los pacientes, la agenda y las citas de todos los doctores. Su función solo requiere los propios.

## Definiciones
- **Sus citas:** las citas con `dentista_id` igual al `dentista_id` de su usuario.
- **Sus pacientes:** los pacientes que tienen al menos una cita con ese doctor.
- **Su agenda:** las citas del día que pertenecen a ese doctor.

## Requisitos
- **R1.** El odontólogo solo lista y busca pacientes que cumplan "sus pacientes".
- **R2.** Abrir la ficha de un paciente ajeno responde 403 (no 404, para no ocultar el rol del control; se documenta la decisión).
- **R3.** La agenda y el panel del odontólogo muestran solo sus citas y sus cifras.
- **R4.** Ver una cita ajena o cambiar su estado responde 403.
- **R5.** El administrador y la recepcionista no tienen esta restricción.
- **R6.** La regla se aplica en el servidor (consulta y política), no solo ocultando botones.

## Regla de acceso
Un odontólogo accede a un paciente o a una cita solo si pertenece a su `dentista_id`.

## Criterios de aceptación
1. Con el Dr. García autenticado, `/pacientes` no lista a pacientes que solo tienen citas con el Dr. Pérez.
2. Con el Dr. García autenticado, abrir `/pacientes/{id}` de un paciente de otro doctor responde 403.
3. `/agenda` del Dr. García muestra únicamente sus citas.
4. `POST /citas/{cita}/estado` sobre una cita del Dr. Pérez, hecho por el Dr. García, responde 403.
5. `/pacientes/buscar` del Dr. García no devuelve pacientes ajenos.
6. La recepcionista ve la agenda completa.

## Evidencia
- Antes: captura de la agenda y la lista de pacientes completas visibles para cualquiera.
- Después: capturas con distintos doctores mostrando datos distintos y un 403 sobre un recurso ajeno.

## Artefactos a crear o modificar
Políticas de acceso para `Paciente` y `Cita`; filtros por doctor en `PacienteController`, `AgendaController` y `DashboardController`; verificación en `CitaController`.

## Trazabilidad
Hallazgo H7. STRIDE: I. Principio: mínimo privilegio. ISO/IEC 27001:2022 A.8.3. OWASP A01:2021.
