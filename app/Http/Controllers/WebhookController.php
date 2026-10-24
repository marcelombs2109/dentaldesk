<?php

namespace App\Http\Controllers;

use App\Models\Recordatorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    public function whatsapp(Request $request)
    {
        $telefono = $this->normalizarTelefono((string) $request->input('From', ''));

        // SDD-04 R3: sin número de origen válido no se toca ningún dato.
        if (! $this->telefonoValido($telefono)) {
            Log::warning('Webhook WhatsApp ignorado: número de origen inválido.', [
                'from' => $request->input('From'),
            ]);

            return $this->respuestaVacia();
        }

        // SDD-04 R4: coincidencia exacta del número normalizado.
        $recordatorio = Recordatorio::whereHas('cita.paciente', function ($query) use ($telefono) {
                $query->whereRaw("regexp_replace(telefono_whatsapp, '[^0-9]', '', 'g') = ?", [$telefono]);
            })
            ->where('estado', 'enviado')
            ->latest()
            ->first();

        if (! $recordatorio) {
            return $this->respuestaVacia();
        }

        $respuesta = $this->normalizarRespuesta((string) $request->input('Body', ''));

        // SDD-04 R5: solo "sí" y "no" exactos cambian el estado.
        if ($respuesta === 'si') {
            $recordatorio->update([
                'estado'       => 'confirmado',
                'respuesta_wa' => $request->input('Body'),
            ]);
            $recordatorio->cita->update(['estado' => 'confirmada']);

            $this->registrarCambio('confirmada', $telefono, $request, $recordatorio);
        } elseif ($respuesta === 'no') {
            $recordatorio->update([
                'estado'       => 'rechazado',
                'respuesta_wa' => $request->input('Body'),
            ]);
            $recordatorio->cita->update(['estado' => 'cancelada']);

            $this->registrarCambio('cancelada', $telefono, $request, $recordatorio);
        } else {
            Log::info('Webhook WhatsApp: respuesta no reconocida, sin cambios.', [
                'telefono'  => $telefono,
                'respuesta' => $request->input('Body'),
                'cita_id'   => $recordatorio->cita_id,
            ]);
        }

        return $this->respuestaVacia();
    }

    /**
     * SDD-04 R6: cada cambio queda en el log con número, respuesta y cita.
     */
    private function registrarCambio(string $estado, string $telefono, Request $request, Recordatorio $recordatorio): void
    {
        Log::info("Webhook WhatsApp: cita {$estado}.", [
            'telefono'        => $telefono,
            'respuesta'       => $request->input('Body'),
            'cita_id'         => $recordatorio->cita_id,
            'recordatorio_id' => $recordatorio->id,
        ]);
    }

    private function normalizarTelefono(string $from): string
    {
        // "whatsapp:+59174272446" -> "59174272446"
        return preg_replace('/\D/', '', $from);
    }

    private function telefonoValido(string $telefono): bool
    {
        return strlen($telefono) >= 8 && strlen($telefono) <= 15;
    }

    /**
     * Minúsculas, sin tildes y sin espacios sobrantes; el resto se rechaza.
     */
    private function normalizarRespuesta(string $body): string
    {
        $texto = Str::lower(trim($body));

        return strtr($texto, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
        ]);
    }

    private function respuestaVacia()
    {
        return response('<Response></Response>')->header('Content-Type', 'text/xml');
    }
}
