<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Consultas y Reportes</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.4;
        }

        /* Encabezado corporativo moderno */
        .header {
            display: flex;
            align-items: center;
            border-bottom: 4px solid #5b8238;
            padding: 15px 0;
            margin-bottom: 25px;
            background: linear-gradient(to right, #f9f9f9 0%, #ffffff 100%);
        }

        .header img {
            width: 85px;
            height: auto;
            margin-right: 20px;
        }

        .header-content {
            flex: 1;
        }

        .header .company {
            font-size: 22px;
            font-weight: bold;
            color: #5b8238;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .sub-title {
            color: #666;
            font-size: 13px;
            font-weight: 500;
        }

        /* Metadata del reporte */
        .report-meta {
            background: #f8f9fa;
            border-left: 4px solid #a3c585;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 0 4px 4px 0;
        }

        .report-meta h3 {
            color: #333;
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .report-meta .meta-info {
            font-size: 10px;
            color: #777;
            margin-top: 5px;
        }

        /* Sección de filtros mejorada */
        .filters {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .filters-header {
            font-size: 13px;
            font-weight: bold;
            color: #5b8238;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #a3c585;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .filter-item {
            background: #f8f9fa;
            padding: 8px 10px;
            border-radius: 4px;
            font-size: 11px;
        }

        .filter-item strong {
            color: #5b8238;
            font-weight: 600;
            margin-right: 5px;
        }

        .filter-item span {
            color: #555;
        }

        .no-filters {
            color: #999;
            font-style: italic;
            font-size: 11px;
        }

        /* Tabla moderna y limpia */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 6px;
            overflow: hidden;
        }

        thead {
            background: linear-gradient(135deg, #5b8238 0%, #6a9745 100%);
        }

        th {
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #e8e8e8;
            font-size: 10px;
            color: #444;
        }

        tbody tr {
            background: white;
            transition: background 0.2s;
        }

        tbody tr:nth-child(even) {
            background: #f9fdf9;
        }

        tbody tr:hover {
            background: #f0f7ed;
        }

        /* Columnas específicas */
        td:first-child,
        th:first-child {
            padding-left: 12px;
        }

        td:last-child,
        th:last-child {
            padding-right: 12px;
        }

        /* Badges para estados */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-vigente {
            background: #d4edda;
            color: #155724;
        }

        .badge-vencido {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-por-vencer {
            background: #fff3cd;
            color: #856404;
        }

        .badge-reemplazado {
            background: #e2e3e5;
            color: #383d41;
        }

        /* Footer del reporte */
        .report-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            font-size: 10px;
            color: #999;
        }

        .report-footer .generated {
            font-weight: 600;
            color: #666;
        }

        /* Resumen de totales */
        .summary {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 12px;
            margin-top: 15px;
            display: flex;
            justify-content: space-around;
        }

        .summary-item {
            text-align: center;
        }

        .summary-item .number {
            font-size: 20px;
            font-weight: bold;
            color: #5b8238;
        }

        .summary-item .label {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }

        /* Marca de agua sutil */
        .watermark {
            position: fixed;
            bottom: 50%;
            left: 50%;
            transform: translate(-50%, 50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(163, 197, 133, 0.08);
            font-weight: bold;
            z-index: -1;
            white-space: nowrap;
        }
    </style>
</head>

<body>

    {{-- Marca de agua opcional --}}
    <div class="watermark">CONFIDENCIAL</div>

    {{-- ENCABEZADO CORPORATIVO --}}
    <div class="header">
        <img src="{{ public_path('imagenes/Logo_solo.png') }}" alt="Logo">
        <div class="header-content">
            <div class="company">Club Campestre Altos del Chicalá</div>
            <div class="sub-title">Sistema de Control Vehicular - Reporte de Documentos</div>
        </div>
    </div>

    {{-- METADATA DEL REPORTE --}}
    <div class="report-meta">
        <h3>Reporte de Documentos Vehiculares</h3>
        <div class="meta-info">
            Generado el: {{ date('d/m/Y H:i:s') }} |
            Total de registros: {{ count($documentos) }}
        </div>
    </div>

    {{-- FILTROS APLICADOS --}}
    <div class="filters">
        <div class="filters-header">Filtros Aplicados</div>

        @php
        $hasFilters = false;
        @endphp

        <div class="filters-grid">
            @if($request->filled('documentos'))
            @php $hasFilters = true; @endphp
            <div class="filter-item">
                <strong>Documentos:</strong>
                <span>{{ implode(', ', $request->input('documentos')) }}</span>
            </div>
            @endif

            @if($request->filled('estado'))
            @php $hasFilters = true; @endphp
            <div class="filter-item">
                <strong>Estado:</strong>
                <span>{{ $request->estado }}</span>
            </div>
            @endif

            @if($request->filled('conductor'))
            @php $hasFilters = true; @endphp
            <div class="filter-item">
                <strong>Conductor:</strong>
                <span>{{ $request->conductor }}</span>
            </div>
            @endif

            @if($request->filled('placa'))
            @php $hasFilters = true; @endphp
            <div class="filter-item">
                <strong>Placa:</strong>
                <span>{{ strtoupper($request->placa) }}</span>
            </div>
            @endif

            @if($request->filled('propietario'))
            @php $hasFilters = true; @endphp
            <div class="filter-item">
                <strong>Propietario:</strong>
                <span>{{ $request->propietario }}</span>
            </div>
            @endif

            @if($request->filled('fecha_from'))
            @php $hasFilters = true; @endphp
            <div class="filter-item">
                <strong>Desde:</strong>
                <span>{{ $request->fecha_from }}</span>
            </div>
            @endif

            @if($request->filled('fecha_to'))
            @php $hasFilters = true; @endphp
            <div class="filter-item">
                <strong>Hasta:</strong>
                <span>{{ $request->fecha_to }}</span>
            </div>
            @endif
        </div>

        @if(!$hasFilters)
        <p class="no-filters">No se aplicaron filtros - Mostrando todos los registros</p>
        @endif
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <table style="width:100%; border-collapse: collapse;" border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Número</th>
                <th>Conductor</th>
                <th>Versión</th>
                <th>F. Registro</th>
                <th>F. Vencimiento</th>
                <th>Estado</th>
                <th>Propietario</th>
                <th>Placa</th>
                <th>Registrado por</th>
            </tr>
        </thead>

        <tbody>
            @forelse($documentos as $d)
            <tr>
                <td>{{ data_get($d, 'tipo_documento') ?? data_get($d, 'Tipo') ?? '—' }}</td>
                <td><strong>{{ data_get($d, 'numero_documento') ?? data_get($d, 'Numero') ?? '—' }}</strong></td>
                <td>{{ data_get($d, 'conductor') ?? '—' }}</td>
                <td style="text-align: center;">{{ data_get($d, 'version') ?? '1' }}</td>
                <td>{{ data_get($d, 'fecha_registro') ?? data_get($d, 'Fecha registro') ?? '—' }}</td>
                <td>{{ data_get($d, 'fecha_vencimiento') ?? data_get($d, 'Fecha vencimiento') ?? '—' }}</td>
                <td>
                    @php
                    $estado = data_get($d, 'estado') ?? data_get($d, 'Estado');
                    $badgeClass = 'badge ';
                    if (stripos($estado, 'VIGENTE') !== false) {
                    $badgeClass .= 'badge-vigente';
                    } elseif (stripos($estado, 'VENCIDO') !== false) {
                    $badgeClass .= 'badge-vencido';
                    } elseif (stripos($estado, 'VENCER') !== false) {
                    $badgeClass .= 'badge-por-vencer';
                    } else {
                    $badgeClass .= 'badge-reemplazado';
                    }
                    @endphp
                    <span class="{{ $badgeClass }}">{{ $estado ?? '—' }}</span>
                </td>
                <td>{{ data_get($d, 'propietario') ?? '—' }}</td>
                <td style="text-align: center;">
                    <strong>{{ data_get($d, 'placa') ?? data_get($d, 'Placa') ?? '—' }}</strong>
                </td>
                <td style="font-size: 9px;">{{ data_get($d, 'creado_por') ?? data_get($d, 'Placa registrada por') ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center; padding: 20px; color: #999;">
                    No se encontraron registros con los filtros aplicados
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- RESUMEN DE ESTADÍSTICAS --}}
    @if(count($documentos) > 0)
    <div class="summary">
        <div class="summary-item">
            <div class="number">{{ count($documentos) }}</div>
            <div class="label">Total Registros</div>
        </div>
        <div class="summary-item">
            <div class="number">
                {{ collect($documentos)->filter(function($d) {
                    $estado = data_get($d, 'estado') ?? data_get($d, 'Estado');
                    return stripos($estado, 'VIGENTE') !== false;
                })->count() }}
            </div>
            <div class="label">Vigentes</div>
        </div>
        <div class="summary-item">
            <div class="number">
                {{ collect($documentos)->filter(function($d) {
                    $estado = data_get($d, 'estado') ?? data_get($d, 'Estado');
                    return stripos($estado, 'VENCIDO') !== false;
                })->count() }}
            </div>
            <div class="label">Vencidos</div>
        </div>
        <div class="summary-item">
            <div class="number">
                {{ collect($documentos)->filter(function($d) {
                    $estado = data_get($d, 'estado') ?? data_get($d, 'Estado');
                    return stripos($estado, 'VENCER') !== false;
                })->count() }}
            </div>
            <div class="label">Por Vencer</div>
        </div>
    </div>
    @endif

    {{-- FOOTER DEL REPORTE --}}
    <div class="report-footer">
        <p class="generated">
            Documento generado automáticamente por el Sistema de Control Vehicular
        </p>
        <p>
            Club Campestre Altos del Chicalá |
            © {{ date('Y') }} Todos los derechos reservados
        </p>
    </div>

</body>

</html>