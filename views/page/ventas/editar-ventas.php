<?php

const NAMEVIEW = "Editar de Ventas";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<div class="container-main mt-5">
  <div class="card border">
    <div class="card-header d-flex justify-content-between align-items-center">
      <!-- Título a la izquierda -->
      <div>
        <h3 class="mb-0">Actualizar</h3>
      </div>
      <!-- Botón a la derecha -->
      <div>
        <a href="listar-ventas2.php" class="btn btn-sm btn-success">
          Mostrar Lista
        </a>
      </div>
    </div>

    <div class="card-body">
      <form action="" method="POST" autocomplete="off" id="formulario-detalle">
        <div class="row g-2">
          <div class="col-md-5">
            <label>
              <input type="radio" name="tipo" value="factura" onclick="inicializarCampos()" >
              Factura
            </label>
            <label>
              <input type="radio" name="tipo" value="boleta" onclick="inicializarCampos()" checked>
              Boleta
            </label>
          </div>
          <!-- N° serie y N° comprobante -->
          <div class="col-md-7 d-flex align-items-center justify-content-end">
            <label for="numserie" class="mb-0">N° serie:</label>
            <input type="text" class="form-control input text-center form-control-sm w-25 ms-2" name="numserie" id="numserie" required
              disabled />
            <label for="numcom" class="mb-0 ms-2">N° comprobante:</label>
            <input type="text" name="numcomprobante" id="numcom" class="form-control text-center input form-control-sm w-25 ms-2" required
              disabled />
          </div>
        </div>
        <!-- Sección Cliente, Fecha y Moneda -->
        <div class="row g-2 mt-3">
          <div class="col-md-5">
            <div class="autocomplete">
              <div class="form-floating">
                <!-- Campo de búsqueda de Cliente -->
                <input name="cliente" id="cliente" type="text" class="autocomplete-input form-control input"
                  placeholder="Buscar Cliente" required>
                <label for="cliente">Buscar Cliente:</label>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="date" class="form-control input" name="fecha" id="fecha" required />
              <label for="fecha">Fecha de venta:</label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <select class="form-select input" id="moneda" name="moneda" style="color: black;" required>
                <option value="soles" selected>Soles</option>
                <!-- Aquí se insertan dinámicamente el resto de monedas -->
              </select>
              <label for="moneda">Moneda:</label>
            </div>
          </div>
        </div>

        <!-- Sección Producto, Precio, Cantidad y Descuento -->
        <div class="row g-2 mt-3">
          <div class="col-md-5">
            <div class="autocomplete">
              <div class="form-floating">
                <!-- Campo de búsqueda de Producto -->
                <input name="producto" id="producto" type="text" class="autocomplete-input form-control input"
                  placeholder="Buscar Producto" required>
                <label for="producto">Buscar Producto:</label>
              </div>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control input" name="precio" id="precio" required />
              <label for="precio">Precio</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control input" name="cantidad" id="cantidad" required />
              <label for="cantidad">Cantidad</label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="input-group">
              <div class="form-floating">
                <input type="number" class="form-control input" name="descuento" id="descuento" required />
                <label for="descuento">Descuento</label>
              </div>
              <button type="button" class="btn btn-success" id="agregarProducto">Agregar</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Sección de Detalles de la Venta -->
  <div class="card mt-2">
    <div class="card-body">
      <table class="table table-striped table-sm" id="tabla-detalle">
        <thead>
          <tr>
            <th>#</th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Dsct</th>
            <th>Importe</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <!-- Aquí se agregarán los detalles de los productos -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- Botón para finalizar la venta -->
  <div class="btn-container text-end mt-3">
    <button id="btnFinalizarVenta" type="button" class="btn btn-success">
      Guardar
    </button>
  </div>
</div>
</div>
<!-- Formulario editar Ventas -->
</body>
</html>
