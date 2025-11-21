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
    public function __construct()
    {
        // proteger con auth
        $this->middleware('auth');
    }

    /**
     * Lista las alertas visibles para el usuario (no eliminadas) paginadas.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Alerta::query()->whereNull('deleted_at')
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
     * (puedes personalizar la redirección al documento relacionado).
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

        // redirigir al documento si existe, si no al listado de alertas
        if ($alerta->id_doc_conductor) {
            return redirect()->route('conductores.edit', $alerta->id_doc_conductor)
                ->with('info', 'Alerta marcada como leída.');
        }

        if ($alerta->id_doc_vehiculo) {
            return redirect()->route('vehiculos.show', $alerta->id_doc_vehiculo)
                ->with('info', 'Alerta marcada como leída.');
        }

        return view('alertas.show', compact('alerta'));
    }

    /**
     * API / AJAX: marca una alerta como leída (retorna JSON).
     */
    public function markAsRead(Request $request, Alerta $alerta)
    {
        $user = Auth::user();
        if (!($alerta->visible_para === 'TODOS' || $alerta->visible_para === $user->rol)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $alerta->leida = 1;
        $alerta->save();

        return response()->json(['ok' => true, 'id_alerta' => $alerta->id_alerta]);
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
