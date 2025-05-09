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
                                    data-id="${row.id}">
                               <i class="fa-solid fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm btn-amortizar" 
                                    data-id="${row.id}" title="Amortizar">
                                <i class="fa-solid fa-dollar-sign"></i>
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
</script>
<!-- Logica para obetner la justificacion en el modal -->
<script>
    $(document).on('click', '.btn-ver-justificacion', async function () {
        const id = $(this).data('id');
        console.log('voy a pedir justificación para id=', id);
        $('#contenidoJustificacion').text('Cargando…');
        try {
            const res = await fetch(`<?= SERVERURL ?>app/controllers/Venta.controller.php?action=justificacion&idventa=${id}`);
            const json = await res.json();
            if (json.status === 'success') {
                $('#contenidoJustificacion').text(json.justificacion);
            } else {
                $('#contenidoJustificacion').text('No hay justificación');
            }
        } catch (e) {
            $('#contenidoJustificacion').text('Error al cargar justificación');
        }
        $('#modalVerJustificacion').modal('show');
    });
</script>
<!-- Vista principal -->
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
                {
                    data: null,
                    render: function (data, type, row) {
                        const cls = row.pagado ? 'btn-success' : 'btn-warning';
                        return `
                            <button class="btn btn-danger btn-sm btn-eliminar" data-id="${row.id}">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <button class="btn ${cls} btn-sm btn-amortizar"
                                data-id="${row.id}"
                                data-total="${row.total}"
                                data-bs-toggle="modal" data-bs-target="#modalAmortizar"
                                title="Amortizar">
                                <i class="fa-solid fa-dollar-sign"></i>
                            </button>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#miModal"
                                    onclick="verDetalleVenta('${row.id}')">
                                <i class="fa-solid fa-circle-info"></i>
                            </button>
                            `;
                    },
                    class: 'text-center'
                }// Cierra columna 7
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
<!-- Vista en el modal de detalle de venta para visualizar informacion de esa venta -->
<script>
    $(document).on('click', '.btn-amortizar', async function () {
        const id = $(this).data('id');
        const total = parseFloat($(this).data('total'));

        // precarga campos
        $('#am_idventa').val(id);
        $('#am_monto').val(total);

        // carga resumen de amortizaciones previas
        $('#modalAmortizar .resumen-amort').remove();
        $('#modalAmortizar .modal-body').prepend(
            '<div class="resumen-amort mb-3"><p class="small text-muted">Cargando resumen…</p></div>'
        );

        // carga formas de pago
        const $sel = $('#am_formapago').prop('disabled', true).html('<option>Cargando…</option>');
        try {
            const resp = await fetch('<?= SERVERURL ?>app/controllers/FormaPago.controller.php');
            const js = await resp.json();
            if (js.status === 'success') {
                $sel.empty();
                js.data.forEach(fp => $sel.append(`<option value="${fp.idformapago}">${fp.formapago}</option>`));
                $sel.find('option').filter((i, opt) => opt.text.toLowerCase() === 'efectivo').prop('selected', true);
            } else {
                $sel.html('<option>Error</option>');
            }
        } catch {
            $sel.html('<option>Error</option>');
        } finally {
            $sel.prop('disabled', false);
        }

        // obtén las amortizaciones ya hechas
        try {
            const res2 = await fetch(`<?= SERVERURL ?>app/controllers/Amortizacion.controller.php?action=list&idventa=${id}`);
            const j2 = await res2.json();
            let html;
            if (j2.status === 'success' && j2.data.length) {
                const cnt = j2.data.length;
                const mts = j2.data.map(a => parseFloat(a.amortizacion).toFixed(2)).join(', ');
                html = `<p class="small"><strong>${cnt}</strong> amortización(es): ${mts}</p>`;
            } else {
                html = '<p class="small text-muted">No hay amortizaciones previas.</p>';
            }
            $('#modalAmortizar .resumen-amort').html(html);
        } catch {
            $('#modalAmortizar .resumen-amort').html('<p class="small text-danger">Error al cargar resumen.</p>');
        }

        // por si no usas data-bs-*, abre el modal manualmente
        $('#modalAmortizar').modal('show');
    });

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
                // pinta productos como antes…
                $("#modeloInput").val(response[0].cliente);
                $("#fechaHora").val(response[0].fechahora);
                $("#vehiculo").val(response[0].vehiculo ?? 'Sin vehiculo');
                $("#kilometraje").val(response[0].kilometraje ?? 'sin kilometraje');
                response.forEach((item, i) => {
                    $("#miModal tbody").append(`
                    <tr>
                        <td>${i + 1}</td>
                        <td>${item.producto}</td>
                        <td>${item.precio}</td>
                        <td>${item.descuento}%</td>
                    </tr>`);
                });

                // ─── AÑADE ESTE BLOQUE PARA LAS AMORTIZACIONES ───
                fetch(`<?= SERVERURL ?>app/controllers/Amortizacion.controller.php?action=list&idventa=${idventa}`)
                    .then(r => r.json())
                    .then(json => {
                        if (json.status === 'success' && json.data.length) {
                            // crear sección y tabla de amortizaciones
                            const cont = $(`
                                <div class="amortizaciones-container mt-4">
                                <h6>Amortizaciones</h6>
                                <table class="table table-sm">
                                    <thead><tr>
                                    <th>#</th><th>Fecha</th><th>Monto</th><th>FP</th><th>Saldo</th>
                                    </tr></thead>
                                    <tbody></tbody>
                                </table>
                                </div>
                            `);
                            // rellenar filas
                            json.data.forEach((a, i) => {
                                cont.find('tbody').append(`
                      <tr>
                        <td>${i + 1}</td>
                        <td>${new Date(a.creado).toLocaleString()}</td>
                        <td>${a.amortizacion}</td>
                        <td>${a.formapago}</td>
                        <td>${a.saldo}</td>
                      </tr>`);
                            });
                            // insertar después de la tabla de productos
                            $("#miModal .modal-body").append(cont);
                        }
                    })
                    .catch(err => console.error("Error amortizaciones:", err));
            },
            error() {
                alert("Ocurrió un error al cargar el detalle.");
            }
        });
    }
</script>
<!-- Vistar la lista de ventas por periodo (YYYY-MM-DD) -->
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

        // pinta filas y mensaje cuando está vacío
        const pintar = data => {
            tablaBody.innerHTML = '';
            if (data.length === 0) {
                tablaBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted">
                No hay datos disponibles en la tabla
                </td>
            </tr>`;
                return;
            }
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
                <button class="btn btn-warning btn-sm btn-amortizar" data-id="${v.id}" title="Amortizar">
                    <i class="fa-solid fa-dollar-sign"></i>
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
            try {
                const res = await fetch(`${API}?modo=${modo}&fecha=${fecha}`);
                const json = await res.json();
                if (json.status === 'success') {
                    pintar(json.data);
                } else {
                    // en error no caigas a getAll()
                    pintar([]);
                    console.error('Error listando:', json.message);
                }
            } catch (e) {
                // si falla la red, tampoco llames a getAll()
                pintar([]);
                console.error('Fetch error:', e);
            }
        };
        /* const cargar = async (modo, fecha) => {
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
        }; */

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
<!-- Modal Amortización -->
<div class="modal fade" id="modalAmortizar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Amortización</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="am_idventa">
                <div class="mb-3">
                    <label>Monto</label>
                    <input type="number" id="am_monto" class="form-control" step="0.01">
                </div>
                <div class="mb-3">
                    <label>Forma de pago</label>
                    <select id="am_formapago" class="form-select">

                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btnSaveAmort" type="button" class="btn btn-success">Guardar</button>
            </div>
        </div>
    </div>
</div>
</body>

</html>