@php
$dias = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($doc->fecha_vencimiento)->startOfDay(), false);
$modalId = 'modalRenovarTecno_' . $doc->id_doc_vehiculo;
@endphp

<div class="col-md-6 col-lg-4 mb-3">
    <div class="card h-100 
        @if($doc->estado == 'VIGENTE') border-success
        @elseif($doc->estado == 'POR_VENCER') border-warning
        @else border-danger
        @endif"
        style="border-width: 2px;">

        {{-- HEADER --}}
        <div class="card-header 
            @if($doc->estado == 'VIGENTE') bg-success text-white
            @elseif($doc->estado == 'POR_VENCER') bg-warning text-dark
            @else bg-danger text-white
            @endif">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="fa-solid fa-car-side me-1"></i> Tecnomecánica
                </h6>
                <span class="badge 
                    @if($doc->estado == 'VIGENTE') bg-white text-success
                    @elseif($doc->estado == 'POR_VENCER') bg-dark text-warning
                    @else bg-white text-danger
                    @endif">
                    v{{ $doc->version }}
                </span>
            </div>
        </div>

        {{-- BODY --}}
        <div class="card-body">
            <p class="mb-2">
                <strong><i class="fa-solid fa-hashtag me-1 text-muted"></i>Certificado:</strong><br>
                <span class="ms-3">{{ $doc->numero_documento }}</span>
            </p>

            @if($doc->entidad_emisora)
            <p class="mb-2">
                <strong><i class="fa-solid fa-building me-1 text-muted"></i>CDA:</strong><br>
                <span class="ms-3">{{ $doc->entidad_emisora }}</span>
            </p>
            @endif

            <p class="mb-2">
                <strong><i class="fa-solid fa-calendar-plus me-1 text-muted"></i>Revisión:</strong><br>
                <span class="ms-3">{{ \Carbon\Carbon::parse($doc->fecha_emision)->format('d/m/Y') }}</span>
            </p>

            <p class="mb-2">
                <strong><i class="fa-solid fa-calendar-xmark me-1 text-muted"></i>Vencimiento:</strong><br>
                <span class="ms-3">{{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}</span>
                <br>
                <span class="badge mt-1 
                    @if($dias > 30) bg-success
                    @elseif($dias > 0) bg-warning text-dark
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