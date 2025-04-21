<?php

const NAMEVIEW = "Registro de Órdenes de servicio";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<style>
  .btn-registrar {
    padding: 0px;
    margin: 0px;
  }

  /* .btn-opciones{
    padding: 7px;
  }
    */
</style>

<div class="container-main">
  <div class="card border mb-3">

    <div class="card-body">
      <div class="row">
        <div class="col-md-6 mb-3">
          <div class="form-floating input-group mb-3">
            <input type="text" disabled class="form-control input" id="propietario"
              placeholder="Propietario" />
            <label for="propietario">Propietario</label>
            <input type="hidden" id="hiddenIdCliente" />
            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
              data-bs-target="#miModal">
              ...
            </button>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="form-floating input-group mb-3">
            <input type="text" disabled class="form-control" id="cliente" placeholder="Cliente">
            <label for="cliente">Cliente</label>
            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
              data-bs-target="#ModalCliente">...</button>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="form-floating">
            <select class="form-select" id="vehiculo" name="vehiculo" style="color:black;">
              <option selected>Eliga un vehículo</option>
            </select>
            <label for="vehiculo">Vehículo:</label>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="form-floating">
            <input type="number" step="0.1" class="form-control input" id="kilometraje" placeholder="201">
            <label for="kilometraje">Kilometraje</label>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="form-floating">
            <input type="date" class="form-control input" id="fechaIngreso">
            <label for="fechaIngreso">Fecha de ingreso:</label>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="form-floating">
            <select class="form-select" id="subcategoria" name="subcategoria" style="color: black;"
              required>
              <option selected>Eliga un tipo de servicio</option>

            </select>
            <label for="subcategoria">Tipo de Servicio:</label>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="input-group mb-3">
            <div class="form-floating">
              <select class="form-select" id="servicio" name="servicio" style="color:black;">
                <option selected>Eliga un servicio</option>
              </select>
              <label for="servicio">Servicio:</label>
            </div>
            <button class="btn btn-sm btn-success" type="button" id="button-addon2" data-bs-toggle="modal" data-bs-target="#ModalServicio">
              <i class="fa-solid fa-circle-plus"></i>
            </button>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="form-floating">
            <select class="form-select" id="mecanico" name="mecanico" style="color:black;">
              <option selected>Eliga un mecánico</option>
            </select>
            <label for="mecanico">Mecánico:</label>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="input-group mb-3">
            <div class="form-floating">
              <input type="number" class="form-control input" step="0.1" placeholder="precio" aria-label="Recipient's username" aria-describedby="button-addon2" min="0">
              <label for="precio">Precio</label>
            </div>
            <button class="btn btn-sm btn-success" type="button" id="button-addon2">Agregar</button>
          </div>
        </div>
      </div>
    </div>


  </div>

  <div class="card mt-2 border">
    <div class="card-body">
      <table class="table table-striped table-sm" id="tabla-detalle">
        <thead>
          <tr>
            <th>#</th>
            <th>Servicio</th>
            <th>Mecanico</th>
            <th>Precio</th>
            <th>Eliminar</th>
          </tr>
        </thead>
        <tbody>
          <!-- Datos asíncronos -->
        </tbody>

      </table>
    </div>
  </div>

  <div class="card mt-2 border">
    <div class="card-body">
      <table class="tabla table-sm">
        <colgroup>
          <col style="width: 5%;">
          <col style="width: 60%;">
          <col style="width: 10%;">
          <col style="width: 10%;">
          <col style="width: 10%;">
          <col style="width: 5%;">
        </colgroup>
        <tbody>
          <tr>
            <td colspan="4" class="text-end">Subtotal</td>
            <td>
              <input type="text" class="form-control form-control-sm text-end" id="subtotal" readonly>
            </td>
            <td></td>
          </tr>
          <tr>
            <td colspan="4" class="text-end">IGV</td>
            <td>
              <input type="text" class="form-control form-control-sm text-end" id="igv" readonly>
            </td>
            <td></td>

          </tr>
          <tr>
            <td colspan="4" class="text-end">Neto</td>
            <td>
              <input type="text" class="form-control form-control-sm text-end" id="neto" readonly>
            </td>
            <td></td>

          </tr>
          <tr>
            <td colspan="4" class="text-end">
              <button class="btn btn-success text-end">Aceptar</button>
            </td>
            <td>
              <a class="btn btn-secondary text-end" href="listar-ordenes.php">Cancelar</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>


</div>


</div>
</div>

<?php

require_once "../../partials/_footer.php";

?>

<div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Encabezado -->
      <div class="modal-header">
        <h2 class="modal-title" id="miModalLabel">Seleccionar Propietario</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Cuerpo -->
      <div class="modal-body">

        <!-- Fila para Tipo de Propietario -->
        <div class="row mb-3">
          <div class="col">
            <label><strong>Tipo de propietario:</strong></label>
            <!-- Contenedor de radio buttons -->
            <div style="display: flex; align-items: center; gap: 10px; margin-left:20px;">
              <div class="form-check form-check-inline" style="margin-right:40px;">
                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnpersona"
                  onclick="actualizarOpciones(); buscarPropietario();" checked >
                <label class="form-check-label" for="rbtnpersona"
                  style="margin-left:5px;">Persona</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnempresa"
                  onclick="actualizarOpciones(); buscarPropietario();">
                <label class="form-check-label" for="rbtnempresa"
                  style="margin-left:5px;">Empresa</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Fila para Método de Búsqueda -->
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <select id="selectMetodo" class="form-select" style="color: black;">
                <!-- Se actualizarán las opciones según el tipo (persona/empresa) -->
              </select>
              <label for="selectMetodo">Método de búsqueda:</label>
            </div>
          </div>
        </div>

        <!-- Fila para Valor Buscado -->
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <input type="text" class="form-control" id="vbuscado" style="background-color: white;"
                placeholder="Valor buscado" />
              <label for="vbuscado">Valor buscado</label>
            </div>
          </div>
        </div>

        <!-- Tabla de Resultados -->
        <p class="mt-3"><strong>Resultado:</strong></p>
        <div class="table-responsive">
          <table id="tabla-resultado" class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Confirmar</th>
              </tr>
            </thead>
            <tbody>
              <!-- Se llenará dinámicamente -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pie del Modal -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="ModalCliente" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
  <div class="modal-dialog"> <!-- Modal grande si lo requieres -->
    <div class="modal-content">

      <!-- Encabezado -->
      <div class="modal-header">
        <h2 class="modal-title" id="miModalLabel">Seleccionar Cliente</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Cuerpo -->
      <div class="modal-body">

        <!-- Fila para Tipo de Propietario -->
        <div class="row mb-3">
          <div class="col">
            <label><strong>Tipo de propietario:</strong></label>
            <!-- Contenedor de radio buttons -->
            <div style="display: flex; align-items: center; gap: 10px; margin-left:20px;">
              <div class="form-check form-check-inline" style="margin-right:40px;">
                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnpersona" onclick="actualizarOpciones(); buscarPropietario();" checked>
                <label class="form-check-label" for="rbtnpersona" style="margin-left:5px;">Persona</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnempresa" onclick="actualizarOpciones(); buscarPropietario();">
                <label class="form-check-label" for="rbtnempresa" style="margin-left:5px;">Empresa</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Fila para Método de Búsqueda -->
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <select id="selectMetodo" class="form-select" style="color: black;">
                <!-- Se actualizarán las opciones según el tipo (persona/empresa) -->
              </select>
              <label for="selectMetodo">Método de búsqueda:</label>
            </div>
          </div>
        </div>

        <!-- Fila para Valor Buscado -->
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <input type="text" class="form-control" id="vbuscado" style="background-color: white;" placeholder="Valor buscado" />
              <label for="vbuscado">Valor buscado</label>
            </div>
          </div>
        </div>

        <!-- Tabla de Resultados -->
        <p class="mt-3"><strong>Resultado:</strong></p>
        <div class="table-responsive">
          <table id="tabla-resultado" class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Confirmar</th>
              </tr>
            </thead>
            <tbody>
              <!-- Se llenará dinámicamente -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pie del Modal -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="ModalServicio" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
  <div class="modal-dialog"> <!-- Modal grande si lo requieres -->
    <div class="modal-content">

      <!-- Encabezado -->
      <div class="modal-header">
        <h2 class="modal-title" id="miModalLabel">Registrar Servicio</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Cuerpo -->
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <select id="selectMetodo" class="form-select input" style="color: black;">
                <!-- Se actualizarán las opciones según el tipo (persona/empresa) -->
              </select>
              <label for="selectMetodo">Tipo de Servicio:</label>
            </div>
          </div>
        </div>

        <!-- Fila para Valor Buscado -->
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <input type="text" class="form-control input" id="vbuscado" style="background-color: white;"
                placeholder="Valor buscado" />
              <label for="vbuscado">Servicio</label>
            </div>
          </div>
        </div>

        <!-- Tabla de Resultados -->
  
      </div>

      <!-- Pie del Modal -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Guardar</button>
      </div>

    </div>
  </div>
</div>

<script src="<?= SERVERURL ?>views/page/ordenservicios/js/registrar-ordenes.js"></script>
</body>

</html>