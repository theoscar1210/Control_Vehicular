<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alerta;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\DocumentoVencidoNotification;

class AlertaController extends Controller
{
    /**
     * Lista las alertas visibles para el usuario (no eliminadas) paginadas.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Alerta::with([
                'documentoVehiculo.vehiculo.conductor',
                'documentoConductor.conductor'
            ])
            ->whereNull('deleted_at')
            ->where(function ($q) use ($user) {
                $q->where('visible_para', 'TODOS')
                    ->orWhere('visible_para', $user->rol);
            })
            ->orderByDesc('fecha_alerta')
            ->orderByDesc('fecha_registro');

        $perPage = (int) $request->input('per_page', 20);
        $alertas = $query->paginate($perPage)->appends($request->except('page'));

        return view('alertas.index', compact('alertas'));
    }

    /**
     * Muestra una alerta concreta, la marca como leída y redirige
     * al documento o vehículo/conductor relacionado.
     */
    public function show(Alerta $alerta)
    {
        $user = Auth::user();

        // verificar visibilidad
        if (!($alerta->visible_para === 'TODOS' || $alerta->visible_para === $user->rol)) {
            abort(403, 'No autorizado para ver esta alerta.');
        }

        if (!$alerta->leida) {
            $alerta->leida = 1;
            $alerta->save();
        }

        // Cargar relaciones para obtener IDs correctos
        $alerta->load(['documentoVehiculo.vehiculo', 'documentoConductor.conductor']);

        // Redirigir al vehículo si es documento de vehículo
        if ($alerta->documentoVehiculo && $alerta->documentoVehiculo->vehiculo) {
            return redirect()->route('vehiculos.edit', $alerta->documentoVehiculo->vehiculo->id_vehiculo)
                ->with('success', 'Alerta marcada como leída.');
        }

        // Redirigir al conductor si es documento de conductor
        if ($alerta->documentoConductor && $alerta->documentoConductor->conductor) {
            return redirect()->route('conductores.edit', $alerta->documentoConductor->conductor->id_conductor)
                ->with('success', 'Alerta marcada como leída.');
        }

        // Si no hay documento relacionado, redirigir al listado de alertas
        return redirect()->route('alertas.index')
            ->with('success', 'Alerta marcada como leída.');
    }

    /**
     * Marca una alerta como leída.
     * Soporta tanto peticiones AJAX (retorna JSON) como formularios normales (redirige).
     */
    public function markAsRead(Request $request, Alerta $alerta)
    {
        $user = Auth::user();

        // Verificar visibilidad
        if (!($alerta->visible_para === 'TODOS' || $alerta->visible_para === $user->rol)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            abort(403, 'No autorizado para modificar esta alerta.');
        }

        // Marcar como leída
        $alerta->leida = 1;
        $alerta->save();

        // Si es AJAX, retornar JSON
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'id_alerta' => $alerta->id_alerta]);
        }

        // Si es formulario normal, redirigir de vuelta
        return redirect()->back()->with('success', 'Alerta marcada como leída.');
    }

    /**
     * Marca todas las alertas visibles para el usuario como leídas.
     */
    public function markAllRead(Request $request)
    {
        $user = Auth::user();

        $updated = Alerta::where(function ($q) use ($user) {
            $q->where('visible_para', 'TODOS')->orWhere('visible_para', $user->rol);
        })
            ->where('leida', 0)
            ->update(['leida' => 1]);

        return redirect()->back()->with('success', "Se marcaron $updated alertas como leídas.");
    }

    /**
     * Crea (almacena) manualmente una alerta y notifica a roles (útil para pruebas o envío manual).
     * Espera: tipo_alerta, id_doc_vehiculo?, id_doc_conductor?, tipo_vencimiento, mensaje, fecha_alerta, visible_para
     */
    public function store(Request $request)
    {
        $this->authorize('create', Alerta::class); // opcional: proteger con policy

        $data = $request->validate([
            'tipo_alerta' => 'required|in:VEHICULO,CONDUCTOR',
            'id_doc_vehiculo' => 'nullable|integer|exists:documentos_vehiculo,id_doc_vehiculo',
            'id_doc_conductor' => 'nullable|integer|exists:documentos_conductor,id_doc_conductor',
            'tipo_vencimiento' => 'required|in:VENCIDO,PROXIMO_VENCER',
            'mensaje' => 'nullable|string|max:255',
            'fecha_alerta' => 'nullable|date',
            'visible_para' => 'required|in:ADMIN,SST,PORTERIA,TODOS',
        ]);

        $data['fecha_alerta'] = $data['fecha_alerta'] ?? now()->toDateString();
        $data['leida'] = 0;
        $data['creado_por'] = Auth::id();

        $alerta = Alerta::create($data);

        // Notificar a usuarios activos según visible_para
        $users = Usuario::where('activo', 1)
            ->when($alerta->visible_para !== 'TODOS', function ($q) use ($alerta) {
                $q->where('rol', $alerta->visible_para);
            })->get();

        foreach ($users as $u) {
            $u->notify(new DocumentoVencidoNotification($alerta));
        }

        return redirect()->back()->with('success', 'Alerta creada y notificaciones enviadas.');
    }

    /**
     * Borrar (soft-delete) una alerta.
     */
    public function destroy(Alerta $alerta)
    {
        $this->authorize('delete', $alerta); // opcional: policy

        $alerta->delete();
        return redirect()->back()->with('success', 'Alerta eliminada.');
    }

    /**
     * Endpoint simple JSON para obtener contador de alertas no leídas
     * (útil para el badge en la barra).
     */
    public function unreadCount()
    {
        $user = Auth::user();

        $count = Alerta::where(function ($q) use ($user) {
            $q->where('visible_para', 'TODOS')->orWhere('visible_para', $user->rol);
        })
            ->where('leida', 0)
            ->count();

        return response()->json(['unread' => $count]);
    }
}
