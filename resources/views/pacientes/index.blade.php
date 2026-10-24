@extends('layouts.app')

@section('title', 'Pacientes')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0 fw-semibold">Listado de pacientes</h5>
    @can('registrar_paciente')
        <a href="{{ route('pacientes.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i>Nuevo paciente
        </a>
    @endcan
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-2 p-md-3">
        <form method="GET" action="{{ route('pacientes.index') }}">
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="q" value="{{ $busqueda }}"
                       class="form-control border-start-0"
                       placeholder="Nombre, apellido o CI...">
                <button class="btn btn-primary">Buscar</button>
                @if($busqueda)
                    <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Vista móvil: cards --}}
<div class="d-md-none">
    @forelse($pacientes as $paciente)
        <div class="card border-0 shadow-sm mb-2">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-semibold">{{ $paciente->nombre_completo }}</div>
                        <div class="text-muted small">CI: {{ $paciente->ci }}</div>
                        <div class="small mt-1">
                            <i class="bi bi-whatsapp text-success me-1"></i>{{ $paciente->telefono_whatsapp }}
                        </div>
                        @if($paciente->email)
                            <div class="small text-muted">
                                <i class="bi bi-envelope me-1"></i>{{ $paciente->email }}
                            </div>
                        @endif
                    </div>
                    <div class="d-flex flex-column gap-1">
                        <a href="{{ route('pacientes.show', $paciente) }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                        @can('crear_cita')
                        <a href="{{ route('citas.create', ['paciente_id' => $paciente->id]) }}"
                           class="btn btn-sm btn-primary">
                            <i class="bi bi-plus"></i>
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-4">
            <i class="bi bi-person-x fs-3 d-block mb-2"></i>
            No se encontraron pacientes.
        </div>
    @endforelse
</div>

{{-- Vista desktop: tabla --}}
<div class="d-none d-md-block">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Paciente</th>
                        <th>CI</th>
                        <th>WhatsApp</th>
                        <th>Email</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pacientes as $paciente)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $paciente->nombre_completo }}</div>
                                @if($paciente->fecha_nacimiento)
                                    <div class="text-muted small">{{ $paciente->fecha_nacimiento->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td>{{ $paciente->ci }}</td>
                            <td>
                                <i class="bi bi-whatsapp text-success me-1"></i>
                                {{ $paciente->telefono_whatsapp }}
                            </td>
                            <td>{{ $paciente->email ?? '—' }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pacientes.show', $paciente) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('modificar_paciente')
                                    <a href="{{ route('pacientes.edit', $paciente) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('crear_cita')
                                    <a href="{{ route('citas.create', ['paciente_id' => $paciente->id]) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-plus"></i> Cita
                                    </a>
                                    @endcan
                                    @can('borrar_paciente')
                                    <form method="POST" action="{{ route('pacientes.destroy', $paciente) }}"
                                          onsubmit="return confirm('¿Eliminar a {{ $paciente->nombre_completo }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No se encontraron pacientes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pacientes->hasPages())
            <div class="card-footer bg-white">
                {{ $pacientes->appends(['q' => $busqueda])->links() }}
            </div>
        @endif
    </div>
</div>

@if($pacientes->hasPages())
    <div class="d-md-none mt-3">
        {{ $pacientes->appends(['q' => $busqueda])->links() }}
    </div>
@endif

@endsection