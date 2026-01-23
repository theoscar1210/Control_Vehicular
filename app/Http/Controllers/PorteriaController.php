<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Alerta;
use App\Models\DocumentoVehiculo;
use App\Models\DocumentoConductor;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PorteriaController extends Controller
{
    /**
     * Vista principal de Portería con alertas y buscador de placas.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Alertas visibles para el usuario
        $alertas = Alerta::with([
                'documentoVehiculo.vehiculo.conductor',
                'documentoConductor.conductor'
            ])
            ->whereNull('deleted_at')
            ->where('leida', 0)
            ->where(function ($q) use ($user) {
                $q->where('visible_para', 'TODOS')
                    ->orWhere('visible_para', $user->rol);
            })
            ->orderByDesc('fecha_alerta')
            ->paginate(10);

        // Resultado de búsqueda de placa
        $vehiculo = null;
        $placaBuscada = $request->input('placa');
        $mensaje = null;

        if ($placaBuscada) {
            $placaBuscada = strtoupper(trim($placaBuscada));
            $vehiculo = Vehiculo::with([
                    'conductor.documentosConductor',
                    'propietario',
                    'documentos'
                ])
                ->where('placa', $placaBuscada)
                ->first();

            if (!$vehiculo) {
                $mensaje = "No se encontró ningún vehículo con la placa: {$placaBuscada}";
            }
        }

        // Calcular estados de documentos si hay vehículo
        $estadosDocumentos = [];
        if ($vehiculo) {
            $estadosDocumentos = $this->calcularEstadosDocumentos($vehiculo);
        }

        // Usar navbar especial (sin menú lateral)
        $navbarEspecial = true;

        return view('porteria.index', compact(
            'alertas',
            'vehiculo',
            'placaBuscada',
            'mensaje',
            'estadosDocumentos',
            'navbarEspecial'
        ));
    }

    /**
     * Calcula el estado de los documentos del vehículo y conductor.
     */
    private function calcularEstadosDocumentos($vehiculo)
    {
        $hoy = Carbon::today();
        $limite20 = Carbon::today()->addDays(20); // Cambio de 30 a 20 días
        $estados = [];

        // Documentos del vehículo
        $tiposVehiculo = ['SOAT', 'Tecnomecanica', 'Tarjeta Propiedad'];
        foreach ($tiposVehiculo as $tipo) {
            $doc = $vehiculo->documentos
                ->where('tipo_documento', $tipo)
                ->where('activo', 1)
                ->first();

            if (!$doc) {
                $estados["vehiculo_{$tipo}"] = [
                    'estado' => 'SIN_REGISTRO',
                    'clase' => 'secondary',
                    'mensaje' => 'Sin registro',
                    'fecha' => null,
                    'dias' => null
                ];
            } else {
                $vencimiento = Carbon::parse($doc->fecha_vencimiento);
                $dias = $hoy->diffInDays($vencimiento, false);

                // Rojo (danger): VENCIDO o 0-5 días | Amarillo (warning): 6-20 días | Verde (success): > 20 días
                if ($dias < 0) {
                    $estados["vehiculo_{$tipo}"] = [
                        'estado' => 'VENCIDO',
                        'clase' => 'danger',
                        'mensaje' => 'Vencido hace ' . abs($dias) . ' días',
                        'fecha' => $vencimiento->format('d/m/Y'),
                        'dias' => abs($dias)
                    ];
                } elseif ($dias <= 5) {
                    $estados["vehiculo_{$tipo}"] = [
                        'estado' => 'POR_VENCER',
                        'clase' => 'danger', // Crítico: 0-5 días
                        'mensaje' => 'Vence en ' . $dias . ' días',
                        'fecha' => $vencimiento->format('d/m/Y'),
                        'dias' => $dias
                    ];
                } elseif ($dias <= 20) {
                    $estados["vehiculo_{$tipo}"] = [
                        'estado' => 'POR_VENCER',
                        'clase' => 'warning', // Advertencia: 6-20 días
                        'mensaje' => 'Vence en ' . $dias . ' días',
                        'fecha' => $vencimiento->format('d/m/Y'),
                        'dias' => $dias
                    ];
                } else {
                    $estados["vehiculo_{$tipo}"] = [
                        'estado' => 'VIGENTE',
                        'clase' => 'success', // Vigente: > 20 días
                        'mensaje' => 'Vigente',
                        'fecha' => $vencimiento->format('d/m/Y'),
                        'dias' => $dias
                    ];
                }
            }
        }

        // Documentos del conductor
        if ($vehiculo->conductor) {
            $tiposConductor = ['Licencia Conducción'];
            foreach ($tiposConductor as $tipo) {
                $doc = $vehiculo->conductor->documentosConductor
                    ->where('tipo_documento', $tipo)
                    ->where('activo', 1)
                    ->first();

                if (!$doc) {
                    $estados["conductor_{$tipo}"] = [
                        'estado' => 'SIN_REGISTRO',
                        'clase' => 'secondary',
                        'mensaje' => 'Sin registro',
                        'fecha' => null,
                        'dias' => null
                    ];
                } else {
                    $vencimiento = Carbon::parse($doc->fecha_vencimiento);
                    $dias = $hoy->diffInDays($vencimiento, false);

                    // Rojo (danger): VENCIDO o 0-5 días | Amarillo (warning): 6-20 días | Verde (success): > 20 días
                    if ($dias < 0) {
                        $estados["conductor_{$tipo}"] = [
                            'estado' => 'VENCIDO',
                            'clase' => 'danger',
                            'mensaje' => 'Vencido hace ' . abs($dias) . ' días',
                            'fecha' => $vencimiento->format('d/m/Y'),
                            'dias' => abs($dias)
                        ];
                    } elseif ($dias <= 5) {
                        $estados["conductor_{$tipo}"] = [
                            'estado' => 'POR_VENCER',
                            'clase' => 'danger', // Crítico: 0-5 días
                            'mensaje' => 'Vence en ' . $dias . ' días',
                            'fecha' => $vencimiento->format('d/m/Y'),
                            'dias' => $dias
                        ];
                    } elseif ($dias <= 20) {
                        $estados["conductor_{$tipo}"] = [
                            'estado' => 'POR_VENCER',
                            'clase' => 'warning', // Advertencia: 6-20 días
                            'mensaje' => 'Vence en ' . $dias . ' días',
                            'fecha' => $vencimiento->format('d/m/Y'),
                            'dias' => $dias
                        ];
                    } else {
                        $estados["conductor_{$tipo}"] = [
                            'estado' => 'VIGENTE',
                            'clase' => 'success', // Vigente: > 20 días
                            'mensaje' => 'Vigente',
                            'fecha' => $vencimiento->format('d/m/Y'),
                            'dias' => $dias
                        ];
                    }
                }
            }
        }

        return $estados;
    }
}
