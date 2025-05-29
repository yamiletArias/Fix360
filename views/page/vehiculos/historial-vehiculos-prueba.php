<?php

const NAMEVIEW = "Historial del Vehiculo";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

$id = intval($_GET['id'] ?? 0);

?>
<style>
  .card .card-body {
    padding: 10px;
  }

  .tab-content {
    padding-bottom: 10px;

  }



  .container-main {
    margin: 30px;
  }
</style>

<div class="container-main">
  <a class="btn btn-sm btn-secondary mb-3" href="javascript:history.back()">volver</a>

  <div class="row">
    <!-- Propietario Actual -->
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
          <h5 class="mb-0"><i class="fa-solid fa-user"></i> Datos del Propietario Actual</h5>
        </div>
        <div class="card-body">
          <p><strong>Nombre:</strong> <span id="prop-nombre">Cargando...</span></p>
          <p><strong>Documento:</strong> <span id="prop-doc">Cargando...</span></p>
          <p><strong>Propietario desde:</strong> <span id="prop-desde">Cargando...</span></p>
          <p><strong>Teléfono:</strong> <span id="prop-tel">Cargando...</span></p>
          <p><strong>Email:</strong> <span id="prop-email">Cargando...</span></p>
        </div>
      </div>

      <div class="mb-3  mt-5">
        <div class="btn-group" role="group" aria-label="Filtros periodo y estado">
          <button type="button" data-modo="mes" class="btn btn-primary filtro-periodo active">Mes</button>
          <button type="button" data-modo="semestral" class="btn btn-primary filtro-periodo">Semestre</button>
          <button type="button" data-modo="anual" class="btn btn-primary filtro-periodo">Anual</button>
          <button id="btnToggleEstado" class="btn btn-secondary" title="Ver eliminadas">
            <i class="fa-solid fa-eye-slash"></i>
          </button>
        </div>
      </div>
    </div>
    <!-- Vehículo -->
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="fa-solid fa-car"></i> Datos del Vehículo</h5>
        </div>
        <div class="card-body">
          <p><strong>Modelo:</strong> <span id="vh-modelo">Cargando...</span></p>
          <p><strong>Año:</strong> <span id="vh-anio">Cargando...</span></p>
          <p><strong>Placa:</strong> <span id="vh-placa">Cargando...</span></p>
          <p><strong>Color:</strong> <span id="vh-color">Cargando...</span></p>
          <p><strong>Combustible:</strong> <span id="vh-combustible">Cargando...</span></p>
          <p><strong>Últ. Modificación:</strong> <span id="vh-modificado">Cargando...</span></p>
          <p><strong>VIN:</strong> <span id="vh-vin">Cargando...</span></p>
          <p><strong>N° Chasis:</strong> <span id="vh-chasis">Cargando...</span></p>
        </div>
      </div>
    </div>

  </div>
  <!-- Botones de periodo: Mes / Semestre / Anual -->





  <ul class="nav nav-tabs mb-3 mt-n3" id="histTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="tab-ordenes" data-bs-toggle="tab" data-bs-target="#content-ordenes" type="button" role="tab">Órdenes de Servicio
        <i class="fa-solid fa-car-tunnel menu-icon"></i>
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tab-ventas" data-bs-toggle="tab" data-bs-target="#content-ventas" type="button" role="tab">Ventas
        <i class="fa-solid fa-tags menu-icon"></i>
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <input type="date" id="Fecha" class="form-control input" style="max-width: 250px;" max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="content-ordenes" role="tabpanel">
      <div class="card mb-4">
        <div class="card-body">
          <table id="tablaOrdenes" class="table table-striped table-hover display w-100">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Propietario</th>
                <th>Cliente</th>
                <th>Fch. Ingreso</th>
                <th>Fch. Salida</th>
                <th>Opciones</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="content-ventas" role="tabpanel">
      <div class="card mb-4">
        <div class="card-body">
          <table id="tablaVentas" class="table table-striped table-hover display w-100">
            <thead>
              <tr>
                <th>#</th>
                <th>Propietario</th>
                <th class="text-center">Comprobante</th>
                <th class="text-center">Kms</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Pendiente</th>
                <th class="text-center">Estado Pago</th>
                <th class="text-center">Opciones</th>

              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

</div>






<div class="modal fade" id="modalVerJustificacion" tabindex="-1" aria-labelledby="modalJustificacionLabel"
  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalJustificacionLabel">Justificación de la eliminación</h5>
        <button type="button" class="btn-sm btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="contenidoJustificacion">
        <!-- Aquí se insertará dinámicamente la justificación -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Detalle de Venta -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" style="max-width: 950px;" style="margin-top: 20px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle de la Venta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Propietario:</strong> <label for="propietario"></label></p>
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" disabled class="form-control input" id="modeloInput"
                placeholder="Cliente">
              <label for="modeloInput">Cliente: </label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" disabled class="form-control input" id="fechaHora"
                placeholder="Fecha & Hora">
              <label for="fechaHora">Fecha & Hora: </label>
            </div>
          </div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" disabled class="form-control input" id="vehiculo" placeholder="Vehiculo">
              <label for="vehiculo">Vehiculo: </label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" disabled class="form-control input" id="kilometraje"
                placeholder="Kilometraje">
              <label for="kilometraje">Kilometraje: </label>
            </div>
          </div>
        </div>
        <div class="table-container">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Productos</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Descuento</th>
                <th>T. producto</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
          <hr>
          <h6>Servicios asociados</h6>
          <table class="table table-striped table-bordered" id="tabla-detalle-servicios-modal">
            <thead>
              <tr>
                <th>#</th>
                <th>Tipo Servicio</th>
                <th>Servicio</th>
                <th>Mecánico</th>
                <th>Precio</th>
              </tr>
            </thead>
            <tbody>
              <!-- Aquí se llenarán con JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalJustificacion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formJustificacion" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Justificación de Eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="jIdOrden">
        <div class="mb-3">
          <label for="jTexto" class="form-label">Justificación:</label>
          <textarea id="jTexto" class="form-control" rows="3" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modalDetalleOrden" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Detalle de la Orden</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Cabecera -->
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" id="dCliente" class="form-control input" disabled>
              <label for="dCliente">Cliente</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" id="dPropietario" class="form-control input" disabled>
              <label for="dPropietario">Propietario</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" id="dVehiculo" class="form-control input" disabled>
              <label for="dVehiculo">Vehículo</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" id="dKilometraje" class="form-control input" disabled>
              <label for="dKilometraje">Kilometraje</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" id="dIngreso" class="form-control input" disabled>
              <label for="dIngreso">Fecha Ingreso</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" id="dSalida" class="form-control input" disabled>
              <label for="dSalida">Fecha Salida</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="dGrua" style="margin-left:10px; transform: scale(1.4);" disabled>
              <label class="input form-check-label" for="dGrua" style="padding-left:30px ;color:black;opacity:1;font-size:16px;">Ingreso por grúa</label>
            </div>
          </div>
          <div class="col-12">
            <label class="form-label">Observaciones</label>
            <textarea id="dObservaciones" class="form-control input" rows="2" disabled></textarea>
          </div>
        </div>
        <!-- Detalle de servicios -->
        <div class="table-responsive mb-3">
          <table class="table table-bordered" id="tablaDetalle">
            <thead>
              <tr>
                <th>#</th>
                <th>Servicio</th>
                <th>Mecánico</th>
                <th>Precio</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <!-- Total -->
        <div class="text-end">
          <strong>Total: </strong><span id="dTotal">0.00</span>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const fechaInput = document.getElementById('Fecha');
    const vehiculoId = <?= $id ?>;
    let currentEstado = true;
    const clean = v => (v == null || v === '' ? '—' : v);
    let currentModo = 'mes'; // por defecto: mes


    const btnToggle = document.getElementById('btnToggleEstado');
    btnToggle.addEventListener('click', () => {

      currentEstado = !currentEstado;
      if (currentEstado) {
        btnToggle.classList.replace('btn-warning', 'btn-secondary');
        btnToggle.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
        btnToggle.title = 'Ver eliminadas';
      } else {
        btnToggle.classList.replace('btn-secondary', 'btn-warning');
        btnToggle.innerHTML = '<i class="fa-solid fa-eye"></i>';
        btnToggle.title = 'Ver activas';
      }
      loadOrdenes(); // si tienes órdenes también…
      loadVentas();
    });

    // 1) Carga datos generales y propietario
    fetch(`<?= SERVERURL ?>app/controllers/Vehiculo.controller.php?task=getHistorial&idvehiculo=${vehiculoId}`)
      .then(r => r.json())
      .then(data => {
        const {
          general: g,
          propietario: p
        } = data;
        document.getElementById("vh-modelo").textContent = clean(g.modelo);
        document.getElementById("vh-anio").textContent = clean(g.anio);
        document.getElementById("vh-placa").textContent = clean(g.placa);
        document.getElementById("vh-color").textContent = clean(g.color);
        document.getElementById("vh-combustible").textContent = clean(g.tcombustible);
        document.getElementById("vh-modificado").textContent = clean(g.modificado);
        document.getElementById("vh-vin").textContent = clean(g.vin);
        document.getElementById("vh-chasis").textContent = clean(g.numchasis);
        document.getElementById("prop-nombre").textContent = p.propietario;
        document.getElementById("prop-doc").textContent = p.documento_propietario;
        document.getElementById("prop-desde").textContent = p.propiedad_desde || '—';
        document.getElementById("prop-tel").textContent = p.telefono_prop || '—';
        document.getElementById("prop-email").textContent = p.email_prop || '—';
      })
      .catch(console.error);

    // 2) Inicializa DataTables
    // 2) Inicializa DataTables SIN leer el <tbody> original:
    const tablaOrdenes = $("#tablaOrdenes").DataTable({
      data: [], // <–– aquí
      searching: false,
      columns: [{
          data: null,
          render: (_, __, ___, meta) => meta.row + 1
        },
        {
          data: "propietario"
        },
        {
          data: "cliente"
        },
        {
          data: "fechaingreso",
          render: d => clean(d)
        },
        {
          data: "fechasalida",
          render: d => clean(d)
        },
        {
          data: null,
          orderable: false,
          className: "text-center",
          render: row => `
        <button class="btn btn-sm btn-info btn-ver-detalle-orden" data-id="${row.idorden}">
          <i class="fa-solid fa-clipboard-list"></i>
        </button>
        <a class="btn btn-sm btn-primary"
           href="<?= SERVERURL ?>views/page/ordenservicios/listar-observacion-orden.php?idorden=${row.idorden}"
           title="Ver Observaciones">
          <i class="fa-solid fa-eye"></i>
        </a>`
        }
      ],
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json",
        emptyTable: "Este vehículo no tiene historial de órdenes"
      }
    });

    const tablaVentas = $("#tablaVentas").DataTable({
      data: [], // <–– y aquí
      searching: false,
      columns: [{
          data: null,
          render: (_, __, ___, m) => m.row + 1
        },
        {
          data: "propietario"
        },
        {
          data: "comprobante",
          className: "text-center"
        },
        {
          data: "kilometraje",
          className: "text-center",
          render: d => clean(d)
        },
        {
          data: "tipo_comprobante",
          className: "text-center"
        },
        {
          data: "total_pendiente",
          className: "text-center"
        },
        {
          data: "estado_pago",
          className: "text-center"
        },
        {
          data: null,
          orderable: false,
          className: "text-center",
          render: row => `
        <button class="btn btn-sm btn-info btn-ver-detalle-venta"    data-id="${row.id}">
          <i class="fa-solid fa-clipboard-list"></i>
        </button>`
        }
      ],
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json",
        emptyTable: "Este vehículo no tiene historial de ventas"
      }
    });




    // 3) Funciones de carga
    function loadOrdenes() {
      const fecha = fechaInput.value;
      // Mapeo: true → 'A', false → 'D'
      const estadoOrden = currentEstado ? 'A' : 'D';
      fetch(`<?= SERVERURL ?>app/controllers/OrdenServicio.controller.php` +
          `?action=historial` +
          `&modo=${currentModo}` +
          `&fecha=${fecha}` +
          `&estado=${estadoOrden}` +
          `&idvehiculo=${vehiculoId}`)
        .then(r => r.json())
        .then(js => {
          if (js.status === 'success') {
            tablaOrdenes.clear(); // primero limpio TODO
            if (js.data.length) {
              tablaOrdenes.rows.add(js.data); // sólo si hay filas
            }
            tablaOrdenes.draw(); // y finalmente dibujo
          }
        });
    }

    function loadVentas() {
      const fecha = fechaInput.value;
      // Mapeo: true → 1, false → 0
      const estadoVenta = currentEstado ? 1 : 0;
      fetch(`<?= SERVERURL ?>app/controllers/Venta.controller.php` +
          `?action=historial` +
          `&modo=${currentModo}` +
          `&fecha=${fecha}` +
          `&idvehiculo=${vehiculoId}` +
          `&estado=${estadoVenta}`)
        .then(r => r.json())
        .then(js => {
          if (js.status === 'success') {
            tablaVentas.clear();
            if (js.data.length) {
              tablaVentas.rows.add(js.data);
            }
            tablaVentas.draw();
          }
        });
    }

    // 4) Filtros de periodo
    document.querySelectorAll('.filtro-periodo').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.filtro-periodo')
          .forEach(b => b.classList.toggle('active', b === btn));
        currentModo = btn.dataset.modo;
        loadOrdenes();
        loadVentas();
      });
    });

    // 5) Delegación de botones
    $('#tablaOrdenes tbody').on('click', '.btn-ver-detalle-orden', function() {
      verDetalleOrden($(this).data('id'));
    });
    $('#tablaVentas tbody').on('click', '.btn-ver-justificacion', function() {
      const id = $(this).data('id');
      fetch(`<?= SERVERURL ?>app/controllers/Venta.controller.php?action=justificacion&idventa=${id}`)
        .then(r => r.json())
        .then(js => {
          $('#contenidoJustificacion').text(js.justificacion || '—');
          $('#modalVerJustificacion').modal('show');
        });
    });
    $('#tablaVentas tbody').on('click', '.btn-ver-detalle-venta', function() {
      verDetalleVenta($(this).data('id'));
    });
    fechaInput.addEventListener('change', () => {
      currentModo = 'mes';
      // marca el botón "Mes" como activo
      document.querySelectorAll('.filtro-periodo')
        .forEach(b => b.classList.toggle('active', b.dataset.modo === 'mes'));
      loadOrdenes();
      loadVentas();
    });
    loadOrdenes();
    loadVentas();
  });
</script>

<script>
  // 1) Función para abrir y rellenar el modal de Detalle de Orden
  function verDetalleOrden(idorden) {
    fetch(`${SERVERURL}app/controllers/OrdenServicio.controller.php?action=getDetalle&idorden=${idorden}`)
      .then(r => r.json())
      .then(json => {
        const {
          cabecera,
          detalle,
          total
        } = json.data;
        // rellena inputs del modal
        document.getElementById('dCliente').value = cabecera.cliente || '';
        document.getElementById('dPropietario').value = cabecera.propietario || '';
        document.getElementById('dVehiculo').value = cabecera.vehiculo || '';
        document.getElementById('dKilometraje').value = cabecera.kilometraje || '';
        document.getElementById('dIngreso').value = cabecera.fecha_ingreso || '';
        document.getElementById('dSalida').value = cabecera.fecha_salida || '';
        document.getElementById('dGrua').checked = !!cabecera.ingresogrua;
        document.getElementById('dObservaciones').value = cabecera.observaciones || '';
        // rellena tabla de servicios
        const tbody = document.querySelector('#tablaDetalle tbody');
        tbody.innerHTML = '';
        detalle.forEach((r, i) => {
          tbody.insertAdjacentHTML('beforeend', `
          <tr>
            <td>${i+1}</td>
            <td>${r.servicio}</td>
            <td>${r.mecanico}</td>
            <td class="text-end">${parseFloat(r.precio).toFixed(2)}</td>
          </tr>
        `);
        });
        document.getElementById('dTotal').textContent = parseFloat(total).toFixed(2);
        new bootstrap.Modal(document.getElementById('modalDetalleOrden')).show();
      });
  }

  function verDetalleVenta(idventa) {
    // — Limpiar modal
    $("#miModal tbody, #tabla-detalle-productos-modal tbody, #tabla-detalle-servicios-modal tbody").empty();
    $("#miModal .amortizaciones-container").remove();
    $("#modeloInput, #fechaHora, #vehiculo, #kilometraje").val('');
    $("label[for='propietario']").text('');

    // — Abrir modal
    $("#miModal").modal("show");

    // 1) Propietario
    fetch(`<?= SERVERURL ?>app/controllers/Venta.controller.php?action=propietario&idventa=${idventa}`)
      .then(r => r.json())
      .then(jsonVenta => {
        if (jsonVenta.status === 'success') {
          $("label[for='propietario']").text(jsonVenta.data.propietario || 'Sin propietario');
        } else {
          $("label[for='propietario']").text('No encontrado');
        }
      })
      .catch(() => {
        $("label[for='propietario']").text('Error al cargar');
      });

    // 2) Detalle completo (productos + servicios)
    fetch(`<?= SERVERURL ?>app/controllers/Detventa.controller.php?idventa=${idventa}`)
      .then(r => r.json())
      .then(json => {
        console.log("DETALLE VENTA RAW:", json);
        if (json.status !== 'success') {
          console.error("Detventa error:", json.message);
          return;
        }
        const {
          productos,
          servicios
        } = json.data;

        // — Productos —
        const $prodBody = $("#tabla-detalle-productos-modal tbody").empty();
        if (!productos.length) {
          $prodBody.append(`<tr><td colspan="6" class="text-center text-muted">No hay productos</td></tr>`);
        } else {
          productos.forEach((p, i) => {
            $prodBody.append(`
                        <tr>
                            <td>${i + 1}</td>
                            <td>${p.producto}</td>
                            <td>${p.cantidad}</td>
                            <td>${parseFloat(p.precio).toFixed(2)} $</td>
                            <td>${parseFloat(p.descuento).toFixed(2)} $</td>
                            <td>${parseFloat(p.total_producto).toFixed(2)} $</td>
                        </tr>`);
          });
          // Campos generales
          $("#modeloInput").val(productos[0].cliente);
          $("#fechaHora").val(productos[0].fechahora);
          $("#vehiculo").val(productos[0].vehiculo || 'Sin vehículo');
          $("#kilometraje").val(productos[0].kilometraje || 'Sin kilometraje');
        }

        const serviciosValidos = servicios.filter(s =>
          s.tiposervicio !== null ||
          s.nombreservicio !== null ||
          s.mecanico !== null ||
          s.precio_servicio !== null
        );
        // — Servicios —
        const $servBody = $("#tabla-detalle-servicios-modal tbody").empty();
        if (!serviciosValidos.length) {
          $servBody.append(`<tr><td colspan="5" class="text-center text-muted">No hay servicios</td></tr>`);
        } else {
          serviciosValidos.forEach((s, i) => {
            $servBody.append(`
      <tr>
        <td>${i + 1}</td>
        <td>${s.tiposervicio ?? '-'}</td>
        <td>${s.nombreservicio ?? '-'}</td>
        <td>${s.mecanico ?? '-'}</td>
        <td>${s.precio_servicio !== null
                                ? parseFloat(s.precio_servicio).toFixed(2) + ' $'
                                : '-'
                            }</td>
      </tr>`);
          });
        }

        // — Amortizaciones —
        fetch(`<?= SERVERURL ?>app/controllers/Amortizacion.controller.php?action=list&idventa=${idventa}`)
          .then(r => r.json())
          .then(jsonA => {
            if (jsonA.status === 'success' && jsonA.data.length) {
              const cont = $(`
                            <div class="amortizaciones-container mt-4">
                                <h6>Amortizaciones</h6>
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Transacción</th>
                                            <th>Nº Transacción</th>
                                            <th>Monto</th>
                                            <th>F. Pago</th>
                                            <th>Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        `);
              jsonA.data.forEach((a, i) => {
                cont.find('tbody').append(`
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${new Date(a.creado).toLocaleString()}</td>
                                    <td>${a.numtransaccion}</td>
                                    <td>${parseFloat(a.amortizacion).toFixed(2)} $</td>
                                    <td>${a.formapago}</td>
                                    <td>${parseFloat(a.saldo).toFixed(2)} $</td>
                                </tr>`);
              });
              $("#miModal .modal-body").append(cont);
            }
          })
          .catch(() => console.error("Error amortizaciones"));

      })
      .catch(() => {
        console.error("Error al cargar detalle de venta");
        alert("Ocurrió un error al cargar el detalle.");
      });
  }

  // 2) Función para abrir y rellenar el modal de Detalle de Venta
</script>