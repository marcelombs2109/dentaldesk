@extends('layouts.app')

@section('title', 'Detalle de cita')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-event me-2 text-primary"></i>Detalle de cita</span>
                <span class="badge badge-{{ $cita->estado }} px-3 py-2">{{ ucfirst($cita->estado) }}</span>
            </div>
            <div class="card-body p-4">

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="text-muted small">Paciente</div>
                        <div class="fw-semibold">{{ $cita->paciente->nombre_completo }}</div>
                        <div class="text-muted small">CI: {{ $cita->paciente->ci }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">WhatsApp</div>
                        <div>
                            <i class="bi bi-whatsapp text-success me-1"></i>
                            {{ $cita->paciente->telefono_whatsapp }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Doctor</div>
                        <div class="fw-semibold">{{ $cita->dentista->nombre }}</div>
                        <div class="text-muted small">{{ $cita->dentista->especialidad }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Consultorio</div>
                        <div class="fw-semibold">{{ $cita->consultorio->nombre }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Fecha</div>
                        <div class="fw-semibold">{{ $cita->fecha->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Horario</div>
                        <div class="fw-semibold">{{ substr($cita->hora_inicio,0,5) }} — {{ substr($cita->hora_fin,0,5) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Duración</div>
                        <div class="fw-semibold">{{ $cita->duracion_minutos }} minutos</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Motivo</div>
                        <div class="fw-semibold">{{ $cita->motivo }}</div>
                    </div>
                    @if($cita->recordatorio)
                    <div class="col-12">
                        <div class="text-muted small">Recordatorio WhatsApp</div>
                        <div>
                            <span class="badge bg-secondary">{{ ucfirst($cita->recordatorio->estado) }}</span>
                            <span class="text-muted small ms-2">
                                Enviado: {{ $cita->recordatorio->enviado_en?->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Cambiar estado -->
                @can('cambiar_estado_cita')
                <div class="border-top pt-4">
                    <div class="fw-semibold mb-3">Cambiar estado de la cita</div>
                    <form method="POST" action="{{ route('citas.estado', $cita) }}">
                        @csrf
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach(['pendiente', 'confirmada', 'cancelada', 'atendida'] as $estado)
                                <button type="submit"
                                        name="estado"
                                        value="{{ $estado }}"
                                        class="btn btn-sm {{ $cita->estado === $estado ? 'btn-secondary disabled' : 'btn-outline-secondary' }}">
                                    {{ ucfirst($estado) }}
                                </button>
                            @endforeach
                        </div>
                    </form>
                </div>
                @endcan

            </div>
            <div class="card-footer bg-white d-flex gap-2">
                <a href="{{ route('pacientes.show', $cita->paciente) }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-person me-1"></i>Ver paciente
                </a>
                <a href="{{ route('agenda.index', ['fecha' => $cita->fecha->format('Y-m-d')]) }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-calendar3 me-1"></i>Ver agenda del día
                </a>
            </div>
        </div>

    </div>
</div>

@endsection