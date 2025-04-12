<?php
const NAMEVIEW = "Lista de compras";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>

<div class="container-main mt-5">
  <div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
      <div class="btn-group" role="group" aria-label="Basic example">
        <button id="btnDia" type="button" class="btn btn-primary">
          Día
        </button>
        <button id="btnSemana" type="button" class="btn btn-primary">
          Semana
        </button>
        <button id="btnMes" type="button" class="btn btn-primary">
          Mes
        </button>
        <button type="button" class="btn btn-outline-danger">
          <i class="fa-solid fa-file-pdf"></i>
        </button>
      </div>
      <div class="row mt-3">
        <div class="col-12">
          <div class="col text-end"><a href="registrar-compras.php" class="btn btn-success" disabled>Registrar
              Compra</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="tableCompraDia" class="table-container">
    <!--Tabla Dia-->
    <table id="tablacomprasdia" class="table table-striped display">
      <thead>
        <tr>
          <th>#</th>
          <th class="text-left">Proveedor</th>
          <th class="text-center">T. Comprobante</th>
          <th class="text-center">N° Comprobante</th>
          <th>Fecha Compra</th>
          <th>Precio</th>
          <!-- <th>Moneda</th> -->
          <th class="text-center">Opciones</th>
        </tr>
      </thead>
      <tbody class="text-center">

      </tbody>
    </table>
  </div>

</div>
</div>
</div>
<!--FIN VENTAS-->

<script>
  function cargarTablaCompras() {
    if ($.fn.DataTable.isDataTable("#tablacomprasdia")) {
      $("#tablacomprasdia").DataTable().destroy();
    } // Cierra if

    $("#tablacomprasdia").DataTable({
      ajax: {
        url: "<?= SERVERURL ?>app/controllers/Compra.controller.php",
        dataSrc: ""
      }, // Cierra ajax
      columns: [
        { // Columna 1: Número de fila
          data: null,
          render: (data, type, row, meta) => meta.row + 1
        }, // Cierra columna 1
        { // Columna 4: proveedor
          data: "proveedores",
          defaultContent: "No disponible",
          class: 'text-start' 
        },
        { // Columna 2: tipocom
          data: "tipocom",
          defaultContent: "No disponible"
        }, // Cierra columna 2
        { // Columna 3: numero de comprobante
          data: "numcom",
          defaultContent: "No disponible",
          class: 'text-center'
        }, // Cierra columna 3
        { // Columna 5: fecha de la compra
          data: "fechacompra",
          defaultContent: "No disponible"
        }, // Cierra columna 5
        { // Columna 6: precio
          data: "preciocompra",
          defaultContent: "No disponible"
        }, // Cierra columna 6
        { // Columna 7: Opciones (botones: editar, ver detalle, y otro para ver más)
          data: null,
          render: function (data, type, row) { // Inicio de render de opciones
            return `
            <a href="editar-cventas.php" class="btn btn-sm btn-warning" title="Editar">
              <i class="fa-solid fa-pen-to-square"></i>
            </a>
            <button title="Eliminar" class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
              <i class="fa-solid fa-trash"></i>
            </button>
            <button title="Detalle" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
              data-bs-target="#miModal">
              <i class="fa-solid fa-circle-info"></i>
            </button>

      `;
          } // Cierra render de opciones
        } // Cierra columna 7
      ], // Cierra columns
      language: { // Inicio de configuración de idioma
        "lengthMenu": "Mostrar _MENU_ registros por página",
        "zeroRecords": "No se encontraron resultados",
        "info": "Mostrando página _PAGE_ de _PAGES_",
        "infoEmpty": "No hay registros disponibles",
        "infoFiltered": "(filtrado de _MAX_ registros totales)",
        "search": "Buscar:",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "emptyTable": "No hay datos disponibles en la tabla"
      } // Cierra language
    }); // Cierra DataTable inicialización
  } // Cierra cargarTablaVehiculos()

  document.addEventListener("DOMContentLoaded", function () {
    cargarTablaCompras();
  });
</script>


<!-- Modal -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle de la Compra 0001-022</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Cliente: Jose Hernandez</p>
        <div class="table-container">
          <!-- Tabla Día -->
          <table id="miTabla" class="table table-striped display">
            <thead>
              <tr>
                <th>#</th>
                <th>Productos</th>
                <th>Precio</th>
                <th>Moneda</th>
                <th>Descuento</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Filtro de aceite</td>
                <td>120.00</td>
                <td>Soles</td>
                <td>0%</td>
              </tr>
              <tr>
                <td>2</td>
                <td>Pastillas de freno</td>
                <td>150.00</td>
                <td>Soles</td>
                <td>0%</td>
              </tr>
              <tr>
                <td>3</td>
                <td>Amortiguador delantero</td>
                <td>250.00</td>
                <td>Soles</td>
                <td>0%</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</div>
<!--FIN VENTAS-->
</body>

</html>
<?php
require_once "../../partials/_footer.php";
?>