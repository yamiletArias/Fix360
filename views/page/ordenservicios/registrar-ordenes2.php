<?php

const NAMEVIEW = "Órdenes de Servicio | Registro";

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
  <form id="form-orden" autocomplete="off">
    <div class="card border mb-3">
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 mb-3">
            <div class="form-floating input-group mb-3">
              <input type="text" disabled class="form-control input" id="propietario"
                placeholder="Propietario" />
              <label for="propietario">Propietario</label>
              <!-- En tu formulario principal -->
              <input type="hidden" id="hiddenIdPropietario" name="idpropietario">
              <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                data-bs-target="#miModal">
                ...
              </button>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="form-floating input-group mb-3">
              <input type="text" disabled class="form-control input" id="cliente" placeholder="Cliente">
              <input type="hidden" id="hiddenIdCliente" name="idcliente">
              <label for="cliente">Cliente</label>
              <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#ModalCliente">…</button>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="form-floating">
              <input type="text" class="form-control input" id="observaciones" placeholder="observaciones" maxlength="255">
              <label for="observaciones">Observaciones</label>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="form-floating">
              <select class="form-select" id="vehiculo" name="vehiculo" style="color:black;">
                <option value="" selected>Eliga un vehículo</option>
              </select>
              <label  for="vehiculo">Vehículo:</label>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="form-floating">
              <input type="number" step="0.1" class="form-control input" id="kilometraje" placeholder="201">
              <label for="kilometraje">Kilometraje</label>
            </div>
          </div>

          <div class="col-md-3 mb-3">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="ingresogrua" style="margin-left:10px; transform: scale(1.4);"  >
              <label class="input form-check-label" for="ingresogrua"  style="transform: scale(1.2);margin-left:80px" >Ingresó por grúa</label>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="form-floating">
            <input type="datetime-local" class="form-control input" id="fechaIngreso">
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
              <button class="btn btn-sm btn-success" type="button" id="btnAgregarDetalle" data-bs-toggle="modal" data-bs-target="#ModalServicio">
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
                <input type="number" class="form-control input" step="0.1" placeholder="precio" aria-label="Recipient's username" aria-describedby="button-addon2" min="0" id="precio">
                <label for="precio">Precio</label>
              </div>
              <button class="btn btn-sm btn-success" type="button" id="btnAgregar">Agregar</button>
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
                <input type="text" class="form-control form-control-sm text-end input" id="subtotal" readonly>
              </td>
              <td></td>
            </tr>
            <tr>
              <td colspan="4" class="text-end">IGV</td>
              <td>
                <input type="text" class="form-control form-control-sm text-end input" id="igv" readonly>
              </td>
              <td></td>

            </tr>
            <tr>
              <td colspan="4" class="text-end">Neto</td>
              <td>
                <input type="text" class="form-control form-control-sm text-end input" id="neto" readonly>
              </td>
              <td></td>

            </tr>
            <tr>
              <td colspan="4" class="text-end">
                <button id="btnAceptarOrden" type="button" class="btn btn-success text-end">Aceptar</button>
              </td>
              <td>
                <a class="btn btn-secondary text-end" href="listar-ordenes.php">Cancelar</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Observaciones e Ingreso grúa 
<div class="row g-2 mt-3">
  <div class="col-md-9">
    <div class="form-floating">
      <textarea class="form-control input" id="observaciones" rows="2" placeholder="Observaciones"></textarea>
      <label for="observaciones">Observaciones</label>
    </div>
  </div>
  <div class="col-md-3 d-flex align-items-center">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" id="ingresogrua">
      <label class="form-check-label" for="ingresogrua">Ingreso grúa</label>
    </div>
  </div>
</div>
-->

  </form>
</div>


</div>
</div>

<?php

require_once "../../partials/_footer.php";

?>

<div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h2 class="modal-title" id="miModalLabel">Seleccionar Propietario</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

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
              <input type="text" class="form-control input" id="vbuscado" style="background-color: white;"
                placeholder="Valor buscado" style="accent-color:white;" autofocus />
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

<div class="modal fade" id="ModalCliente" tabindex="-1" aria-labelledby="ModalClienteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header">
        <h2 class="modal-title" id="ModalClienteLabel">Seleccionar Cliente</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <!-- Forzamos tipo “persona” -->
        <input type="hidden" id="tipoBusquedaCliente" value="persona">

        <!-- Método de búsqueda -->
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <select id="selectMetodoCliente" class="form-select" style="background-color: white;color:black;">
                <option value="dni">DNI</option>
                <option value="nombre">Nombre</option>
              </select>
              <label for="selectMetodoCliente">Método de búsqueda</label>
            </div>
          </div>
        </div>

        <!-- Valor buscado -->
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <input type="text" class="form-control input" id="vbuscadoCliente" style="background-color: white;" placeholder="Valor buscado" autofocus>
              <label for="vbuscadoCliente">Valor buscado</label>
            </div>
          </div>
        </div>
        <!-- Resultados -->
        <p class="mt-3"><strong>Resultado:</strong></p>
        <div class="table-responsive">
          <table
            id="tabla-resultado-cliente"
            class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Confirmar</th>
              </tr>
            </thead>
            <tbody>
              <!-- Se llena dinámicamente -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal">Cerrar</button>
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
<script>
  // Ahora esto sí se evaluará en el HTML
  window.SERVERURL = '<?= SERVERURL ?>';
</script>

<!--Hace de que siempre se pida la ultima version del js (por esto no me cargaba lo ultimo q le agregue al js y me quede viendo xq no cargaba el kilometraje cuando ya lo estaba haciendo
(siempre revisar sources y ver si el archivo que esta ahi tiene el mismo n° de lineas que el que tiene en vscode)) -->

<script
  src="<?= rtrim(SERVERURL, '/') ?>/views/page/ordenservicios/js/registrar-ordenes.js?v=<?= time() ?>"
  defer
></script>
<script>
  let clienteTimer;

  const vbuscado = document.getElementById("vbuscadoCliente");
  const selectMetodo = document.getElementById("selectMetodoCliente");

  // Disparamos búsqueda automática al tipear o cambiar el método
  vbuscado.addEventListener("input", () => {
    clearTimeout(clienteTimer);
    clienteTimer = setTimeout(buscarCliente, 300);
  });
  selectMetodo.addEventListener("change", () => {
    clearTimeout(clienteTimer);
    clienteTimer = setTimeout(buscarCliente, 300);
  });

  function buscarCliente() {
    const tipo = document.getElementById("tipoBusquedaCliente").value; // siempre “persona”
    const metodo = selectMetodo.value;
    const valor = vbuscado.value.trim();
    if (!valor) {
      // limpiamos tabla si no hay texto
      document.querySelector("#tabla-resultado-cliente tbody").innerHTML = "";
      return;
    }

    fetch(
        `http://localhost/fix360/app/controllers/propietario.controller.php?` +
        `task=buscarPropietario&tipo=${tipo}` +
        `&metodo=${metodo}&valor=${encodeURIComponent(valor)}`
      )
      .then(res => res.json())
      .then(data => {
        const tbody = document.querySelector("#tabla-resultado-cliente tbody");
        tbody.innerHTML = "";
        data.forEach((item, i) => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${i + 1}</td>
            <td>${item.nombre}</td>
            <td>${item.documento}</td>
            <td>
              <button
                class="btn btn-sm btn-success"
                onclick="seleccionarCliente(${item.idcliente}, '${item.nombre}')"
              >
              <i class="fa-solid fa-circle-check"></i>
              </button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      })
      .catch(console.error);
  }

  function seleccionarCliente(id, nombre) {
    // Seteamos el formulario principal
    document.getElementById("hiddenIdCliente").value = id;
    document.getElementById("cliente").value = nombre;
    // Simulamos clic en la “X” para cerrar correctamente
    document.querySelector("#ModalCliente .btn-close").click();
  }
</script>




</body>

</html>