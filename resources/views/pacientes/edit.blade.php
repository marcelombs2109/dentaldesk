@extends('layouts.app')

@section('title', 'Editar paciente')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-bottom">
                <i class="bi bi-pencil me-2 text-primary"></i>Editar — {{ $paciente->nombre_completo }}
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('pacientes.update', $paciente) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" value="{{ old('nombre', $paciente->nombre) }}"
                                   class="form-control @error('nombre') is-invalid @enderror">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="apellido" value="{{ old('apellido', $paciente->apellido) }}"
                                   class="form-control @error('apellido') is-invalid @enderror">
                            @error('apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">CI / DNI <span class="text-danger">*</span></label>
                            <input type="text" name="ci" value="{{ old('ci', $paciente->ci) }}"
                                   class="form-control @error('ci') is-invalid @enderror">
                            @error('ci')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-whatsapp text-success me-1"></i>
                                Teléfono WhatsApp <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="telefono_whatsapp"
                                   value="{{ old('telefono_whatsapp', $paciente->telefono_whatsapp) }}"
                                   class="form-control @error('telefono_whatsapp') is-invalid @enderror">
                            @error('telefono_whatsapp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-muted small">(opcional)</span></label>
                            <input type="email" name="email" value="{{ old('email', $paciente->email) }}"
                                   class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de nacimiento <span class="text-muted small">(opcional)</span></label>
                            <input type="date" name="fecha_nacimiento"
                                   value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento?->format('Y-m-d')) }}"
                                   class="form-control @error('fecha_nacimiento') is-invalid @enderror">
                            @error('fecha_nacimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Observaciones médicas <span class="text-muted small">(opcional)</span></label>
                            <textarea name="observaciones" rows="3"
                                      class="form-control @error('observaciones') is-invalid @enderror">{{ old('observaciones', $paciente->observaciones) }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Guardar cambios
                        </button>
                        <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection