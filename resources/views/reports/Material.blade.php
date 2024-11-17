<!DOCTYPE html>
<html>
<head>
  <title>Reporte de Movimientos</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 8px;
    }

    th {
      text-align: left;
      background-color: #f2f2f2;
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
  </style>
</head>
    <body>
        <div class="logo-container">
            <img src="./img/logo_rp.png" alt="Logo Credisol" width="80">
        </div>

        <h2>Reporte de Material: {{ $movements->name }}</h2>
        <h4>(Ultimos 6 meses)</h4>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Origen</th>
                <th>Material</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Fecha de Creación</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($movements->detail_movements as $movementDetail)
                <tr>
                <td>{{ $movementDetail->movement_id }}</td>
                <td>{{ $movementDetail->movement->source }}</td>
                <td>{{ $movementDetail->material->name }}</td>
                <td>{{ $movementDetail->type == 1 ?'INGRESO' : 'SALIDA' }}</td>
                <td>{{ $movementDetail->quantity }}</td>
                <td>{{ $movementDetail->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </body>
</html>