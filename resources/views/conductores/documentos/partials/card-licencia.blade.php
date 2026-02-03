@php
    $estado = $doc->estado;
    $claseBadge = $doc->clase_badge;
    $diasRestantes = $doc->diasRestantes();

    // Determinar clase del borde según estado
    $claseBorde = match($estado) {
        'VENCIDO' => 'danger',
        'POR_VENCER' => $diasRestantes <= 5 ? 'danger' : 'warning',
        default => 'success'
    };
@endphp

<div class="col-md-6 col-lg-4 mb-3">
    <div class="card h-100 border-{{ $claseBorde }}" style="border-width: 2px;">
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
            {{-- Categoría Principal --}}
            <div class="mb-3">
                <small class="text-muted d-block mb-1">Categoría Principal:</small>
                <span class="badge bg-dark fs-6 px-3 py-2">
                    {{ $doc->categoria_licencia ?? 'N/A' }}
                </span>
            </div>

            {{-- Categorías Adicionales --}}
            @if($doc->categorias_adicionales)
            <div class="mb-3">
                <small class="text-muted d-block mb-1">Categorías Adicionales:</small>
                @foreach(explode(',', $doc->categorias_adicionales) as $cat)
                <span class="badge bg-secondary me-1">{{ trim($cat) }}</span>
                @endforeach
            </div>
            @endif

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
                <strong>Expedición:</strong> {{ \Carbon\Carbon::parse($doc->fecha_emision)->format('d/m/Y') }}
            </p>
            @endif

            {{-- Fecha de vencimiento --}}
            @if($doc->fecha_vencimiento)
            <div class="bg-light rounded p-3 mb-3">
                <p class="mb-1 small text-muted">Fecha de vencimiento:</p>
                <h5 class="text-{{ $claseBorde }} mb-1">{{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}</h5>
                @if($estado !== 'VENCIDO')
                <p class="mb-0 small text-muted">
                    <i class="fa-solid fa-clock me-1"></i>
                    Días restantes: <strong class="text-{{ $claseBorde }}">{{ $diasRestantes }}</strong>
                </p>
                @else
                <p class="mb-0 small text-danger">
                    <i class="fa-solid fa-exclamation-circle me-1"></i>
                    Vencido hace <strong>{{ abs($diasRestantes) }}</strong> días
                </p>
                @endif
            </div>
            @endif

            {{-- Versión --}}
            <p class="mb-0 small text-muted">
                <i class="fa-solid fa-code-branch me-1"></i>
                Versión: {{ $doc->version }}
            </p>
        </div>

        {{-- Footer con botón de renovar si está vencido o por vencer --}}
        @if(in_array($estado, ['VENCIDO', 'POR_VENCER']))
        <div class="card-footer bg-light">
            <button type="button"
                class="btn btn-{{ $claseBorde }} w-100"
                data-bs-toggle="modal"
                data-bs-target="#modalRenovarLicencia_{{ $doc->id_doc_conductor }}">
                <i class="fa-solid fa-sync-alt me-1"></i> Renovar Licencia
            </button>
        </div>
        @else
        <div class="card-footer bg-light text-center">
            <span class="badge bg-success py-2 px-3">
                <i class="fa-solid fa-check-circle me-1"></i> Documento al día
            </span>
        </div>
        @endif
    </div>
</div>
