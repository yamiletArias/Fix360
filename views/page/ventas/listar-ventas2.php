<?php
const NAMEVIEW = "Ventas";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<style>
    #am_formapago {
        color: black;
        /* Cambia solo el color de la letra */
    }

    /* Reduce el padding inferior del header */
    #miModal .modal-header {
        padding-bottom: 0.5rem;
    }

    /* Reduce el padding superior del body */
    #miModal .modal-body {
        padding-top: 0.5rem;
    }

    /* Quita márgenes extra de ese párrafo */
    #miModal .modal-body>p {
        margin-top: 0.25rem;
        margin-bottom: 0.5rem;
    }
</style>
<div class="container-main mt-5">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" data-modo="dia" class="btn btn-primary text-white">Día</button>
                <button type="button" data-modo="semana" class="btn btn-primary text-white">Semana</button>
                <button type="button" data-modo="mes" class="btn btn-primary text-white">Mes</button>
                <!-- Nuevo botón para ver eliminados -->
                <!--                 <button id="btnVerEliminados" type="button" class="btn btn-secondary text-white">
                    <i class="fa-solid fa-eye-slash"></i>
                </button> -->
                <button id="btnVerEliminados" type="button" class="btn btn-secondary text-white" title="Ver eliminados"
                    data-estado="A">
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
                        <a href="registrar-ventas-orden.php" class="btn btn-success text-center" type="button"
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

</div>
</div>
<!--FIN VENTAS-->
<?php
require_once "../../partials/_footer.php";
?>

<script>
    let currentEstado = 'A'; // 'A' para activos, 'E' para eliminados
    const btnVerElim = document.getElementById("btnVerEliminados");
    const contActivos = document.getElementById("tableDia");
    const contEliminados = document.getElementById("tableEliminados");

    // Función para actualizar la apariencia del toggle
    const actualizarToggleEstado = () => {
        if (currentEstado === 'A') {
            btnVerElim.classList.replace('btn-warning', 'btn-secondary');
            btnVerElim.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
            btnVerElim.title = 'Ver eliminados';
        } else {
            btnVerElim.classList.replace('btn-secondary', 'btn-warning');
            btnVerElim.innerHTML = '<i class="fa-solid fa-eye"></i>';
            btnVerElim.title = 'Ver activos';
        }
        btnVerElim.setAttribute('data-estado', currentEstado);
    };

    // Inicializa el toggle con el estado por defecto
    actualizarToggleEstado();

    btnVerElim.addEventListener("click", () => {
        if (currentEstado === 'A') {
            // Pasar a eliminados
            contActivos.style.display = "none";
            contEliminados.style.display = "block";
            cargarVentasEliminadas();
            currentEstado = 'E';
        } else {
            // Volver a activos
            contEliminados.style.display = "none";
            contActivos.style.display = "block";
            currentEstado = 'A';
        }
        actualizarToggleEstado();
    });
</script>
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
<!-- Vista en el modal de detalle de venta para visualizar informacion de esa venta -->
<script>
    function toggleNumTransAmort() {
        const texto = $('#am_formapago option:selected').text().trim().toLowerCase();
        if (texto === 'efectivo') {
            $('#div_num_transaccion').hide()
                .find('input').val('');
        } else {
            $('#div_num_transaccion').show();
        }
    }
    $(document).on('click', '.btn-amortizar', async function () {
        const id = $(this).data('id');
        const monto = parseFloat($(this).data('total')) || 0;

        // ── 1) Primero, obtenemos total_venta desde el API ──
        let totalPendiente = 0;
        try {
            const resTotal = await fetch(`<?= SERVERURL ?>app/controllers/Amortizacion.controller.php?idventa=${id}`);
            const jsTotal = await resTotal.json();
            /* console.log("RESPUESTA AMORTIZACION.API:", jsTotal); */
            if (jsTotal.status === 'success') {
                totalPendiente = parseFloat(jsTotal.total_pendiente) || 0;
                window.currentVentaPagada = jsTotal.pagado;
            }
        } catch (e) {
            console.error('No se pudo obtener total_venta:', e);
        }

        // ── 2) precarga campos del modal usando el total obtenido ──
        $('#am_idventa').val(id);
        $('#am_monto').val(totalPendiente.toFixed(2));

        // ── resto de tu código intacto ──
        $('#modalAmortizar .resumen-amort').remove();
        $('#modalAmortizar .modal-body').prepend(
            '<div class="resumen-amort mb-3"><p class="small text-muted">Cargando resumen…</p></div>'
        );

        // carga formas de pago...
        const $sel = $('#am_formapago').prop('disabled', true).html('<option>Cargando…</option>');
        try {
            const resp = await fetch('<?= SERVERURL ?>app/controllers/FormaPagos.controller.php');
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
            toggleNumTransAmort();
            $sel.off('change', toggleNumTransAmort)
                .on('change', toggleNumTransAmort);
        }

        // carga amortizaciones previas...
        try {
            const res2 = await fetch(`<?= SERVERURL ?>app/controllers/Amortizacion.controller.php?idventa=${id}`);
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

        $('#modalAmortizar').modal('show');
    });

    function verDetalleVenta(idventa) {
        // — Limpiar modal
        $("#miModal tbody, #tabla-detalle-productos-modal tbody, #tabla-detalle-servicios-modal tbody").empty();
        $("#miModal .amortizaciones-container").remove();
        $("#modeloInput, #fechaHora, #vehiculo, #kilometraje").val('');
        $("label[for='propietario']").text('');

        // — Abrir modal
        $("#miModal").modal("show");

        // 1) Propietario
        fetch(`<?= SERVERURL ?>app/controllers/Venta.controller.php?action=propietario&idventa=${idventa}`)
            .then(r => r.json())
            .then(jsonVenta => {
                if (jsonVenta.status === 'success') {
                    $("label[for='propietario']").text(jsonVenta.data.propietario || 'Sin propietario');
                } else {
                    $("label[for='propietario']").text('No encontrado');
                }
            })
            .catch(() => {
                $("label[for='propietario']").text('Error al cargar');
            });

        // 2) Detalle completo (productos + servicios)
        fetch(`<?= SERVERURL ?>app/controllers/Detventa.controller.php?idventa=${idventa}`)
            .then(r => r.json())
            .then(json => {
                console.log("DETALLE VENTA RAW:", json);
                if (json.status !== 'success') {
                    console.error("Detventa error:", json.message);
                    return;
                }
                const { productos, servicios } = json.data;

                // — Productos —
                const $prodBody = $("#tabla-detalle-productos-modal tbody").empty();
                if (!productos.length) {
                    $prodBody.append(`<tr><td colspan="6" class="text-center text-muted">No hay productos</td></tr>`);
                } else {
                    productos.forEach((p, i) => {
                        $prodBody.append(`
                        <tr>
                            <td>${i + 1}</td>
                            <td>${p.producto}</td>
                            <td>${p.cantidad}</td>
                            <td>${parseFloat(p.precio).toFixed(2)} $</td>
                            <td>${parseFloat(p.descuento).toFixed(2)} $</td>
                            <td>${parseFloat(p.total_producto).toFixed(2)} $</td>
                        </tr>`);
                    });
                    // Campos generales
                    $("#modeloInput").val(productos[0].cliente || 'Sin Cliente');
                    $("#fechaHora").val(productos[0].fechahora);
                    $("#vehiculo").val(productos[0].vehiculo || 'Sin vehículo');
                    $("#kilometraje").val(productos[0].kilometraje || 'Sin kilometraje');
                }

                const serviciosValidos = servicios.filter(s =>
                    s.tiposervicio !== null ||
                    s.nombreservicio !== null ||
                    s.mecanico !== null ||
                    s.precio_servicio !== null
                );
                // — Servicios —
                const $servBody = $("#tabla-detalle-servicios-modal tbody").empty();
                if (!serviciosValidos.length) {
                    $servBody.append(`<tr><td colspan="5" class="text-center text-muted">No hay servicios</td></tr>`);
                } else {
                    serviciosValidos.forEach((s, i) => {
                        $servBody.append(`
      <tr>
        <td>${i + 1}</td>
        <td>${s.tiposervicio ?? '-'}</td>
        <td>${s.nombreservicio ?? '-'}</td>
        <td>${s.mecanico ?? '-'}</td>
        <td>${s.precio_servicio !== null
                                ? parseFloat(s.precio_servicio).toFixed(2) + ' $'
                                : '-'
                            }</td>
      </tr>`);
                    });
                }

                // — Amortizaciones —
                fetch(`<?= SERVERURL ?>app/controllers/Amortizacion.controller.php?action=list&idventa=${idventa}`)
                    .then(r => r.json())
                    .then(jsonA => {
                        if (jsonA.status === 'success' && jsonA.data.length) {
                            const cont = $(`
                            <div class="amortizaciones-container mt-4">
                                <h6>Amortizaciones</h6>
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Transacción</th>
                                            <th>Nº Transacción</th>
                                            <th>Monto</th>
                                            <th>F. Pago</th>
                                            <th>Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        `);
                            jsonA.data.forEach((a, i) => {
                                cont.find('tbody').append(`
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${new Date(a.creado).toLocaleString()}</td>
                                    <td>${a.numtransaccion}</td>
                                    <td>${parseFloat(a.amortizacion).toFixed(2)} $</td>
                                    <td>${a.formapago}</td>
                                    <td>${parseFloat(a.saldo).toFixed(2)} $</td>
                                </tr>`);
                            });
                            $("#miModal .modal-body").append(cont);
                        }
                    })
                    .catch(() => console.error("Error amortizaciones"));

            })
            .catch(() => {
                console.error("Error al cargar detalle de venta");
                alert("Ocurrió un error al cargar el detalle.");
            });
    }
</script>
<script>
    let tablaVentas;
    const API = "<?= SERVERURL ?>app/controllers/Venta.controller.php";
    const fechaInput = document.getElementById('Fecha');
    const btnDia = document.querySelector('button[data-modo="dia"]');
    const btnSemana = document.querySelector('button[data-modo="semana"]');
    const btnMes = document.querySelector('button[data-modo="mes"]');
    const filtros = [btnDia, btnSemana, btnMes];

    function marcarActivo(btn) {
        filtros.forEach(b => b.classList.toggle('active', b === btn));
    }

    function cargarTablaVentas(modo, fecha) {
        if (tablaVentas) {
            tablaVentas.destroy();
            $("#tablaventasdia tbody").empty();
        }

        tablaVentas = $("#tablaventasdia").DataTable({
            ajax: {
                url: "<?= SERVERURL ?>app/controllers/Venta.controller.php",
                data: { modo, fecha },
                dataSrc: "data"
            },
            columns: [
                { // Columna 1: Número de fila
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                }, // Cierra columna 1
                { // Columna 2: cliente
                    data: "cliente",
                    defaultContent: "Sin cliente",
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
                    class: "text-center",
                    render: renderOpciones
                }
            ], // Cierra columns
            language: {
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando página _PAGE_ de _PAGES_",
                infoEmpty: "No hay registros disponibles",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                search: "Buscar:",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                emptyTable: "No hay datos disponibles en la tabla"
            }
        }); // Cierra DataTable inicialización
    } // Cierra cargarTablaVehiculos()

    // Carga la tabla de registros eliminados
    function cargarVentasEliminadas() {
        if ($.fn.DataTable.isDataTable("#tablaventaseliminadas")) {
            $("#tablaventaseliminadas").DataTable().destroy();
            $("#tablaventaseliminadas tbody").empty();
        }
        $("#tablaventaseliminadas").DataTable({
            ajax: {
                url: API + "?action=ventas_eliminadas",
                dataSrc(json) {
                    console.log("ventas_eliminadas response:", json);
                    return json.status === 'success' ? json.data : [];
                }
            },
            columns: [
                { data: null, render: (d, t, r, m) => m.row + 1 },
                { data: "cliente", class: "text-start", defaultContent: "—" },
                { data: "tipocom", class: "text-center", defaultContent: "—" },
                { data: "numcom", class: "text-center", defaultContent: "—" },
                {
                    data: null,
                    class: "text-center",
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-primary btn-sm btn-ver-justificacion"
                                data-id="${row.id}"
                                data-bs-toggle="modal"
                                data-bs-target="#modalVerJustificacion">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm btn-amortizar"
                                data-id="${row.id}"
                                data-bs-toggle="modal"
                                data-bs-target="#modalAmortizar">
                                <i class="fa-solid fa-dollar-sign"></i>
                            </button>
                            <button title="Detalle de la venta" class="btn btn-info btn-sm btn-detalle"
                                    data-action="detalle"
                                    data-id="${row.id}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#miModal">
                                <i class='fa-solid fa-clipboard-list'></i>
                            </button>`;
                    }
                }
            ],
            language: {
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando página _PAGE_ de _PAGES_",
                infoEmpty: "No hay registros disponibles",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                search: "Buscar:",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                emptyTable: "No hay datos disponibles en la tabla"
            }
        });
    }

    function renderOpciones(data, type, row) {
        const pagado = row.estado_pago === 'pagado';
        const btnAmort = pagado
            ? `<button class="btn btn-success btn-sm" disabled><i class="fa-solid fa-check"></i></button>`
            : `<button title="Amortizacion" class="btn btn-warning btn-sm btn-amortizar"
         data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#modalAmortizar">
         <i class="fa-solid fa-dollar-sign"></i>
       </button>`;

        return `
        <button title="Eliminar" class="btn btn-danger btn-sm btn-eliminar" data-id="${row.id}">
        <i class="fa-solid fa-trash"></i>
        </button>
        ${btnAmort}
        <button title="Detalle de la venta" class="btn btn-info btn-sm btn-detalle"
                data-action="detalle"
                data-id="${row.id}"
                data-bs-toggle="modal"
                data-bs-target="#miModal">
            <i class='fa-solid fa-clipboard-list'></i>
        </button>
        <button title="Pdf" class="btn btn-outline-dark btn-sm btn-descargar-pdf"
                onclick="descargarPDF('${row.id}')">
        <i class="fa-solid fa-file-pdf"></i>
        </button>`;
    }
    function descargarPDF(idventa) {
        const url = `<?= SERVERURL ?>app/reports/reporteventa.php?idventa=${encodeURIComponent(idventa)}`;
        window.open(url, '_blank');
    }
    document.addEventListener("DOMContentLoaded", () => {
        // inicializo fecha de hoy
        const hoy = new Date().toISOString().slice(0, 10);
        fechaInput.value = hoy;
        let currentModo = 'dia';
        marcarActivo(btnDia);
        cargarTablaVentas(currentModo, hoy);

        // clicks en filtros
        filtros.forEach(btn => {
            btn.addEventListener("click", () => {
                currentModo = btn.dataset.modo;
                marcarActivo(btn);
                cargarTablaVentas(currentModo, fechaInput.value);
            });
        });

        // cambio de fecha → día
        fechaInput.addEventListener("change", () => {
            currentModo = 'dia';
            marcarActivo(btnDia);
            cargarTablaVentas(currentModo, fechaInput.value);
        });

        // eliminación con justificación
        $(document).on('click', '.btn-eliminar', function () {
            const idv = $(this).data('id');
            $('#justificacion').val('');
            $('#btnEliminarVenta').data('id', idv);
            $('#modalJustificacion').modal('show');
        });

        // confirmar eliminación
        $(document).on('click', '#btnEliminarVenta', async function () {
            const just = $('#justificacion').val().trim();
            const idv = $(this).data('id');
            if (!just) return alert('Escribe la justificación.');
            if (!await ask('¿Confirmar eliminación?', 'Eliminar')) return;
            $.post(API, { action: 'eliminar', idventa: idv, justificacion: just }, res => {
                if (res.status === 'success') {
                    showToast('Venta eliminada', 'SUCCESS', 1500);
                    $('#modalJustificacion').modal('hide');
                    cargarTablaVentas(currentModo, fechaInput.value);
                } else {
                    showToast(res.message || 'Error', 'ERROR', 1500);
                }
            }, 'json');
        });

        // guardar amortización
        $(document).on('click', '#btnGuardarAmortizacion', async function () {
            const idventa = +$('#am_idventa').val();
            const monto = parseFloat($('#am_monto').val());
            const formapago = +$('#am_formapago').val();
            if (!monto || monto <= 0) return alert('Monto inválido');

            const form = new FormData();
            form.append('idventa', idventa);
            form.append('monto', monto);
            form.append('idformapago', formapago);

            // *** Aquí: solo si el input está visible y tiene valor ***
            const $numField = $('#num_transaccion');
            if ($numField.is(':visible') && $numField.val().trim() !== '') {
                form.append('numtransaccion', $numField.val().trim());
            }

            const res = await fetch("<?= SERVERURL ?>app/controllers/Amortizacion.controller.php", {
                method: 'POST',
                body: form
            }).then(r => r.json());

            if (res.status === 'success') {
                showToast(res.message, 'SUCCESS', 1500);
                $('#modalAmortizar').modal('hide');
                cargarTablaVentas(currentModo, fechaInput.value);
                verDetalleVenta(idventa);
            } else {
                showToast(res.detail || res.message, 'ERROR', 2000);
            }
        });
        $(document).on('click', '.btn-detalle', function () {
            const idventa = $(this).data('id');
            verDetalleVenta(idventa);
            $('#miModal').modal('show');
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
                <input type="hidden" id="am_idventa" value="1"> <!-- Aquí asegúrate de setear el id de la venta -->
                <div class="mb-3">
                    <label>Monto</label>
                    <input type="number" id="am_monto" class="form-control input" step="0.01">
                </div>
                <div class="mb-3">
                    <label>Forma de pago</label>
                    <select id="am_formapago" class="form-select">
                        <!-- Aquí puedes cargar las formas de pago si tienes disponibles -->
                    </select>
                </div>
                <!-- Oculto por defecto -->
                <div class="mb-3" id="div_num_transaccion" style="display: none;">
                    <label>Numero de Transacción</label>
                    <input type="text" id="num_transaccion" name="numtransaccion" class="form-control input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnGuardarAmortizacion" type="button" class="btn btn-primary btn-sm">Guardar</button>
            </div>
        </div>
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
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalle de Venta -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 950px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de la Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Propietario:</strong> <label for="propietario"></label></p>
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
                    <table id="tabla-detalle-productos-modal" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Productos</th>
                                <th>Cantidad</th>
                                <th>Precio UNT</th>
                                <th>Descuento UNT</th>
                                <th>T. producto</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <hr>
                    <h6>Servicios asociados</h6>
                    <table class="table table-striped table-bordered" id="tabla-detalle-servicios-modal">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tipo Servicio</th>
                                <th>Servicio</th>
                                <th>Mecánico</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aquí se llenarán con JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
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
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEliminarVenta" class="btn btn-danger btn-sm">Eliminar</button>
            </div>
        </div>
    </div>
</div>
</body>

</html>