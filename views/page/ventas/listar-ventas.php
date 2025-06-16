<?php
const NAMEVIEW = "Ventas";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<style>
    #comboTipocom {
        color: black;
    }

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
    <!-- filtros generales (día/semana/mes + fecha + registrar + ver eliminados) -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div class="btn-group" role="group" aria-label="Periodo">
                <button type="button" data-modo="dia" class="btn btn-primary text-white active">Día</button>
                <button type="button" data-modo="semana" class="btn btn-primary text-white">Semana</button>
                <button type="button" data-modo="mes" class="btn btn-primary text-white">Mes</button>
                <button id="btnVerEliminados" type="button" class="btn btn-secondary text-white" title="Ver eliminados"
                    data-estado="A">
                    <i class="fa-solid fa-eye-slash"></i>
                </button>
                <!-- <button type="button" class="btn btn-danger text-white">
                    <i class="fa-solid fa-file-pdf"></i>
                </button> -->
            </div>

            <div class="input-group" style="max-width: 300px;">
                <input type="date" id="Fecha" class="form-control input" value="<?= date('Y-m-d') ?>">
                <a href="registrar-ventas-orden.php" class="btn btn-success" id="button-addon2">Registrar</a>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="ventasTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-ventas" data-bs-toggle="tab" data-bs-target="#pane-ventas"
                type="button" role="tab">
                Ventas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-ot" data-bs-toggle="tab" data-bs-target="#pane-ot" type="button"
                role="tab">
                orden de trabajo
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="pane-ventas" role="tabpanel">
            <div id="tableDia">
                <table class="table table-striped display w-100" id="tablaventasdia">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Propietario</th>
                            <th>Cliente</th>
                            <th class="text-center">T. Comprobante</th>
                            <th class="text-center">N° Comprobante</th>
                            <th class="text-center">Opciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div id="tableEliminados" style="display:none;">
                <table class="table table-striped display w-100" id="tablaventaseliminadas">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th class="text-center">T. Comprobante</th>
                            <th class="text-center">N° Comprobante</th>
                            <th class="text-center">Opciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>


        <!-- === PESTAÑA OT (orden de trabajo) === -->
        <div class="tab-pane fade" id="pane-ot" role="tabpanel">
            <button id="btnCombinarOT" class="btn btn-primary mb-2" disabled>
                <i class="fa-solid fa-compress-arrows-alt"></i> Combinar OT
            </button>
            <table class="table table-striped display w-100" id="tabla_ot">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Propietario</th>
                        <th>Cliente</th>
                        <th class="text-center">T. Comprobante</th>
                        <!-- <th class="text-center">F. Hora</th> -->
                        <th class="text-center">N° Serie</th>
                        <th>Combinar</th>
                        <th class="text-center">Opciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div><!-- /.tab-content -->
</div><!-- /.container-main -->
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
        console.log("verDetalleVenta se está ejecutando para idventa =", idventa);

        // — Limpiar modal —
        $("#miModal tbody, #tabla-detalle-productos-modal tbody, #tabla-detalle-servicios-modal tbody").empty();
        $("#miModal .amortizaciones-container").remove();
        $("#modeloInput, #fechaHora, #vehiculo, #kilometraje").val('');
        $("label[for='propietario']").text('');

        // — Abrir modal —
        $("#miModal").modal("show");

        // 1) Propietario
        fetch(`<?= SERVERURL ?>app/controllers/Venta.controller.php?action=propietario&idventa=${idventa}`)
            .then(r => r.json())
            .then(jsonVenta => {
                $("label[for='propietario']").text(
                    jsonVenta.status === 'success'
                        ? (jsonVenta.data.propietario || 'Sin propietario')
                        : 'No encontrado'
                );
            })
            .catch(() => {
                $("label[for='propietario']").text('Error al cargar');
            });

        // 2) Detalle completo (productos + servicios)
        fetch(`<?= SERVERURL ?>app/controllers/Detventa.controller.php?idventa=${idventa}`)
            .then(r => r.json())
            .then(json => {
                if (json.status !== 'success') {
                    console.error("Detventa error:", json.message);
                    return;
                }
                const { productos, servicios } = json.data;

                // — Extraer datos generales del primer elemento disponible —
                let header = {};
                if (productos.length) {
                    header = productos[0];
                } else if (servicios.length) {
                    header = servicios[0];
                }
                // Rellenar siempre los campos generales
                $("#modeloInput").val(header.cliente ?? 'Sin Cliente');
                $("#fechaHora").val(header.fechahora ?? '');
                $("#vehiculo").val(header.vehiculo ?? 'Sin vehículo');
                $("#kilometraje").val(header.kilometraje ?? 'Sin kilometraje');

                // — Productos —
                const $prodBody = $("#tabla-detalle-productos-modal tbody").empty();
                if (!productos.length) {
                    $prodBody.append(`
                    <tr>
                        <td colspan="6" class="text-center text-muted">No hay productos</td>
                    </tr>
                `);
                } else {
                    productos.forEach((p, i) => {
                        $prodBody.append(`
                        <tr>
                            <td>${i + 1}</td>
                            <td>${p.producto}</td>
                            <td>${p.cantidad}</td>
                            <td>S/ ${parseFloat(p.precio).toFixed(2)}</td>
                            <td>S/ ${parseFloat(p.descuento).toFixed(2)}</td>
                            <td>S/ ${parseFloat(p.total_producto).toFixed(2)}</td>
                        </tr>
                    `);
                    });
                }

                // — Servicios —
                const serviciosValidos = servicios.filter(s =>
                    s.tiposervicio || s.nombreservicio || s.mecanico || s.precio_servicio
                );
                const $servBody = $("#tabla-detalle-servicios-modal tbody").empty();
                if (!serviciosValidos.length) {
                    $servBody.append(`
                    <tr>
                        <td colspan="5" class="text-center text-muted">No hay servicios</td>
                    </tr>
                `);
                } else {
                    serviciosValidos.forEach((s, i) => {
                        $servBody.append(`
                        <tr>
                            <td>${i + 1}</td>
                            <td>${s.tiposervicio ?? '-'}</td>
                            <td>${s.nombreservicio ?? '-'}</td>
                            <td>${s.mecanico ?? '-'}</td>
                            <td>${s.precio_servicio != null
                                ? parseFloat(s.precio_servicio).toFixed(2) + ' S/'
                                : '-'
                            }</td>
                        </tr>
                    `);
                    });
                }

                // — Amortizaciones —
                return fetch(`<?= SERVERURL ?>app/controllers/Amortizacion.controller.php?idventa=${idventa}`)
            })
            .then(r => r && r.json())
            .then(jsonA => {
                if (!jsonA || jsonA.status !== 'success') return;
                const amort = jsonA.data;
                const totalVenta = jsonA.total_original;
                const amortizado = jsonA.total_pagado;
                const saldoPendiente = jsonA.total_pendiente;

                if (amort.length > 0) {
                    let html = `
    <div class="amortizaciones-container mt-4">
        <h6>Amortizaciones</h6>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th><th>Fecha</th><th>Nº Transacción</th>
                    <th>Monto</th><th>Forma de Pago</th>
                </tr>
            </thead>
            <tbody>`;
                    amort.forEach((a, i) => {
                        const dt = new Date(a.creado);
                        const fecha = dt.toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' });
                        const hora = dt.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });
                        html += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${fecha} ${hora}</td>
                    <td>${a.numtransaccion}</td>
                    <td>S/ ${parseFloat(a.amortizacion).toFixed(2)}</td>
                    <td>${a.formapago}</td>
                </tr>`;
                    });
                    html += `
            </tbody>
        </table>
        <div class="text-end pe-3 mt-3">
            <p><strong>Total Venta</strong> S/ ${totalVenta.toFixed(2)}</p>
            <p><strong>Amortizado</strong> S/ ${amortizado.toFixed(2)}</p>
            <p><strong>Saldo Pendiente</strong> S/ ${saldoPendiente.toFixed(2)}</p>
        </div>
    </div>`;
                    $("#miModal .modal-body").append(html);
                }
            })
            .catch(err => {
                console.error("Error al cargar detalle o amortizaciones:", err);
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
    let tablaOT;

    // — 2) función para cargar OT según periodo/fecha —
    function cargarTablaOT(modo, fecha) {
        if ($.fn.DataTable.isDataTable("#tabla_ot")) {
            tablaOT.destroy();
            $("#tabla_ot tbody").empty();
        }

        tablaOT = $("#tabla_ot").DataTable({
            ajax: {
                url: "<?= SERVERURL ?>app/controllers/Venta.controller.php",
                data: {
                    action: "ot_por_periodo",
                    modo: modo,
                    fecha: fecha
                },
                dataSrc: "data"
            },
            columns: [
                { data: null, render: (_, __, ___, meta) => meta.row + 1 },   // #
                { data: "propietario", className: "text-start", defaultContent: "Sin propietario" },
                { data: "cliente", className: "text-start", defaultContent: "Sin cliente" },        // Cliente
                { data: "tipocom", className: "text-center" },        // F. Hora
                { data: "numserie", className: "text-center" },        // N° Serie
                {
                    data: null,
                    orderable: false,
                    className: "select-checkbox text-center",
                    render: (row, type, set, meta) =>
                        `<input type="checkbox" class="select-ot" data-id="${row.id}" data-prop="${row.idpropietario}">`
                },
                {
                    data: null,
                    className: "text-center",
                    render: renderOpciones2
                }
            ],
            language: {
                emptyTable: "No hay OT en este periodo"
            }
        });
    }

    function renderOpciones2(data, type, row) {
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
        <button class="btn btn-sm btn-info"
                onclick="verDetalleVenta(${row.id});"
                title="Detalle OT">
            <i class="fa-solid fa-clipboard-list"></i>
        </button>
        <button title="Pdf" class="btn btn-outline-dark btn-sm btn-descargar-pdf"
                onclick="descargarPDF('${row.id}')">
        <i class="fa-solid fa-file-pdf"></i>
        </button>`;
    }
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
                    data: "propietario",
                    defaultContent: "Sin cliente",
                    class: 'text-start'
                },
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
                            data-id="${row.idventa}"
                            data-bs-toggle="modal"
                            data-bs-target="#modalVerJustificacion">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-sm btn-amortizar"
                            data-id="${row.idventa}"
                            data-bs-toggle="modal"
                            data-bs-target="#modalAmortizar">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </button>
                    <button title="Detalle de la venta" class="btn btn-info btn-sm btn-detalle"
                            data-id="${row.idventa}"
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
         data-id="${row.id}"
         data-bs-toggle="modal" data-bs-target="#modalAmortizar">
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
        cargarTablaOT(currentModo, hoy);
        const comboTipocom = document.getElementById("comboTipocom");
        if (comboTipocom) {
            comboTipocom.addEventListener("change", inicializarCombinarOT);
        }

        // B) Cada vez que se muestre el modal (evento Bootstrap 'show.bs.modal')
        const modalCombinar = document.getElementById("modalCombinarOT");
        if (modalCombinar) {
            modalCombinar.addEventListener("show.bs.modal", () => {
                inicializarCombinarOT();
            });
        }

        // ¡También puedes invocar inmediatamente para tener valores iniciales!
        if (comboTipocom) {
            inicializarCombinarOT();
        }

        filtros.forEach(btn => {
            btn.addEventListener('click', () => {
                currentModo = btn.dataset.modo;
                marcarActivo(btn);
                cargarTablaVentas(currentModo, fechaInput.value);
                cargarTablaOT(currentModo, fechaInput.value);
            });
        });

        fechaInput.addEventListener('change', () => {
            currentModo = 'dia';
            marcarActivo(btnDia);
            cargarTablaVentas(currentModo, fechaInput.value);
            cargarTablaOT(currentModo, fechaInput.value);
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
        const $btnCombinar = $('#btnCombinarOT'),
            $tablaOT = $('#tabla_ot');

        // cada vez que (re)señalas un checkbox:
        $(document).on('change', '.select-ot', () => {
            const seleccionadas = $('.select-ot:checked');
            if (seleccionadas.length < 2) {
                // al menos 2 para combinar
                $btnCombinar.prop('disabled', true);
            } else {
                // extrae todos los propietarios
                const props = seleccionadas.map((i, el) => $(el).data('prop')).get();
                const todosIguales = props.every(p => p === props[0]);
                $btnCombinar.prop('disabled', !todosIguales);
            }
        });
        // Mostrar modal
        $btnCombinar.on('click', () => {
            const count = $('.select-ot:checked').length;
            $('#countOT').text(count);
            new bootstrap.Modal($('#modalCombinarOT')).show();
        });

        // Al enviar formulario
        $('#formCombinarOT').on('submit', async function (e) {
            e.preventDefault();

            // 1) Construimos el array de OT seleccionadas
            const idsOT = $('.select-ot:checked').map((i, el) => $(el).data('id')).get();
            const tipocom = $('#comboTipocom').val();

            // 2) Obtenemos la serie y el número generados
            const numserie = $('#numSerieCombinar').val();
            const numcom = $('#numComCombinar').val();

            // 3) Enviamos todo en el payload
            const res = await fetch(`<?= SERVERURL ?>app/controllers/Venta.controller.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'combinar_ot',
                    ids_ot: idsOT,
                    tipocom: tipocom,
                    numserie: numserie,
                    numcom: numcom
                })
            });

            const js = await res.json();
            if (js.status === 'success') {
                showToast('Venta creada con éxito (ID: ' + js.idventa + ')', 'SUCCESS');

                // 1) Recargar tablas (si quieres)
                cargarTablaOT(currentModo, fechaInput.value);
                cargarTablaVentas(currentModo, fechaInput.value);

                // 2) Actualizar filas seleccionadas “al vuelo” en la DataTable de Ventas
                const dt = tablaVentas;
                $('.select-ot:checked').each(function () {
                    const $tr = $(this).closest('tr');
                    const row = dt.row($tr);
                    const data = row.data();
                    data.tipocom = tipocom;
                    data.numserie = numserie;
                    data.numcom = numcom;
                    row.data(data).invalidate();
                });
                dt.draw(false);

                $('#modalCombinarOT').modal('hide');
            } else {
                showToast(js.message || 'Error al combinar OT', 'ERROR');
            }
        });

        // guardar amortización
        $(document).on('click', '#btnGuardarAmortizacion', async function () {
            const idventa = +$('#am_idventa').val();
            const monto = parseFloat($('#am_monto').val());
            const formapago = +$('#am_formapago').val();
            if (!monto || monto <= 0) return alert('Monto inválido');

            const form = new FormData();
            form.append('tipo', 'venta');
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
    // --- Funciones para generar Serie y Número de Comprobante ---
    function generateNumber(prefix) {
        // Tres dígitos aleatorios entre 000 y 099
        return `${prefix}${String(Math.floor(Math.random() * 100)).padStart(3, "0")}`;
    }

    function generateComprobanteNumber(prefix) {
        // Siete dígitos aleatorios entre 0000000 y 9999999
        return `${prefix}-${String(Math.floor(Math.random() * 1e7)).padStart(7, "0")}`;
    }

    // --- Función para inicializar (o regenerar) Serie y Número en el modal ---
    function inicializarCombinarOT() {
        const comboTipocom = document.getElementById("comboTipocom");
        const tipo = comboTipocom.value; // 'boleta' o 'factura'

        let prefijoSerie = "";
        let prefijoComprobante = "";

        switch (tipo) {
            case "factura":
                prefijoSerie = "F";
                prefijoComprobante = "F";
                break;
            case "boleta":
                prefijoSerie = "B";
                prefijoComprobante = "B";
                break;
            default:
                prefijoSerie = "";
                prefijoComprobante = "";
                break;
        }

        const numSerieInput = document.getElementById("numSerieCombinar");
        const numComInput = document.getElementById("numComCombinar");

        numSerieInput.value = generateNumber(prefijoSerie);
        numComInput.value = generateComprobanteNumber(prefijoComprobante);
    }
</script>

<!-- Modal Combinar OT -->
<div class="modal fade" id="modalCombinarOT" tabindex="-1">
    <div class="modal-dialog">
        <form id="formCombinarOT" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Combinar Órdenes de Trabajo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Vas a combinar <span id="countOT"></span> OT para el mismo propietario.</p>

                <div class="mb-3">
                    <label class="form-label">Tipo de comprobante</label>
                    <select id="comboTipocom" class="form-select" required>
                        <option value="boleta">Boleta</option>
                        <option value="factura">Factura</option>
                    </select>
                </div>

                <!-- NUEVOS CAMPOS PARA SERIE Y NÚMERO DE COMPROBANTE -->
                <div class="mb-3">
                    <label class="form-label">Serie</label>
                    <input type="text" id="numSerieCombinar" class="form-control input" readonly
                        placeholder="Se genera automáticamente">
                </div>
                <div class="mb-3">
                    <label class="form-label">Número de Comprobante</label>
                    <input type="text" id="numComCombinar" class="form-control input" readonly
                        placeholder="Se genera automáticamente">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-sm btn-combinar-ot">Confirmar</button>
            </div>
        </form>
    </div>
</div>
<!-- Modal justificacion -->
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

<!-- Modal de Detalle de Venta -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 970px;">
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
                <textarea id="justificacion" class="form-control input" rows="4"
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