<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen Semanal de Alertas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background-color: #5B8238;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .summary {
            background-color: #f8f9fa;
            border-left: 4px solid #5B8238;
            padding: 15px;
            margin: 20px 0;
        }
        .alert-section {
            margin: 20px 0;
        }
        .alert-type-header {
            background-color: #5B8238;
            color: white;
            padding: 10px;
            font-weight: bold;
            margin-top: 15px;
            border-radius: 4px;
        }
        .alert-item {
            border-left: 3px solid #dc3545;
            background-color: #fff8f8;
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .alert-item.warning {
            border-left-color: #ffc107;
            background-color: #fffbf0;
        }
        .alert-date {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            background-color: #5B8238;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Resumen Semanal de Alertas</h1>
            <p style="margin: 5px 0 0 0;">Control Vehicular - Club Campestre Altos del Chicalá</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hola <strong>{{ $usuario->nombre }}</strong>,
            </div>

            <p>Este es tu resumen semanal de alertas pendientes del sistema de Control Vehicular.</p>

            <div class="summary">
                <strong>Total de alertas pendientes:</strong> {{ $totalAlertas }}<br>
                <strong>Fecha del reporte:</strong> {{ $fecha }}
            </div>

            @if($totalAlertas > 0)
                <div class="alert-section">
                    <h3>Detalle de Alertas por Tipo:</h3>

                    @foreach($alertasPorTipo as $tipo => $alertas)
                        <div class="alert-type-header">
                            {{ $tipo }} ({{ $alertas->count() }})
                        </div>

                        @foreach($alertas as $alerta)
                            <div class="alert-item {{ $alerta->tipo_vencimiento === 'PROXIMO_VENCER' ? 'warning' : '' }}">
                                <strong>{{ $alerta->mensaje }}</strong>
                                <div class="alert-date">
                                    Fecha de alerta: {{ optional($alerta->fecha_alerta)->format('d/m/Y') ?? 'No especificada' }}
                                </div>
                                @if($alerta->id_doc_vehiculo)
                                    <div class="alert-date">Tipo: Documento de Vehículo</div>
                                @endif
                                @if($alerta->id_doc_conductor)
                                    <div class="alert-date">Tipo: Documento de Conductor</div>
                                @endif
                            </div>
                        @endforeach
                    @endforeach
                </div>

                <div style="text-align: center;">
                    <a href="{{ url('/dashboard') }}" class="button">Ver Alertas en el Sistema</a>
                </div>
            @else
                <p style="text-align: center; color: #28a745; font-weight: bold;">
                    No hay alertas pendientes esta semana.
                </p>
            @endif

            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                <p style="font-size: 14px; color: #666;">
                    <strong>Nota:</strong> Este correo se envía automáticamente cada lunes a las 01:00 AM.
                    Para más detalles, ingresa al sistema de Control Vehicular.
                </p>
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0;">© 2025 Club Campestre Altos del Chicalá</p>
            <p style="margin: 5px 0 0 0;">Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>
