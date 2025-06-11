<?php
const NAMEVIEW = "compras";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<style>
    #am_formapago_com {
        color: black;
        /* Cambia solo el color de la letra */
    }
</style>
<div class="container-main mt-5">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" data-modo="dia" class="btn btn-primary text-white">Día</button>
                <button button type="button" data-modo="semana" class="btn btn-primary text-white">Semana</button>
                <button type="button" data-modo="mes" class="btn btn-primary text-white">Mes</button>
                <!-- Nuevo botón para ver eliminados -->
                <!-- <button id="btnVerEliminados" type="button" class="btn btn-secondary text-white">
                    <i class="fa-solid fa-eye-slash"></i>
                </button> -->
                <button id="btnVerEliminados" type="button" class="btn btn-secondary text-white" title="Ver eliminados"
                    data-estado="A">
                    <i class="fa-solid fa-eye-slash"></i>
                </button>
                <!-- <button type="button" class="btn btn-danger text-white">
                    <i class="fa-solid fa-file-pdf"></i>
                </button> -->
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="input-group">
                        <input type="date" class="form-control input" aria-label="Fecha"
                            aria-describedby="button-addon2" id="Fecha">
                        <a href="registrar-compras.php" class="btn btn-success text-center" type="button"
                            id="button-addon2">Registrar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="tableDia" class="col-12">
            <table class="table table-striped display" id="tablacomprasdia">
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-left">Proveedor</th>
                        <th class="text-center">T. Comprobante</th>
                        <th class="text-center">N° Comprobante</th>
                        <th class="text-center">N° Serie</th>
                        <th class="text-center">Opciones</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <!-- Los datos se agrega dinamicamente -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Agregar aquí la tabla para las compras eliminadas, inicialmente oculta -->
    <div id="tableEliminados" class="col-12" style="display: none;">
        <table class="table table-striped display" id="tablacompraseliminadas">
            <thead>
                <tr>
                    <th>#</th>
                    <th class="text-left">Proveedor</th>
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
<!-- FIN COMPRAS -->
<?php
require_once "../../partials/_footer.php";
?>
<script>
    // ——— Toggle Activos / Eliminados ———
    let currentEstado = 'A'; // 'A' = activos, 'E' = eliminadas
    const btnVerEliminados = document.getElementById("btnVerEliminados");
    const contActivos = document.getElementById("tableDia");
    const contEliminados = document.getElementById("tableEliminados");

    // Actualiza clase, icono y título según currentEstado
    const actualizarToggleEstado = () => {
        if (currentEstado === 'A') {
            btnVerEliminados.classList.replace('btn-warning', 'btn-secondary');
            btnVerEliminados.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
            btnVerEliminados.title = 'Ver eliminados';
        } else {
            btnVerEliminados.classList.replace('btn-secondary', 'btn-warning');
            btnVerEliminados.innerHTML = '<i class="fa-solid fa-eye"></i>';
            btnVerEliminados.title = 'Ver compras activas';
        }
        btnVerEliminados.setAttribute('data-estado', currentEstado);
    };

    // Inicializa el estado visual al cargar la página
    actualizarToggleEstado();

    btnVerEliminados.addEventListener("click", () => {
        if (currentEstado === 'A') {
            // Cambiamos a eliminadas
            contActivos.style.display = "none";
            contEliminados.style.display = "block";
            cargarComprasEliminadas();
            currentEstado = 'E';
        } else {
            // Volvemos a activas
            contEliminados.style.display = "none";
            contActivos.style.display = "block";
            currentEstado = 'A';
        }
        actualizarToggleEstado();
    });
</script>
<script>
    function toggleNumTransAmort() {
        // Obtenemos el texto de la opción seleccionada, en minúsculas
        const texto = $('#am_formapago_com option:selected').text().trim().toLowerCase();
        if (texto === 'efectivo') {
            // Si es efectivo, ocultar y limpiar
            $('#div_num_transaccion_com').hide()
                .find('input').val('');
        } else {
            // Si no es efectivo, mostrar
            $('#div_num_transaccion_com').show();
        }
    }
    $(document).on('click', '.btn-amortizar', async function () {
        const id = $(this).data('id');
        const monto = parseFloat($(this).data('total')) || 0;

        // ── 1) Primero, obtenemos total_venta desde el API ──
        let totalPendiente = 0;
        try {
            const resTotal = await fetch(`<?= SERVERURL ?>app/controllers/Amortizacion.controller.php?idcompra=${id}`);
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
        $('#am_idcompra').val(id);
        $('#am_monto_com').val(totalPendiente.toFixed(2));

        // ── resto de tu código intacto ──
        $('#modalAmortizar .resumen-amort').remove();
        $('#modalAmortizar .modal-body').prepend(
            '<div class="resumen-amort mb-3"><p class="small text-muted">Cargando resumen…</p></div>'
        );

        // carga formas de pago...
        const $sel = $('#am_formapago_com').prop('disabled', true).html('<option>Cargando…</option>');
        try {
            const resp = await fetch('<?= SERVERURL ?>app/controllers/FormaPagos.controller.php');
            const js = await resp.json();
            if (js.status === 'success') {
                $sel.empty();
                js.data.forEach(fp => {
                    $sel.append(`<option value="${fp.idformapago}">${fp.formapago}</option>`);
                });
                // Seleccionar por defecto 'Efectivo' si existe
                $sel.find('option').filter((i, opt) => opt.text.toLowerCase() === 'efectivo').prop('selected', true);
            } else {
                $sel.html('<option>Error</option>');
            }
        } catch {
            $sel.html('<option>Error</option>');
        } finally {
            $sel.prop('disabled', false);
            // Registrar listener y ejecutar inmediatamente para ajustar visibilidad
            $sel.off('change', toggleNumTransAmort).on('change', toggleNumTransAmort);
            toggleNumTransAmort();
        }

        // carga amortizaciones previas...
        try {
            const res2 = await fetch(`<?= SERVERURL ?>app/controllers/Amortizacion.controller.php?idcompra=${id}`);
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
</script>
<script>
    let tablaCompras;
    const API = "<?= SERVERURL ?>app/controllers/Compra.controller.php";
    const fechaInput = document.getElementById('Fecha');
    const btnSemana = document.querySelector('button[data-modo="semana"]');
    const btnMes = document.querySelector('button[data-modo="mes"]');
    const filtros = [btnSemana, btnMes];
    let currentModo = 'dia';

    function marcarActivo(btn) {
        filtros.forEach(b => b.classList.toggle('active', b === btn));
    }

    // Render de botones en cada fila (activa)
    function renderOpciones(data, type, row) {
        const pagado = row.estado_pago === 'pagado';
        const btnAmort = pagado
            ? `<button class="btn btn-success btn-sm" disabled title="Pago completo">
           <i class="fa-solid fa-check"></i>
         </button>`
            : `<button class="btn btn-warning btn-sm btn-amortizar"
           data-id="${row.id}"
           data-bs-toggle="modal"
           data-bs-target="#modalAmortizar">
           <i class="fa-solid fa-dollar-sign"></i>
         </button>`;

        return `
      <button class="btn btn-danger btn-sm btn-eliminar" data-id="${row.id}">
        <i class="fa-solid fa-trash"></i>
      </button>
      ${btnAmort}
      <button class="btn btn-info btn-sm btn-detalle"
              data-id="${row.id}"
              data-bs-toggle="modal"
              data-bs-target="#miModal">
        <i class='fa-solid fa-clipboard-list'></i>
      </button>`;
    }

    // Carga la tabla principal: día / semana / mes
    function cargarTablaCompras(modo, fecha) {
        if (tablaCompras) {
            tablaCompras.destroy();
            $("#tablacomprasdia tbody").empty();
        }
        tablaCompras = $("#tablacomprasdia").DataTable({
            ajax: {
                url: API,
                data: { modo, fecha },
                dataSrc: "data"
            },
            columns: [
                { data: null, render: (d, t, r, m) => m.row + 1 },
                { data: "proveedor", defaultContent: "—", class: "text-start" },
                { data: "tipocom", defaultContent: "—", class: "text-center" },
                { data: "numcom", defaultContent: "—", class: "text-center" },
                { data: "numserie", defaultContent: "—", class: "text-center" },
                { data: null, class: "text-center", render: renderOpciones }
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

    // Carga la tabla de compras eliminadas
    function cargarComprasEliminadas() {
        if ($.fn.DataTable.isDataTable("#tablacompraseliminadas")) {
            $("#tablacompraseliminadas").DataTable().destroy();
            $("#tablacompraseliminadas tbody").empty();
        }
        $("#tablacompraseliminadas").DataTable({
            ajax: {
                url: API + "?action=compras_eliminadas",
                dataSrc(json) {
                    console.log("compras_eliminadas:", json);
                    return json.status === 'success' ? json.data : [];
                }
            },
            columns: [
                { data: null, render: (d, t, r, m) => m.row + 1 },
                { data: "proveedor", class: "text-start", defaultContent: "—" },
                { data: "tipocom", class: "text-center", defaultContent: "—" },
                { data: "numcom", class: "text-center", defaultContent: "—" },
                {
                    data: null,
                    class: "text-center",
                    render: (d, t, row) => `
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
                        <button class="btn btn-info btn-sm btn-detalle"
                                data-id="${row.id}"
                                data-bs-toggle="modal"
                                data-bs-target="#miModal">
                            <i class='fa-solid fa-clipboard-list'></i>
                        </button>`
                }
            ],
            language: {
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "No hay datos disponibles en la tabla"
            }
        });
    }

    function verDetalleCompra(idcompra) {
    const modal = $('#miModal');
    const tbody = modal.find('tbody');

    // Limpiar contenido previo
    modal.find('#proveedor, #fechaCompra').val('');
    tbody.empty();
    modal.find('.amortizaciones-container, .totales-container').remove();

    // Mostrar modal
    modal.modal('show');

    // 1) Petición AJAX para detalle de compra
    $.ajax({
        url: "<?= SERVERURL ?>app/controllers/Detcompra.controller.php",
        method: "GET",
        data: { idcompra },
        dataType: "json",
        success(response) {
            if (!Array.isArray(response) || response.length === 0) {
                tbody.append(
                    `<tr><td colspan="6" class="text-center">No hay detalles disponibles</td></tr>`
                );
                return;
            }

            // Rellenar encabezados: proveedor y fecha
            modal.find('#proveedor').val(response[0].proveedor);
            modal.find('#fechaCompra').val(response[0].fechacompra);

            // Agregar filas de productos
            response.forEach((item, i) => {
                tbody.append(`
                    <tr>
                        <td>${i + 1}</td>
                        <td>${item.producto}</td>
                        <td>${item.cantidad}</td>
                        <td>${parseFloat(item.precio).toFixed(2)} $</td>
                        <td>${parseFloat(item.descuento).toFixed(2)} $</td>
                        <td>${parseFloat(item.total_producto).toFixed(2)} $</td>
                    </tr>
                `);
            });

            // 2) Cargar amortizaciones y totales
            fetch(`<?= SERVERURL ?>app/controllers/Amortizacion.controller.php?idcompra=${idcompra}`)
                .then(r => r.json())
                .then(json => {
                    if (json.status !== 'success') return;

                    const amort = json.data;                            // array de amortizaciones
                    const totalCompra     = json.total_original;        // total original
                    const amortizado      = json.total_pagado;          // ya pagado
                    const saldoPendiente  = json.total_pendiente;       // queda por pagar

                    // Sólo si hay amortizaciones, agregamos la tabla
                    if (amort.length) {
                        let html = `
                        <div class="amortizaciones-container mt-4">
                            <h6>Amortizaciones</h6>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>Nº Transacción</th>
                                        <th>Monto</th>
                                        <th>Forma de Pago</th>
                                        <th>Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                        amort.forEach((a, i) => {
                            const dt = new Date(a.creado);
                            const fecha = dt.toLocaleDateString('es-PE', {
                                day: '2-digit', month: '2-digit', year: 'numeric'
                            });
                            const hora = dt.toLocaleTimeString('es-PE', {
                                hour: '2-digit', minute: '2-digit'
                            });
                            html += `
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${fecha} ${hora}</td>
                                    <td>${a.numtransaccion}</td>
                                    <td>S/ ${parseFloat(a.amortizacion).toFixed(2)}</td>
                                    <td>${a.formapago}</td>
                                    <td>S/ ${parseFloat(a.saldo).toFixed(2)}</td>
                                </tr>`;
                        });

                        html += `
                                </tbody>
                            </table>
                        </div>`;

                        modal.find('.modal-body').append(html);
                    }

                    // 3) Agregar bloque de totales siempre
                    const totalesHtml = `
                        <div class="totales-container text-end pe-3 mt-3">
                            <p><strong>Total Compra:</strong> S/ ${parseFloat(totalCompra).toFixed(2)}</p>
                            <p><strong>Amortizado:</strong> S/ ${parseFloat(amortizado).toFixed(2)}</p>
                            <p><strong>Saldo Pendiente:</strong> S/ ${parseFloat(saldoPendiente).toFixed(2)}</p>
                        </div>
                    `;
                    modal.find('.modal-body').append(totalesHtml);
                })
                .catch(err => {
                    console.error("Error amortizaciones:", err);
                });
        },
        error() {
            alert("Ocurrió un error al cargar el detalle de compra.");
        }
    });
}

    $(document).ready(function () {
        // Inicializar fecha de hoy y tabla
        const hoy = new Date().toISOString().slice(0, 10);
        fechaInput.value = hoy;
        marcarActivo(btnSemana); // enfatizamos “día” como activo; si falta botón “día”, ignora
        cargarTablaCompras(currentModo, hoy);

        // Filtros semana/mes
        filtros.forEach(btn => btn.addEventListener("click", () => {
            currentModo = btn.dataset.modo;
            marcarActivo(btn);
            cargarTablaCompras(currentModo, fechaInput.value);
        }));
        fechaInput.addEventListener("change", () => {
            currentModo = 'dia';
            marcarActivo(btnSemana);
            cargarTablaCompras(currentModo, fechaInput.value);
        });

        // Ver eliminados
        /* $("#btnVerEliminados").on("click", () => {
            $("#tableDia").hide();
            $("#tableEliminados").show();
            cargarComprasEliminadas();
        }); */

        // Delegación de eventos
        $(document).on('click', '.btn-eliminar', function () {
            const idc = $(this).data('id');
            $('#justificacion').val('');
            $('#btnEliminarCompra').data('id', idc);
            $('#modalJustificacion').modal('show');
        });
        $(document).on('click', '#btnEliminarCompra', async function () {
            const just = $('#justificacion').val().trim();
            const idc = $(this).data('id');
            if (!just) return alert('Escribe la justificación.');
            if (!await ask('¿Confirmar eliminación?', 'Eliminar')) return;
            $.post(API, { action: 'eliminar', idcompra: idc, justificacion: just }, res => {
                if (res.status === 'success') {
                    showToast('Compra eliminada', 'SUCCESS', 1500);
                    $('#modalJustificacion').modal('hide');
                    cargarTablaCompras(currentModo, fechaInput.value);
                } else {
                    showToast(res.message || 'Error', 'ERROR', 1500);
                }
            }, 'json');
        });

        // Justificación en eliminados
        $(document).on('click', '.btn-ver-justificacion', async function () {
            const id = $(this).data('id');
            $('#contenidoJustificacion').text('Cargando…');
            try {
                const res = await fetch(`${API}?action=justificacion&idcompra=${id}`);
                const json = await res.json();
                $('#contenidoJustificacion')
                    .text(json.status === 'success' ? json.justificacion : 'No hay justificación');
            } catch {
                $('#contenidoJustificacion').text('Error al cargar justificación');
            }
            $('#modalVerJustificacion').modal('show');
        });

        // Ver detalle activa
        $(document).on('click', '.btn-detalle', function () {
            const idcompra = $(this).data('id');
            verDetalleCompra(idcompra);
        });
        // Ver detalle eliminados
        $(document).on('click', '.btn-detalle-elim', function () {
            const idcompra = $(this).data('id');
            verDetalleCompra(idcompra);
        });

        // guardar amortización
        $(document).on('click', '#btnGuardarAmortizacion', async function () {
            const idcompra = +$('#am_idcompra').val();
            const monto = parseFloat($('#am_monto_com').val());
            const formapago = +$('#am_formapago_com').val();
            if (!monto || monto <= 0) return alert('Monto inválido');

            const form = new FormData();
            form.append('idcompra', idcompra);
            form.append('monto', monto);
            form.append('idformapago', formapago);

            const $numField = $('#num_transaccion_com');
            // Sólo si está visible y no vacío, lo agregamos
            if ($numField.is(':visible') && $numField.val().trim() !== '') {
                form.append('numtransaccion', $numField.val().trim());
            }

            const res = await fetch("<?= SERVERURL ?>app/controllers/Amortizacion.controller.php", {
                method: 'POST', body: form
            }).then(r => r.json());
            if (res.status === 'success') {
                showToast(res.message, 'SUCCESS', 1500);
                $('#modalAmortizar').modal('hide');
                cargarTablaCompras(currentModo, fechaInput.value);
                verDetalleCompra(idcompra);
            } else {
                showToast(res.detail || res.message, 'ERROR', 2000);
            }
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
                <input type="hidden" id="am_idcompra" value="1"> <!-- Aquí asegúrate de setear el id de la venta -->
                <div class="mb-3">
                    <label>Monto</label>
                    <input type="number" id="am_monto_com" class="form-control input" step="0.01">
                </div>
                <div class="mb-3">
                    <label>Forma de pago</label>
                    <select id="am_formapago_com" class="form-select">
                        <!-- Aquí puedes cargar las formas de pago si tienes disponibles -->
                    </select>
                </div>
                <!-- Oculto por defecto -->
                <div class="mb-3" id="div_num_transaccion_com" style="display: none;">
                    <label>Numero de Transacción</label>
                    <input type="text" id="num_transaccion_com" name="numtransaccion" class="form-control input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnGuardarAmortizacion" type="button" class="btn btn-primary btn-sm">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para la vista de la justificacion -->
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
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEliminarCompra" class="btn btn-danger btn-sm">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalle de Compras -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 950px;" style="margin-top: 20px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de la Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" disabled class="form-control input" id="proveedor"
                                placeholder="Proveedor">
                            <label for="proveedor">Proveedor: </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" disabled class="form-control input" id="fechaCompra"
                                placeholder="Fecha & Hora">
                            <label for="fechaCompra">Fecha & Hora: </label>
                        </div>
                    </div>
                </div>
                <div class="table-container">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Productos</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Descuento</th>
                                <th>T. producto</th>
                            </tr>
                        </thead>
                        <tbody>

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

</body>

</html>