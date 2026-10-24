@extends('layouts.app')

@section('title', 'Nuevo paciente')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-bottom">
                <i class="bi bi-person-plus me-2 text-primary"></i>Registrar nuevo paciente
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('pacientes.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   placeholder="Ej: Juan">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="apellido" value="{{ old('apellido') }}"
                                   class="form-control @error('apellido') is-invalid @enderror"
                                   placeholder="Ej: Pérez">
                            @error('apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">CI / DNI <span class="text-danger">*</span></label>
                            <input type="text" name="ci" value="{{ old('ci') }}"
                                   class="form-control @error('ci') is-invalid @enderror"
                                   placeholder="Ej: 12345678">
                            @error('ci')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-whatsapp text-success me-1"></i>
                                Teléfono WhatsApp <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="telefono_whatsapp" value="{{ old('telefono_whatsapp') }}"
                                   class="form-control @error('telefono_whatsapp') is-invalid @enderror"
                                   placeholder="Ej: 59170012345">
                            @error('telefono_whatsapp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-muted small">(opcional)</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="Ej: juan@email.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de nacimiento <span class="text-muted small">(opcional)</span></label>
                            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"
                                   class="form-control @error('fecha_nacimiento') is-invalid @enderror">
                            @error('fecha_nacimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Observaciones médicas <span class="text-muted small">(opcional)</span></label>
                            <textarea name="observaciones" rows="3"
                                      class="form-control @error('observaciones') is-invalid @enderror"
                                      placeholder="Alergias, condiciones especiales...">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Registrar paciente
                        </button>
                        <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

@endsection