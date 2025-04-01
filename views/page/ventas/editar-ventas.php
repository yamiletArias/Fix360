<?php
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SESSION VENTAS</title>
  <style>
    .container-ventas {
      background: transparent;
      padding: 30px;
      border-radius: 8px;
      box-shadow: none;
      width: 1500px; /* Aumenta el tamaño */
      min-height: 700px; /* Aumenta la altura */
      margin-left: 80px; /* Lo mueve más a la derecha */
      margin-top: 50px;
    }

    .form-group {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      margin-top: 25px;
      gap: 15px;
    }

    .form-group label {
      margin-right: 10px;
    }

    /* Mantener el borde visible en todo momento incluso al enfocar el campo */
    input:focus,
    select:focus {
      outline: none;
      /* Elimina el contorno predeterminado del navegador */
      border: 1px solid #ccc;
      /* Mantiene el borde visible */
      box-shadow: none;
      /* Elimina cualquier sombra que aparezca al enfocar */
    }

    input,
    select,
    button {
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    input[type="text"],
    select {
      flex: 1;
    }

    input[type="date"] {
      width: 160px;
    }

    .small-input {
      width: 130px;
    }

    .medium-input {
      width: 200px;
    }

    .table-container {
      margin-top: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    table,
    th,
    td {
      border: 1px solid #ccc;
      text-align: center;
      padding: 10px;
    }

    .btn-container {
      display: flex;
      justify-content: flex-end;
      margin-top: 40px;
    }

    .btn-finalizar {
      background: green;
      color: white;
      padding: 12px;
      border: none;
      cursor: pointer;
      font-size: 16px;
    }

    .header-group {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .right-group {
      display: flex;
      gap: 10px;
    }
  </style>
</head>

<body>
      <div class="container-ventas">
        <div class="header-group">
          <div class="form-group">
            <input type="radio" name="tipo" id="factura" checked />
            <label for="factura">Factura</label>
            <input type="radio" name="tipo" id="boleta" />
            <label for="boleta">Boleta</label>
          </div>
          <div class="right-group">
            <input type="text" class="small-input" placeholder="N° serie" />
            <input type="text" class="small-input" placeholder="N° comprobante" />
          </div>
        </div>

        <!-- Proveedor, fecha y moneda -->
        <div class="form-group">
          <select class="medium-input">
            <option>Proveedor</option>
          </select>
          <input type="date" />
          <select class="small-input">
            <option>Soles</option>
            <option>Dolares</option>
          </select>
        </div>

        <!-- Productos -->
        <div class="form-group">
          <input type="text" class="medium-input" placeholder="PRODUCTO" />
          <input type="text" class="small-input" placeholder="PRECIO" />
          <input type="text" class="small-input" placeholder="CANTIDAD" />
          <input type="text" class="small-input" placeholder="DESCUENTO" />
          <button>AGREGAR</button>
        </div>
        <div class="table-container">
          <table id="miTabla" class="table table-striped display">
            <thead>
              <tr>
                <th>N°</th>
                <th>PRODUCTO</th>
                <th>PRECIO</th>
                <th>CANTIDAD</th>
                <th>DSCT</th>
                <th>TOTAL</th>
                <th>
                  <button class="delete-btn">
                    <i class="fas fa-times"></i>
                  </button>
                </th>
              </tr>
            </thead>
            <tbody>
              <!-- Aquí irán los datos dinámicos -->
            </tbody>
          </table>
        </div>

        <!-- Botón Finalizar alineado a la derecha -->
        <div class="btn-container">
          <button type="button" onclick="window.location.href='listar-ventas.html'" class="btn btn-success">
            Finalizar
          </button>
        </div>
      </div>
    </div>
  </div>
  <!--FIN VENTAS-->

  <!-- plugins:js -->
  <script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="../../assets/js/off-canvas.js"></script>
  <script src="../../assets/js/hoverable-collapse.js"></script>
  <script src="../../assets/js/misc.js"></script>
  <script src="../../assets/js/settings.js"></script>
  <script src="../../assets/js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page -->
  <!-- End custom js for this page -->

  <!-- jQuery (necesario para DataTables) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

  <script>
    $(document).ready(function () {
      $("#miTabla").DataTable();
    });
  </script>

</body>

</html>