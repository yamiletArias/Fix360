<?php
const NAMEVIEW = "Lista de cotizaciones";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<div class="container-main">
  <div>
    <div class="text-end">
      <button type="button" onclick="window.location.href='registrar-cotizacion.php'" class="btn btn-success text-end">
        Registrar
      </button>
    </div>
    <div class="table-container mt-5">
      <table id="tablacotizacion" class="table table-striped display">
        <thead>
          <tr>
            <th class="text-center">#</th>
            <th>Cliente</th>
            <th>Precio</th>
            <th>vigencia</th>
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

<!-- Modal -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle de la Cotizacion 001-002</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div>
          <div class="table-container">
            <!-- Tabla Día -->
            <table id="miTabla" class="table table-striped display">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Productos</th>
                  <th>Precio</th>
                  <td>Moneda</td>
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
                  <td>20%</td>
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
</div>

<?php
require_once "../../partials/_footer.php";
?>

<script>
    function cargarTablaCotizacion() {
        if ($.fn.DataTable.isDataTable("#tablacotizacion")) {
            $("#tablacotizacion").DataTable().destroy();
        } // Cierra if

        $("#tablacotizacion").DataTable({ // Inicio de configuración DataTable para vehículos
            ajax: {
                url: "<?= SERVERURL ?>app/controllers/Cotizacion.controller.php",
                dataSrc: ""
            }, // Cierra ajax
            columns: [
                { // Columna 1: Número de fila
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                }, // Cierra columna 1
                { // Columna 2: cliente
                    data: "cliente",
                    defaultContent: "No disponible",
                    class: 'text-start'
                }, // Cierra columna 2
                { // Columna 3: tipo de comprobante
                    data: "precio",
                    defaultContent: "No disponible",
                }, // Cierra columna 3
                { // Columna 4: numero de comprobante
                    data: "vigencia",
                    defaultContent: "No disponible",

                }, // Cierra columna 6
                { // Columna 7: Opciones (botones: editar, ver detalle, y otro para ver más)
                    data: null,
                    render: function (data, type, row) { // Inicio de render de opciones
                        return `
                        <a href="editar-ventas.php" class="btn btn-sm btn-warning" title="Editar">
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
      cargarTablaCotizacion();
    });
</script>

</body>

</html>