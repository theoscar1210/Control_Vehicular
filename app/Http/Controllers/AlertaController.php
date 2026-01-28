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
     * Lista TODAS las alertas visibles para el usuario (no eliminadas) paginadas.
     * Muestra alertas leídas y no leídas - las alertas permanecen hasta que se solucionen.
     * El filtro de "leídas desaparecen" solo aplica en el dashboard, no aquí.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Alerta::with([
                'documentoVehiculo.vehiculo.conductor',
                'documentoConductor.conductor',
                'usuariosQueLeyeron' // Cargar relacion para verificar lectura
            ])
            ->whereNull('deleted_at')
            ->activas() // Solo alertas no solucionadas
            ->where(function ($q) use ($user) {
                $q->where('visible_para', 'TODOS')
                    ->orWhere('visible_para', $user->rol);
            });

        // NO filtrar por leídas aquí - mostrar todas las alertas activas
        // El filtro de leídas solo aplica en el dashboard

        $query->orderByDesc('fecha_alerta')
            ->orderByDesc('fecha_registro');

        $perPage = (int) $request->input('per_page', 20);
        $alertas = $query->paginate($perPage)->appends($request->except('page'));

        // Pasar el ID del usuario para verificar lectura en la vista
        $userId = $user->id_usuario;

        return view('alertas.index', compact('alertas', 'userId'));
    }

    /**
     * Muestra una alerta concreta, la marca como leida para el usuario actual
     * y redirige al documento o vehiculo/conductor relacionado.
     */
    public function show(Alerta $alerta)
    {
        $user = Auth::user();

        // verificar visibilidad
        if (!($alerta->visible_para === 'TODOS' || $alerta->visible_para === $user->rol)) {
            abort(403, 'No autorizado para ver esta alerta.');
        }

        // Marcar como leida para este usuario especifico
        $alerta->marcarLeidaPara($user->id_usuario);

        // Cargar relaciones para obtener IDs correctos
        $alerta->load(['documentoVehiculo.vehiculo', 'documentoConductor.conductor']);

        // Redirigir al vehiculo si es documento de vehiculo
        if ($alerta->documentoVehiculo && $alerta->documentoVehiculo->vehiculo) {
            return redirect()->route('vehiculos.edit', $alerta->documentoVehiculo->vehiculo->id_vehiculo)
                ->with('success', 'Alerta marcada como leida.');
        }

        // Redirigir al conductor si es documento de conductor
        if ($alerta->documentoConductor && $alerta->documentoConductor->conductor) {
            return redirect()->route('conductores.edit', $alerta->documentoConductor->conductor->id_conductor)
                ->with('success', 'Alerta marcada como leida.');
        }

        // Si no hay documento relacionado, redirigir al listado de alertas
        return redirect()->route('alertas.index')
            ->with('success', 'Alerta marcada como leida.');
    }

    /**
     * Marca una alerta como leida para el usuario actual.
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

        // Marcar como leida para este usuario especifico
        $alerta->marcarLeidaPara($user->id_usuario);

        // Si es AJAX, retornar JSON
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'id_alerta' => $alerta->id_alerta]);
        }

        // Si es formulario normal, redirigir de vuelta
        return redirect()->back()->with('success', 'Alerta marcada como leida.');
    }

    /**
     * Marca todas las alertas visibles para el usuario actual como leidas.
     * Solo afecta al usuario que ejecuta la accion.
     */
    public function markAllRead(Request $request)
    {
        $user = Auth::user();

        // Obtener alertas no leidas por este usuario
        $alertas = Alerta::where(function ($q) use ($user) {
                $q->where('visible_para', 'TODOS')->orWhere('visible_para', $user->rol);
            })
            ->whereNull('deleted_at')
            ->noLeidasPor($user->id_usuario)
            ->get();

        $count = 0;
        foreach ($alertas as $alerta) {
            $alerta->marcarLeidaPara($user->id_usuario);
            $count++;
        }

        return redirect()->back()->with('success', "Se marcaron $count alertas como leidas.");
    }

    /**
     * Crea (almacena) manualmente una alerta y notifica a roles (util para pruebas o envio manual).
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
        $data['leida'] = 0; // Campo legacy, ya no se usa pero mantenemos por compatibilidad
        $data['creado_por'] = Auth::id();

        $alerta = Alerta::create($data);

        // Notificar a usuarios activos segun visible_para
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
     * Endpoint simple JSON para obtener contador de alertas no leidas por el usuario actual.
     * Cada usuario ve su propio contador.
     */
    public function unreadCount()
    {
        $user = Auth::user();

        $count = Alerta::where(function ($q) use ($user) {
                $q->where('visible_para', 'TODOS')->orWhere('visible_para', $user->rol);
            })
            ->whereNull('deleted_at')
            ->activas() // Solo alertas no solucionadas
            ->noLeidasPor($user->id_usuario)
            ->count();

        return response()->json(['unread' => $count]);
    }
}
