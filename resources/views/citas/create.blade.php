@extends('layouts.app')

@section('title', 'Nueva cita')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-bottom">
                <i class="bi bi-plus-circle me-2 text-primary"></i>Agendar nueva cita
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('citas.store') }}" id="form-cita">
                    @csrf

                    <!-- Paciente -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Paciente <span class="text-danger">*</span>
                        </label>

                        @if($paciente)
                            <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width:32px;height:32px">
                                        <i class="bi bi-person-fill text-white" style="font-size:0.8rem"></i>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-semibold text-truncate" style="font-size:0.85rem">
                                            {{ $paciente->nombre_completo }}
                                        </div>
                                        <div class="text-muted" style="font-size:0.75rem">CI: {{ $paciente->ci }}</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('citas.create') }}" class="btn btn-sm btn-outline-secondary w-100">
                                        <i class="bi bi-arrow-left-right me-1"></i>Cambiar paciente
                                    </a>
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="paciente_id" id="paciente_id">
                            <input type="text"
                                   id="busqueda-paciente"
                                   class="form-control @error('paciente_id') is-invalid @enderror"
                                   placeholder="Buscar por nombre, apellido o CI...">
                            @error('paciente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="resultados-paciente" class="mt-2"></div>
                            <div id="paciente-seleccionado" class="mt-2 d-none"></div>
                            <div class="mt-2">
                                <a href="{{ route('pacientes.create') }}" class="text-primary small">
                                    <i class="bi bi-person-plus me-1"></i>Registrar nuevo paciente
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="row g-3">

                        <!-- Fecha -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha <span class="text-danger">*</span></label>
                            <input type="date"
                                   name="fecha"
                                   id="fecha"
                                   value="{{ old('fecha') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="form-control @error('fecha') is-invalid @enderror">
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hora — el JS lo reemplaza por un select 24h -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hora de inicio <span class="text-danger">*</span></label>
                            <select name="hora_inicio"
                                    id="hora_inicio"
                                    class="form-select @error('hora_inicio') is-invalid @enderror">
                                <option value="">Seleccionar hora...</option>
                            </select>
                            @error('hora_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duración -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Duración <span class="text-danger">*</span></label>
                            <select name="duracion_minutos"
                                    id="duracion_minutos"
                                    class="form-select @error('duracion_minutos') is-invalid @enderror">
                                <option value="">Seleccionar...</option>
                                @foreach($duraciones as $valor => $etiqueta)
                                    <option value="{{ $valor }}" {{ old('duracion_minutos') == $valor ? 'selected' : '' }}>
                                        {{ $etiqueta }}
                                    </option>
                                @endforeach
                            </select>
                            @error('duracion_minutos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Doctor -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Doctor <span class="text-danger">*</span></label>
                            <select name="dentista_id"
                                    id="dentista_id"
                                    class="form-select @error('dentista_id') is-invalid @enderror">
                                <option value="">Seleccionar fecha, hora y duración primero...</option>
                            </select>
                            @error('dentista_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="msg-dentistas" class="form-text"></div>
                        </div>

                        <!-- Consultorio -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Consultorio <span class="text-danger">*</span></label>
                            <select name="consultorio_id"
                                    id="consultorio_id"
                                    class="form-select @error('consultorio_id') is-invalid @enderror">
                                <option value="">Seleccionar fecha, hora y duración primero...</option>
                            </select>
                            @error('consultorio_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="msg-consultorios" class="form-text"></div>
                        </div>

                        <!-- Motivo -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Motivo <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="motivo"
                                   value="{{ old('motivo') }}"
                                   class="form-control @error('motivo') is-invalid @enderror"
                                   placeholder="Ej: Limpieza dental, extracción, revisión...">
                            @error('motivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4 flex-wrap">
                        <button type="submit" class="btn btn-primary flex-grow-1 flex-md-grow-0">
                            <i class="bi bi-check-circle me-2"></i>Agendar cita
                        </button>
                        <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary flex-grow-1 flex-md-grow-0">
                            Cancelar
                        </a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Búsqueda de paciente ──────────────────────────────────────────
const inputBusqueda   = document.getElementById('busqueda-paciente');
const inputPacienteId = document.getElementById('paciente_id');
const resultadosPaciente  = document.getElementById('resultados-paciente');
const pacienteSeleccionado = document.getElementById('paciente-seleccionado');
let timerPaciente;

if (inputBusqueda) {
    inputBusqueda.addEventListener('input', function () {
        clearTimeout(timerPaciente);
        const q = this.value.trim();
        if (q.length < 2) { resultadosPaciente.innerHTML = ''; return; }

        timerPaciente = setTimeout(() => {
            fetch(`/pacientes/buscar?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(pacientes => {
                    if (pacientes.length === 0) {
                        resultadosPaciente.innerHTML = `
                            <div class="text-muted small p-2">
                                No encontrado.
                                <a href="/pacientes/create">Registrar nuevo paciente</a>
                            </div>`;
                        return;
                    }
                    resultadosPaciente.innerHTML = pacientes.map(p => `
                        <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-1 bg-light">
                            <div>
                                <strong>${p.nombre} ${p.apellido}</strong>
                                <span class="text-muted small ms-2">CI: ${p.ci}</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary"
                                onclick="seleccionarPaciente('${p.id}', '${p.nombre} ${p.apellido}', '${p.ci}', '${p.telefono_whatsapp}')">
                                Seleccionar
                            </button>
                        </div>
                    `).join('');
                });
        }, 300);
    });
}

function seleccionarPaciente(id, nombre, ci, tel) {
    inputPacienteId.value = id;
    resultadosPaciente.innerHTML = '';
    inputBusqueda.classList.add('d-none');
    pacienteSeleccionado.classList.remove('d-none');
    pacienteSeleccionado.innerHTML = `
        <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:36px;height:36px">
                <i class="bi bi-person-fill text-white small"></i>
            </div>
            <div class="flex-grow-1 min-w-0">
                <div class="fw-semibold small text-truncate">${nombre}</div>
                <div class="text-muted" style="font-size:0.75rem">CI: ${ci}</div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary flex-shrink-0"
                onclick="limpiarPaciente()">Cambiar</button>
        </div>`;
}

function limpiarPaciente() {
    inputPacienteId.value = '';
    inputBusqueda.classList.remove('d-none');
    inputBusqueda.value = '';
    pacienteSeleccionado.classList.add('d-none');
    pacienteSeleccionado.innerHTML = '';
}

// ── Horas en formato 24h ──────────────────────────────────────────
const horaSelect = document.getElementById('hora_inicio');
const oldHora    = '{{ old("hora_inicio") }}';

const horas = [];
for (let h = 9; h <= 21; h++) {
    horas.push(`${String(h).padStart(2, '0')}:00`);
    horas.push(`${String(h).padStart(2, '0')}:30`);
}
horas.push('22:00');

horas.forEach(hora => {
    const opt = document.createElement('option');
    opt.value = hora;
    opt.textContent = hora;
    if (oldHora === hora) opt.selected = true;
    horaSelect.appendChild(opt);
});

// ── Disponibilidad dinámica ───────────────────────────────────────
const fecha      = document.getElementById('fecha');
const duracion   = document.getElementById('duracion_minutos');
const selDoctor  = document.getElementById('dentista_id');
const selConsult = document.getElementById('consultorio_id');
const msgDoctores = document.getElementById('msg-dentistas');
const msgConsult  = document.getElementById('msg-consultorios');

function verificarDisponibilidad() {
    if (!fecha.value || !horaSelect.value || !duracion.value) return;

    selDoctor.innerHTML  = '<option value="">Cargando...</option>';
    selConsult.innerHTML = '<option value="">Cargando...</option>';

    const params = new URLSearchParams({
        fecha:            fecha.value,
        hora_inicio:      horaSelect.value,
        duracion_minutos: duracion.value,
    });

    fetch(`/citas/disponibilidad?${params}`)
        .then(r => r.json())
        .then(data => {
            // Doctores
            if (data.dentistas.length === 0) {
                selDoctor.innerHTML = '<option value="">Sin doctores disponibles</option>';
                msgDoctores.textContent = 'No hay doctores disponibles en ese horario.';
                msgDoctores.className = 'form-text text-danger';
            } else {
                selDoctor.innerHTML = '<option value="">Seleccionar doctor...</option>';
                data.dentistas.forEach(d => {
                    const turno = d.turno === 'manana' ? 'Mañana'
                                : d.turno === 'tarde_noche' ? 'Tarde/Noche'
                                : 'Libre';
                    selDoctor.innerHTML += `<option value="${d.id}">${d.nombre} — ${turno}</option>`;
                });
                msgDoctores.textContent = `${data.dentistas.length} doctor(es) disponible(s).`;
                msgDoctores.className = 'form-text text-success';
            }

            // Consultorios
            if (data.consultorios.length === 0) {
                selConsult.innerHTML = '<option value="">Sin consultorios disponibles</option>';
                msgConsult.textContent = 'No hay consultorios disponibles en ese horario.';
                msgConsult.className = 'form-text text-danger';
            } else {
                selConsult.innerHTML = '<option value="">Seleccionar consultorio...</option>';
                data.consultorios.forEach(c => {
                    selConsult.innerHTML += `<option value="${c.id}">${c.nombre}</option>`;
                });
                msgConsult.textContent = `${data.consultorios.length} consultorio(s) disponible(s).`;
                msgConsult.className = 'form-text text-success';
            }
        });
}

fecha.addEventListener('change', verificarDisponibilidad);
horaSelect.addEventListener('change', verificarDisponibilidad);
duracion.addEventListener('change', verificarDisponibilidad);

// Disparar si ya hay valores al cargar (old())
document.addEventListener('DOMContentLoaded', function () {
    if (fecha.value && horaSelect.value && duracion.value) {
        verificarDisponibilidad();
    }
});
</script>
@endpush