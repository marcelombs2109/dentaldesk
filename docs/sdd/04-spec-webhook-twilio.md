# SDD-04 · Protección del webhook de WhatsApp (Twilio)

**Responde a:** H4, H5, H6. **Depende de:** nada (puede hacerse en paralelo).

## Problema
El webhook no puede usar el login (lo llama Twilio, una máquina) y por eso no tiene CSRF; pero hoy tampoco valida su origen. Cualquiera que conozca la URL puede confirmar o cancelar citas.

## Requisitos
- **R1.** El webhook solo procesa peticiones con una firma `X-Twilio-Signature` válida, calculada con el token de autenticación de Twilio.
- **R2.** Una petición sin firma válida responde 403 y no modifica ningún dato.
- **R3.** Si el número de origen (`From`) está vacío o no es un número válido, la petición se ignora.
- **R4.** El paciente se identifica por coincidencia exacta del número normalizado (no por `LIKE '%...%'`).
- **R5.** Solo se aceptan respuestas exactas "sí" y "no" (sin distinguir mayúsculas ni tildes); cualquier otro texto no cambia el estado.
- **R6.** Cada cambio de estado hecho por el webhook queda registrado en el log (número, respuesta y cita).

## Regla de acceso
El webhook no se autentica con usuario y rol: se autentica con la firma criptográfica de Twilio.

## Criterios de aceptación
1. Una petición `POST` sin la cabecera de firma responde 403 y no cambia ninguna cita.
2. Una petición con firma inválida responde 403.
3. Una petición con `From` vacío no cambia ninguna cita.
4. Un mensaje "buenos días" no cambia el estado de ninguna cita.
5. Una respuesta legítima "Sí" firmada por Twilio confirma la cita del paciente correcto.
6. El cambio queda en el log con número, respuesta y cita.

## Evidencia
- Antes: petición de prueba sin firma que cambia el estado de una cita (resultado visible en la agenda).
- Después: la misma petición responde 403 y la cita permanece igual; una respuesta legítima sigue funcionando.

## Artefactos a crear o modificar
Middleware de validación de firma para la ruta del webhook; ajustes en `WebhookController`; variable de entorno con el token de Twilio (ya existe `TWILIO_TOKEN`).

## Trazabilidad
Hallazgos H4, H5, H6. STRIDE: S, T. ISO/IEC 27001:2022 A.8.5. OWASP A07:2021.
