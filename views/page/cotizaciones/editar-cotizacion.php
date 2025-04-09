<?php
CONST NAMEVIEW = "Editar datos de cotizacion";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<div class="container-main">
  <div class="header-group">
    <div class="left-group">
      <div class="form-label">
        <label for="">Fecha:</label>
        <input type="date">
      </div>
      <div class="form-label">
        <label for="">Vigencia:</label>
        <input type="date">
      </div>
    </div>
  </div>

  <!-- Proveedor, fecha y moneda -->
  <div class="form-group">
    <select class="medium-input">
      <option>Cliente:</option>
      <option>Max</option>
      <option>Jesus</option>
      <option>Eduardo</option>
    </select>
    <select class="small-input">
      <option>SOLES</option>
      <option>SOLARES</option>
    </select>
  </div>

  <!-- Productos -->
  <div class="form-group">
    <input type="text" class="medium-input" placeholder="PRODUCTO" />
    <input type="text" class="small-input" placeholder="PRECIO" />
    <input type="text" class="small-input" placeholder="CANTIDAD" />
    <input type="text" class="small-input" placeholder="DESCUENTO" />
    <button class="btn btn-success">AGREGAR</button>
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
            <button class="btn btn-danger btn-sm">
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
    <button
      onclick="window.location.href='listar-cotizacion.php'"
      class="btn btn-success">
      FINALIZAR
    </button>
  </div>
</div>
</div>
</div>

<?php

require_once "../../partials/_footer.php";

?>

</body>

</html>