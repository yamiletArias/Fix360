<!-- VISTA de prueba con OT -->
<?php
const NAMEVIEW = "Ventas | Registro";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<div class="container-main mt-5">
  <div class="card border">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div></div>
      <!-- Botón a la derecha -->
      <div>
        <a href="listar-ventas.php" class="btn btn-sm btn-success">
          Mostrar Lista
        </a>
        <a href="#" id="btnToggleService" class="btn btn-sm btn-success">
          Agregar servicio
        </a>
      </div>
    </div>
    <div class="card-body">
      <form action="" method="POST" autocomplete="off" id="formulario-detalle">
        <div class="row g-2">
          <div class="col-md-5">
            <label>
              <input class="form-check-input text-start" type="radio" name="tipo" value="factura"
                onclick="inicializarCampos()">
              Factura
            </label>
            <label style="padding-left: 10px;">
              <input class="form-check-input text-start" type="radio" name="tipo" value="boleta"
                onclick="inicializarCampos()" checked>
              Boleta
            </label>
            <label style="padding-left: 10px;">
              <input class="form-check-input text-start" type="radio" name="tipo" value="orden de trabajo"
                onclick="inicializarCampos()">
              OT
            </label>
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
          <div class="col-md-4">
            <div class="form-floating input-group mb-3">
              <input type="text" disabled class="form-control input" id="propietario" placeholder="Propietario" />
              <label for="propietario"><strong>Propietario</strong></label>
              <input type="hidden" id="hiddenIdPropietario" name="idpropietario" />
              <!-- <input type="hidden" id="hiddenIdCliente" /> -->
              <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                data-bs-target="#miModal">
                ...
              </button>
            </div>
          </div>
          <div class="col-md-4 ">
            <div class="form-floating input-group mb-3">
              <input type="text" disabled class="form-control input" id="cliente" placeholder="Cliente">
              <input type="hidden" id="hiddenIdCliente" name="idcliente">
              <label for="cliente">Cliente</label>
              <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                data-bs-target="#ModalCliente">…</button>
            </div>
          </div>

          <div class="col-md-4 ">
            <div class="form-floating">
              <input type="text" class="form-control input" id="observaciones" placeholder="observaciones"
                maxlength="255" disabled>
              <label for="observaciones">Observaciones</label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <select class="form-select" id="vehiculo" name="vehiculo" style="color:black;">
                <option selected>Sin vehiculo</option>
              </select>
              <label for="vehiculo"><strong>Eliga un vehículo</strong></label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" step="0.1" class="form-control input" id="kilometraje" placeholder="201">
              <label for="kilometraje"><strong>Kilometraje</strong></label>
            </div>
          </div>
          <div class="col-md-3 d-flex">
            <div class="form-floating flex-grow-1">
              <input type="datetime-local" class="form-control input" name="fechaIngreso" id="fechaIngreso" required />
              <label for="fechaIngreso">Fecha de venta:</label>
            </div>
            <!-- Botón más delgado y estilizado -->
            <button type="button" id="btnPermitirFechaPasada" class="btn btn-outline-secondary px-2"
              style="height: 58px; width: 40px;" title="Permitir fechas pasadas">
              <i class="fa-solid fa-unlock"></i>
            </button>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <select class="form-select input" id="moneda" name="moneda" style="color: black;" required>
                <!-- “Soles” siempre estático y seleccionado -->
                <option value="Soles" selected>Soles</option>
                <!-- Aquí sólo meteremos el resto -->
              </select>
              <label for="moneda">Moneda:</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="ingresogrua"
                  style="margin-left:10px; transform: scale(1.4);" disabled>
                <label class="input form-check-label" for="ingresogrua"
                  style="transform: scale(1.2);margin-left:80px">Ingresó por grúa</label>
              </div>
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
                  placeholder="Buscar Producto" autofocus required>
                <label for="producto"><strong>Buscar Producto:</strong></label>
              </div>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-floating">
              <input type="number" class="form-control input" name="stock" id="stock" placeholder="Stock" required
                readonly />
              <label for="stock">Stock</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control input" name="precio" id="precio" placeholder="Precio" required />
              <label for="precio"><strong>Precio</strong></label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control input" name="cantidad" id="cantidad" placeholder="Cantidad"
                required />
              <label for="cantidad"><strong>Cantidad</strong></label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="input-group">
              <div class="form-floating">
                <input type="number" class="form-control input" name="descuento" id="descuento" placeholder="DSCT"
                  required />
                <label for="descuento">DSCT UNT</label>
              </div>
              <button type="button" class="btn btn-sm btn-success" id="agregarProducto">Agregar</button>
            </div>
          </div>
          <!-- Sección Servicios -->
          <div id="serviceSection" class="row g-2 mt-3 d-none">
            <!-- 1) SELECT de subcategoría (Tipo de servicio) -->
            <div class="col-md-3">
              <div class="form-floating">
                <select class="form-select" id="subcategoria" name="subcategoria" style="color: black;" required>
                  <option value="">Eliga un tipo de servicio</option>
                  <?php
                  // Ejemplo de carga con PHP (reemplaza con tu fuente real)
                  // foreach($tusSubcategorias as $row) {
                  //   echo "<option value=\"{$row['idsubcategoria']}\">{$row['subcategoria']}</option>";
                  // }
                  ?>
                </select>
                <label for="subcategoria">Tipo de Servicio:</label>
              </div>
            </div>

            <!-- 2) SELECT de servicio dentro de la subcategoría -->
            <div class="col-md-3">
              <div class="input-group">
                <div class="form-floating">
                  <select class="form-select" id="servicio" name="servicio" style="color: black;" required>
                    <option value="">Eliga un servicio</option>
                    <!-- Se cargan vía AJAX al cambiar #subcategoria -->
                  </select>
                  <label for="servicio">Servicio:</label>
                </div>
                <!-- Botón que abre el modal para registrar un servicio nuevo -->
                <button class="btn btn-sm btn-success" type="button" id="btnAgregarServicio">
                  <i class="fa-solid fa-circle-plus"></i>
                </button>
              </div>
            </div>

            <!-- 3) SELECT de mecánico -->
            <div class="col-md-3">
              <div class="form-floating">
                <select class="form-select" id="mecanico" name="mecanico" style="color:black;">
                  <option value="">Eliga un mecánico</option>
                  <!-- … -->
                </select>
                <label for="mecanico">Mecánico:</label>
              </div>
            </div>

            <!-- 4) Input de precio de servicio y botón para agregar -->
            <div class="col-md-3">
              <div class="input-group">
                <div class="form-floating">
                  <input type="number" class="form-control input" step="0.1" placeholder="Precio Servicio"
                    aria-label="Precio Servicio" min="0.01" id="precioServicio" />
                  <label for="precioServicio">Precio Servicio</label>
                </div>
                <button class="btn btn-sm btn-success" type="button" id="btnAgregarDetalleServicio">
                  Agregar
                </button>
              </div>
            </div>
          </div>
          <!-- FIN sección de servicios -->
          <!-- 
          <div id="serviceSection" class="row g-2 mt-3 d-none">
            <div class="col-md-3">
              <div class="form-floating">
                <select class="form-select" id="subcategoria" name="subcategoria" style="color: black;" required>
                  <option selected>Eliga un tipo de servicio</option>
                </select>
                <label for="subcategoria">Tipo de Servicio:</label>
              </div>
            </div>
            <div class="col-md-3 ">
              <div class="input-group ">
                <div class="form-floating">
                  <select class="form-select" id="servicio" name="servicio" style="color:black;">
                    <option selected>Eliga un servicio</option>
                  </select>
                  <label for="servicio">Servicio:</label>
                </div>
                <button class="btn btn-sm btn-success" type="button" id="btnAgregarDetalle" data-bs-toggle="modal"
                  data-bs-target="#ModalServicio">
                  <i class="fa-solid fa-circle-plus"></i>
                </button>
              </div>
            </div>
            <div class="col-md-3 ">
              <div class="form-floating">
                <select class="form-select" id="mecanico" name="mecanico" style="color:black;">
                  <option selected>Eliga un mecánico</option>
                </select>
                <label for="mecanico">Mecánico:</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <div class="form-floating">
                  <input type="number" class="form-control input" step="0.1" placeholder="Precio Servicio"
                    aria-label="Precio Servicio" min="0.01" id="precioServicio" />
                  <label for="precioServicio">Precio Servicio</label>
                </div>
                <button class="btn btn-sm btn-success" type="button" id="btnAgregarServicio">Agregar</button>
              </div>
            </div>
          </div> -->
        </div>
      </form>
    </div>
  </div>
  <!-- Sección de Detalles de la Venta -->
  <div class="card mt-2 border">
    <!-- <div class="card border"> -->
    <div class="card-body">
      <table class="table table-striped table-sm" id="tabla-detalle">
        <thead>
          <tr>
            <th>#</th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Dsct $</th>
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
  <div id="serviceListCard" class="card mt-2 border d-none">
    <div class="card-body">
      <table class="table table-striped table-sm" id="tabla-detalle-servicios">
        <thead>
          <tr>
            <th>#</th>
            <th>Servicio</th>
            <th>Mecánico</th>
            <th>Precio</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <!-- Datos asíncronos -->
        </tbody>
      </table>
    </div>
  </div>
  <div class="card mt-2 border">
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
            <td colspan="4" class="text-end">NETO</td>
            <td>
              <input type="text" class="form-control input form-control-sm text-end" id="neto" readonly>
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
            <td colspan="4" class="text-end">Importe</td>
            <td>
              <input type="text" class="form-control input form-control-sm text-end" id="total" readonly>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="mt-4">
        <button id="btnFinalizarVenta" type="button" class="btn btn-success text-end">Aceptar</button>
        <a href="" type="reset" class="btn btn-secondary" id="btnCancelarVenta">
          Cancelar
        </a>
      </div>
    </div>
  </div>
</div>
</div>
</div>

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
                <label class="form-check-label" for="rbtnpersona" style="margin-left:5px;">Persona</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnempresa"
                  onclick="actualizarOpciones(); buscarPropietario();">
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
              <input type="text" class="form-control input" id="vbuscadoCliente" style="background-color: white;"
                placeholder="Valor buscado" autofocus>
              <label for="vbuscadoCliente">Valor buscado</label>
            </div>
          </div>
        </div>
        <!-- Resultados -->
        <p class="mt-3"><strong>Resultado:</strong></p>
        <div class="table-responsive">
          <table id="tabla-resultado-cliente" class="table table-striped">
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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>
<!-- Modal para registrar Nuevo Servicio -->
<div class="modal fade" id="ModalServicio" tabindex="-1" aria-labelledby="ModalServicioLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formNuevoServicio">
        <div class="modal-header">
          <h5 class="modal-title" id="ModalServicioLabel">Registrar Nuevo Servicio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <!-- Subcategoría seleccionada (sólo lectura) -->
          <div class="form-floating mb-3">
            <input type="text" id="modalSubcategoriaNombre" class="form-control" readonly>
            <label for="modalSubcategoriaNombre">Tipo de Servicio</label>
          </div>
          <input type="hidden" id="modalSubcategoriaId">
          <!-- Nombre del servicio nuevo -->
          <div class="form-floating mb-3">
            <input type="text" id="modalServicioNombre" class="form-control" placeholder="Nombre del servicio" required>
            <label for="modalServicioNombre">Servicio</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnRegistrarServicioModal" class="btn btn-primary">Registrar Servicio</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php
require_once "../../partials/_footer.php";
?>
<script>
  window.FIX360_BASE_URL = "<?= SERVERURL ?>";
</script>
<!-- Formulario Venta -->
<script src="<?= SERVERURL ?>views/page/ventas/js/registrar-ventas-orden2.js"></script>
<script src="<?= SERVERURL ?>views/page/ordenservicios/js/registrar-ordenes.js"></script>
<!-- js de carga moneda -->
<script src="<?= SERVERURL ?>views/assets/js/moneda.js"></script>
<script>
  const obsField = document.getElementById("observaciones");
  const gruField = document.getElementById("ingresogrua");
  document.getElementById('btnToggleService').addEventListener('click', function (e) {
    e.preventDefault();
    // 1) Mostrar la sección de servicios
    document.getElementById('serviceSection').classList.remove('d-none');
    document.getElementById('serviceListCard').classList.remove('d-none')
    // 2) Deshabilitar el botón y cambiar su estilo a gris (btn-secondary)
    this.disabled = true;
    this.classList.remove('btn-success');
    this.classList.add('btn-secondary');

    obsField.disabled = false;
    gruField.disabled = false;
  });
  // 2) Cargar lista de servicios por subcategoría
  /* async function cargarServiciosPorSubcategoria(idsubcat) {
    if (!idsubcat) {
      document.getElementById('servicio').innerHTML = '<option value="">Eliga un servicio</option>';
      return;
    }
    try {
      const resp = await fetch(`${FIX360_BASE_URL}app/controllers/Servicio.Controller.php?task=getServicioBySubcategoria&idsubcategoria=${idsubcat}`);
      const data = await resp.json();
      let html = '<option value="">Eliga un servicio</option>';
      data.forEach(item => {
        html += `<option value="${item.idservicio}">${item.servicio}</option>`;
      });
      document.getElementById('servicio').innerHTML = html;
    } catch (err) {
      console.error('Error al cargar servicios:', err);
    }
  }

  document.getElementById('subcategoria').addEventListener('change', function() {
    const idsubcat = this.value;
    cargarServiciosPorSubcategoria(idsubcat);
  });

  // 3) Abrir modal para registrar un servicio nuevo
  document.getElementById('btnAgregarServicio').addEventListener('click', function() {
    const selectSub = document.getElementById('subcategoria');
    const idsubcat = selectSub.value;
    const textoSub = selectSub.options[selectSub.selectedIndex]?.text || '';

    if (!idsubcat) {
      alert('Primero debe seleccionar un Tipo de Servicio (subcategoría).');
      return;
    }
    document.getElementById('modalSubcategoriaId').value     = idsubcat;
    document.getElementById('modalSubcategoriaNombre').value = textoSub;
    document.getElementById('modalServicioNombre').value     = '';
    new bootstrap.Modal(document.getElementById('ModalServicio')).show();
  });

  // 4) Registrar servicio desde el modal
  document.getElementById('btnRegistrarServicioModal').addEventListener('click', async function() {
    const idsubcategoria = document.getElementById('modalSubcategoriaId').value;
    const servicioNombre = document.getElementById('modalServicioNombre').value.trim();

    if (!servicioNombre) {
      alert('Debe ingresar el nombre del servicio.');
      return;
    }

    const payload = {
      task: 'registerServicio',
      idsubcategoria: idsubcategoria,
      servicio: servicioNombre
    };

    try {
      const resp = await fetch(`${FIX360_BASE_URL}app/controllers/Servicio.Controller.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const json = await resp.json();
      if (json.error) {
        alert('Error: ' + json.error);
        return;
      }

      // Si tuvo éxito:
      const nuevoId  = json.idservicio;
      const nuevoNom = json.servicio;

      // Cerrar modal
      const modalEl = document.getElementById('ModalServicio');
      bootstrap.Modal.getInstance(modalEl).hide();

      // Agregar nueva opción al <select id="servicio"> y seleccionarla
      const selectServ = document.getElementById('servicio');
      const opt = document.createElement('option');
      opt.value = nuevoId;
      opt.textContent = nuevoNom;
      opt.selected = true;
      selectServ.appendChild(opt);

      alert('Servicio registrado correctamente.');
    } catch (err) {
      console.error('Error al registrar servicio:', err);
      alert('Ocurrió un error al registrar el servicio.');
    }
  });
 */
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    function initDateField(id) {
      const el = document.getElementById(id);
      if (!el) return; // si no existe, no hace nada
      const today = new Date();
      const twoAgo = new Date();
      twoAgo.setDate(today.getDate() - 2);
      const fmt = (d) => d.toISOString().split("T")[0];
      el.value = fmt(today);
      el.min = fmt(twoAgo);
      el.max = fmt(today);
    }
    initDateField("fechaIngreso");
    const fechaInput = document.getElementById("fechaIngreso");
    const monedaSelect = document.getElementById("moneda");
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    setFechaDefault();
  });

  function setFechaDefault() {
    const input = document.getElementById('fechaIngreso');
    if (!input) {
      console.warn('No encontré #fechaIngreso');
      return;
    }
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    const yyyy = now.getFullYear(),
      MM = pad(now.getMonth() + 1),
      dd = pad(now.getDate()),
      hh = pad(now.getHours()),
      mm = pad(now.getMinutes());

    input.value = `${yyyy}-${MM}-${dd}T${hh}:${mm}`;
    // opcional: rango
    const twoDaysAgo = new Date(now);
    twoDaysAgo.setDate(now.getDate() - 2);
    input.min = `${twoDaysAgo.getFullYear()}-${pad(twoDaysAgo.getMonth() + 1)}-${pad(twoDaysAgo.getDate())}T00:00`;
    input.max = `${yyyy}-${MM}-${dd}T23:59`;
  }
</script>
</body>

</html>