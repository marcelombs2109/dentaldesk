<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * SDD-04 R1/R2: el webhook solo procesa peticiones firmadas por Twilio.
 *
 * Twilio firma con HMAC-SHA1 sobre la URL completa seguida de los parámetros
 * POST ordenados por nombre. Sin firma válida la petición se rechaza con 403
 * y no llega al controlador.
 */
class ValidarFirmaTwilio
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = (string) config('services.twilio.token');
        $firma = (string) $request->header('X-Twilio-Signature', '');

        if ($token === '' || $firma === '') {
            Log::warning('Webhook WhatsApp rechazado: falta la firma o el token de Twilio.', [
                'ip' => $request->ip(),
            ]);

            abort(403, 'Firma de Twilio ausente.');
        }

        $parametros = $request->post();
        ksort($parametros);

        $cadena = $request->fullUrl();
        foreach ($parametros as $clave => $valor) {
            $cadena .= $clave . (is_array($valor) ? implode('', $valor) : $valor);
        }

        $esperada = base64_encode(hash_hmac('sha1', $cadena, $token, true));

        if (! hash_equals($esperada, $firma)) {
            Log::warning('Webhook WhatsApp rechazado: firma inválida.', [
                'ip' => $request->ip(),
            ]);

            abort(403, 'Firma de Twilio inválida.');
        }

        return $next($request);
    }
}
