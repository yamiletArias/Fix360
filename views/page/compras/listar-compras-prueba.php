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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                            <button title="Eliminar"
                                class="btn btn-danger btn-sm btn-eliminar"
                                data-id="${row.id}">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <button title="Detalle" type="button" class="btn btn-primary btn-sm"
                            data-bs-toggle="modal" data-bs-target="#miModal"
                            onclick="verDetalleCompra('${row.id}', '${row.proveedores}')">
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

    // Manejar click en “Eliminar”
$(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    $('#modalJustificacion').modal('show');  // Mostrar el modal de justificación inmediatamente
    $('#btnEliminarCompra').data('id', id);  // Guardar el ID de la compra para eliminar más tarde
});

// Eliminar compra con justificación
$('#btnEliminarCompra').on('click', function () {
    const justificacion = $('#justificacion').val().trim();
    const idcompra = $(this).data('id');

    if (!justificacion) {
        alert('Por favor, escribe una justificación para eliminar la compra.');
        return;
    }

    // Enviar la justificación junto con la solicitud de eliminación
    $.ajax({
        url: "<?= SERVERURL ?>app/controllers/Compra.controller.php",
        method: "POST",
        data: {
            action: 'eliminar',
            idcompra: idcompra,
            justificacion: justificacion
        },
        dataType: "json",
        success: function (res) {
            if (res.status === 'success') {
                alert('Compra anulada con éxito.');
                cargarTablaCompras();  // Recargar la tabla
                $('#modalJustificacion').modal('hide'); // Cerrar el modal después de la eliminación
            } else {
                alert('Error: ' + res.message);
            }
        },
        error: function () {
            alert('Ocurrió un error en la petición.');
        }
    });
});
</script>

<script>
    function verDetalleCompra(idcompra, proveedor) {
        $("#miModal").modal("show");
        $("#miModal label[for='proveedor']").text(proveedor);

        $.ajax({
            url: "<?= SERVERURL ?>app/controllers/Detcompra.controller.php",
            method: "GET",
            data: { idcompra: idcompra },
            dataType: "json",
            success: function (response) {
                const tbody = $("#miModal tbody").empty();
                if (response.length > 0) {
                    response.forEach((item, i) => {
                        tbody.append(`
            <tr>
              <td>${i + 1}</td>
              <td>${item.producto}</td>
              <td>${item.precio}</td>
              <td>${item.descuento}%</td>
            </tr>
          `);
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

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="modalJustificacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Por qué deseas eliminar esta compra? (Escribe una justificación)</p>
                <textarea id="justificacion" class="form-control" rows="4"
                    placeholder="Escribe tu justificación aquí..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEliminarCompra" class="btn btn-danger">Eliminar Compra</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalle de Compras -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 800px;"> <!-- Cambié el tamaño aquí -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de la Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Proveedor:</strong> <label for="proveedor"></label></p>
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
<!--FIN VENTAS-->
</body>

</html>
<?php
require_once "../../partials/_footer.php";
?>