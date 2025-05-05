<?php
const NAMEVIEW = "Ventas";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<div class="container-main mt-5">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" data-modo="semana" class="btn btn-primary text-white">Semana</button>
                <button type="button" data-modo="mes" class="btn btn-primary text-white">Mes</button>
                <button type="button" class="btn btn-danger text-white">
                    <i class="fa-solid fa-file-pdf"></i>
                </button>
                <!-- Nuevo botón para ver eliminados -->
                <button id="btnVerEliminados" type="button" class="btn btn-secondary text-white">
                    <i class="fa-solid fa-eye-slash"></i>
                </button>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="input-group">
                        <input type="date" class="form-control input" aria-label="Fecha"
                            aria-describedby="button-addon2" id="Fecha">
                        <a href="registrar-ventas.php" class="btn btn-success text-center" type="button"
                            id="button-addon2">Registrar</a>
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
                        <th class="text-center">Opciones</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <!-- Aquí se agregan los datos dinámicos -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Agregar aquí la tabla para las ventas eliminadas, inicialmente oculta -->
    <div id="tableEliminados" class="col-12" style="display: none;">
        <table class="table table-striped display" id="tablaventaseliminadas">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th class="text-center">T. Comprobante</th>
                    <th class="text-center">N° Comprobante</th>
                    <th class="text-center">Opciones</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <!-- Aquí se agregan los datos dinámicos de eliminados -->
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalVerJustificacion" tabindex="-1" aria-labelledby="modalJustificacionLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalJustificacionLabel">Justificación de la eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="contenidoJustificacion">
                <!-- Aquí se insertará dinámicamente la justificación -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalle de Venta -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de la Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
                <!--<div class="form-group">
                    <div class="form-floating input-group">
                        <input type="text" disabled class="form-control input" id="modeloInput" />
                        <label for="modeloInput">Cliente</label>
                    </div>
                    <div class="form-floating input-group">
                        <input type="text" disabled class="form-control input" id="fechaHora" />
                        <label for="fechaHora">Fecha & Hora:</label>
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
                <p>¿Por qué deseas eliminar esta Venta? (Escribe una justificación)</p>
                <textarea id="justificacion" class="form-control" rows="4"
                    placeholder="Escribe tu justificación aquí..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEliminarVenta" class="btn btn-danger">Eliminar Venta</button>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<!--FIN VENTAS-->
<?php
require_once "../../partials/_footer.php";
?>
<!-- Logica para ver los registro eliminados -->
<script>
    document.getElementById("btnVerEliminados").addEventListener("click", function () {
        const tableDia = document.getElementById("tableDia");
        const tableEliminados = document.getElementById("tableEliminados");

        // Ocultar tabla activa y mostrar la de eliminados
        tableDia.style.display = "none";
        tableEliminados.style.display = "block";

        // Destruir si ya está inicializada
        if ($.fn.DataTable.isDataTable("#tablaventaseliminadas")) {
            $("#tablaventaseliminadas").DataTable().destroy();
        }

        // Inicializar DataTable para ventas eliminadas
        $("#tablaventaseliminadas").DataTable({
            ajax: {
                url: "<?= SERVERURL ?>app/controllers/Venta.controller.php?action=ventas_eliminadas",
                dataSrc: function (json) {
                    return json.status === 'success' ? json.data : [];
                }
            },
            columns: [
                {
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                { data: "cliente", defaultContent: "No disponible", class: 'text-start' },
                { data: "tipocom", defaultContent: "No disponible", class: 'text-center' },
                { data: "numcom", defaultContent: "No disponible", class: 'text-center' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-info btn-sm btn-ver-justificacion" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalVerJustificacion"
                                    data-justificacion="${row.id}">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal" data-bs-target="#miModal"
                                onclick="verDetalleVenta('${row.id}')">
                                <i class="fa-solid fa-circle-info"></i>
                            </button>`;
                    },
                    class: 'text-center'
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
    });
    document.addEventListener("DOMContentLoaded", function () {
        cargarTablaVentas();
    });

    // Puedes capturar eventos de restauración aquí
    $(document).on('click', '.btn-restaurar', function () {
        const idVenta = $(this).data('id');
        console.log("Restaurar venta con ID:", idVenta);
        // Aquí puedes agregar lógica para restaurar la venta, usando fetch o Ajax
    });
</script>
<script>
    $(document).on('click', '.btn-ver-justificacion', function () {
        const justificacion = $(this).data('id') || 'Sin justificación';
        $('#contenidoJustificacion').text(justificacion);
    });
</script>
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
                { // Columna 7: Opciones (botones: editar, ver detalle, y otro para ver más)
                    data: null,
                    render: function (data, type, row) { // Inicio de render de opciones
                        return `
                        <button title="Eliminar"
                                class="btn btn-danger btn-sm btn-eliminar"
                                data-id="${row.id}">
                                <i class="fa-solid fa-trash"></i>
                        </button>
                        <button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal" data-bs-target="#miModal"
                                onclick="verDetalleVenta('${row.id}')">
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
    $(document).on('click', '.btn-eliminar', function () {
        const idVenta = $(this).data('id');
        console.log("ID recibido en el botón eliminar:", idVenta);
        $('#justificacion').val('');
        $('#btnEliminarVenta').data('id', idVenta);
        $('#modalJustificacion').modal('show');
    });
</script>
<script>
    function verDetalleVenta(idventa) {
        $("#miModal").modal("show");
        // limpia cualquier contenido previo
        $("#modeloInput, #fechaHora, #vehiculo, #kilometraje").val('');
        $("#miModal tbody").empty();

        $.ajax({
            url: "<?= SERVERURL ?>app/controllers/Detventa.controller.php",
            method: "GET",
            data: { idventa },
            dataType: "json",
            success(response) {
                if (response.length === 0) {
                    return $("#miModal tbody").append(
                        `<tr><td colspan="4" class="text-center">No hay detalles disponibles</td></tr>`
                    );
                }
                // pinta cabecera (todos los datos vienen de la misma fila 0)
                $("#modeloInput").val(response[0].cliente);
                $("#fechaHora").val(response[0].fechahora);
                // si es null o undefined, muestra la cadena "null"
                const vehiculoVal = response[0].vehiculo ?? 'null';
                const kilometrajeVal = response[0].kilometraje ?? 'null';

                $("#vehiculo").val(vehiculoVal);
                $("#kilometraje").val(kilometrajeVal);

                // pinta cada producto
                response.forEach((item, i) => {
                    $("#miModal tbody").append(`
                    <tr>
                        <td>${i + 1}</td>
                        <td>${item.producto}</td>
                        <td>${item.precio}</td>
                        <td>${item.descuento}%</td>
                    </tr>
                    `);
                });
            },
            error() {
                alert("Ocurrió un error al cargar el detalle.");
            }
        });
    }
</script>
<!-- <script>
    // reemplaza el handler existente por éste
    $(document).off('click', '#btnEliminarVenta');  // quita cualquier handler previo
    $(document).on('click', '#btnEliminarVenta', async function () {
        const justificacion = $('#justificacion').val().trim();
        const idventa = $(this).data('id');

        if (!justificacion) {
            alert('Escribe la justificación.');
            return;
        }

        // 1) pregunto con tu helper ask()
        const confirmado = await ask(
            "¿Estás seguro de eliminar esta venta?",
            "Confirmar eliminación"
        );
        if (!confirmado) {
            showToast('Eliminación cancelada.', 'WARNING', 1500);
            return;
        }

        // 2) feedback de “eliminando…”
        showToast('Eliminando Venta…', 'INFO', 1000);

        // 3) envío la petición de eliminación
        $.post("<?= SERVERURL ?>app/controllers/Venta.controller.php", {
            action: 'eliminar',
            idventa: idventa,
            justificacion: justificacion
        }, function (res) {
            // 4) tras respuesta muestro éxito o error
            if (res.status === 'success') {
                showToast('Venta eliminada.', 'SUCCESS', 1500);
                $('#modalJustificacion').modal('hide');
                setTimeout(cargarTablaVentas, 500);
            } else {
                showToast(res.message || 'Error al eliminar.', 'ERROR', 1500);
            }
        }, 'json')
            .fail(function () {
                showToast('Error de conexión.', 'ERROR', 1500);
            });
    });
</script> -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fechaInput = document.getElementById('Fecha');
        const tablaBody = document.querySelector('#tablaventasdia tbody');
        const btnSemana = document.querySelector('button[data-modo="semana"]');
        const btnMes = document.querySelector('button[data-modo="mes"]');
        const filtros = [btnSemana, btnMes];
        const API = "<?= SERVERURL ?>app/controllers/Venta.controller.php";

        // utilitario para formatear
        const fmt = iso => {
            if (!iso) return '';
            const d = new Date(iso);
            const pad = v => String(v).padStart(2, '0');
            return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ` +
                `${pad(d.getHours())}:${pad(d.getMinutes())}`;
        };

        // pinta filas
        const pintar = data => {
            tablaBody.innerHTML = '';
            data.forEach((v, i) => {
                tablaBody.insertAdjacentHTML('beforeend', `
          <tr>
            <td>${i + 1}</td>
            <td class="text-start">${v.cliente || ''}</td>
            <td class="text-center">${v.tipocom || ''}</td>
            <td class="text-center">${v.numcom || ''}</td>
            <td class="text-center">
              <button class="btn btn-danger btn-sm" data-id="${v.id}" data-action="eliminar">
                <i class="fa-solid fa-trash"></i>
              </button>
              <button class="btn btn-primary btn-sm" data-action="detalle" data-id="${v.id}">
                <i class="fa-solid fa-circle-info"></i>
              </button>
            </td>
          </tr>`);
            });
        };

        // Llama al endpoint y pinta
        const cargar = async (modo, fecha) => {
            console.log(`> cargando modo=${modo} fecha=${fecha}`);
            try {
                const res = await fetch(`${API}?modo=${modo}&fecha=${fecha}`);
                console.log('HTTP status:', res.status);
                const json = await res.json();
                console.log('JSON recibido:', json);
                if (json.status === 'success') pintar(json.data);
                else console.error('Error listando:', json.message);
            } catch (e) {
                console.error('Fetch error:', e);
            }
        };

        const marcaActivo = btn => {
            filtros.forEach(b => b.classList.toggle('active', b === btn));
        };

        // inicializo en día
        const hoy = new Date().toISOString().split('T')[0];
        fechaInput.value = hoy;
        let currentModo = 'dia';
        cargar(currentModo, hoy);

        // clicks en Semana/Mes
        filtros.forEach(btn => {
            btn.addEventListener('click', () => {
                currentModo = btn.dataset.modo;
                marcaActivo(btn);
                cargar(currentModo, fechaInput.value);
            });
        });

        // cambio de fecha → Día
        fechaInput.addEventListener('change', () => {
            currentModo = 'dia';
            marcaActivo(null);
            cargar(currentModo, fechaInput.value);
        });

        // event‑delegation para eliminar y detalle
        tablaBody.addEventListener('click', async ev => {
            const btn = ev.target.closest('button[data-action]');
            if (!btn) return;
            const id = btn.dataset.id;
            if (btn.dataset.action === 'eliminar') {
                // abre modal
                $('#justificacion').val('');
                $('#btnEliminarVenta').data('id', id);
                $('#modalJustificacion').modal('show');
            }
            if (btn.dataset.action === 'detalle') {
                verDetalleVenta(id);
            }
        });

        // handler del botón de confirmación en el modal
        $(document).off('click', '#btnEliminarVenta');
        $(document).on('click', '#btnEliminarVenta', async function () {
            const just = $('#justificacion').val().trim();
            const idv = $(this).data('id');
            if (!just) { alert('Escribe la justificación.'); return; }
            if (!await ask('¿Estás seguro de eliminar esta venta?', 'Confirmar eliminación')) {
                showToast('Eliminación cancelada.', 'WARNING', 1500);
                return;
            }
            showToast('Eliminando Venta…', 'INFO', 1000);
            $.post(API, { action: 'eliminar', idventa: idv, justificacion: just }, res => {
                if (res.status === 'success') {
                    showToast('Venta eliminada.', 'SUCCESS', 1500);
                    $('#modalJustificacion').modal('hide');
                    cargar(currentModo, fechaInput.value);
                } else {
                    showToast(res.message || 'Error al eliminar.', 'ERROR', 1500);
                }
            }, 'json').fail(() => showToast('Error de conexión.', 'ERROR', 1500));
        });
    });
</script>
</body>

</html>