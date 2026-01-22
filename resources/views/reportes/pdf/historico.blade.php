<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Histórico</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; position: relative; }

        /* Marca de agua CONFIDENCIAL */
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

        .header { background: #5B8238; color: white; padding: 15px; margin-bottom: 15px; }
        .header h1 { font-size: 16px; }
        .header-table { width: 100%; border: none; }
        .header-table td { border: none; vertical-align: middle; }
        .section-title { background: #f8f9fa; padding: 8px 10px; font-weight: bold; border-left: 4px solid #5B8238; margin: 15px 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 5px 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #5B8238; color: white; font-size: 9px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .renovacion { color: #155724; }
        .nuevo { color: #0056b3; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
        .confidencial-badge { background: #dc3545; color: white; font-size: 8px; padding: 2px 6px; border-radius: 3px; margin-left: 10px; }
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
                    <h1>REPORTE HISTÓRICO DE DOCUMENTOS <span class="confidencial-badge">CONFIDENCIAL</span></h1>
                    <p>Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">DOCUMENTOS DE VEHÍCULOS ({{ $historialVehiculos->count() }})</div>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Placa</th>
                <th>Documento</th>
                <th>Número</th>
                <th>Acción</th>
                <th>Estado</th>
                <th>Versión</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historialVehiculos as $doc)
            <tr>
                <td>{{ \Carbon\Carbon::parse($doc->fecha_registro)->format('d/m/Y H:i') }}</td>
                <td><strong>{{ $doc->vehiculo->placa ?? 'N/A' }}</strong></td>
                <td>{{ $doc->tipo_documento }}</td>
                <td>{{ $doc->numero_documento }}</td>
                <td class="{{ $doc->version > 1 ? 'renovacion' : 'nuevo' }}">{{ $doc->version > 1 ? 'Renovación' : 'Nuevo' }}</td>
                <td>{{ $doc->estado }}</td>
                <td>v{{ $doc->version }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align: center;">Sin registros en este período</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">DOCUMENTOS DE CONDUCTORES ({{ $historialConductores->count() }})</div>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Conductor</th>
                <th>Documento</th>
                <th>Número</th>
                <th>Acción</th>
                <th>Estado</th>
                <th>Versión</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historialConductores as $doc)
            <tr>
                <td>{{ \Carbon\Carbon::parse($doc->fecha_registro)->format('d/m/Y H:i') }}</td>
                <td><strong>{{ $doc->conductor ? $doc->conductor->nombre . ' ' . $doc->conductor->apellido : 'N/A' }}</strong></td>
                <td>{{ $doc->tipo_documento }}</td>
                <td>{{ $doc->numero_documento }}</td>
                <td class="{{ $doc->version > 1 ? 'renovacion' : 'nuevo' }}">{{ $doc->version > 1 ? 'Renovación' : 'Nuevo' }}</td>
                <td>{{ $doc->estado }}</td>
                <td>v{{ $doc->version }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align: center;">Sin registros en este período</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Documento CONFIDENCIAL - Uso interno</p>
        <p>Sistema de Control Vehicular - Club Campestre Altos del Chicalá</p>
    </div>
</body>
</html>
