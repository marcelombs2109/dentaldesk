<?php

namespace App\Http\Controllers;

use App\Models\Recordatorio;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function whatsapp(Request $request)
    {
        $telefono = preg_replace('/\D/', '', $request->input('From', ''));
        $mensaje  = strtolower(trim($request->input('Body', '')));

        // Buscar el recordatorio más reciente del paciente por teléfono
        $recordatorio = Recordatorio::whereHas('cita.paciente', function ($q) use ($telefono) {
                $q->whereRaw("regexp_replace(telefono_whatsapp, '[^0-9]', '', 'g') LIKE ?", ["%{$telefono}%"]);
            })
            ->where('estado', 'enviado')
            ->latest()
            ->first();

        if (!$recordatorio) {
            return response('<Response></Response>')->header('Content-Type', 'text/xml');
        }

        if (str_contains($mensaje, 'si') || str_contains($mensaje, 'sí')) {
            $recordatorio->update(['estado' => 'confirmado', 'respuesta_wa' => $request->input('Body')]);
            $recordatorio->cita->update(['estado' => 'confirmada']);
        } elseif (str_contains($mensaje, 'no')) {
            $recordatorio->update(['estado' => 'rechazado', 'respuesta_wa' => $request->input('Body')]);
            $recordatorio->cita->update(['estado' => 'cancelada']);
        }

        return response('<Response></Response>')->header('Content-Type', 'text/xml');
    }
}