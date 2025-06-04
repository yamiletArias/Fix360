<?php
const NAMEVIEW = "Historial del vehículo";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
.
<style>
.form-check-label {
  margin-left: 0 !important;
}

</style>
<div class="container-main">
  <?php $idvehiculo = (int)($_GET['id'] ?? 0); ?>

<div class="mb-4">
  <label class="form-label"><strong>Se están listando:</strong></label>
  <div class="d-flex gap-4">
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="tipo" id="radio-orden" value="orden" checked>
      <label class="form-check-label" for="radio-orden">Órdenes de Servicio</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="tipo" id="radio-venta" value="venta">
      <label class="form-check-label" for="radio-venta">Ventas</label>
    </div>
  </div>
</div>



  <table id="tablaHistorial" class="table table-striped" style="width:100%">
    <thead>
      <tr id="headRow"></tr>
    </thead>
    <tbody></tbody>
  </table>
</div>
</div>

<!-- Modal detalle de orden -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle de Orden</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table" id="tblDetalle">
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
    </div>
  </div>
</div>

<!-- Modal justificación -->
<div class="modal fade" id="modalJustificacion" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Justificación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p id="txtJustificacion"></p>
      </div>
    </div>
  </div>
</div>

<!-- Modal detalle de Venta -->
<div class="modal fade" id="modalDetalleVenta" tabindex="-1" aria-labelledby="modalDetalleVentaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalDetalleVentaLabel">Detalle de la Venta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <!-- 1) Encabezado con datos generales -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label"><strong>Propietario:</strong></label>
            <p id="dvPropietario">—</p>
          </div>
          <div class="col-md-6">
            <label class="form-label"><strong>Cliente:</strong></label>
            <p id="dvCliente">—</p>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label"><strong>Fecha y Hora:</strong></label>
            <p id="dvFechaHora">—</p>
          </div>
          <div class="col-md-6">
            <label class="form-label"><strong>Kilometraje:</strong></label>
            <p id="dvKilometraje">—</p>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col-md-12">
            <label class="form-label"><strong>Vehículo:</strong></label>
            <p id="dvVehiculo">—</p>
          </div>
        </div>

        <!-- 2) Tabla de Productos -->
        <h6 class="mt-3">Productos asociados</h6>
        <div class="table-responsive mb-4">
          <table class="table table-striped" id="tblProd">
            <thead>
              <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Descuento</th>
                <th>T. producto</th>
              </tr>
            </thead>
            <tbody>
              <!-- Se llenará dinámicamente -->
            </tbody>
          </table>
        </div>

        <!-- 3) Tabla de Servicios -->
        <h6 class="mt-3">Servicios asociados</h6>
        <div class="table-responsive">
          <table class="table table-striped" id="tblServ">
            <thead>
              <tr>
                <th>#</th>
                <th>Tipo Servicio</th>
                <th>Servicio</th>
                <th>Mecánico</th>
                <th>Precio Serv.</th>
              </tr>
            </thead>
            <tbody>
              <!-- Se llenará dinámicamente -->
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>


<?php require_once "../../partials/_footer.php"; ?>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const idVeh = <?php echo $idvehiculo; ?>;
    const baseUrl = '<?= SERVERURL ?>app/controllers/vehiculo.controller.php';
    const urlDetVenta = '<?= SERVERURL ?>app/controllers/Detventa.controller.php';
    let tabla;

    // 1) Columnas para Órdenes de Servicio
    const columnasOrden = [
      { data: 'idorden',      title: '#' },
      { data: 'Administrador', title: 'Registró' },
      { data: 'propietario',  title: 'Propietario' },
      { data: 'cliente',      title: 'Cliente' },
      { data: 'kilometraje',  title: 'Kilometraje' },
      { data: 'estado',       title: 'Estado' },
      { data: 'fechaingreso', title: 'Ingreso' },
      { data: 'fechasalida',  title: 'Salida' },
      {
        data: null,
        title: 'Opciones',
        orderable: false,
        render: row => {
          let btn = `<button class="btn btn-sm btn-primary ver-detalle" data-id="${row.idorden}">
                       <i class="fa fa-list"></i>
                     </button>`;
          if (row.estado === 'D') {
            btn += ` <button class="btn btn-sm btn-warning ver-just" data-id="${row.idorden}">
                       <i class="fa fa-comment-dots"></i>
                     </button>`;
          }
          return btn;
        }
      }
    ];

    // 2) Columnas para Ventas (listado de productos)
    const columnasVenta = [
      { data: 'idventa',         title: '#' },
      { data: 'fechahora',       title: 'Fecha' },
      { data: 'propietario',     title: 'Propietario' },
      { data: 'cliente',         title: 'Cliente' },
      { data: 'kilometraje',     title: 'Kilometraje' },
      { data: 'vehiculo',        title: 'Vehículo' },
      { data: 'producto',        title: 'Producto' },
      { data: 'cantidad',        title: 'Cantidad' },
      { data: 'precio',          title: 'Precio' },
      { data: 'descuento',       title: 'Descuento' },
      { data: 'total_producto',  title: 'Total' },
      {
        data: null,
        title: 'Opciones',
        orderable: false,
        render: row => {
          return `<button class="btn btn-sm btn-info ver-detalle-venta" data-id="${row.idventa}">
                    <i class="fa fa-list"></i>
                  </button>`;
        }
      }
    ];

    /**
     * Función para cargar la DataTable según el tipo:
     * - 'orden': lista de órdenes de servicio.
     * - 'venta': lista de productos de cada venta.
     */
    function cargar(tipo) {
      if (tabla) {
        tabla.clear();
        tabla.destroy();
        $('#tablaHistorial tbody').empty();
      }

      $('#headRow').empty();
      const cols = (tipo === 'orden') ? columnasOrden : columnasVenta;
      cols.forEach(col => {
        $('#headRow').append(`<th>${col.title}</th>`);
      });

      if (tipo === 'orden') {
        tabla = $('#tablaHistorial').DataTable({
          ajax: {
            url: baseUrl,
            data: {
              task: 'getOrdenesByVehiculo',
              idvehiculo: idVeh
            },
            dataSrc: ''
          },
          columns: columnasOrden
        });
      } else if (tipo === 'venta') {
        tabla = $('#tablaHistorial').DataTable({
          ajax: {
            url: baseUrl,
            data: {
              task: 'getVentasByVehiculo',
              idvehiculo: idVeh
            },
            dataSrc: 'data.productos'
          },
          columns: columnasVenta
        });
      }
    }

    // Cambio de radio (“Órdenes” / “Ventas”)
    $('input[name="tipo"]').on('change', e => {
      cargar(e.target.value);
    });

    // Eventos para botones de Órdenes de Servicio
    $('#tablaHistorial tbody')
      .on('click', 'button.ver-detalle', function() {
        const id = $(this).data('id');
        $.getJSON(baseUrl, {
            task: 'getDetalleOrdenServicio',
            idorden: id
          })
          .done(rows => {
            const $tbd = $('#tblDetalle tbody').empty();
            rows.forEach((r, i) => {
              $tbd.append(`
                <tr>
                  <td>${i + 1}</td>
                  <td>${r.servicio}</td>
                  <td>${r.mecanico}</td>
                  <td>${r.precio}</td>
                </tr>`);
            });
            new bootstrap.Modal($('#modalDetalle')).show();
          });
      })
      .on('click', 'button.ver-just', function() {
        const id = $(this).data('id');
        $.getJSON(baseUrl, {
            task: 'getJustificacionByOrden',
            idorden: id
          })
          .done(rows => {
            $('#txtJustificacion').text(rows[0]?.justificacion || '—');
            new bootstrap.Modal($('#modalJustificacion')).show();
          });
      });

    // Evento para “Ver detalle” de venta: usa el controller detventa.php
    $('#tablaHistorial tbody').on('click', 'button.ver-detalle-venta', function() {
      const idVenta = $(this).data('id');

      $.getJSON(urlDetVenta, {
          idventa: idVenta
        })
        .done(response => {
          const productos = response.data.productos || [];
          const servicios = response.data.servicios || [];

          // Llenar datos generales con la primera fila disponible
          const primeraFila = productos.length ? productos[0] : (servicios[0] || {});
          $('#dvPropietario').text(primeraFila.propietario ?? '—');
          $('#dvCliente').text(primeraFila.cliente ?? '—');
          $('#dvFechaHora').text(primeraFila.fechahora ?? '—');
          $('#dvKilometraje').text(primeraFila.kilometraje ?? '—');
          $('#dvVehiculo').text(primeraFila.vehiculo ?? '—');

          // Llenar tabla de productos (tblProd)
          const $tbodyProd = $('#tblProd tbody').empty();
          productos.forEach((p, idx) => {
            $tbodyProd.append(`
              <tr>
                <td>${idx + 1}</td>
                <td>${p.producto ?? ''}</td>
                <td>${p.cantidad ?? ''}</td>
                <td>${p.precio ?? ''}</td>
                <td>${p.descuento ?? ''}</td>
                <td>${p.total_producto ?? ''}</td>
              </tr>
            `);
          });

          // Llenar tabla de servicios (tblServ)
          const $tbodyServ = $('#tblServ tbody').empty();
          servicios.forEach((s, idx) => {
            $tbodyServ.append(`
              <tr>
                <td>${idx + 1}</td>
                <td>${s.tiposervicio ?? ''}</td>
                <td>${s.nombreservicio ?? ''}</td>
                <td>${s.mecanico ?? ''}</td>
                <td>${s.precio_servicio ? s.precio_servicio + ' $' : ''}</td>
              </tr>
            `);
          });

          new bootstrap.Modal($('#modalDetalleVenta')).show();
        })
        .fail((xhr, status, error) => {
          console.error('Error al obtener detalle de venta:', xhr.responseText);
          alert('No se pudo cargar el detalle de la venta. Revisa la consola.');
        });
    });

    // Carga inicial: mostrar Órdenes de Servicio
    cargar('orden');
  });
</script>


