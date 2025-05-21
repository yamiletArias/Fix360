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
                <button button type="button" data-modo="semana" class="btn btn-primary text-white">Semana</button>
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
    let mostrandoComprasEliminadas = false;
    const btnVerEliminados = document.getElementById("btnVerEliminados");
    const contActivos = document.getElementById("tableDia");
    const contEliminados = document.getElementById("tableEliminados");

    btnVerEliminados.addEventListener("click", () => {
        if (!mostrandoComprasEliminadas) {
            // Mostrar eliminadas
            contActivos.style.display = "none";
            contEliminados.style.display = "block";
            cargarComprasEliminadas();
            btnVerEliminados.innerHTML = `<i class="fa-solid fa-arrow-left"></i>`;
            btnVerEliminados.title = "Volver a compras activas";
        } else {
            // Volver a activas
            contEliminados.style.display = "none";
            contActivos.style.display = "block";
            // No recargamos: la tabla activa ya está cargada
            btnVerEliminados.innerHTML = `<i class="fa-solid fa-eye-slash"></i>`;
            btnVerEliminados.title = "Ver eliminados";
        }
        mostrandoComprasEliminadas = !mostrandoComprasEliminadas;
    });
</script>
<script>
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
      <button class="btn btn-primary btn-sm btn-detalle"
              data-id="${row.id}"
              data-bs-toggle="modal"
              data-bs-target="#miModal">
        <i class="fa-solid fa-circle-info"></i>
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
                        <button class="btn btn-info btn-sm btn-ver-justificacion"
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
                        <button class="btn btn-primary btn-sm btn-detalle-elim"
                                data-id="${row.id}"
                                data-bs-toggle="modal"
                                data-bs-target="#miModal">
                        <i class="fa-solid fa-circle-info"></i>
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
        modal.find('.amortizaciones-container').remove();

        // Mostrar modal
        modal.modal('show');

        // Petición AJAX para detalle de compra
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
                        <td>${parseFloat(item.precio).toFixed(2)}</td>
                        <td>${parseFloat(item.descuento).toFixed(2)} $</td>
                        <td>${parseFloat(item.total_producto).toFixed(2)} $</td>
                    </tr>
                    `);
                });

                // Cargar amortizaciones si existen
                fetch(`<?= SERVERURL ?>app/controllers/Amortizacion.controller.php?action=list&idcompra=${idcompra}`)
                    .then(r => r.json())
                    .then(json => {
                        if (json.status === 'success' && Array.isArray(json.data) && json.data.length) {
                            const cont = $(
                                `<div class="amortizaciones-container mt-4">
                <h6>Amortizaciones</h6>
                <table class="table table-sm">
                  <thead><tr>
                    <th>#</th><th>Transacción</th><th>Monto</th><th>F. Pago</th><th>Saldo</th>
                  </tr></thead>
                  <tbody></tbody>
                </table>
              </div>`
                            );
                            const bodyAmp = cont.find('tbody');
                            json.data.forEach((a, i) => {
                                bodyAmp.append(`
                <tr>
                  <td>${i + 1}</td>
                  <td>${new Date(a.creado).toLocaleString()}</td>
                  <td>${parseFloat(a.amortizacion).toFixed(2)}</td>
                  <td>${a.formapago}</td>
                  <td>${parseFloat(a.saldo).toFixed(2)}</td>
                </tr>
              `);
                            });
                            modal.find('.modal-body').append(cont);
                        }
                    })
                    .catch(err => console.error("Error amortizaciones:", err));
            },
            error() {
                alert("Ocurrió un error al cargar el detalle.");
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
            </div>
            <div class="modal-footer">
                <button id="btnGuardarAmortizacion" type="button" class="btn btn-success">Guardar</button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEliminarCompra" class="btn btn-danger">Eliminar Compra</button>
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
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

</body>

</html>