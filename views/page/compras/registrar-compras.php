<?php
const NAMEVIEW = "Registro de Compras";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>

<div class="container-main mt-5">
  <div class="card border">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h3 class="mb-0">Complete los datos</h3>
      </div>
      <div>
        <a href="listar-compras.php" class="btn btn-success">
          Mostrar Lista
        </a>
      </div>
    </div>
    <div class="card-body">
      <form action="" method="POST" autocomplete="off" id="formulario-detalle">
        <div class="row g-2">
          <div class="col-md-5">
            <label>
              <input class="form-check-input text-start" type="radio" name="tipo" value="factura"
                onclick="inicializarCampos()" checked>
              Factura
            </label>
            <label style="padding-left: 10px;">
              <input class="form-check-input text-start" type="radio" name="tipo" value="boleta"
                onclick="inicializarCampos()">
              Boleta
            </label>
            <!-- <label>
              <input type="radio" name="tipo" value="factura" onclick="inicializarCampos()" checked>
              Factura
            </label>
            <label>
              <input type="radio" name="tipo" value="boleta" onclick="inicializarCampos()">
              Boleta
            </label> -->
          </div>
          <!-- N° serie y N° comprobante -->
          <div class="col-md-7 d-flex align-items-center justify-content-end">
            <label for="numserie" class="mb-0">N° serie:</label>
            <input type="text" class="form-control input text-center form-control-sm w-25 ms-2" name="numserie"
              id="numserie" required disabled />
            <label for="numcom" class="mb-0 ms-2">N° comprobante:</label>
            <input type="text" name="numcomprobante" id="numcom"
              class="form-control text-center input form-control-sm w-25 ms-2" required disabled />
          </div>
        </div>
        <!-- Sección Cliente, Fecha y Moneda -->
        <div class="row g-2 mt-3">
          <div class="col-md-5">
            <div class="form-floating">
              <select class="form-select" id="proveedor" name="proveedor" style="color: black;" required>
                <option selected>Selecciona proveedor</option>
                <!-- Se llenará dinámicamente vía AJAX -->
              </select>
              <label for="proveedor">Proveedor</label>
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
                <!-- “Soles” siempre estático y seleccionado -->
                <option value="Soles" selected>Soles</option>
                <!-- Aquí sólo meteremos el resto -->
              </select>
              <label for="moneda">Moneda:</label>
            </div>
          </div>
        </div>

        <!-- Sección Producto, Precio, Cantidad y Descuento -->
        <div class="row g-2 mt-3">
          <div class="col-md-5">
            <div class="form-floating input-group mb-3">
              <!-- Campo de búsqueda de Producto -->
              <input name="producto" id="producto" type="text" class="autocomplete-input form-control input"
                placeholder="Buscar Producto" required>
              <label for="producto">Buscar Producto: </label>
              <input type="hidden" id="hiddenIdCliente" />
              <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                data-bs-target="#miModal">
                <i class="fas fa-plus-square"></i>
              </button>
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

  <!-- seccion de detalles de la compra -->
  <div class="container-main-2 mt-4">
    <div class="card border">
      <div class="card-body p-3">
        <table class="table table-striped table-sm mb-0" id="tabla-detalle-compra">
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
            <!-- se agregan los detalles del producto -->
          </tbody>
        </table>
      </div>
      <div class="card-footer text-end">
        <table class="tabla table-sm">
          <colgroup>
            <col style="width: 10%;">
            <col style="width: 60%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col style="width: 5%;">
          </colgroup>
          <tbody>
            <tr>
              <td colspan="4" class="text-end">Importe</td>
              <td>
                <input type="text" class="form-control input form-control-sm text-end" id="total" readonly>
              </td>
            </tr>
            <tr>
              <td colspan="4" class="text-end">DSCT</td>
              <td>
                <input type="text" class="form-control input form-control-sm text-end" id="totalDescuento" readonly>
              </td>
            </tr>
            <tr>
              <td colspan="4" class="text-end">IGV</td>
              <td>
                <input type="text" class="form-control input form-control-sm text-end" id="igv" readonly>
              </td>
            </tr>
            <tr>
              <td colspan="4" class="text-end">NETO</td>
              <td>
                <input type="text" class="form-control input form-control-sm text-end" id="neto" readonly>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="mt-4">
          <a href="" type="button" class="btn input btn-success" id="btnFinalizarCompra">
            Guardar
          </a>
          <a href="" type="reset" class="btn input btn-secondary" id="btnCancelarCompra">
            Cancelar
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de registrar producto (versión compacta con estilos) -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md" style="margin-top: 60px;">
    <div class="modal-content" style="background-color: #fff; color: #000;">
      <div class="modal-header">
        <h5 class="modal-title">Registrar producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="form-nuevo-producto">
          <div class="row g-3">
            <div class="col-12">
              <div class="form-floating">
                <select class="form-select" id="marca" name="idmarca" required
                  style="background-color: white; color: black;">
                  <option>Seleccione una opción</option>
                </select>
                <label for="marca">Marca:</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <select class="form-select" id="categoria" name="categoria" required
                  style="background-color: white; color: black;">
                  <option>Seleccione una opción</option>
                </select>
                <label for="categoria">Categoría:</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <select class="form-select" name="subcategoria" id="subcategoria" required
                  style="background-color: white; color: black;">
                  <option value="">Seleccione una opción</option>
                </select>
                <label for="subcategoria">Subcategoría:</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Descripción"
                  style="height: 70px; background-color: white; color: black;"></textarea>
                <label for="descripcion">Descripción:</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <input type="text" class="form-control" id="presentacion" name="presentacion" placeholder="Presentación"
                  style="background-color: white; color: black;" />
                <label for="presentacion">Presentación:</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-floating">
                <input type="number" class="form-control" id="cantidad" name="cantidad" placeholder="Cantidad"
                  style="background-color: white; color: black;" />
                <label for="cantidad">Cantidad:</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="undmedida" name="undmedida" placeholder="Unidad de Medida"
                  style="background-color: white; color: black;" />
                <label for="undmedida">Und. Medida:</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <input type="number" class="form-control" id="precio" name="precio" placeholder="Precio"
                  style="background-color: white; color: black;" />
                <label for="precio">Precio:</label>
              </div>
            </div>
            <div class="col-12">
              <label for="img" class="form-label" style="color: black;">Imagen del producto:</label>
              <input type="file" class="form-control" name="img" id="img" accept="image/png, image/jpeg"
                style="background-color: white; color: black;" />
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary" id="btnRegistrarProducto">Guardar</button>
      </div>
    </div>
  </div>
</div>

</div>
</div>
</body>

</html>

<script src="<?= SERVERURL ?>views/page/compras/js/registrar-compras.js"></script>
<!-- js de carga moneda -->
<script src="<?= SERVERURL ?>views/assets/js/moneda.js"></script>
<?php
require_once "../../partials/_footer.php";
?>