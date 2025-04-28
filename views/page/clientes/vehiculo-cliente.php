<?php

const NAMEVIEW = "Vehiculo a nombre del cliente";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";



// Obtener el ID del cliente desde la URL
$idCliente = isset($_GET['idcliente']) ? intval($_GET['idcliente']) : 0;
?>

<div class="container-main">
  <div class="header-group mb-3">
    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
      &larr; Volver
    </button>
  </div>
  <div class="table-container" id="tablaVehiculosContainer">
    <table id="tablaVehiculosCliente" class="table table-striped display">
      <thead>
        <tr>
          <th>#</th>
          <th title="Tipo de vehiculo">T. Vehiculo</th>
          <th title="Marca del vehiculo">Marca</th>
          <th title="Placa del vehiculo">Placa</th>
          <th title="Color del vehiculo">Color</th>
          <th>Opciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- Modal detalle del vehiculo -->
<div class="modal fade" id="miModalCliente" tabindex="-1" aria-labelledby="miModalClienteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="miModalClienteLabel">Detalle del vehiculo</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3">
        <div class="row">
          <div class="col-6 mb-2">
            <div class="form-floating">
              <input type="text" disabled class="form-control" id="modeloInput" />
              <label for="modeloInput">Modelo:</label>
            </div>
          </div>
          <div class="col-6 mb-2">
            <div class="form-floating">
              <input type="text" disabled class="form-control" id="anioInput" />
              <label for="anioInput">Año:</label>
            </div>
          </div>
          <div class="col-6 mb-2">
            <div class="form-floating">
              <input type="text" disabled class="form-control" id="serieInput" />
              <label for="serieInput">N° Serie:</label>
            </div>
          </div>
          <div class="col-6 mb-2">
            <div class="form-floating">
              <input type="text" disabled class="form-control" id="combustibleInput" />
              <label for="combustibleInput">Combustible:</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<?php
require_once "../../partials/_footer.php";
?>

<script>
  // Inicializar DataTable filtrado por cliente
  function cargarTablaVehiculosCliente() {
    if ($.fn.DataTable.isDataTable('#tablaVehiculosCliente')) {
      $('#tablaVehiculosCliente').DataTable().destroy();
    }
    $('#tablaVehiculosCliente').DataTable({
      ajax: {
        url: "<?= SERVERURL ?>app/controllers/vehiculo.controller.php?task=getByCliente&idcliente=<?=$idCliente?>",
        dataSrc: ''
      },
      columns: [
        { // Contador de filas
          data: null,
          render: (data, type, row, meta) => meta.row + 1
        },
        { data: 'tipov', defaultContent: 'No disponible' },
        { data: 'nombre', defaultContent: 'No disponible' },
        { data: 'placa', defaultContent: 'No disponible' },
        { data: 'color', defaultContent: 'No disponible' },
        { data: null,
          render: function(data, type, row) {
            return `
              <button class="btn btn-sm btn-info me-1" title="Detalle" onclick="verDetalle('${row.modelo}', '${row.anio}', '${row.numserie}', '${row.tipocombustible}')">
                <i class="fa-solid fa-clipboard-list"></i>
              </button>
              <a href="editar-vehiculos.php?id=${row.idvehiculo}" class="btn btn-sm btn-warning" title="Editar">
                <i class="fa-solid fa-pen-to-square"></i>
              </a>
            `;
          }
        }
      ],
      language: {
        "lengthMenu": "Mostrar _MENU_ registros por página",
        "zeroRecords": "No se encontraron resultados",
        "info": "Mostrando página _PAGE_ de _PAGES_",
        "infoEmpty": "No hay registros disponibles",
        "infoFiltered": "(filtrado de _MAX_ registros totales)",
        "search": "Buscar:",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "emptyTable": "No hay datos disponibles en la tabla"
      }
    });
  }

  // Mostrar detalles en el modal
  function verDetalle(modelo, anio, serie, combustible) {
    document.querySelector('#modeloInput').value = modelo || 'No proporcionado';
    document.querySelector('#anioInput').value = anio || 'No proporcionado';
    document.querySelector('#serieInput').value = serie || 'No proporcionado';
    document.querySelector('#combustibleInput').value = combustible || 'No proporcionado';
    const modal = new bootstrap.Modal(document.getElementById('miModalCliente'));
    modal.show();
  }

  // Ejecutar al cargar la página
  document.addEventListener('DOMContentLoaded', cargarTablaVehiculosCliente);
</script>