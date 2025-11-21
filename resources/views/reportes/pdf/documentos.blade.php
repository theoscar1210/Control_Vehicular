<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Consultas y Reportes</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        /* Encabezado corporativo */
        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #5b8238;
            /* color corporativo */
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header img {
            width: 80px;
            margin-right: 15px;
        }

        .header .company {
            font-size: 20px;
            font-weight: bold;
            color: #a3c585;
        }

        .sub-title {
            color: #555;
            font-size: 13px;
        }

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #5b8238;
            color: white;
        }

        tr:nth-child(even) {
            background: #f5f8ff;
        }

        .filters p {
            margin: 0;
            font-size: 12px;
        }

        .filters strong {
            color: #9de4afff;
        }
    </style>
</head>

<body>

    {{-- ENCABEZADO CON LOGO --}}
    <div class="header">
        <img src="{{ public_path('imagenes/Logo_solo.png') }}" alt="Logo Empresa">
        <div>
            <div class="company">Club Campestre Altos del Chicalá</div>
            <div class="sub-title">Reporte de Documentos – Control Vehicular</div>
        </div>
    </div>

    <h3 style="color:#333333;">Reporte de Documentos</h3>

    {{-- FILTROS --}}
    <div class="filters">
        <p><strong>Filtros aplicados:</strong></p>
        @if($request->filled('documentos'))
        <p>Documentos: {{ implode(',', $request->input('documentos')) }}</p>
        @endif

        @if($request->filled('estado'))
        <p>Estado: {{ $request->estado }}</p>
        @endif

        @if($request->filled('conductor'))
        <p>Conductor: {{ $request->conductor }}</p>
        @endif

        @if($request->filled('placa'))
        <p>Placa: {{ $request->placa }}</p>
        @endif

        @if($request->filled('propietario'))
        <p>Propietario: {{ $request->propietario }}</p>
        @endif
    </div>

    {{-- TABLA --}}
    <table>
        <thead>
            <tr>
                <th>Origen</th>
                <th>Tipo</th>
                <th>Número</th>
                <th>Conductor</th>
                <th>Versión</th>
                <th>Registro</th>
                <th>Vencimiento</th>
                <th>Estado</th>
                <th>Propietario</th>
                <th>Placa</th>
                <th>Creado por</th>
            </tr>
        </thead>

        <tbody>
            @foreach($documentos as $d)
            <tr>
                <td>{{ data_get($d, 'tipo_documento') ?? data_get($d, 'Tipo') }}</td>
                <td>{{ data_get($d, 'numero_documento') ?? data_get($d, 'Numero') }}</td>
                <td>{{ data_get($d, 'conductor') ?? '' }}</td>
                <td>{{ data_get($d, 'version') ?? '' }}</td>
                <td>{{ data_get($d, 'fecha_registro') ?? data_get($d, 'Fecha registro') }}</td>
                <td>{{ data_get($d, 'fecha_vencimiento') ?? data_get($d, 'Fecha vencimiento') }}</td>
                <td>{{ data_get($d, 'estado') ?? data_get($d, 'Estado') }}</td>
                <td>{{ data_get($d, 'propietario') ?? '' }}</td>
                <td>{{ data_get($d, 'placa') ?? data_get($d, 'Placa') }}</td>
                <td>{{ data_get($d, 'creado_por') ?? data_get($d, 'Placa registrada por') }}</td>
            </tr>
            @endforeach
        </tbody>

    </table>

</body>

</html>