<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha Vehículo - {{ $vehiculo->placa }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
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
            margin-bottom: 20px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .header-logo {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
        }

        .header-logo img {
            width: 60px;
            height: auto;
        }

        .header-title {
            display: table-cell;
            vertical-align: middle;
            padding-left: 15px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header .placa {
            background: white;
            color: #333;
            padding: 8px 15px;
            display: inline-block;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            background: #f8f9fa;
            padding: 8px 10px;
            font-weight: bold;
            border-left: 4px solid #5B8238;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
            font-weight: bold;
            width: 35%;
        }

        .estado-vigente {
            color: #28a745;
            font-weight: bold;
        }

        .estado-por_vencer {
            color: #ffc107;
            font-weight: bold;
        }

        .estado-vencido {
            color: #dc3545;
            font-weight: bold;
        }

        .estado-sin_registro {
            color: #6c757d;
        }

        .estado-exento {
            color: #28a745;
            font-weight: bold;
        }

        .doc-card {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }

        .doc-card h4 {
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .two-col {
            width: 48%;
            display: inline-block;
            vertical-align: top;
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
        <table style="border: none; width: 100%;">
            <tr>
                <td style="border: none; width: 70px; vertical-align: middle;">
                    <img src="{{ public_path('imagenes/Logo_solo.png') }}" style="width: 55px; height: auto;">
                </td>
                <td style="border: none; width: 50%;">
                    <h1>FICHA DE VEHÍCULO</h1>
                    <span>Club Campestre Altos del Chicalá</span>
                    <p>{{ $vehiculo->marca }} {{ $vehiculo->modelo }} - {{ $vehiculo->tipo }}</p>
                </td>
                <td style="border: none; text-align: right;">
                    <div class="placa">{{ $vehiculo->placa }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">INFORMACIÓN DEL VEHÍCULO</div>
        <table>
            <tr>
                <th>Placa</th>
                <td>{{ $vehiculo->placa }}</td>
            </tr>
            <tr>
                <th>Tipo</th>
                <td>{{ $vehiculo->tipo }}</td>
            </tr>
            <tr>
                <th>Marca</th>
                <td>{{ $vehiculo->marca }}</td>
            </tr>
            <tr>
                <th>Modelo</th>
                <td>{{ $vehiculo->modelo }}</td>
            </tr>
            <tr>
                <th>Color</th>
                <td>{{ $vehiculo->color }}</td>
            </tr>
            <tr>
                <th>Estado</th>
                <td>{{ $vehiculo->estado }}</td>
            </tr>
            <tr>
                <th>Fecha Registro</th>
                <td>{{ \Carbon\Carbon::parse($vehiculo->fecha_registro)->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="two-col">
        <div class="section">
            <div class="section-title">PROPIETARIO</div>
            @if($vehiculo->propietario)
            <table>
                <tr>
                    <th>Nombre</th>
                    <td>{{ $vehiculo->propietario->nombre }} {{ $vehiculo->propietario->apellido }}</td>
                </tr>
                <tr>
                    <th>Documento</th>
                    <td>{{ $vehiculo->propietario->tipo_doc }} {{ $vehiculo->propietario->identificacion }}</td>
                </tr>
            </table>
            @else
            <p style="color: #666; padding: 10px;">Sin propietario registrado</p>
            @endif
        </div>
    </div>

    <div class="two-col">
        <div class="section">
            <div class="section-title">CONDUCTOR ASIGNADO</div>
            @if($vehiculo->conductor)
            <table>
                <tr>
                    <th>Nombre</th>
                    <td>{{ $vehiculo->conductor->nombre }} {{ $vehiculo->conductor->apellido }}</td>
                </tr>
                <tr>
                    <th>Documento</th>
                    <td>{{ $vehiculo->conductor->tipo_doc }} {{ $vehiculo->conductor->identificacion }}</td>
                </tr>
                <tr>
                    <th>Teléfono</th>
                    <td>{{ $vehiculo->conductor->telefono ?? 'N/A' }}</td>
                </tr>
            </table>
            @else
            <p style="color: #666; padding: 10px;">Sin conductor asignado</p>
            @endif
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="section">
        <div class="section-title">ESTADO DE DOCUMENTACIÓN</div>
        <table>
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Número</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estadosDocumentos as $tipo => $info)
                @php
                    $nombreTipo = str_replace('conductor_', 'Conductor - ', $tipo);
                    $esExentoPdf = false;
                    $fechaPrimeraRevPdf = null;
                    if ($tipo === 'Tecnomecanica' && !$info['documento']) {
                        $requiereTecnoPdf = $vehiculo->requiereTecnomecanica();
                        $fechaPrimeraRevPdf = $vehiculo->fechaPrimeraTecnomecanica();
                        $esExentoPdf = $vehiculo->fecha_matricula && !$requiereTecnoPdf;
                    }
                @endphp
                <tr>
                    <td><strong>{{ $nombreTipo }}</strong></td>
                    @if($esExentoPdf)
                    <td>-</td>
                    <td>{{ $fechaPrimeraRevPdf?->format('d/m/Y') }}</td>
                    <td class="estado-exento">Vehículo "Nuevo" (Exención)</td>
                    @else
                    <td>{{ $info['documento']->numero_documento ?? '-' }}</td>
                    <td>{{ $info['documento'] ? \Carbon\Carbon::parse($info['documento']->fecha_vencimiento)->format('d/m/Y') : '-' }}</td>
                    <td class="estado-{{ strtolower($info['estado']) }}">{{ $info['mensaje'] }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($historialReciente->count() > 0)
    <div class="section">
        <div class="section-title">HISTORIAL DE DOCUMENTOS (Últimos 10)</div>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Número</th>
                    <th>Emisión</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historialReciente as $doc)
                <tr style="color: #999;">
                    <td>{{ $doc->tipo_documento }}</td>
                    <td>{{ $doc->numero_documento }}</td>
                    <td>{{ \Carbon\Carbon::parse($doc->fecha_emision)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}</td>
                    <td>{{ $doc->estado }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Ficha generada el {{ now()->format('d/m/Y H:i') }} | Documento CONFIDENCIAL - Uso interno</p>
        <p>Sistema de Control Vehicular - Club Campestre Altos del Chicalá</p>
    </div>
</body>

</html>