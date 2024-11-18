<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Movimientos</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 20px;
            color: #333; /* Color del texto */
            position: relative; /* Necesario para usar z-index */
        }

        .header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            position: relative; /* Necesario para usar z-index */
            z-index: 1; /* El título está por encima del logo */
        }

        h2, h3, h4 {
            color: #333; /* Color de los títulos */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px; 
        }

        th, td {
            border: 1px solid #ddd; /* Color del borde de la tabla */
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2; /* Color de fondo de la cabecera */
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9; /* Color de fondo de las filas pares */
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .details-table th, .details-table td {
            border: 1px solid #ddd; 
            padding: 5px;
            text-align: left;
        }

        .movement-section {
            margin-bottom: 20px; 
        }

        .details-section {
            margin-bottom: 10px;
        }

        .details-label {
            font-weight: bold;
        }

        .logo-container {
            position: absolute;
            top: -30px; /* Ajusta la posición vertical */
            right: -30px; /* Ajusta la posición horizontal */
            z-index: 0; /* El logo está detrás del título */
        }

        .logo-container img {
            max-width: 200px;
            height: auto;
        }

        .separator {
            border-top: 2px dashed #ddd; /* Línea punteada */
            margin-top: 20px; /* Espacio arriba de la línea */
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="./img/logo_rp.png" alt="Logo Credisol" width="80">
    </div>

    <div class="header">
        <h3>Informe de Movimientos ({{$date_init}} - {{$date_finish}})</h3>
    </div>
    <div class="table-container">
        @if (count($movements) > 0)
            @php
                $orden = 1; // Inicializa el contador de orden
            @endphp
            @foreach ($movements as $movimiento)
                <div class="movement-section">
                    <h3>Movimiento N°{{ $orden }}</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Fuente</th>
                                <th>Descripción</th>
                                <th>Usuario que creó</th>
                                <th>Fecha de Creación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $movimiento->id }}</td>
                                <td>{{ $movimiento->source }}</td>
                                <td>{{ $movimiento->description ?? '-' }}</td>
                                <td>{{ $movimiento->user_created_at }}</td>
                                <td>{{ $movimiento->created_at }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="details-section">
                        <h4>Detalles del Movimiento</h4>
                        @if (count($movimiento->movementDetails) > 0)
                            <table class="details-table">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Material</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $ordenDetalle = 1; // Inicializa el contador de orden
                                    @endphp
                                    @foreach ($movimiento->movementDetails as $detalle)
                                        <tr>
                                            <td>{{ $ordenDetalle }}</td>
                                            <td>{{ $detalle->material_id }}</td>
                                            <td>{{ $detalle->type == 1 ? 'INGRESO': 'SALIDA' }}</td>
                                            <td>{{ $detalle->quantity }}</td>
                                        </tr>
                                        @php
                                            $ordenDetalle++; // Inicializa el contador de orden
                                        @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>No se encontraron detalles para este movimiento.</p>
                        @endif
                    </div>
                    <div class="separator"></div>  <!-- Línea punteada separadora -->
                    @php
                        $orden++; // Incrementa el contador de orden
                    @endphp
                </div>
            @endforeach
        @else
            <p>No se encontraron movimientos.</p>
        @endif
    </div>
</body>
</html>