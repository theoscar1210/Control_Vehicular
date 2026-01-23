@php
$dias = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($doc->fecha_vencimiento)->startOfDay(), false);
$modalId = 'modalRenovarSOAT_' . $doc->id_doc_vehiculo;
// Colores: Rojo (danger): VENCIDO o 0-5 días | Amarillo (warning): 6-20 días | Verde (success): > 20 días
$claseColor = $dias < 0 || $dias <= 5 ? 'danger' : ($dias <= 20 ? 'warning' : 'success');
$textoColor = $claseColor === 'warning' ? 'text-dark' : 'text-white';
@endphp

<div class="col-md-6 col-lg-4 mb-3">
    <div class="card h-100 border-{{ $claseColor }}" style="border-width: 2px;">

        {{-- HEADER --}}
        <div class="card-header bg-{{ $claseColor }} {{ $textoColor }}">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="fa-solid fa-shield-halved me-1"></i> SOAT
                </h6>
                <span class="badge
                    @if($claseColor == 'success') bg-white text-success
                    @elseif($claseColor == 'warning') bg-dark text-warning
                    @else bg-white text-danger
                    @endif">
                    v{{ $doc->version }}
                </span>
            </div>
        </div>

        {{-- BODY --}}
        <div class="card-body">
            <p class="mb-2">
                <strong><i class="fa-solid fa-hashtag me-1 text-muted"></i>Número:</strong><br>
                <span class="ms-3">{{ $doc->numero_documento }}</span>
            </p>

            @if($doc->entidad_emisora)
            <p class="mb-2">
                <strong><i class="fa-solid fa-building me-1 text-muted"></i>Aseguradora:</strong><br>
                <span class="ms-3">{{ $doc->entidad_emisora }}</span>
            </p>
            @endif

            <p class="mb-2">
                <strong><i class="fa-solid fa-calendar-plus me-1 text-muted"></i>Emisión:</strong><br>
                <span class="ms-3">{{ \Carbon\Carbon::parse($doc->fecha_emision)->format('d/m/Y') }}</span>
            </p>

            <p class="mb-2">
                <strong><i class="fa-solid fa-calendar-xmark me-1 text-muted"></i>Vencimiento:</strong><br>
                <span class="ms-3">{{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}</span>
                <br>
                <span class="badge mt-1
                    @if($dias > 20) bg-success
                    @elseif($dias > 5) bg-warning text-dark
                    @else bg-danger
                    @endif">
                    @if($dias > 0)
                    <i class="fa-solid fa-clock"></i> {{ $dias }} días restantes
                    @elseif($dias == 0)
                    <i class="fa-solid fa-exclamation-circle"></i> Vence hoy
                    @else
                    <i class="fa-solid fa-times-circle"></i> Vencido hace {{ abs($dias) }} días
                    @endif
                </span>
            </p>

            @if($doc->nota)
            <p class="mb-0">
                <strong><i class="fa-solid fa-note-sticky me-1 text-muted"></i>Nota:</strong><br>
                <span class="ms-3"><em class="text-muted small">{{ $doc->nota }}</em></span>
            </p>
            @endif
        </div>

        {{-- FOOTER --}}
        <div class="card-footer bg-light">
            @if($doc->estado === 'VENCIDO')
            <button type="button"
                class="btn btn-danger w-100"
                data-bs-toggle="modal"
                data-bs-target="#{{ $modalId }}">
                <i class="fa-solid fa-arrow-rotate-right"></i> Renovar Ahora
            </button>
            @elseif($doc->estado === 'POR_VENCER')
            <button type="button"
                class="btn btn-warning w-100"
                data-bs-toggle="modal"
                data-bs-target="#{{ $modalId }}">
                <i class="fa-solid fa-exclamation-triangle"></i> Próximo a Vencer
            </button>
            @else
            <div class="text-center">
                <span class="badge bg-success py-2 px-3">
                    <i class="fa-solid fa-check-circle me-1"></i> Vigente
                </span>
            </div>
            @endif
        </div>
    </div>
</div>