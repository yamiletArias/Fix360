<?php
const NAMEVIEW = "Cotizacion";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-DQvkBjpPgn7RC31MCQoOeC9TI2kdqa4+BSgNMNj8v77fdC77Kj5zpWFTJaaAoMbC" crossorigin="anonymous">

</head>
<div class="container mt-5">
  <form action="" method="POST" autocomplete="off" id="formulario-detalle">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col"><strong>Registrar Cotización</strong></div>
          <div class="col text-end">
            <a href="listar-cotizacion.php" class="btn btn-sm btn-success"
              style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
              Mostrar Lista
            </a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <!-- Fecha y Vigencia -->
        <div class="row g-2">
          <div class="col-md-6">
            <div class="form-floating">
              <input type="date" class="form-control" name="fecha" required />
              <label for="fecha">Fecha de Cotización</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="date" class="form-control" name="vigencia" required />
              <label for="vigencia">Vigencia</label>
            </div>
          </div>
        </div>

        <!-- Cliente y Moneda -->
        <div class="row g-2 mt-3">
          <div class="col-md-6">
            <div class="form-floating">
              <select class="form-select" id="cliente" name="cliente" style="color: black;" required>
                <option>Cliente:</option>
              </select>
              <label for="cliente">Cliente:</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <select class="form-select" id="moneda" name="moneda" style="color: black;" required>
                <option value="soles" selected>Soles</option>
                <!-- Aquí se insertan dinámicamente el resto de monedas -->
              </select>
              <label for="moneda">Moneda:</label>
            </div>
          </div>
        </div>

        <!-- Productos -->
        <div class="row g-2 mt-3">
          <div class="col-md-6">
            <div class="form-floating">
              <input name="producto" id="producto" type="text" class="form-control" placeholder="Producto" required />
              <label for="producto">Producto</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control" name="precio" id="precio" placeholder="Precio" required
                readonly />
              <label for="precio">Precio</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control" name="cantidad" id="cantidad" placeholder="Cantidad" required />
              <label for="cantidad">Cantidad</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="input-group">
              <div class="form-floating">
                <input type="number" class="form-control" name="descuento" id="descuento" required>
                <label for="descuento">Descuento</label>
              </div>
              <button type="button" class="btn btn-success" id="agregarProducto" type="submit">Agregar</button>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Tabla de Productos -->
    <div class="card mt-2">
      <div class="card-body">
        <table class="table table-striped table-sm" id="tabla-detalle-cotizacion">
          <thead>
            <tr>
              <th>N°</th>
              <th>Producto</th>
              <th>Precio</th>
              <th>Cantidad</th>
              <th>Descuento</th>
              <th>Total</th>
              <th>
                Acciones
                <!-- <button class="btn btn-danger btn-sm">
                <i class="fas fa-times"></i>
              </button> -->
              </th>
            </tr>
          </thead>
          <tbody>
            <!-- Se agregarán los detalles -->
          </tbody>
        </table>
      </div>
    </div>


    <!-- Botón Finalizar -->
    <div class="btn-container text-end mt-4">
      <button onclick="window.location.href='listar-cotizacion.php'" class="btn btn-success">
        Finalizar
      </button>
    </div>
  </form>
</div>


</div>
</div>

<?php

require_once "../../partials/_footer.php";

?>

</body>

</html>