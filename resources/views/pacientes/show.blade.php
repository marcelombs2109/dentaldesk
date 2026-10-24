@extends('layouts.app')

@section('title', 'Ficha del paciente')

@section('content')

<div class="row g-4">

    <!-- Datos del paciente -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-bottom">
                <i class="bi bi-person-circle me-2 text-primary"></i>Datos del paciente
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:72px;height:72px">
                        <i class="bi bi-person-fill fs-2 text-white"></i>
                    </div>
                    <h5 class="fw-bold mb-0">{{ $paciente->nombre_completo }}</h5>
                    <span class="text-muted small">CI: {{ $paciente->ci }}</span>
                </div>

                <ul class="list-unstyled">
                    <li class="mb-3">
                        <div class="text-muted small">WhatsApp</div>
                        <div>
                            <i class="bi bi-whatsapp text-success me-1"></i>
                            {{ $paciente->telefono_whatsapp }}
                        </div>
                    </li>
                    @if($paciente->email)
                    <li class="mb-3">
                        <div class="text-muted small">Email</div>
                        <div><i class="bi bi-envelope me-1"></i>{{ $paciente->email }}</div>
                    </li>
                    @endif
                    @if($paciente->fecha_nacimiento)
                    <li class="mb-3">
                        <div class="text-muted small">Fecha de nacimiento</div>
                        <div><i class="bi bi-calendar me-1"></i>{{ $paciente->fecha_nacimiento->format('d/m/Y') }}</div>
                    </li>
                    @endif
                    @if($paciente->observaciones)
                    <li class="mb-3">
                        <div class="text-muted small">Observaciones</div>
                        <div class="text-dark">{{ $paciente->observaciones }}</div>
                    </li>
                    @endif
                </ul>

                <div class="d-flex flex-column gap-2 mt-3">
                    <a href="{{ route('citas.create', ['paciente_id' => $paciente->id]) }}"
                       class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Agendar cita
                    </a>
                    <a href="{{ route('pacientes.edit', $paciente) }}"
                       class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>Editar datos
                    </a>
                    <form method="POST" action="{{ route('pacientes.destroy', $paciente) }}"
                          onsubmit="return confirm('¿Eliminar a {{ $paciente->nombre_completo }}?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-2"></i>Eliminar paciente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de citas -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar3 me-2 text-primary"></i>Historial de citas</span>
                <span class="badge bg-primary">{{ $citas->count() }}</span>
            </div>
            <div class="card-body">
                @forelse($citas as $cita)
                    <div class="cita-card {{ $cita->estado }} mb-3">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold small">
                                    {{ $cita->fecha->format('d/m/Y') }}
                                    — {{ substr($cita->hora_inicio,0,5) }} a {{ substr($cita->hora_fin,0,5) }}
                                    <span class="badge badge-{{ $cita->estado }} ms-1">
                                        {{ ucfirst($cita->estado) }}
                                    </span>
                                </div>
                                <div class="text-muted mt-1" style="font-size:0.78rem">
                                    <i class="bi bi-person-badge me-1"></i>{{ $cita->dentista->nombre }}
                                    · <i class="bi bi-door-open me-1"></i>{{ $cita->consultorio->nombre }}
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
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                        Este paciente no tiene citas registradas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection