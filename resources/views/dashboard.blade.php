@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="row g-2 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card card-stat h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:36px;height:36px;background:#e8f4ff">
                        <i class="bi bi-calendar-check text-primary"></i>
                    </div>
                    <div class="fs-2 fw-bold text-primary lh-1">{{ $stats['total'] }}</div>
                </div>
                <div class="text-muted small">Citas hoy</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card card-stat h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:36px;height:36px;background:#e8f9f0">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div class="fs-2 fw-bold text-success lh-1">{{ $stats['confirmadas'] }}</div>
                </div>
                <div class="text-muted small">Confirmadas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card card-stat h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:36px;height:36px;background:#fff8e1">
                        <i class="bi bi-clock text-warning"></i>
                    </div>
                    <div class="fs-2 fw-bold text-warning lh-1">{{ $stats['pendientes'] }}</div>
                </div>
                <div class="text-muted small">Pendientes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card card-stat h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:36px;height:36px;background:#ffeaea">
                        <i class="bi bi-x-circle text-danger"></i>
                    </div>
                    <div class="fs-2 fw-bold text-danger lh-1">{{ $stats['canceladas'] }}</div>
                </div>
                <div class="text-muted small">Canceladas</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    <!-- Accesos rápidos -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-bottom">
                <i class="bi bi-lightning me-2 text-warning"></i>Acciones rápidas
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('citas.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Nueva cita
                </a>
                <a href="{{ route('pacientes.create') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-person-plus me-2"></i>Nuevo paciente
                </a>
                <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-calendar3 me-2"></i>Ver agenda de hoy
                </a>
            </div>
        </div>
    </div>

    <!-- Buscar paciente -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-bottom">
                <i class="bi bi-search me-2 text-primary"></i>Buscar paciente
            </div>
            <div class="card-body">
                <div class="input-group input-group-lg mb-3">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                           id="busqueda-dashboard"
                           class="form-control border-start-0"
                           placeholder="Nombre, apellido o CI...">
                </div>
                <div id="resultados-dashboard"></div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
const input = document.getElementById('busqueda-dashboard');
const resultados = document.getElementById('resultados-dashboard');
let timer;

input.addEventListener('input', function () {
    clearTimeout(timer);
    const q = this.value.trim();

    if (q.length < 2) {
        resultados.innerHTML = '';
        return;
    }

    timer = setTimeout(() => {
        fetch(`/pacientes/buscar?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(pacientes => {
                if (pacientes.length === 0) {
                    resultados.innerHTML = `
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-person-x fs-4 d-block mb-1"></i>
                            No se encontró ningún paciente.
                            <a href="/pacientes/create" class="d-block mt-2">Registrar nuevo paciente</a>
                        </div>`;
                    return;
                }

                resultados.innerHTML = pacientes.map(p => `
                    <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-2 bg-light">
                        <div>
                            <strong>${p.nombre} ${p.apellido}</strong>
                            <span class="text-muted small ms-2">CI: ${p.ci}</span>
                            <span class="text-muted small ms-2">
                                <i class="bi bi-whatsapp text-success"></i> ${p.telefono_whatsapp}
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="/pacientes/${p.id}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="/citas/create?paciente_id=${p.id}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus"></i> Cita
                            </a>
                        </div>
                    </div>
                `).join('');
            });
    }, 300);
});
</script>
@endpush