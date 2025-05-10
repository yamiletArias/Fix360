<?php
CONST NAMEVIEW = "Historial del vehículo";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<div class="container-main">
  <h1><?php echo NAMEVIEW; ?></h1>
  <?php $idvehiculo = (int)($_GET['id'] ?? 0); ?>

  <div class="mb-4">
    <label class="me-3"><strong>Se están listando:</strong></label>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="tipo" id="radio-orden" value="orden" checked>
      <label class="form-check-label" for="radio-orden">Órdenes de Servicio</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="tipo" id="radio-venta" value="venta">
      <label class="form-check-label" for="radio-venta">Ventas</label>
    </div>
  </div>

  <table id="tablaHistorial" class="table table-striped" style="width:100%">
    <thead><tr id="headRow"></tr></thead>
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
              <th>#</th><th>Servicio</th><th>Mecánico</th><th>Precio</th>
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

<?php require_once "../../partials/_footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', ()=> {
  const idVeh = <?php echo $idvehiculo;?>;
  const baseUrl = '<?= SERVERURL ?>app/controllers/vehiculo.controller.php';

  let tabla;
  const columnas = {
  orden: [
    { data: 'idorden',        title: '#'           },
    { data: 'registrador',    title: 'Registró'    }, // antes: Administrador
    { data: 'propietario',    title: 'Propietario' },
    { data: 'cliente',        title: 'Cliente'     },
    { data: 'kilometraje',    title: 'Kilometraje' },
    { data: 'estado',         title: 'Estado'      },
    { data: 'fechaingreso',   title: 'Ingreso'     },
    { data: 'fechasalida',    title: 'Salida'      },
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
  ],
  venta: [
    { data: 'idventa',           title: '#'              },
    { data: 'registrador',       title: 'Registró'       },
    { data: 'propietario',       title: 'Propietario'    },
    { data: 'cliente',           title: 'Cliente'        },
    { data: 'tipo_comprobante',  title: 'Comprobante'    },
    { data: 'fechahora',         title: 'Fecha'          },
    { data: 'comprobante',       title: 'Número'         },
    { data: 'kilometraje',       title: 'Kilometraje'    },
    { data: 'estado',            title: 'Estado'         }
  ]
};


  function cargar(tipo) {
    if (tabla) tabla.clear().destroy();
    const task = tipo==='orden'
      ? 'getOrdenesByVehiculo'
      : 'getVentasByVehiculo';

    // Reconstruyo los headers
    $('#headRow').empty();
    columnas[tipo].forEach(col =>
      $('#headRow').append(`<th>${col.title}</th>`)
    );

    tabla = $('#tablaHistorial').DataTable({
      ajax: {
        url: baseUrl,               // <-- aquí la ruta absoluta
        data: { task, idvehiculo: idVeh },
        dataSrc: '',
        dataType: 'json',
        error(xhr) {
          console.error('AJAX ERROR', xhr.status, xhr.responseText);
          alert('Error cargando historial (revisa la consola)');
        }
      },
      columns: columnas[tipo]
    });
  }

  // Cambio de radio
  $('input[name="tipo"]').on('change', e => cargar(e.target.value));

  // Delegación de eventos para los botones
  $('#tablaHistorial tbody')
    .on('click', 'button.ver-detalle', function(){
      const id = $(this).data('id');
      $.getJSON(baseUrl, { task:'getDetalleOrdenServicio', idorden: id })
       .done(rows=>{
         const $b = $('#tblDetalle tbody').empty();
         rows.forEach((r,i)=> $b.append(`
           <tr>
             <td>${i+1}</td>
             <td>${r.servicio}</td>
             <td>${r.mecanico}</td>
             <td>${r.precio}</td>
           </tr>`));
         new bootstrap.Modal($('#modalDetalle')).show();
       });
    })
    .on('click', 'button.ver-just', function(){
      const id = $(this).data('id');
      $.getJSON(baseUrl, { task:'getJustificacionByOrden', idorden: id })
       .done(rows=>{
         $('#txtJustificacion').text(rows[0]?.justificacion || '—');
         new bootstrap.Modal($('#modalJustificacion')).show();
       });
    });

  // Carga inicial
  cargar('orden');
});
</script>
