<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Conductor;
use App\Models\Propietario;
use Illuminate\Http\Request;

class BusquedaController extends Controller
{
    private const PREVIEW_LIMIT = 4;

    public function ajax(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['vehiculos' => [], 'conductores' => [], 'propietarios' => []]);
        }

        $rol = auth()->user()->rol;

        $vehiculos = Vehiculo::where(function ($query) use ($q) {
            $query->where('placa', 'LIKE', "%{$q}%")
                ->orWhere('marca', 'LIKE', "%{$q}%")
                ->orWhere('modelo', 'LIKE', "%{$q}%")
                ->orWhere('color', 'LIKE', "%{$q}%");
        })->whereNull('deleted_at')
            ->limit(self::PREVIEW_LIMIT)
            ->get(['id_vehiculo', 'placa', 'marca', 'modelo', 'color', 'tipo']);

        $conductores = Conductor::where(function ($query) use ($q) {
            $query->where('nombre', 'LIKE', "%{$q}%")
                ->orWhere('apellido', 'LIKE', "%{$q}%")
                ->orWhere('identificacion', 'LIKE', "%{$q}%");
        })->whereNull('deleted_at')
            ->limit(self::PREVIEW_LIMIT)
            ->get(['id_conductor', 'nombre', 'apellido', 'identificacion', 'activo']);

        $propietarios = collect();
        if (in_array($rol, ['ADMIN', 'SST'])) {
            $propietarios = Propietario::where(function ($query) use ($q) {
                $query->where('nombre', 'LIKE', "%{$q}%")
                    ->orWhere('apellido', 'LIKE', "%{$q}%")
                    ->orWhere('identificacion', 'LIKE', "%{$q}%");
            })->whereNull('deleted_at')
                ->limit(self::PREVIEW_LIMIT)
                ->get(['id_propietario', 'nombre', 'apellido', 'identificacion']);
        }

        $esGestor = in_array($rol, ['ADMIN', 'SST']);

        return response()->json([
            'vehiculos' => $vehiculos->map(fn($v) => [
                'label' => $v->placa . ' — ' . $v->marca . ($v->modelo ? ' ' . $v->modelo : ''),
                'sub'   => trim(($v->color ?? '') . ' · ' . ($v->tipo ?? '')),
                'url'   => $esGestor
                    ? route('reportes.ficha', $v->id_vehiculo)
                    : route('porteria.index', ['busqueda' => $v->placa, 'tipo_busqueda' => 'placa']),
            ]),
            'conductores' => $conductores->map(fn($c) => [
                'label' => $c->nombre . ' ' . $c->apellido,
                'sub'   => $c->identificacion . ($c->activo ? '' : ' · Inactivo'),
                'url'   => route('reportes.ficha.conductor', $c->id_conductor),
            ]),
            'propietarios' => $propietarios->map(fn($p) => [
                'label' => $p->nombre . ' ' . $p->apellido,
                'sub'   => $p->identificacion,
                'url'   => route('reportes.propietarios', ['propietario' => $p->id_propietario]),
            ]),
            'total' => $vehiculos->count() + $conductores->count() + $propietarios->count(),
        ]);
    }

    public function resultados(Request $request)
    {
        $q = trim($request->input('q', ''));
        $rol = auth()->user()->rol;
        $esGestor = in_array($rol, ['ADMIN', 'SST']);

        if (mb_strlen($q) < 2) {
            return view('busqueda.resultados', [
                'q'            => $q,
                'vehiculos'    => collect(),
                'conductores'  => collect(),
                'propietarios' => collect(),
                'esGestor'     => $esGestor,
            ]);
        }

        $vehiculos = Vehiculo::where(function ($query) use ($q) {
            $query->where('placa', 'LIKE', "%{$q}%")
                ->orWhere('marca', 'LIKE', "%{$q}%")
                ->orWhere('modelo', 'LIKE', "%{$q}%")
                ->orWhere('color', 'LIKE', "%{$q}%");
        })->whereNull('deleted_at')->orderBy('placa')->get();

        $conductores = Conductor::where(function ($query) use ($q) {
            $query->where('nombre', 'LIKE', "%{$q}%")
                ->orWhere('apellido', 'LIKE', "%{$q}%")
                ->orWhere('identificacion', 'LIKE', "%{$q}%")
                ->orWhere('telefono', 'LIKE', "%{$q}%");
        })->whereNull('deleted_at')->orderBy('nombre')->get();

        $propietarios = collect();
        if ($esGestor) {
            $propietarios = Propietario::where(function ($query) use ($q) {
                $query->where('nombre', 'LIKE', "%{$q}%")
                    ->orWhere('apellido', 'LIKE', "%{$q}%")
                    ->orWhere('identificacion', 'LIKE', "%{$q}%");
            })->whereNull('deleted_at')->orderBy('nombre')->get();
        }

        return view('busqueda.resultados', compact('q', 'vehiculos', 'conductores', 'propietarios', 'esGestor'));
    }
}
