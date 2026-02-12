<?php

namespace App\Http\Controllers;

use App\Models\DocumentoVehiculo;
use App\Models\Vehiculo;
use App\Services\DocumentoVehiculoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreDocumentoVehiculoRequest;

class DocumentoVehiculoController extends Controller
{
    private DocumentoVehiculoService $documentoService;

    public function __construct(DocumentoVehiculoService $documentoService)
    {
        $this->documentoService = $documentoService;
    }

    /**
     * ============================================
     * GUARDAR DOCUMENTO DE VEHÍCULO
     * ============================================
     */
    public function store(StoreDocumentoVehiculoRequest $request, $idVehiculo)
    {
        $validated = $request->validated();
        $vehiculo = Vehiculo::with(['propietario'])->findOrFail($idVehiculo);

        try {
            $nuevoDocumento = $this->documentoService->crearDocumento($vehiculo, $validated);

            if (\Route::has('vehiculos.create')) {
                return redirect()
                    ->route('vehiculos.create', ['vehiculo' => $vehiculo->id_vehiculo])
                    ->with('success', "Documento {$nuevoDocumento->tipo_documento} guardado correctamente.");
            }

            return redirect()
                ->route('vehiculos.index')
                ->with('success', "Documento {$nuevoDocumento->tipo_documento} guardado correctamente.");
        } catch (\Exception $e) {
            Log::error('Error al guardar documento', [
                'vehiculo' => $idVehiculo,
                'tipo_documento' => $validated['tipo_documento'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al guardar el documento: ' . $e->getMessage());
        }
    }

    /**
     * ============================================
     * RENOVAR DOCUMENTO (CREA NUEVA VERSIÓN)
     * ============================================
     */
    public function update(Request $request, $idVehiculo, $idDocumento)
    {
        $vehiculo = Vehiculo::findOrFail($idVehiculo);
        $documentoAnterior = DocumentoVehiculo::where('id_doc_vehiculo', $idDocumento)
            ->where('id_vehiculo', $vehiculo->id_vehiculo)
            ->firstOrFail();

        if (!$this->documentoService->esRenovable($documentoAnterior)) {
            return back()->with('error', 'Solo se pueden renovar documentos vencidos o próximos a vencer.');
        }

        $validated = $request->validate([
            'numero_documento' => 'required|string|max:50',
            'entidad_emisora'  => 'nullable|string|max:100',
            'fecha_emision'    => 'required|date',
            'nota'             => 'nullable|string|max:255',
        ]);

        try {
            $nuevoDocumento = $this->documentoService->renovarDocumento(
                $vehiculo,
                $documentoAnterior,
                $validated
            );

            if (\Route::has('vehiculos.documentos.historial.completo')) {
                return redirect()
                    ->route('vehiculos.documentos.historial.completo', $vehiculo->id_vehiculo)
                    ->with('success', "¡Documento {$nuevoDocumento->tipo_documento} renovado correctamente!");
            }

            return redirect()
                ->route('vehiculos.index')
                ->with('success', "¡Documento {$nuevoDocumento->tipo_documento} renovado correctamente!");
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error al renovar documento', [
                'documento' => $idDocumento,
                'vehiculo' => $idVehiculo,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al renovar el documento: ' . $e->getMessage());
        }
    }

    /**
     * ============================================
     * FORMULARIO DE EDICIÓN/RENOVACIÓN
     * ============================================
     */
    public function edit($idVehiculo, $idDocumento)
    {
        $vehiculo = Vehiculo::findOrFail($idVehiculo);

        $documento = DocumentoVehiculo::where('id_doc_vehiculo', $idDocumento)
            ->where('id_vehiculo', $vehiculo->id_vehiculo)
            ->firstOrFail();

        $nuevaVersion = $documento->version + 1;

        return view('vehiculos.documentos.edit', compact('vehiculo', 'documento', 'nuevaVersion'));
    }

    /**
     * ============================================
     * HISTORIAL COMPLETO DE DOCUMENTOS DEL VEHÍCULO
     * ============================================
     */
    public function historial($idVehiculo, $tipoDocumento = null)
    {
        $vehiculo = Vehiculo::findOrFail($idVehiculo);

        $query = DocumentoVehiculo::where('id_vehiculo', $vehiculo->id_vehiculo)
            ->with('creador');

        if ($tipoDocumento) {
            $query->where('tipo_documento', $tipoDocumento);
        }

        $historial = $query->orderBy('tipo_documento')
            ->orderByDesc('version')
            ->get();

        return view('vehiculos.documentos.historial', compact('vehiculo', 'historial', 'tipoDocumento'));
    }

    /**
     * ============================================
     * HISTORIAL COMPLETO (ALIAS)
     * ============================================
     */
    public function historialCompleto($idVehiculo)
    {
        return $this->historial($idVehiculo);
    }
}
