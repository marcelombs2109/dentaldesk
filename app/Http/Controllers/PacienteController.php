<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->get('q');

        $pacientes = $this->consultaVisiblePara($request->user())
            ->when($busqueda, function ($query, $busqueda) {
                $query->where(function ($sub) use ($busqueda) {
                    $sub->where('nombre', 'ilike', "%{$busqueda}%")
                        ->orWhere('apellido', 'ilike', "%{$busqueda}%")
                        ->orWhere('ci', 'ilike', "%{$busqueda}%");
                });
            })
            ->orderBy('apellido')
            ->paginate(15);

        return view('pacientes.index', compact('pacientes', 'busqueda'));
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'            => 'required|string|max:100',
            'apellido'          => 'required|string|max:100',
            'ci'                => 'required|string|unique:pacientes,ci',
            'telefono_whatsapp' => 'required|string|max:20',
            'email'             => 'nullable|email|unique:pacientes,email',
            'fecha_nacimiento'  => 'nullable|date',
            'observaciones'     => 'nullable|string',
        ]);

        $paciente = Paciente::create($datos);

        return redirect()->route('citas.create', ['paciente_id' => $paciente->id])
            ->with('success', "Paciente {$paciente->nombre_completo} registrado correctamente.");
    }

    public function show(Request $request, Paciente $paciente)
    {
        $this->authorize('view', $paciente);

        $citas = $paciente->citas()
            ->with(['dentista', 'consultorio'])
            ->when($request->user()->esOdontologo(), function ($query) use ($request) {
                $query->where('dentista_id', $request->user()->dentista_id);
            })
            ->orderBy('fecha', 'desc')
            ->get();

        return view('pacientes.show', compact('paciente', 'citas'));
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente)
    {
        $this->authorize('update', $paciente);

        $datos = $request->validate([
            'nombre'            => 'required|string|max:100',
            'apellido'          => 'required|string|max:100',
            'ci'                => "required|string|unique:pacientes,ci,{$paciente->id}",
            'telefono_whatsapp' => 'required|string|max:20',
            'email'             => "nullable|email|unique:pacientes,email,{$paciente->id}",
            'fecha_nacimiento'  => 'nullable|date',
            'observaciones'     => 'nullable|string',
        ]);

        $paciente->update($datos);

        return redirect()->route('pacientes.show', $paciente)
            ->with('success', 'Datos del paciente actualizados correctamente.');
    }

    public function destroy(Paciente $paciente)
    {
        $this->authorize('delete', $paciente);

        $paciente->delete(); // SoftDelete

        return redirect()->route('pacientes.index')
            ->with('success', "Paciente {$paciente->nombre_completo} eliminado correctamente.");
    }

    public function buscar(Request $request)
    {
        $q = $request->get('q');

        $pacientes = $this->consultaVisiblePara($request->user())
            ->when($q, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nombre', 'ilike', "%{$q}%")
                        ->orWhere('apellido', 'ilike', "%{$q}%")
                        ->orWhere('ci', 'ilike', "%{$q}%");
                });
            })
            ->limit(10)
            ->get(['id', 'nombre', 'apellido', 'ci', 'telefono_whatsapp']);

        return response()->json($pacientes);
    }

    /**
     * SDD-03 R1/R6: el odontólogo solo consulta pacientes que tienen
     * al menos una cita con él. El filtro se aplica en la consulta.
     */
    private function consultaVisiblePara(User $user): Builder
    {
        $query = Paciente::query();

        if ($user->esOdontologo()) {
            $query->whereHas('citas', function ($sub) use ($user) {
                $sub->where('dentista_id', $user->dentista_id);
            });
        }

        return $query;
    }
}
