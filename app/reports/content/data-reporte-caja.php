<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Caja</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container-main {
            background: transparent;
            padding: 30px;
            width: 100%;
            margin: 0;
        }

        .title {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 5px;
            margin-top: -40px; /* Ajuste para mover el título hacia arriba */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 7px;
            text-align: left;
            font-size: 15px;
        }

        th {
            font-weight: bold;
        }

        td {
            font-size: 15px;
            position: relative; /* Necesario para agregar la línea de forma correcta */
        }

        /* Línea extendida */
        td::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100vw; /* Extiende la línea hasta el borde de la ventana */
            border-bottom: 2px solid #000;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .input-container {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            margin-left: 50px; /* Ajuste para mover los inputs hacia la derecha */
        }

        .label-container {
            width: 40%;
            font-weight: bold;
        }

        .value-container {
            width: 60%;
            text-align: right;
        }

        .highlight {
            background-color: #FFEB3B;
            font-weight: bold;
        }

    </style>
</head>
<body>

<div class="container-main">
    <div class="title">
        ARQUEO DE CAJA - FIX 360
    </div>
    <table>
        <tr>
            <th>Presentado por</th>
            <td>Elena Castilla</td>
        </tr>
        <tr>
            <th>Fecha</th>
            <td>2025-03-24</td>
        </tr>
        <tr>
            <th>Hora de inicio</th>
            <td>08:00</td>
        </tr>
        <tr>
            <th>Hora de cierre</th>
            <td>18:00</td>
        </tr>
    </table>

    <!-- Saldo Inicial -->
    <div class="section-title">Saldo Inicial</div>
    <div class="input-container">
        <div>Saldo restante:</div>
        <div class="value-container">S/ 385.00</div>
    </div>

    <div class="section-title">Ingresos</div>
    <div class="input-container">
        <div>Efectivo</div>
        <div class="value-container">S/ 50.00</div>
    </div>

    <div class="label-container">Digital</div>
    <div class="input-container">
        <div>Yape</div>
        <div class="value-container">S/ 50.00</div>
        <div>Plin</div>
        <div class="value-container">S/ 40.00</div>
        <div>Visa</div>
        <div class="value-container">S/ 25.00</div>
        <div>Deposito</div>
        <div class="value-container">S/ 200.00</div>
    </div>

    <div class="section-title">Egresos</div>
    <div class="input-container">
        <div>Combustible</div>
        <div class="value-container">S/ -</div>
        <div>Almuerzo</div>
        <div class="value-container">S/ -</div>
        <div>Pasaje</div>
        <div class="value-container">S/ 16.00</div>
        <div>Compra de insumo</div>
        <div class="value-container">S/ 20.00</div>
        <div>Servicios varios</div>
        <div class="value-container">S/ -</div>
        <div>Otros conceptos</div>
        <div class="value-container">S/ -</div>
        <div class="label-container">Gerencia</div>
        <div class="value-container">S/ -</div>
    </div>

    <div class="section-title">Resumen</div>
    <div class="input-container">
        <div>Saldo anterior en efectivo</div>
        <div class="value-container">S/ 385.00</div>
        <div>Ingreso diario efectivo</div>
        <div class="value-container">S/ 545.00</div>
        <div>Total efectivo</div>
        <div class="value-container">S/ 930.00</div>
        <div>Total egresos</div>
        <div class="value-container">S/ 272.00</div>
        <div>Total caja</div>
        <div class="value-container">S/ 658.00</div>
        <div>Otros Aportes registrados</div>
        <div class="value-container">S/ -</div>
        <div class="label-container">Banco, Yape, Plin</div>
        
    </div>

</div>

</body>
</html>
