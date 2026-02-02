<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ficha Conductor - {{ $conductor->nombre }} {{ $conductor->apellido }}</title>
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
            margin-bottom: 5px;
        }

        .header-table {
            width: 100%;
            border: none;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            background: #f8f9fa;
            padding: 8px 10px;
            border-left: 4px solid #5B8238;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: bold;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 5px 8px;
            border: 1px solid #ddd;
        }

        .info-table .label {
            background: #f8f9fa;
            font-weight: bold;
            width: 30%;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
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

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .categoria-box {
            display: inline-block;
            border: 1px solid #ddd;
            padding: 8px 12px;
            margin: 5px;
            border-radius: 5px;
            text-align: center;
            min-width: 120px;
        }

        .categoria-box.vigente {
            border-color: #28a745;
        }

        .categoria-box.por-vencer {
            border-color: #ffc107;
        }

        .categoria-box.vencido {
            border-color: #dc3545;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        table.historial {
            width: 100%;
            border-collapse: collapse;
        }

        table.historial th,
        table.historial td {
            padding: 5px 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 9px;
        }

        table.historial th {
            background: #5B8238;
            color: white;
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
                    <h1>FICHA DE CONDUCTOR</h1>
                    <span>Club Campestre Altos del Chicala</span>
                </td>
                <td style="text-align: right;">
                    <span class="badge badge-{{ $estadoGeneral['clase'] }}">{{ $estadoGeneral['texto'] }}</span>
                    <br><br>
                    <small>Generado: {{ now()->format('d/m/Y H:i') }}</small>
                </td>
            </tr>
        </table>
    </div>

    {{-- Informacion Personal --}}
    <div class="section">
        <div class="section-title">INFORMACION PERSONAL</div>
        <table class="info-table">
            <tr>
                <td class="label">Nombre Completo</td>
                <td>{{ $conductor->nombre }} {{ $conductor->apellido }}</td>
                <td class="label">Documento</td>
                <td>{{ $conductor->tipo_doc }} {{ $conductor->identificacion }}</td>
            </tr>
            <tr>
                <td class="label">Telefono</td>
                <td>{{ $conductor->telefono ?? 'No registrado' }}</td>
                <td class="label">Tel. Emergencia</td>
                <td>{{ $conductor->telefono_emergencia ?? 'No registrado' }}</td>
            </tr>
            <tr>
                <td class="label">Estado</td>
                <td colspan="3">
                    <span class="badge badge-{{ $conductor->activo ? 'success' : 'danger' }}">
                        {{ $conductor->activo ? 'ACTIVO' : 'INACTIVO' }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Vehiculo Asignado --}}
    <div class="section">
        <div class="section-title">VEHICULO(S) ASIGNADO(S)</div>
        @if($conductor->vehiculos->count() > 0)
        <table class="info-table">
            <tr>
                <td class="label">Placa</td>
                <td class="label">Tipo</td>
                <td class="label">Marca / Modelo</td>
                <td class="label">Color</td>
            </tr>
            @foreach($conductor->vehiculos as $vehiculo)
            <tr>
                <td><strong>{{ $vehiculo->placa }}</strong></td>
                <td>{{ $vehiculo->tipo }}</td>
                <td>{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</td>
                <td>{{ $vehiculo->color }}</td>
            </tr>
            @endforeach
        </table>
        @else
        <p style="padding: 10px; color: #666;">Sin vehiculo asignado</p>
        @endif
    </div>

    {{-- Licencia de Conduccion --}}
    <div class="section">
        <div class="section-title">LICENCIA DE CONDUCCION</div>
        @if($licencia)
        <table class="info-table">
            <tr>
                <td class="label">Numero de Licencia</td>
                <td>{{ $licencia->numero_documento ?? 'N/A' }}</td>
                <td class="label">Fecha Expedicion</td>
                <td>{{ $licencia->fecha_emision ? $licencia->fecha_emision->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @if($licencia->entidad_emisora)
            <tr>
                <td class="label">Entidad Emisora</td>
                <td colspan="3">{{ $licencia->entidad_emisora }}</td>
            </tr>
            @endif
        </table>

        <p style="padding: 5px 0; font-weight: bold;">Categorias y Vencimientos:</p>
        <div style="padding: 10px;">
            {{-- Categoria Principal --}}
            @php
                $claseCatPrincipal = $licencia->clase_badge;
            @endphp
            <div class="categoria-box {{ $claseCatPrincipal == 'success' ? 'vigente' : ($claseCatPrincipal == 'warning' ? 'por-vencer' : 'vencido') }}">
                <span class="badge badge-info">{{ $licencia->categoria_licencia ?? 'N/A' }}</span>
                <small style="display: block; color: #666;">Principal</small>
                <br>
                @if($licencia->fecha_vencimiento)
                <strong class="{{ $claseCatPrincipal == 'danger' ? 'text-danger' : '' }}">
                    {{ $licencia->fecha_vencimiento->format('d/m/Y') }}
                </strong>
                <br>
                <small>({{ $licencia->diasRestantes() }} dias)</small>
                @else
                <small>Sin fecha</small>
                @endif
            </div>

            {{-- Categorias Adicionales --}}
            @if($licencia->categorias_adicionales)
            @php
                $categoriasAdicionales = explode(',', $licencia->categorias_adicionales);
                $fechasPorCategoria = $licencia->fechas_por_categoria ?? [];
            @endphp
            @foreach($categoriasAdicionales as $catAdicional)
            @php
                $catAdicional = trim($catAdicional);
                $fechaVencCat = $fechasPorCategoria[$catAdicional]['fecha_vencimiento'] ?? null;
                $estadoCat = 'secondary';
                $diasCat = null;
                $claseCat = 'secondary';

                if ($fechaVencCat) {
                    $fechaVencCatCarbon = \Carbon\Carbon::parse($fechaVencCat);
                    $diasCat = (int) now()->startOfDay()->diffInDays($fechaVencCatCarbon->startOfDay(), false);

                    if ($diasCat < 0) {
                        $claseCat = 'danger';
                        $estadoCat = 'vencido';
                    } elseif ($diasCat <= 20) {
                        $claseCat = 'warning';
                        $estadoCat = 'por-vencer';
                    } else {
                        $claseCat = 'success';
                        $estadoCat = 'vigente';
                    }
                }
            @endphp
            <div class="categoria-box {{ $estadoCat }}">
                <span class="badge badge-info">{{ $catAdicional }}</span>
                <small style="display: block; color: #666;">Adicional</small>
                <br>
                @if($fechaVencCat)
                <strong>{{ \Carbon\Carbon::parse($fechaVencCat)->format('d/m/Y') }}</strong>
                <br>
                <small>({{ $diasCat }} dias)</small>
                @else
                <small>Sin fecha</small>
                @endif
            </div>
            @endforeach
            @endif
        </div>
        @else
        <p style="padding: 10px; color: #856404; background: #fff3cd;">
            Sin licencia de conduccion registrada en el sistema.
        </p>
        @endif
    </div>

    {{-- Historial --}}
    @if($historialDocumentos->count() > 0)
    <div class="section">
        <div class="section-title">HISTORIAL DE DOCUMENTOS</div>
        <table class="historial">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Numero</th>
                    <th>Emision</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                    <th>Version</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historialDocumentos as $doc)
                <tr>
                    <td>{{ $doc->tipo_documento }}</td>
                    <td>{{ $doc->numero_documento ?? '-' }}</td>
                    <td>{{ $doc->fecha_emision ? $doc->fecha_emision->format('d/m/Y') : '-' }}</td>
                    <td>{{ $doc->fecha_vencimiento ? $doc->fecha_vencimiento->format('d/m/Y') : '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $doc->activo ? $doc->clase_badge : 'secondary' }}">
                            {{ $doc->activo ? $doc->estado : 'INACTIVO' }}
                        </span>
                    </td>
                    <td>v{{ $doc->version ?? 1 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Documento CONFIDENCIAL - Uso interno</p>
        <p>Sistema de Control Vehicular - Club Campestre Altos del Chicala</p>
    </div>
</body>

</html>
