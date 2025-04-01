<?php

CONST NAMEVIEW = "Kardex";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<div class="container-main">
  <div class="mb-3">
    <div class="form-group">
      <div class="mb-3">
        <div class="input-group" style="padding-left:0px;">
          <span for="busqueda" class="input-group-text">Producto:</span>
          <input type="text" id="busqueda" class="form-control" style="width:  350px;">
          <button class="btn btn-danger btn-sm">
            <i class="fa-solid fa-file-pdf"></i>
          </button>
        </div>
      </div>

      <div class="mb-3" style="margin-left:620px;">
        <div class="input-group" style="margin-bottom: 10px;">
          <span class="input-group-text" id="basic-addon3">Cantidad Min:</span>
          <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3 basic-addon4"
            disabled>
        </div>
        <div class="mb-3">
          <div class="input-group">
            <span class="input-group-text" id="basic-addon3">Cantidad Max:</span>
            <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3 basic-addon4"
              disabled>
          </div>
        </div>
      </div>
    </div>
    <div class="table-container">
      <table id="miTabla" class="table table-striped display">
        <thead>
          <tr>
            <th class="text-center">#</th>
            <th class="text-center">Fecha</th>
            <th class="text-center">Flujo</th>
            <th class="text-center">T. Movimiento</th>
            <th>Cantidad</th>
            <th>Saldo</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>19/03/2024</td>
            <td>Entrada</td>
            <td>Compra</td>
            <td>20</td>
            <td>25</td>
          </tr>
          <tr>
            <td>2</td>
            <td>18/03/2024</td>
            <td>Salida</td>
            <td>Venta</td>
            <td>1</td>
            <td>24</td>
          </tr>
        </tbody>

      </table>
    </div>
  </div>


  <?php

  require_once "../../partials/_footer.php";
  
 ?>

  <script>
    $(document).ready(function() {
      let timeoutId; // Variable para almacenar el temporizador de debounce

      $("#busqueda").on("input", function() {
        clearTimeout(timeoutId); // Cancela la ejecución anterior

        timeoutId = setTimeout(() => {
          let query = $(this).val();
          if (query.length > 2) { // Evita búsquedas con pocas letras
            console.log("Buscando:", query);


          }
        }, 500); // Espera 500ms después de que el usuario deja de escribir
      });
    });
  </script>

  </body>

  </html>