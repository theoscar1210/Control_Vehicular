<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alerta;
use App\Models\Conductor;
use App\Models\DocumentoConductor;
use App\Models\DocumentoVehiculo;
use App\Models\Vehiculo;

class DashboardController extends Controller
{
    /**
     * Constructor. Establece la autenticación obligatoria para ver el dashboard.
     */


    public function index(Request $request)
    {
        // valores resumen
        $totalVehiculos = Vehiculo::count();
        $conductoresActivos = Conductor::where('activo', 1)->count();

        // contadores por estado (documentos conductor + vehiculo)
        $porVencerCount = DocumentoConductor::where('estado', 'POR_VENCER')->count()
            + DocumentoVehiculo::where('estado', 'POR_VENCER')->count();

        $vencidosCount = DocumentoConductor::where('estado', 'VENCIDO')->count()
            + DocumentoVehiculo::where('estado', 'VENCIDO')->count();

        // obtener rol del usuario autenticado (si existe)
        $user = Auth::user();
        $role = $user ? $user->rol : null;

        // consultas de alertas visibles para el rol (si no hay usuario devolvemos none)
        $alertasQuery = Alerta::query();

        if ($role) {
            $alertasQuery->where(function ($q) use ($role) {
                $q->where('visible_para', 'TODOS')
                    ->orWhere('visible_para', $role);
            });
        } else {
            // si no hay usuario autenticado, solo mostrar las visibles para TODOS
            $alertasQuery->where('visible_para', 'TODOS');
        }

        $alertas = $alertasQuery->orderByDesc('fecha_alerta')->paginate(10);

        return view('dashboard', compact(
            'totalVehiculos',
            'conductoresActivos',
            'porVencerCount',
            'vencidosCount',
            'alertas'
        ));
    }
}
