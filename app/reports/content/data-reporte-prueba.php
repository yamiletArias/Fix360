<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cotizacion</title>
</head>

<body>
  <!---
codigos de los colores que se utilizan en el logo de fix
azul:#01122c;
verde:b1fc40;
blanco:f2f5f8;
todo en español para q no c note que es de chat
--->


  <style>
    html {
      min-height: 100%;
      position: relative;
    }

    body {
      font-family: sans-serif;
      margin: 0px;
      display: flex;
      margin-bottom: 40px;

    }

    .cabezera {

      height: 200px;
      width: 1000px;
      display: block;
      align-items: center;
      justify-content: space-between;
      padding: 20px;
    }

    .direccion {
      margin-left: 20px;
      font-size: 14px;
    }

    .numcotizacion {
      font-size: 18px;
      font-weight: bold;
      margin-left: 300px;
      display: flex;

    }

    .barrazul {
      width: 500px;
      height: 5px;
      background: #01122c;
    }

    .barraverde {
      width: 200px;
      height: 5px;
      background: #b1fc40;
    }

    .blanco {
      color: #f2f5f8;
    }

    .verde {
      color: #b1fc40;
    }

    .azul {
      color: #01122c;
    }

    .img-header {
      width: 700px;
    }

    .img-footer {
      width: 700px;
      margin-top: 70px;
    }


    p {
      padding: 0px;
      margin: 3px;
    }

    th {
      border-bottom: 1pt solid #01122c;
    }

    table {
      border-collapse: collapse;
    }

    footer {
      height: 80px;
      position: absolute;
      bottom: 0;
      width: 100%;
      
    }



    .parent {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      grid-template-rows: repeat(6, 1fr);
      grid-column-gap: 0px;
      grid-row-gap: 0px;
    }

    .div1 {
      grid-area: 2 / 1 / 3 / 2;
    }

    .div2 {
      grid-area: 2 / 5 / 3 / 6;
    }

    .div3 {
      grid-area: 3 / 1 / 4 / 6;
    }

    .div4 {
      grid-area: 4 / 1 / 5 / 6;
    }

    .div5 {
      grid-area: 5 / 1 / 6 / 6;
    }

    .div6 {
      grid-area: 6 / 1 / 7 / 6;
    }

    .div7 {
      grid-area: 1 / 1 / 2 / 6;
    }
  </style>

  <div class="parent">
    <div class="div7">
      <img class="img-header" src="<?php echo realpath(__DIR__ . '/../../../images/headert.png'); ?>" alt="">
    </div>
    <div class="div1">
      <p>chincha 25 de Marzo 2025</p>
    </div>
    <div class="div2" style="margin-left: 500px;">
      <p>Cotizacion Nro: 010-000010</p>
    </div>
    <div class="div3">
      <p>Cliente: Jose Hernandez</p>
      <p>Attn. Sr(es):</p>
      <p>Estimado señor, por medio de la presente nos es grato dirigirnos a ustedes para saludarlos y presentarles nuestra cotizacion, por lo siguiente:</p>
    </div>
    <div class="div4">
      <table>
        <tbody>
          <tr>
            <th>CANT.</th>
            <th>UNI.</th>
            <th>Descripcion</th>
            <th>P.UNIT.</th>
            <th>P.TOTAL</th>
          </tr>
          <tr>
            <td>2</td>
            <td>UND</td>
            <td>Aceite para auto</td>
            <td>20.00</td>
            <td>40.00</td>
          </tr>
          <tr>
            <td>2</td>
            <td>UND</td>
            <td>Repuesto para auto</td>
            <td>10.00</td>
            <td>20.00</td>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>TOTAL S/.</td>
            <td>60.00</td>
          </tr>

        </tbody>
      </table>
    </div>
    <div class="div5">
      <p>Sin otro en particular y en espera de su pronta respuesta, quedamos a su disposicion para cualquier consulta que ustedes estimen conveniente.</p>
      <strong>
        <h4> Condiciones Generales</h4>
      </strong>
      <p>Cuenta corriente BCP: $</p>
      <p>Cuenta corriente BCP: S/ </p>
      <p>Cuenta corriente BBVA: $</p>
      <p>Cuenta corriente BBVA: S/ </p>
      <p>Cuenta corriente INTERBANK: $</p>
      <p>Cuenta corriente INTERBANK: S/ </p>
      <p>Precios: De acuerdo al tipo de cambio del dia</p>
      <p>Validez de la oferta: 24 horas</p>
      <p>Forma de Pago: Efectivo y transferencia</p>
      <p>924 160 710</p>
      <p>Saludos cordiales,</p>
      <p>Elena</p>
    </div>
    <div class=" div6">
      <div class="footer">
        <img class="img-footer" src="<?php echo realpath(__DIR__ . '/../../../images/footer.png'); ?>">
      </div>
    </div>
  </div>

</body>


</html>