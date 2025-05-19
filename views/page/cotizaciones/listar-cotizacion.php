<?php
const NAMEVIEW = "Cotizaciones";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>

<div class="container-main mt-5">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" data-modo="dia" class="btn btn-primary text-white">Día</button>
                <button type="button" data-modo="semana" class="btn btn-primary text-white">Semana</button>
                <button type="button" data-modo="mes" class="btn btn-primary text-white">Mes</button>
                <!-- Nuevo botón para ver eliminados -->
                <button id="btnVerEliminados" type="button" class="btn btn-secondary text-white">
                    <i class="fa-solid fa-eye-slash"></i>
                </button>
                <button type="button" class="btn btn-danger text-white">
                    <i class="fa-solid fa-file-pdf"></i>
                </button>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="input-group">
                        <input type="date" class="form-control input" aria-label="Fecha"
                            aria-describedby="button-addon2" id="Fecha">
                        <a href="registrar-cotizacion.php" class="btn btn-success text-center" type="button"
                            id="button-addon2">Registrar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="tableDia" class="col-12">
            <table class="table table-striped display" id="tablacotizacion">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Cliente</th>
                        <th>Precio</th>
                        <th>D. vigencia</th>
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
</div>

<!-- Modal de Detalle de Cotizacion -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 800px;"> <!-- Cambié el tamaño aquí -->
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
                                <th>Cantidad</th>
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

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="modalJustificacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Por qué deseas eliminar esta Cotizacion? (Escribe una justificación)</p>
                <textarea id="justificacion" class="form-control" rows="4"
                    placeholder="Escribe tu justificación aquí..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEliminarCotizacion" class="btn btn-danger">Eliminar Cotizacion</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once "../../partials/_footer.php";
?>
<script>
    let tablaCot;
    const API = "<?= SERVERURL ?>app/controllers/Cotizacion.controller.php";
    const fechaInput = document.getElementById('Fecha');
    const botones = document.querySelectorAll('.btn-group button');

    function marcarActivo(b) {
        botones.forEach(x => x.classList.toggle('active', x === b));
    }

    function cargarTablaCotizacion(modo, fecha) {
        if (tablaCot) {
            tablaCot.destroy();
            $('#tablacotizacion tbody').empty();
        }
        tablaCot = $('#tablacotizacion').DataTable({
            ajax: {
                url: API,
                data: { modo, fecha },
                dataSrc: 'data'
            },
            columns: [
                { data: null, render: (d, t, r, m) => m.row + 1, class: 'text-center' },
                { data: 'cliente', class: 'text-start' },
                { data: 'total', class: 'text-end' },
                { data: 'vigencia', class: 'text-center' },
                { // acciones…
                    data: null, class: 'text-center',
                    render: (row) => `
                    <button title="Eliminar" class="btn btn-danger btn-sm btnEliminar" data-id="${row.id}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                    <button class="btn btn-info btn-sm"
                            onclick="verDetalleCotizacion(${row.id},'${row.cliente}')">
                        <i class="fa-solid fa-circle-info"></i>
                    </button>
                    <button class="btn btn-success btn-sm"
                            onclick="window.location='../ventas/registrar-ventas.php?id=${row.id}'">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    </button>`
                }
            ],
            language: {
                lengthMenu: "Mostrar _MENU_ por página",
                zeroRecords: "Sin resultados",
                info: "Página _PAGE_ de _PAGES_",
                search: "Buscar:",
                emptyTable: "No hay datos"
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // inicializa hoy
        const hoy = new Date().toISOString().slice(0, 10);
        fechaInput.value = hoy;

        // evento botones
        botones.forEach(btn => {
            btn.addEventListener('click', () => {
                marcarActivo(btn);
                cargarTablaCotizacion(btn.dataset.modo, fechaInput.value);
            });
        });

        // cambiamos fecha → modo día
        fechaInput.addEventListener('change', () => {
            const diaBtn = document.querySelector('button[data-modo="dia"]');
            marcarActivo(diaBtn);
            cargarTablaCotizacion('dia', fechaInput.value);
        });

        // carga inicial
        marcarActivo(document.querySelector('button[data-modo="dia"]'));
        cargarTablaCotizacion('dia', hoy);

        document.getElementById('btnEliminarCotizacion').addEventListener('click', function () {
            const justificacion = document.getElementById('justificacion').value.trim();

            if (!justificacion) {
                alert('Debes escribir una justificación para eliminar.');
                return;
            }

            $.ajax({
                url: API,
                method: 'POST',
                data: {
                    accion: 'eliminar',
                    idcotizacion: idCotizacionEliminar,
                    justificacion
                },
                success: function (response) {
                    $('#modalJustificacion').modal('hide');
                    tablaCot.ajax.reload(null, false); // recargar sin reiniciar paginación
                },
                error: function () {
                    alert('Error al eliminar la cotización.');
                }
            });
        });
    });
    let idCotizacionEliminar = null;

    $(document).on('click', '.btnEliminar', function () {
        idCotizacionEliminar = $(this).data('id');
        $('#justificacion').val('');
        $('#modalJustificacion').modal('show');
    });
</script>
<script>
    function verDetalleCotizacion(idcotizacion, cliente) {
        $("#miModal").modal("show");
        $("#miModal label[for='cliente']").text(cliente);

        $.ajax({
            url: "<?= SERVERURL ?>app/controllers/Detcotizacion.controller.php",
            method: "GET",
            data: { idcotizacion: idcotizacion },
            dataType: "json",
            success: function (response) {
                /* console.log(response); */

                const tbody = $("#miModal tbody");
                tbody.empty();

                if (response.length > 0) {
                    response.forEach((item, index) => {
                        const fila = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.producto}</td>
                            <td>${item.cantidad}</td>
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
</body>

</html>

<!-- <script>
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
                        <button title="Eliminar" class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        <button title="Detalle" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#miModal"
                        onclick="verDetalleCotizacion('${row.idcotizacion}', '${row.cliente}')">
                            <i class="fa-solid fa-circle-info"></i>
                        </button>
                        <button title="Registrara una venta" class="btn btn-success btn-sm"
                                onclick="window.location.href='../ventas/registrar-ventas.php?id=${row.idcotizacion}'">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i>
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
</script> -->