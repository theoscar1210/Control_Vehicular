<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Consultas y Reportes</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            padding: 20px;
            color: #333;
        }

        /* HEADER */
        .header {
            border-bottom: 3px solid #5b8238;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .header table {
            width: 100%;
        }

        .header img {
            width: 80px;
        }

        .company {
            font-size: 18px;
            font-weight: bold;
            color: #5b8238;
        }

        .sub-title {
            font-size: 12px;
            color: #666;
        }

        /* META */
        .report-meta {
            background: #f2f2f2;
            border-left: 4px solid #5b8238;
            padding: 10px;
            margin-bottom: 15px;
        }

        .report-meta h3 {
            font-size: 14px;
            margin-bottom: 4px;
        }

        /* FILTERS */
        .filters {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
        }

        .filters-title {
            font-weight: bold;
            color: #5b8238;
            margin-bottom: 6px;
        }

        .filter-item {
            margin-bottom: 3px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #5b8238;
        }

        th {
            color: #ffffff;
            font-size: 9px;
            padding: 6px;
            border: 1px solid #ffffff;
            text-align: left;
        }

        td {
            font-size: 9px;
            padding: 6px;
            border: 1px solid #dddddd;
        }

        tbody tr:nth-child(even) {
            background-color: #f6f6f6;
        }

        /* BADGES */
        .badge {
            padding: 2px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 3px;
        }

        .vigente {
            background: #d4edda;
            color: #155724;
        }

        .vencido {
            background: #f8d7da;
            color: #721c24;
        }

        .por-vencer {
            background: #fff3cd;
            color: #856404;
        }

        .otro {
            background: #e2e3e5;
            color: #383d41;
        }

        /* SUMMARY */
        .summary {
            margin-top: 15px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .summary-item {
            margin-bottom: 5px;
        }

        /* FOOTER */
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #777;
        }

        /* WATERMARK */
        .watermark {
            position: fixed;
            top: 45%;
            left: 15%;
            font-size: 60px;
            color: #cccccc;
            opacity: 0.15;
            transform: rotate(-45deg);
        }
    </style>
</head>

<body>

    <div class="watermark">CONFIDENCIAL</div>

    <!-- HEADER -->
    <div class="header">
        <table>
            <tr>
                <td width="90">
                    <img src="{{ public_path('imagenes/Logo_solo.png') }}">
                </td>
                <td>
                    <div class="company">Club Campestre Altos del Chicalá</div>
                    <div class="sub-title">Sistema de Control Vehicular - Reporte de Documentos</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- META -->
    <div class="report-meta">
        <h3>Reporte de Documentos Vehiculares</h3>
        Generado el: {{ date('d/m/Y H:i:s') }} |
        Total registros: {{ count($documentos) }}
    </div>

    <!-- FILTERS -->
    <div class="filters">
        <div class="filters-title">Filtros Aplicados</div>

        @php $hasFilters = false; @endphp

        @if($request->filled('documentos'))
        @php $hasFilters = true; @endphp
        <div class="filter-item"><strong>Documentos:</strong> {{ implode(', ', $request->documentos) }}</div>
        @endif

        @if($request->filled('estado'))
        @php $hasFilters = true; @endphp
        <div class="filter-item"><strong>Estado:</strong> {{ $request->estado }}</div>
        @endif

        @if($request->filled('placa'))
        @php $hasFilters = true; @endphp
        <div class="filter-item"><strong>Placa:</strong> {{ strtoupper($request->placa) }}</div>
        @endif

        @if(!$hasFilters)
        <div class="filter-item">Sin filtros aplicados</div>
        @endif
    </div>

    <!-- TABLE -->
    <table border="1" cellpadding="0" cellspacing="0">
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
            @php
            $estado = data_get($d,'estado') ?? data_get($d,'Estado','');
            $class = str_contains($estado,'VIGENTE') ? 'vigente'
            : (str_contains($estado,'VENCIDO') ? 'vencido'
            : (str_contains($estado,'VENCER') ? 'por-vencer' : 'otro'));
            @endphp
            <tr>
                <td>{{ data_get($d,'tipo_documento','—') }}</td>
                <td>{{ data_get($d,'numero_documento','—') }}</td>
                <td>{{ data_get($d,'conductor','—') }}</td>
                <td align="center">{{ data_get($d,'version','1') }}</td>
                <td>{{ data_get($d,'fecha_registro','—') }}</td>
                <td>{{ data_get($d,'fecha_vencimiento','—') }}</td>
                <td><span class="badge {{ $class }}">{{ $estado ?: '—' }}</span></td>
                <td>{{ data_get($d,'propietario','—') }}</td>
                <td align="center">{{ data_get($d,'placa','—') }}</td>
                <td>{{ data_get($d,'creado_por','—') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" align="center">No se encontraron registros</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SUMMARY -->
    @if(count($documentos))
    <div class="summary">
        <div class="summary-item"><strong>Total:</strong> {{ count($documentos) }}</div>
    </div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        Documento generado automáticamente – {{ date('Y') }}
    </div>

</body>

</html>