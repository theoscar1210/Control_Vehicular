<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Conductor;
use App\Models\Propietario;
use App\Models\Alerta;
use App\Models\DocumentoVehiculo;
use App\Models\DocumentoConductor;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\DocumentStatusService;

class PorteriaController extends Controller
{
    public function __construct(
        private DocumentStatusService $documentStatusService
    ) {}

    /**
     * Vista principal de Portería con alertas y buscador múltiple.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Alertas visibles para el usuario (solo activas = no solucionadas)
        $alertas = Alerta::with([
                'documentoVehiculo.vehiculo.conductor',
                'documentoConductor.conductor'
            ])
            ->whereNull('deleted_at')
            ->activas() // Solo alertas no solucionadas
            ->conDocumentoVigente() // Solo alertas de documentos no reemplazados
            ->where(function ($q) use ($user) {
                $q->where('visible_para', 'TODOS')
                    ->orWhere('visible_para', $user->rol);
            })
            ->orderByDesc('fecha_alerta')
            ->paginate(10);

        // Resultado de búsqueda múltiple
        $vehiculo = null;
        $vehiculos = collect(); // Para múltiples resultados
        $busqueda = $request->input('busqueda');
        $tipoBusqueda = $request->input('tipo_busqueda', 'placa');
        $mensaje = null;

        if ($busqueda) {
            $busqueda = strtoupper(trim($busqueda));

            // Mínimo 2 caracteres para evitar consultas masivas tipo "%a%"
            if (mb_strlen($busqueda) < 2) {
                return redirect()->route('porteria.index')
                    ->with('error', 'La búsqueda debe tener al menos 2 caracteres.');
            }

            $resultado = $this->realizarBusqueda($busqueda, $tipoBusqueda);

            $vehiculos = $resultado['vehiculos'];
            $mensaje = $resultado['mensaje'];

            // Si solo hay un resultado, mostrarlo directamente
            if ($vehiculos->count() === 1) {
                $vehiculo = $vehiculos->first();
            }
        }

        // Calcular estados de documentos si hay vehículo único
        $estadosDocumentos = [];
        if ($vehiculo) {
            $estadosDocumentos = $this->documentStatusService->calcularEstadosDetallados($vehiculo);
        }

        // Usar navbar especial (sin menú lateral)
        $navbarEspecial = true;

        return view('porteria.index', compact(
            'alertas',
            'vehiculo',
            'vehiculos',
            'busqueda',
            'tipoBusqueda',
            'mensaje',
            'estadosDocumentos',
            'navbarEspecial'
        ));
    }

    /**
     * Realiza búsqueda según el tipo seleccionado.
     */
    private function realizarBusqueda(string $busqueda, string $tipo): array
    {
        $vehiculos = collect();
        $mensaje = null;

        switch ($tipo) {
            case 'placa':
                // Búsqueda por placa (parcial o completa) — máx 50 resultados para evitar DoS
                $vehiculos = Vehiculo::with(['conductor.documentosConductor', 'propietario', 'documentos'])
                    ->where('placa', 'LIKE', "%{$busqueda}%")
                    ->where('estado', 'ACTIVO')
                    ->limit(50)
                    ->get();

                if ($vehiculos->isEmpty()) {
                    $mensaje = "No se encontró ningún vehículo con placa: {$busqueda}";
                }
                break;

            case 'conductor':
                // Búsqueda por nombre/apellido de conductor — máx 50 resultados
                $vehiculos = Vehiculo::with(['conductor.documentosConductor', 'propietario', 'documentos'])
                    ->whereHas('conductor', function ($q) use ($busqueda) {
                        $q->where('activo', 1)
                          ->where(function ($q2) use ($busqueda) {
                              $q2->where('nombre', 'LIKE', "%{$busqueda}%")
                                 ->orWhere('apellido', 'LIKE', "%{$busqueda}%")
                                 ->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$busqueda}%"]);
                          });
                    })
                    ->where('estado', 'ACTIVO')
                    ->limit(50)
                    ->get();

                if ($vehiculos->isEmpty()) {
                    $mensaje = "No se encontró ningún conductor con nombre: {$busqueda}";
                }
                break;

            case 'propietario':
                // Búsqueda por nombre/apellido de propietario — máx 50 resultados
                $vehiculos = Vehiculo::with(['conductor.documentosConductor', 'propietario', 'documentos'])
                    ->whereHas('propietario', function ($q) use ($busqueda) {
                        $q->where('nombre', 'LIKE', "%{$busqueda}%")
                          ->orWhere('apellido', 'LIKE', "%{$busqueda}%")
                          ->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$busqueda}%"]);
                    })
                    ->where('estado', 'ACTIVO')
                    ->limit(50)
                    ->get();

                if ($vehiculos->isEmpty()) {
                    $mensaje = "No se encontró ningún propietario con nombre: {$busqueda}";
                }
                break;

            case 'documento':
                // Búsqueda por número de documento de identidad — máx 50 resultados
                $vehiculos = Vehiculo::with(['conductor.documentosConductor', 'propietario', 'documentos'])
                    ->where('estado', 'ACTIVO')
                    ->where(function ($q) use ($busqueda) {
                        $q->whereHas('conductor', function ($q2) use ($busqueda) {
                            $q2->where('identificacion', 'LIKE', "%{$busqueda}%");
                        })
                        ->orWhereHas('propietario', function ($q2) use ($busqueda) {
                            $q2->where('identificacion', 'LIKE', "%{$busqueda}%");
                        });
                    })
                    ->limit(50)
                    ->get();

                if ($vehiculos->isEmpty()) {
                    $mensaje = "No se encontró ningún registro con documento: {$busqueda}";
                }
                break;

            case 'todo':
            default:
                // Búsqueda global (placa, conductor, propietario, documento) — máx 50 resultados
                $vehiculos = Vehiculo::with(['conductor.documentosConductor', 'propietario', 'documentos'])
                    ->where('estado', 'ACTIVO')
                    ->where(function ($q) use ($busqueda) {
                        // Buscar por placa
                        $q->where('placa', 'LIKE', "%{$busqueda}%")
                        // O por conductor
                        ->orWhereHas('conductor', function ($q2) use ($busqueda) {
                            $q2->where('activo', 1)
                               ->where(function ($q3) use ($busqueda) {
                                   $q3->where('nombre', 'LIKE', "%{$busqueda}%")
                                      ->orWhere('apellido', 'LIKE', "%{$busqueda}%")
                                      ->orWhere('identificacion', 'LIKE', "%{$busqueda}%");
                               });
                        })
                        // O por propietario
                        ->orWhereHas('propietario', function ($q2) use ($busqueda) {
                            $q2->where('nombre', 'LIKE', "%{$busqueda}%")
                               ->orWhere('apellido', 'LIKE', "%{$busqueda}%")
                               ->orWhere('identificacion', 'LIKE', "%{$busqueda}%");
                        });
                    })
                    ->limit(50)
                    ->get();

                if ($vehiculos->isEmpty()) {
                    $mensaje = "No se encontró ningún resultado para: {$busqueda}";
                }
                break;
        }

        return [
            'vehiculos' => $vehiculos,
            'mensaje' => $mensaje
        ];
    }

    /**
     * Calcula el estado de los documentos del vehículo y conductor.
     */
    // calcularEstadosDocumentos() movido a DocumentStatusService
}
