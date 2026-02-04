@php
    use Carbon\Carbon;

    $estado = $doc->estado;
    $claseBadge = $doc->clase_badge;
    $diasRestantes = $doc->diasRestantes();
    $todasCategorias = $doc->todas_categorias;
    $categoriasMonitoreadas = $doc->getCategoriasAMonitorear();
    $fechasPorCategoria = $doc->fechas_por_categoria ?? [];

    // Determinar clase del borde según estado general
    $claseBorde = match($estado) {
        'VENCIDO' => 'danger',
        'POR_VENCER' => $diasRestantes <= 5 ? 'danger' : 'warning',
        default => 'success'
    };

    // Función para calcular estado de categoría individual
    $getEstadoCategoria = function($categoria) use ($fechasPorCategoria, $doc) {
        $fechaVenc = $fechasPorCategoria[$categoria]['fecha_vencimiento'] ?? null;

        if (!$fechaVenc) {
            // Si no tiene fecha específica, usar la general
            $fechaVenc = $doc->fecha_vencimiento;
        }

        if (!$fechaVenc) {
            return ['estado' => 'VIGENTE', 'clase' => 'success', 'dias' => 0, 'fecha' => null];
        }

        $hoy = Carbon::today();
        $vence = Carbon::parse($fechaVenc)->startOfDay();
        $dias = (int) $hoy->diffInDays($vence, false);

        if ($dias < 0) {
            return ['estado' => 'VENCIDO', 'clase' => 'danger', 'dias' => $dias, 'fecha' => $vence];
        } elseif ($dias <= 5) {
            return ['estado' => 'POR_VENCER', 'clase' => 'danger', 'dias' => $dias, 'fecha' => $vence];
        } elseif ($dias <= 20) {
            return ['estado' => 'POR_VENCER', 'clase' => 'warning', 'dias' => $dias, 'fecha' => $vence];
        }
        return ['estado' => 'VIGENTE', 'clase' => 'success', 'dias' => $dias, 'fecha' => $vence];
    };
@endphp

<div class="col-12 mb-4">
    <div class="card doc-card h-100 border-{{ $claseBorde }}" style="border-width: 2px;">
        <div class="card-header bg-{{ $claseBorde }} text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="fa-solid fa-id-card me-1"></i> Licencia de Conducción
                </h6>
                <span class="badge bg-white text-{{ $claseBorde }}">
                    @if($estado === 'VIGENTE')
                    <i class="fa-solid fa-check-circle me-1"></i> VIGENTE
                    @elseif($estado === 'POR_VENCER')
                    <i class="fa-solid fa-clock me-1"></i> POR VENCER
                    @else
                    <i class="fa-solid fa-times-circle me-1"></i> VENCIDO
                    @endif
                </span>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                {{-- Columna izquierda: Información general --}}
                <div class="col-md-4 border-end">
                    {{-- Número de documento --}}
                    <p class="mb-2">
                        <i class="fa-solid fa-hashtag text-muted me-1"></i>
                        <strong>N°:</strong> {{ $doc->numero_documento }}
                    </p>

                    {{-- Entidad emisora --}}
                    @if($doc->entidad_emisora)
                    <p class="mb-2">
                        <i class="fa-solid fa-building text-muted me-1"></i>
                        <strong>Entidad:</strong> {{ $doc->entidad_emisora }}
                    </p>
                    @endif

                    {{-- Fecha de expedición --}}
                    @if($doc->fecha_emision)
                    <p class="mb-2">
                        <i class="fa-solid fa-calendar-plus text-muted me-1"></i>
                        <strong>Expedición:</strong> {{ Carbon::parse($doc->fecha_emision)->format('d/m/Y') }}
                    </p>
                    @endif

                    {{-- Versión --}}
                    <p class="mb-2 small text-muted">
                        <i class="fa-solid fa-code-branch me-1"></i>
                        Versión: {{ $doc->version }}
                    </p>

                    {{-- Leyenda de monitoreo --}}
                    <hr>
                    <p class="mb-0 small text-muted">
                        <i class="fa-solid fa-bell me-1"></i>
                        <strong>Monitoreadas:</strong> Las categorías con
                        <i class="fa-solid fa-eye text-primary"></i> generan alertas
                    </p>
                </div>

                {{-- Columna derecha: Categorías con fechas individuales --}}
                <div class="col-md-8">
                    <h6 class="fw-bold mb-3">
                        <i class="fa-solid fa-layer-group me-1"></i>
                        Categorías de la Licencia
                    </h6>

                    @if(count($todasCategorias) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 100px;">Categoría</th>
                                    <th>Vencimiento</th>
                                    <th class="text-center" style="width: 80px;">Estado</th>
                                    <th class="text-center" style="width: 50px;">
                                        <i class="fa-solid fa-bell" title="Monitoreada"></i>
                                    </th>
                                    <th class="text-center" style="width: 100px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todasCategorias as $categoria)
                                @php
                                    $infoCat = $getEstadoCategoria($categoria);
                                    $esMonitoreada = in_array($categoria, $categoriasMonitoreadas);
                                    $esPrincipal = $categoria === $doc->categoria_licencia;
                                    $necesitaRenovar = in_array($infoCat['estado'], ['VENCIDO', 'POR_VENCER']);
                                @endphp
                                <tr class="{{ $necesitaRenovar ? 'table-' . $infoCat['clase'] : '' }}" style="{{ $necesitaRenovar ? 'opacity: 0.95;' : '' }}">
                                    <td>
                                        <span class="badge {{ $esPrincipal ? 'bg-dark' : 'bg-secondary' }} px-2 py-1">
                                            {{ $categoria }}
                                        </span>
                                        @if($esPrincipal)
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Principal</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($infoCat['fecha'])
                                        <span class="fw-medium">{{ $infoCat['fecha']->format('d/m/Y') }}</span>
                                        <br>
                                        <small class="text-{{ $infoCat['clase'] }}">
                                            @if($infoCat['dias'] < 0)
                                            <i class="fa-solid fa-exclamation-triangle me-1"></i>
                                            Vencido hace {{ abs($infoCat['dias']) }} días
                                            @elseif($infoCat['dias'] == 0)
                                            <i class="fa-solid fa-exclamation-circle me-1"></i>
                                            ¡Vence hoy!
                                            @else
                                            <i class="fa-solid fa-clock me-1"></i>
                                            {{ $infoCat['dias'] }} días restantes
                                            @endif
                                        </small>
                                        @else
                                        <span class="text-muted">Sin fecha</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $infoCat['clase'] }}">
                                            @if($infoCat['estado'] === 'VIGENTE')
                                            <i class="fa-solid fa-check"></i>
                                            @elseif($infoCat['estado'] === 'POR_VENCER')
                                            <i class="fa-solid fa-clock"></i>
                                            @else
                                            <i class="fa-solid fa-times"></i>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($esMonitoreada)
                                        <i class="fa-solid fa-eye text-primary" title="Generando alertas"></i>
                                        @else
                                        <i class="fa-solid fa-eye-slash text-muted" title="Sin alertas"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($necesitaRenovar)
                                        <button type="button"
                                            class="btn btn-sm btn-{{ $infoCat['clase'] }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalRenovarCategoria_{{ $doc->id_doc_conductor }}_{{ $categoria }}"
                                            title="Refrendar categoría {{ $categoria }}">
                                            <i class="fa-solid fa-sync-alt"></i>
                                        </button>
                                        @else
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="fa-solid fa-check"></i> OK
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-warning mb-0">
                        <i class="fa-solid fa-exclamation-triangle me-1"></i>
                        No hay categorías registradas
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer: Resumen y botón de renovar todo --}}
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        <i class="fa-solid fa-calendar me-1"></i>
                        Próximo vencimiento general:
                        <strong class="text-{{ $claseBorde }}">
                            {{ $doc->fecha_vencimiento ? Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}
                        </strong>
                    </small>
                </div>
                @if(in_array($estado, ['VENCIDO', 'POR_VENCER']))
                <button type="button"
                    class="btn btn-{{ $claseBorde }}"
                    data-bs-toggle="modal"
                    data-bs-target="#modalRenovarLicencia_{{ $doc->id_doc_conductor }}">
                    <i class="fa-solid fa-sync-alt me-1"></i> Renovar Licencia Completa
                </button>
                @else
                <span class="badge bg-success py-2 px-3">
                    <i class="fa-solid fa-check-circle me-1"></i> Documento al día
                </span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modales de renovación por categoría --}}
@foreach($todasCategorias as $categoria)
@php
    $infoCat = $getEstadoCategoria($categoria);
    $necesitaRenovar = in_array($infoCat['estado'], ['VENCIDO', 'POR_VENCER']);
@endphp
@if($necesitaRenovar)
@include('conductores.documentos.partials.modal-renovar-categoria', [
    'doc' => $doc,
    'conductor' => $conductor,
    'categoria' => $categoria,
    'infoCat' => $infoCat,
    'modalId' => 'modalRenovarCategoria_' . $doc->id_doc_conductor . '_' . $categoria
])
@endif
@endforeach
