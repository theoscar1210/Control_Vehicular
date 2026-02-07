<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Vehiculo;
use App\Models\Conductor;
use App\Models\DocumentoConductor;
use App\Models\DocumentoVehiculo;
use App\Models\Alerta;

class DashboardController extends Controller
{
    /**
     * Constructor. Requiere auth middleware.
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar dashboard con variables exactas que la vista espera.
     */
    public function index(Request $request)
    {
        // Contadores cacheados por 1 hora (se invalidan al crear/editar/eliminar registros)
        $stats = Cache::remember('dashboard_stats', 3600, function () {
            $hoy = Carbon::today();
            $proximo = $hoy->copy()->addDays(20);

            $totalVehiculos = Vehiculo::where('estado', 'Activo')->count();
            $conductoresActivos = Conductor::where('activo', 1)->count();

            $porVencerVeh = DocumentoVehiculo::whereNotNull('fecha_vencimiento')
                ->whereBetween('fecha_vencimiento', [$hoy, $proximo])
                ->where('estado', '!=', 'REEMPLAZADO')
                ->count();

            $porVencerCond = DocumentoConductor::whereNotNull('fecha_vencimiento')
                ->whereBetween('fecha_vencimiento', [$hoy, $proximo])
                ->where('estado', '!=', 'REEMPLAZADO')
                ->count();

            $vencidosVeh = DocumentoVehiculo::where('estado', '!=', 'REEMPLAZADO')
                ->where(function ($q) use ($hoy) {
                    $q->whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '<', $hoy)
                        ->orWhere('estado', 'VENCIDO');
                })->count();

            $vencidosCond = DocumentoConductor::where('estado', '!=', 'REEMPLAZADO')
                ->where(function ($q) use ($hoy) {
                    $q->whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '<', $hoy)
                        ->orWhere('estado', 'VENCIDO');
                })->count();

            return [
                'totalVehiculos' => $totalVehiculos,
                'conductoresActivos' => $conductoresActivos,
                'porVencerCount' => $porVencerVeh + $porVencerCond,
                'vencidosCount' => $vencidosVeh + $vencidosCond,
            ];
        });

        $totalVehiculos = $stats['totalVehiculos'];
        $conductoresActivos = $stats['conductoresActivos'];
        $porVencerCount = $stats['porVencerCount'];
        $vencidosCount = $stats['vencidosCount'];

        $ultima_actualizacion = Carbon::now()->format('d M Y, H:i:s');

        // Alertas: visibles para el rol del usuario o para TODOS, no borradas (softDelete),
        // Solo mostrar alertas NO LEÍDAS por este usuario específico.
        // ADMIN: no ve alertas en el dashboard
        // SST: las leídas desaparecen del dashboard
        // PORTERIA: ve todas las alertas (no se filtran por leídas)
        $user = Auth::user();
        $role = $user ? $user->rol : null;

        // ADMIN no ve alertas en el dashboard
        if ($role === 'ADMIN') {
            $alertas = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        } else {
            $alertasQuery = Alerta::with([
                    'documentoVehiculo.vehiculo.conductor',
                    'documentoConductor.conductor',
                    'usuariosQueLeyeron'
                ])
                ->whereNull('deleted_at')
                ->activas() // Solo alertas no solucionadas
                ->conDocumentoVigente(); // Solo alertas de documentos no reemplazados

            if ($role) {
                $alertasQuery->where(function ($q) use ($role) {
                    $q->where('visible_para', 'TODOS')
                        ->orWhere('visible_para', $role);
                });
            } else {
                $alertasQuery->where('visible_para', 'TODOS');
            }

            // SST: filtrar solo alertas no leídas (las leídas desaparecen del dashboard)
            // PORTERIA: ver todas las alertas
            if ($user && $role !== 'PORTERIA') {
                $alertasQuery->noLeidasPor($user->id_usuario);
            }

            // ordenar por fecha desc
            $alertas = $alertasQuery->orderByDesc('fecha_alerta')->orderByDesc('fecha_registro')->paginate(10);
        }

        return view('dashboard', compact(
            'totalVehiculos',
            'conductoresActivos',
            'porVencerCount',
            'vencidosCount',
            'alertas',
            'ultima_actualizacion'
        ));
    }
}
