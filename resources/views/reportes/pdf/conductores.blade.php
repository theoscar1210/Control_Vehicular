<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte por Conductor</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            position: relative;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(91, 130, 56, 0.08);
            font-weight: bold;
            letter-spacing: 10px;
            z-index: -1;
            white-space: nowrap;
        }

        .header {
            background: #5B8238;
            color: white;
            padding: 15px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
        }

        .header-table {
            width: 100%;
            border: none;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
        }

        .conductor {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .conductor-header {
            background: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #5B8238;
            margin-bottom: 10px;
        }

        .conductor-header h3 {
            margin: 0;
            font-size: 12px;
        }

        .conductor-header small {
            color: #666;
        }

        .licencia-info {
            background: #e9f5e9;
            padding: 8px 10px;
            margin-bottom: 10px;
            border-radius: 3px;
        }

        .licencia-info span {
            margin-right: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 5px 8px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background: #5B8238;
            color: white;
            font-size: 9px;
        }

        .estado-vigente {
            color: #155724;
        }

        .estado-por_vencer {
            color: #856404;
        }

        .estado-vencido {
            color: #721c24;
        }

        .estado-sin_documentos {
            color: #6c757d;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-secondary {
            background: #e2e3e5;
            color: #6c757d;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="watermark">CONFIDENCIAL</div>

    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 70px;">
                    <img src="{{ public_path('imagenes/Logo_solo.png') }}" style="width: 55px; height: auto;">
                </td>
                <td>
                    <h1>REPORTE POR CONDUCTOR</h1>
                    <span>Club Campestre Altos del Chicalá</span>
                    <p>Generado el {{ now()->format('d/m/Y H:i') }} | Total: {{ $conductores->count() }} conductores</p>
                </td>
            </tr>
        </table>
    </div>

    @forelse($conductores as $conductor)
    @php
        $licencia = $conductor->documentosConductor->where('tipo_documento', 'Licencia Conducción')->first();
    @endphp
    <div class="conductor">
        <div class="conductor-header">
            <h3>{{ $conductor->nombre }} {{ $conductor->apellido }}</h3>
            <small>
                {{ $conductor->tipo_doc }} {{ $conductor->identificacion }}
                @if($conductor->telefono) | Tel: {{ $conductor->telefono }} @endif
            </small>
        </div>

        @if($licencia)
        <div class="licencia-info">
            <span><strong>Licencia:</strong> {{ $licencia->numero_documento ?? 'N/A' }}</span>
            <span><strong>Categoría:</strong> {{ $licencia->categoria_licencia ?? 'N/A' }}
                @if($licencia->categorias_adicionales) + {{ $licencia->categorias_adicionales }} @endif
            </span>
            <span><strong>Vence:</strong>
                @if($licencia->fecha_vencimiento)
                    <span class="estado-{{ strtolower($licencia->estado) }}">
                        {{ $licencia->fecha_vencimiento->format('d/m/Y') }} ({{ $licencia->estado }})
                    </span>
                @else
                    Sin fecha
                @endif
            </span>
        </div>
        @else
        <div class="licencia-info" style="background: #f8d7da;">
            <span style="color: #721c24;">Sin licencia de conducción registrada</span>
        </div>
        @endif

        @if($conductor->vehiculos->count() > 0)
        <div style="margin-top: 5px;">
            <strong style="font-size: 9px; color: #5B8238;">Vehículo(s) Asignado(s):</strong>
            @foreach($conductor->vehiculos as $v)
            <span class="badge badge-secondary" style="margin-left: 5px;">
                {{ $v->placa }} - {{ $v->tipo }} ({{ $v->marca }} {{ $v->modelo }})
            </span>
            @endforeach
        </div>
        @else
        <p style="color: #666; font-size: 9px; margin-top: 5px;">Sin vehículos asignados</p>
        @endif
    </div>
    @empty
    <p style="text-align: center; padding: 30px;">No hay conductores registrados</p>
    @endforelse

    <div class="footer">
        <p>Documento CONFIDENCIAL - Uso interno</p>
        <p>Sistema de Control Vehicular - Club Campestre Altos del Chicalá</p>
    </div>
</body>

</html>
