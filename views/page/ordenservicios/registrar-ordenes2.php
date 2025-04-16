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
    <div class="card-header">
      <div class="row">
        <h4 class="col-md-4">Propietario</h4>
        <div class="col-md-7"></div>
        <button class="text-center col-md-1  btn btn-sm btn-registrar btn-success">
          Registrar
        </button>
      </div>
    </div>
    <div class="card-body">
      <div class="row">


        <div class="col-md-6">
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
        <div class="col-md-6">
          <div class="form-floating input-group mb-3">
            <input type="text" disabled class="form-control" id="cliente" placeholder="Cliente">
            <label for="cliente">Cliente</label>
            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
              data-bs-target="#miModal">...</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card border">
    <div class="card-header">
      <div class="row">
        <h4 class="col-md-4">Vehiculos</h4>
        <div class="col-md-7"></div>
        <button class="text-center col-md-1  btn btn-sm btn-registrar btn-success">
          Registrar
        </button>
      </div>
    </div>
    <div class="card-body">
      <table class="table table-striped ">
        <thead>
          <tr>
            <th>#</th>
            <th>T. Vehiculo</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Placa</th>
            <th>Color</th>
            <th>mecanico</th>
            <th>Operaciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Camioneta</td>
            <td>Briliance</td>
            <td>MarcaDCarro</td>
            <td>8S5WCK</td>
            <td>Rojo</td>
            <td>Walter Aquije</td>
            <td>
              <a href="#" class="btn btn-sm btn-warning btn-opciones" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a> <!-- Edita DETEQUIPOS -->
              <a href="#" class="btn btn-sm btn-danger btn-opciones" title="Eliminar"><i class="fa-solid fa-trash"></i></a> <!-- Elimina de forma física de DETEQUIPOS -->
              <a href="#" class="btn btn-sm btn-info btn-opciones" title="Características"><i class="fa-solid fa-clipboard-list"></i></a> <!-- Datos de CARACTERÍSTICAS / ESPECIFICACIONES -->
              <a href="listar-observacion-orden2.php" class="btn btn-sm btn-primary btn-opciones" title="observaciones"><i class="fa-solid fa-eye"></i></a> <!-- observaciones -->
              <a href="#" class="btn btn-sm btn-outline-dark btn-opciones" title="Asignar mecanico"><i class="fa-solid fa-user-gear"></i></a> <!-- Asignar mecanico DETALLE_SERVICIO -->
              <a href="listar-serviciosbrindados.php" class="btn btn-sm btn-outline-dark btn-opciones" title="Revisión"><i class="fa-solid fa-toolbox"></i></a> <!-- Mostrar vista DETALLE_SERVICIO (x equipo) -->
            </td>
          </tr>
          <tr>
            <td>1</td>
            <td>Camioneta</td>
            <td>Briliance</td>
            <td>MarcaDCarro</td>
            <td>8S5WCK</td>
            <td>Rojo</td>
            <td>Walter Aquije</td>
            <td>
              <a href="#" class="btn btn-sm btn-warning btn-opciones" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a> <!-- Edita DETEQUIPOS -->
              <a href="#" class="btn btn-sm btn-danger btn-opciones" title="Eliminar"><i class="fa-solid fa-trash"></i></a> <!-- Elimina de forma física de DETEQUIPOS -->
              <a href="#" class="btn btn-sm btn-info btn-opciones" title="Características"><i class="fa-solid fa-clipboard-list"></i></a> <!-- Datos de CARACTERÍSTICAS / ESPECIFICACIONES -->
              <a href="listar-observacion-orden2.php" class="btn btn-sm btn-primary btn-opciones" title="observaciones"><i class="fa-solid fa-eye"></i></a> <!-- observaciones -->
              <a href="#" class="btn btn-sm btn-outline-dark btn-opciones" title="Asignar mecanico"><i class="fa-solid fa-user-gear"></i></a> <!-- Asignar mecanico DETALLE_SERVICIO -->
              <a href="listar-serviciosbrindados.php" class="btn btn-sm btn-outline-dark btn-opciones" title="Revisión"><i class="fa-solid fa-toolbox"></i></a> <!-- Mostrar vista DETALLE_SERVICIO (x equipo) -->
            </td>
          </tr>
          <tr>
            <td>1</td>
            <td>Camioneta</td>
            <td>Briliance</td>
            <td>MarcaDCarro</td>
            <td>8S5WCK</td>
            <td>Rojo</td>
            <td>Walter Aquije</td>
            <td>
              <a href="#" class="btn btn-sm btn-warning btn-opciones" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a> <!-- Edita DETEQUIPOS -->
              <a href="#" class="btn btn-sm btn-danger btn-opciones" title="Eliminar"><i class="fa-solid fa-trash"></i></a> <!-- Elimina de forma física de DETEQUIPOS -->
              <a href="#" class="btn btn-sm btn-info btn-opciones" title="Características"><i class="fa-solid fa-clipboard-list"></i></a> <!-- Datos de CARACTERÍSTICAS / ESPECIFICACIONES -->
              <a href="listar-observacion-orden2.php" class="btn btn-sm btn-primary btn-opciones" title="observaciones"><i class="fa-solid fa-eye"></i></a> <!-- observaciones -->
              <a href="#" class="btn btn-sm btn-outline-dark btn-opciones" title="Asignar mecanico"><i class="fa-solid fa-user-gear"></i></a> <!-- Asignar mecanico DETALLE_SERVICIO -->
              <a href="listar-serviciosbrindados.php" class="btn btn-sm btn-outline-dark btn-opciones" title="Revisión"><i class="fa-solid fa-toolbox"></i></a> <!-- Mostrar vista DETALLE_SERVICIO (x equipo) -->
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="card-footer text-right">
      <a href="listar-observacion-orden2.php" class="btn btn-sm btn-secondary">Volver</a>
    </div>
  </div>

</div>
</div>
</div>

<?php

require_once "../../partials/_footer.php";

?>

<div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
  <div class="modal-dialog"> <!-- Modal grande si lo requieres -->
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
                  onclick="actualizarOpciones(); buscarPropietario();" checked>
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

<script src="<?= SERVERURL ?>views/page/ordenservicios/js/registrar-ordenes.js"></script>
</body>

</html>