@extends('layouts.app')

@section('title', 'Agenda del día')

@section('content')

<div class="mb-3">
    {{-- Fila 1: navegación de fecha --}}
    <div class="d-flex align-items-center gap-2 mb-2">
        <a href="{{ route('agenda.index', ['fecha' => \Carbon\Carbon::parse($fecha)->subDay()->toDateString()]) }}"
           class="btn btn-outline-secondary btn-sm flex-shrink-0">
            <i class="bi bi-chevron-left"></i>
        </a>
        <span class="fw-semibold text-center flex-grow-1" style="font-size:0.88rem">
            <span class="d-none d-md-inline">
                {{ \Carbon\Carbon::parse($fecha)->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </span>
            <span class="d-md-none" style="font-size:0.8rem">
                {{ \Carbon\Carbon::parse($fecha)->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </span>
        </span>
        <a href="{{ route('agenda.index', ['fecha' => \Carbon\Carbon::parse($fecha)->addDay()->toDateString()]) }}"
           class="btn btn-outline-secondary btn-sm flex-shrink-0">
            <i class="bi bi-chevron-right"></i>
        </a>
    </div>

    {{-- Fila 2: selector de fecha + botón nueva cita --}}
    <div class="d-flex gap-2 mb-3">
        <form method="GET" action="{{ route('agenda.index') }}" class="d-flex gap-1 flex-grow-1">
            <input type="date" name="fecha" value="{{ $fecha }}" class="form-control form-control-sm flex-grow-1">
            <button class="btn btn-primary btn-sm px-3">Ir</button>
        </form>
        <a href="{{ route('citas.create') }}" class="btn btn-success btn-sm flex-shrink-0">
            <i class="bi bi-plus-circle me-1"></i>Nueva
        </a>
    </div>

    {{-- Fila 3: leyenda --}}
    <div class="d-flex gap-2 flex-wrap mb-3">
        <span class="badge badge-pendiente px-2 py-1">Pendiente</span>
        <span class="badge badge-confirmada px-2 py-1">Confirmada</span>
        <span class="badge badge-cancelada px-2 py-1">Cancelada</span>
        <span class="badge badge-atendida px-2 py-1">Atendida</span>
    </div>
</div>

<div class="row g-3">
    {{-- Consultorio 1 --}}
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white fw-semibold py-2">
                <i class="bi bi-door-open me-2"></i>Consultorio 1
                <span class="badge bg-white text-primary ms-2">{{ $consultorio1->count() }}</span>
            </div>
            <div class="card-body p-2 p-md-3">
                @forelse($consultorio1 as $cita)
                    <div class="cita-card {{ $cita->estado }} mb-2">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold small">
                                    {{ substr($cita->hora_inicio,0,5) }} — {{ substr($cita->hora_fin,0,5) }}
                                    <span class="badge badge-{{ $cita->estado }} ms-1">{{ ucfirst($cita->estado) }}</span>
                                </div>
                                <div class="text-dark small text-truncate">
                                    <i class="bi bi-person me-1"></i>{{ $cita->paciente->nombre_completo }}
                                </div>
                                <div class="text-muted" style="font-size:0.78rem">
                                    <i class="bi bi-person-badge me-1"></i>{{ $cita->dentista->nombre }}
                                    · <i class="bi bi-clock me-1"></i>{{ $cita->duracion_minutos }} min
                                </div>
                                <div class="text-muted text-truncate" style="font-size:0.78rem">
                                    <i class="bi bi-chat-left-text me-1"></i>{{ $cita->motivo }}
                                </div>
                            </div>
                            <a href="{{ route('citas.show', $cita) }}"
                               class="btn btn-sm btn-outline-secondary flex-shrink-0">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-calendar-x fs-4 d-block mb-1"></i>
                        <small>Sin citas para este día</small>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Consultorio 2 --}}
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white fw-semibold py-2">
                <i class="bi bi-door-open me-2"></i>Consultorio 2
                <span class="badge bg-white text-info ms-2">{{ $consultorio2->count() }}</span>
            </div>
            <div class="card-body p-2 p-md-3">
                @forelse($consultorio2 as $cita)
                    <div class="cita-card {{ $cita->estado }} mb-2">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold small">
                                    {{ substr($cita->hora_inicio,0,5) }} — {{ substr($cita->hora_fin,0,5) }}
                                    <span class="badge badge-{{ $cita->estado }} ms-1">{{ ucfirst($cita->estado) }}</span>
                                </div>
                                <div class="text-dark small text-truncate">
                                    <i class="bi bi-person me-1"></i>{{ $cita->paciente->nombre_completo }}
                                </div>
                                <div class="text-muted" style="font-size:0.78rem">
                                    <i class="bi bi-person-badge me-1"></i>{{ $cita->dentista->nombre }}
                                    · <i class="bi bi-clock me-1"></i>{{ $cita->duracion_minutos }} min
                                </div>
                                <div class="text-muted text-truncate" style="font-size:0.78rem">
                                    <i class="bi bi-chat-left-text me-1"></i>{{ $cita->motivo }}
                                </div>
                            </div>
                            <a href="{{ route('citas.show', $cita) }}"
                               class="btn btn-sm btn-outline-secondary flex-shrink-0">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-calendar-x fs-4 d-block mb-1"></i>
                        <small>Sin citas para este día</small>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection