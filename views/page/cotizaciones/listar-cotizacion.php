<?php
const NAMEVIEW = "Cotizaciones";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<style>
    #am_formapago {
        color: black;
        /* Cambia solo el color de la letra */
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
                <!-- <button id="btnVerEliminados" type="button" class="btn btn-secondary text-white">
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
                        <th>#</th>
                        <th>Cliente</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">D. Vigencia</th>
                        <th class="text-center">Opciones</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <!-- Aquí se agregan los datos dinámicos -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Agregar aquí la tabla para las cotizaciones eliminadas, inicialmente oculta -->
    <div id="tableEliminados" class="col-12" style="display: none;">
        <table class="table table-striped display" id="tablacotizacioneseliminadas">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">D. Vigencia</th>
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
<!--FIN COTIZACIONES-->
<?php
require_once "../../partials/_footer.php";
?>
<script>
    // ——— Toggle Activos / Eliminados ———
    let currentEstado = 'A'; // 'A' = activos, 'E' = eliminadas
    const btnVerEliminados = document.getElementById("btnVerEliminados");
    const contActivos = document.getElementById("tableDia");
    const contEliminados = document.getElementById("tableEliminados");

    // Actualiza la apariencia del botón según currentEstado
    const actualizarToggleEstado = () => {
        if (currentEstado === 'A') {
            btnVerEliminados.classList.replace('btn-warning', 'btn-secondary');
            btnVerEliminados.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
            btnVerEliminados.title = 'Ver eliminados';
        } else {
            btnVerEliminados.classList.replace('btn-secondary', 'btn-warning');
            btnVerEliminados.innerHTML = '<i class="fa-solid fa-eye"></i>';
            btnVerEliminados.title = 'Ver cotizaciones activas';
        }
        btnVerEliminados.setAttribute('data-estado', currentEstado);
    };

    // Inicializa el estado visual al cargar la página
    actualizarToggleEstado();

    btnVerEliminados.addEventListener("click", () => {
        if (currentEstado === 'A') {
            // Mostrar eliminadas
            contActivos.style.display = "none";
            contEliminados.style.display = "block";
            cargarCotizacionesEliminadas();
            currentEstado = 'E';
        } else {
            // Volver a activas
            contEliminados.style.display = "none";
            contActivos.style.display = "block";
            currentEstado = 'A';
        }
        actualizarToggleEstado();
    });
</script>
<script>
    $(document).on('click', '.btn-ver-justificacion', async function() {
        const id = $(this).data('id');
        console.log('voy a pedir justificación para id=', id);
        $('#contenidoJustificacion').text('Cargando…');
        try {
            const res = await fetch(`<?= SERVERURL ?>app/controllers/Cotizacion.controller.php?action=justificacion&idcotizacion=${id}`);
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

<!-- Vista en el modal de detalle de cotizacion para visualizar informacion de esa cotizacion -->
<script>
    function verDetalleCotizacion(idcotizacion) {
        // 1) Limpiar modal
        const $tbodyProd = $("#miModal table").first().find("tbody").empty();
        const $tbodyServ = $("#tabla-detalle-servicios-modal tbody").empty();
        $("#modeloInput, #fechaHora, #vigencia, #estadoCotizacion").val('');

        // 2) Mostrar modal
        $("#miModal").modal("show");

        // 3) Cabecera (igual que antes)…
        fetch(`<?= SERVERURL ?>app/controllers/Cotizacion.controller.php?action=detalle&idcotizacion=${idcotizacion}`)
            .then(res => res.json())
            .then(json => {
                if (json.status === 'success') {
                    $("#modeloInput").val(json.data.cliente || '—');
                    $("#fechaHora").val(json.data.fechahora || '—');
                    $("#vigencia").val(json.data.vigenciadias || '—');
                    $("#estadoCotizacion").val(json.data.estado ? 'Activa' : 'Anulada');
                } else {
                    $("#modeloInput, #fechaHora, #vigencia, #estadoCotizacion").val('—');
                }
            })
            .catch(() => {
                $("#modeloInput, #fechaHora, #vigencia, #estadoCotizacion").val('Error');
            });

        // 4) Detalle: un solo endpoint que trae productos y servicios
        $.ajax({
            url: "<?= SERVERURL ?>app/controllers/Detcotizacion.controller.php",
            method: "GET",
            data: {
                idcotizacion
            },
            dataType: "json",
            success(response) {
                // filtrar
                const productos = response.filter(r => r.tipo === 'producto');
                const servicios = response.filter(r => r.tipo === 'servicio');

                // 4a) Pinta productos
                if (productos.length === 0) {
                    $tbodyProd.append(`
          <tr><td colspan="6" class="text-center">No hay productos registrados</td></tr>
        `);
                } else {
                    productos.forEach((row, i) => {
                        $tbodyProd.append(`
            <tr>
              <td>${i+1}</td>
              <td>${row.producto}</td>
              <td class="text-center">${row.cantidad}</td>
              <td class="text-center">S/ ${parseFloat(row.precio).toFixed(2)}</td>
              <td class="text-center">${row.descuento}%</td>
              <td class="text-center">S/ ${parseFloat(row.total).toFixed(2)}</td>
            </tr>
          `);
                    });
                }

                // 4b) Pinta servicios
                if (servicios.length === 0) {
                    $tbodyServ.append(`
          <tr><td colspan="4" class="text-center">No hay servicios registrados</td></tr>
        `);
                } else {
                    servicios.forEach((row, i) => {
                        $tbodyServ.append(`
            <tr>
              <td class="text-center">${i+1}</td>
              <td class="text-center">${row.tiposervicio}</td>
              <td class="text-center">${row.nombreservicio}</td>
              <td class="text-center">S/ ${parseFloat(row.precio_servicio).toFixed(2)}</td>
            </tr>
          `);
                    });
                }
            },
            error() {
                // Error genérico
                $tbodyProd.append(`<tr><td colspan="6" class="text-center text-danger">Error al cargar productos</td></tr>`);
                $tbodyServ.append(`<tr><td colspan="4" class="text-center text-danger">Error al cargar servicios</td></tr>`);
            }
        });
    }
</script>
<script>
    let tablaCotizaciones;
    const API = "<?= SERVERURL ?>app/controllers/Cotizacion.controller.php";
    const fechaInput = document.getElementById('Fecha');
    const btnDia = document.querySelector('button[data-modo="dia"]');
    const btnSemana = document.querySelector('button[data-modo="semana"]');
    const btnMes = document.querySelector('button[data-modo="mes"]');
    const filtros = [btnDia, btnSemana, btnMes];

    function marcarActivo(btn) {
        filtros.forEach(b => b.classList.toggle('active', b === btn));
    }

    function cargarTablaCotizaciones(modo, fecha) {
        if (tablaCotizaciones) {
            tablaCotizaciones.destroy();
            $("#tablacotizacion tbody").empty();
        }

        tablaCotizaciones = $("#tablacotizacion").DataTable({
            ajax: {
                url: "<?= SERVERURL ?>app/controllers/Cotizacion.controller.php",
                data: {
                    modo,
                    fecha
                },
                dataSrc: "data"
            },
            columns: [{ // Columna 1: Número de fila
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                }, // Cierra columna 1
                { // Columna 2: cliente
                    data: "cliente",
                    defaultContent: "No disponible",
                    class: 'text-start'
                }, // Cierra columna 2
                { // Columna 3: precio total
                    data: "total",
                    defaultContent: "0.00",
                    class: 'text-center',
                    render: (data) => `$ ${parseFloat(data).toFixed(2)}`
                }, // Cierra columna 3
                { // Columna 4: dias de vigencia
                    data: "vigencia",
                    defaultContent: "No disponible",
                    class: 'text-center'
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
    } // Cierra cargarTablaCotizaciones()

    // Carga la tabla de registros eliminados
    function cargarCotizacionesEliminadas() {
        if ($.fn.DataTable.isDataTable("#tablacotizacioneseliminadas")) {
            $("#tablacotizacioneseliminadas").DataTable().destroy();
            $("#tablacotizacioneseliminadas tbody").empty();
        }
        $("#tablacotizacioneseliminadas").DataTable({
            ajax: {
                url: API + "?action=cotizaciones_eliminadas",
                dataSrc(json) {
                    console.log("cotizaciones_eliminadas response:", json);
                    return json.status === 'success' ? json.data : [];
                }
            },
            columns: [{
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: "cliente",
                    class: "text-start",
                    defaultContent: "—"
                },
                {
                    data: "total",
                    class: "text-center",
                    defaultContent: "—",
                    render: (data) => data ? `$${parseFloat(data).toFixed(2)}` : '—'
                },
                {
                    data: "vigencia",
                    class: "text-center",
                    defaultContent: "—"
                },
                {
                    data: null,
                    class: "text-center",
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-primary btn-sm btn-ver-justificacion"
                                data-id="${row.idcotizacion}"
                                data-bs-toggle="modal"
                                data-bs-target="#modalVerJustificacion">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button title="Detalle de la cotización" class="btn btn-info btn-sm btn-detalle"
                                    data-action="detalle"
                                    data-id="${row.idcotizacion}"
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
        return `
        <button title="Eliminar" class="btn btn-danger btn-sm btnEliminar" data-id="${row.id}">
            <i class="fa-solid fa-trash"></i>
        </button>
        <button class="btn btn-info btn-sm"
                onclick="verDetalleCotizacion(${row.id}, '${row.cliente}')">
            <i class='fa-solid fa-clipboard-list'></i>
        </button>
        <button class="btn btn-success btn-sm"
                onclick="window.location='../ventas/registrar-ventas-orden.php?id=${row.id}'">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
        </button>
        <button title="Pdf" class="btn btn-outline-dark btn-sm btn-descargar-pdf"
                onclick="descargarPDF('${row.id}')">
            <i class="fa-solid fa-file-pdf"></i>
        </button>
    `;
    }

    function descargarPDF(idcotizacion) {
        const url = `<?= SERVERURL ?>app/reports/reportecotizacion.php?idcotizacion=${encodeURIComponent(idcotizacion)}`;
        window.open(url, '_blank');
    }

    document.addEventListener("DOMContentLoaded", () => {
        // inicializo fecha de hoy
        const hoy = new Date().toISOString().slice(0, 10);
        fechaInput.value = hoy;
        let currentModo = 'dia';
        marcarActivo(btnDia);
        cargarTablaCotizaciones(currentModo, hoy);

        // clicks en filtros
        filtros.forEach(btn => {
            btn.addEventListener("click", () => {
                currentModo = btn.dataset.modo;
                marcarActivo(btn);
                cargarTablaCotizaciones(currentModo, fechaInput.value);
            });
        });

        // cambio de fecha → día
        fechaInput.addEventListener("change", () => {
            currentModo = 'dia';
            marcarActivo(btnDia);
            cargarTablaCotizaciones(currentModo, fechaInput.value);
        });

        // eliminación con justificación
        $(document).on('click', '.btnEliminar', function() {
            const idv = $(this).data('id');
            $('#justificacion').val('');
            $('#btnEliminarCotizacion').data('id', idv);
            $('#modalJustificacion').modal('show');
        });

        // confirmar eliminación
        $(document).on('click', '#btnEliminarCotizacion', async function() {
            const just = $('#justificacion').val().trim();
            const idv = $(this).data('id');
            if (!just) {
                return alert('Escribe la justificación.');
            }
            // Reemplaza ask() por confirm()
            if (!confirm('¿Estás seguro de que quieres eliminar esta cotización?')) {
                return;
            }
            $.post(API, {
                    action: 'eliminar',
                    idcotizacion: idv,
                    justificacion: just
                },
                res => {
                    if (res.status === 'success') {
                        showToast('Cotización eliminada', 'SUCCESS', 1500);
                        $('#modalJustificacion').modal('hide');
                        cargarTablaCotizaciones(currentModo, fechaInput.value);
                    } else {
                        showToast(res.message || 'Error', 'ERROR', 1500);
                    }
                },
                'json'
            );
        });

        $(document).on('click', '.btn-detalle', function() {
            const idcotizacion = $(this).data('id');
            verDetalleCotizacion(idcotizacion);
            $('#miModal').modal('show');
        });
    });
</script>

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

<!-- Modal de Detalle de Cotización -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 950px;" style="margin-top: 20px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de la Cotización</h5>
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
                            <input type="text" disabled class="form-control input" id="vigencia" placeholder="Vigencia">
                            <label for="vigencia">Días de Vigencia: </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" disabled class="form-control input" id="estadoCotizacion"
                                placeholder="Estado">
                            <label for="estadoCotizacion">Estado: </label>
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
                    <hr>
                    <h6>Servicios asociados</h6>
                    <table class="table table-striped table-bordered" id="tabla-detalle-servicios-modal">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tipo Servicio</th>
                                <th>Servicio</th>
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
                <p>¿Por qué deseas eliminar esta Cotización? (Escribe una justificación)</p>
                <textarea id="justificacion" class="form-control" rows="4"
                    placeholder="Escribe tu justificación aquí..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEliminarCotizacion" class="btn btn-danger btn-sm">Eliminar</button>
            </div>
        </div>
    </div>
</div>
</body>

</html>