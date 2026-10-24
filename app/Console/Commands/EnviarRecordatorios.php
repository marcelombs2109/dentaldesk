<?php

namespace App\Console\Commands;

use App\Models\Cita;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EnviarRecordatorios extends Command
{
    protected $signature   = 'dentaldesk:recordatorios';
    protected $description = 'Envía recordatorios WhatsApp a pacientes con cita mañana';

    public function handle(WhatsAppService $whatsApp): void
    {
        $manana = Carbon::tomorrow()->toDateString();

        $citas = Cita::with(['paciente', 'dentista', 'consultorio'])
            ->where('fecha', $manana)
            ->where('estado', 'pendiente')
            ->whereDoesntHave('recordatorio')
            ->get();

        if ($citas->isEmpty()) {
            $this->info('No hay citas para mañana sin recordatorio.');
            return;
        }

        $this->info("Enviando {$citas->count()} recordatorio(s)...");

        foreach ($citas as $cita) {
            $ok = $whatsApp->enviarRecordatorio($cita);
            $estado = $ok ? '✅' : '❌';
            $this->line("{$estado} {$cita->paciente->nombre_completo} — {$cita->hora_inicio}");
        }

        $this->info('Proceso completado.');
    }
}