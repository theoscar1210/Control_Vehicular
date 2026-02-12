<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Vehículos</title>
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

        .header {
            background: #5B8238;
            color: white;
            padding: 15px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
        }

        .header p {
            font-size: 10px;
            margin-top: 5px;
        }

        .header-table {
            width: 100%;
            border: none;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background: #5B8238;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        .estado-vigente {
            background: #d4edda;
            color: #155724;
        }

        .estado-por_vencer {
            background: #fff3cd;
            color: #856404;
        }

        .estado-vencido {
            background: #f8d7da;
            color: #721c24;
        }

        .estado-sin_documentos {
            background: #e2e3e5;
            color: #383d41;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .stats {
            margin-bottom: 15px;
        }

        .stats span {
            display: inline-block;
            padding: 5px 10px;
            margin-right: 10px;
            border-radius: 3px;
        }

        .confidencial-badge {
            background: #dc3545;
            color: white;
            font-size: 8px;
            padding: 2px 6px;
            border-radius: 3px;
            margin-left: 10px;
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
                    <h1>REPORTE GENERAL DE VEHÍCULOS</h1>
                    <span>Club Campestre Altos del Chicalá</span>
                    <p>Generado el {{ now()->format('d/m/Y H:i') }} | Total: {{ $vehiculos->count() }} vehículos</p>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Placa</th>
                <th>Tipo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Clasificación</th>
                <th>Propietario</th>
                <th>Conductor</th>
                <th>Estado Documental</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehiculos as $v)
            <tr>
                <td><strong>{{ $v->placa }}</strong></td>
                <td>{{ $v->tipo }}</td>
                <td>{{ $v->marca }}</td>
                <td>{{ $v->modelo }}</td>
                <td>{{ ucfirst(strtolower($v->clasificacion ?? 'N/A')) }}</td>
                <td>{{ $v->propietario ? $v->propietario->nombre . ' ' . $v->propietario->apellido : '-' }}</td>
                <td>{{ $v->conductor ? $v->conductor->nombre . ' ' . $v->conductor->apellido : '-' }}</td>
                <td class="estado-{{ strtolower($v->estado_general['estado']) }}">{{ $v->estado_general['texto'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px;">No hay vehículos registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Documento CONFIDENCIAL - Uso interno</p>
        <p>Sistema de Control Vehicular - Club Campestre Altos del Chicalá</p>
    </div>
</body>

</html>