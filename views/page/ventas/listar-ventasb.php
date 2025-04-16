<?php
const NAMEVIEW = "Lista de ventas";
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
                    <div class="col text-end"><a href="registrar-ventas.php" class="btn btn-success" disabled>Registrar
                            Venta</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="tableDia" class="col-12">
            <table class="table table-striped display" id="tablaventasdia">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th class="text-center">T. Comprobante</th>
                        <th class="text-center">N° Comprobante</th>
                        <th>Fecha Hora</th>
                        <th class="text-center">Opciones</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <!-- Aquí se agregan los datos dinámicos -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal de Detalle de Venta -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 600px;"> <!-- Cambié el tamaño aquí -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de la Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p><strong>Cliente:</strong> <label for="cliente"></label></p>
                <!-- <div class="form-group" style="margin: 10px">
                  <div class="form-floating input-group">
                    <input type="text" disabled class="form-control input" id="modeloInput" />
                    <label for="modeloInput">Cliente</label>
                  </div>
                </div> -->
                <div class="table-container">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Productos</th>
                                <th>Precio</th>
                                <th>Descuento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- <tr>
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
                            </tr> -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<!--FIN VENTAS-->
</body>
</html>
<script>
    function cargarTablaVentas() {
        if ($.fn.DataTable.isDataTable("#tablaventasdia")) {
            $("#tablaventasdia").DataTable().destroy();
        } // Cierra if

        $("#tablaventasdia").DataTable({ // Inicio de configuración DataTable para vehículos
            ajax: {
                url: "<?= SERVERURL ?>app/controllers/Venta.controller.php",
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
                    data: "tipocom",
                    defaultContent: "No disponible",
                    class: 'text-center' // Centrado de la columna numcom
                }, // Cierra columna 3
                { // Columna 4: numero de comprobante
                    data: "numcom",
                    defaultContent: "No disponible",
                    class: 'text-center' // Centrado de la columna numcom
                }, // Cierra columna 4
                { // Columna 5: fecha y hora de la venta
                    data: "fechahora",
                    defaultContent: "No disponible"
                }, // Cierra columna 6
                { // Columna 7: Opciones (botones: editar, ver detalle, y otro para ver más)
                    data: null,
                    render: function (data, type, row) { // Inicio de render de opciones
                        return `
                        <a href="editar-ventas.php?id=${row.idventa}" class="btn btn-sm btn-warning" title="Editar">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <button title="Eliminar" class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        <button title="Detalle" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#miModal"
                        onclick="verDetalleVenta('${row.idventa}', '${row.cliente}')">
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
      cargarTablaVentas();
    });
</script>
<script>
  function verDetalleVenta(idventa, cliente) {
    $("#miModal").modal("show");
    $("#miModal label[for='cliente']").text(cliente);

    $.ajax({
        url: "<?= SERVERURL ?>app/controllers/Detventa.controller.php",
        method: "GET",
        data: { idventa: idventa },
        dataType: "json",
        success: function (response) {
    console.log(response);  // Verifica la respuesta del servidor

    const tbody = $("#miModal tbody");
    tbody.empty(); // Limpiar contenido anterior

    if (response.length > 0) {
        response.forEach((item, index) => {
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.producto}</td>
                    <td>${item.precio}</td>
                    <td>${item.descuento}%</td>
                </tr>
            `;
            tbody.append(fila);
        });
    } else {
        tbody.append(`<tr><td colspan="4" class="text-center">No hay detalles disponibles</td></tr>`);
    }
},
        error: function () {
            alert("Ocurrió un error al cargar el detalle.");
        }
    });
}
</script>
<?php
require_once "../../partials/_footer.php";
?>