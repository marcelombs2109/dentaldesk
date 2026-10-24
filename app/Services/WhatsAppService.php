<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\Recordatorio;
use Twilio\Rest\Client;
use Carbon\Carbon;

class WhatsAppService
{
    protected Client $twilio;
    protected string $from;

    public function __construct()
    {
        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->from = config('services.twilio.whatsapp_from');
    }

    public function enviarRecordatorio(Cita $cita): bool
    {
        $paciente  = $cita->paciente;
        $dentista  = $cita->dentista;
        $fecha     = Carbon::parse($cita->fecha)->isoFormat('dddd D [de] MMMM');
        $hora      = substr($cita->hora_inicio, 0, 5);
        $telefono  = 'whatsapp:+' . preg_replace('/\D/', '', $paciente->telefono_whatsapp);

        $mensaje = "Hola {$paciente->nombre}, le recordamos su cita dental:\n\n"
                 . "📅 {$fecha}\n"
                 . "⏰ {$hora}\n"
                 . "👨‍⚕️ {$dentista->nombre}\n\n"
                 . "¿Confirma su asistencia?\n"
                 . "Responda *SI* para confirmar o *NO* para cancelar.";

        try {
            $this->twilio->messages->create($telefono, [
                'from' => $this->from,
                'body' => $mensaje,
            ]);

            Recordatorio::create([
                'cita_id'    => $cita->id,
                'enviado_en' => now(),
                'estado'     => 'enviado',
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error("Error enviando WhatsApp a {$telefono}: " . $e->getMessage());
            return false;
        }
    }
}