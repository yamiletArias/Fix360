<?php
const NAMEVIEW = "Egresos";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>

<div class="container-main mt-5">
    <div class="card border">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-12 d-flex justify-content-between align-items-center mb-md-0">
                    <div class="btn-group" role="group" aria-label="Filtros periodo">
                        <button type="button" data-modo="dia" class="btn btn-primary text-white">Día</button>
                        <button type="button" data-modo="semana" class="btn btn-primary text-white">Semana</button>
                        <button type="button" data-modo="mes" class="btn btn-primary text-white">Mes</button>
                        <!-- Ver eliminados -->
                        <button id="btnVerEliminados" type="button" class="btn btn-secondary text-white" title="Ver eliminados">
                            <i class="fa-solid fa-eye-slash"></i>
                        </button>
                        <!-- Exportar PDF -->
                        <button type="button" class="btn btn-danger text-white" title="Exportar PDF">
                            <i class="fa-solid fa-file-pdf"></i>
                        </button>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="input-group">
                                <input type="date" class="form-control input" id="Fecha">
                                <a href="registrar-egresos.php" class="btn btn-success" id="btnRegistrar">Registrar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">

            <!-- Tabla egresos activos -->
            <div id="tableDia" class="col-12">
                <table class="table table-striped display" id="tablaegresosdia">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Registrador</th>
                            <th>Receptor</th>
                            <th>Concepto</th>
                            <th class="text-end">Monto</th>
                            <th>N° Comprobante</th>
                            <th class="text-center">Opciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center"></tbody>
                </table>
            </div>

            <!-- Tabla egresos eliminados -->
            <div id="tableEliminados" class="col-12" style="display: none;">
                <table class="table table-striped display" id="tablaegresoseliminados">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Registrador</th>
                            <th>Receptor</th>
                            <th>Concepto</th>
                            <th class="text-end">Monto</th>
                            <th>N° Comprobante</th>
                            <th class="text-center">Opciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- Modal Justificación Eliminación -->
<div class="modal fade" id="modalJustificacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Por qué deseas eliminar este egreso?</p>
                <textarea id="justificacion" class="form-control input" rows="3" placeholder="Escribe tu justificación..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEliminarEgreso" class="btn btn-sm btn-danger">Eliminar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Ver Justificación -->
<div class="modal fade" id="modalVerJustificacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Justificación de Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="textoJustificacion"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<?php require_once "../../partials/_footer.php"; ?>

<script>
    const API = SERVERURL + 'app/controllers/Egreso.controller.php';

    // Referencias DOM
    const fechaInput = document.getElementById('Fecha');
    const btnDia = document.querySelector('button[data-modo="dia"]');
    const btnSemana = document.querySelector('button[data-modo="semana"]');
    const btnMes = document.querySelector('button[data-modo="mes"]');
    const btnVerEliminados = document.getElementById('btnVerEliminados');
    const filtros = [btnDia, btnSemana, btnMes];

    // Estado actual
    let currentModo = 'dia';
    let showingDeleted = false; // <-- bandera: ¿estamos viendo eliminados?

    function marcarActivo(btn) {
        filtros.forEach(b => b.classList.toggle('active', b === btn));
    }

    function cargarTabla(modo, fecha, eliminados = false) {
        const selector = eliminados ? '#tablaegresoseliminados' : '#tablaegresosdia';
        // destruyo DataTable si existe
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().destroy();
            $(`${selector} tbody`).empty();
        }
        // inicializo DataTable con el parámetro estado
        $(selector).DataTable({
            ajax: {
                url: API,
                data: function() {
                    return {
                        modo: currentModo,
                        fecha: fecha,
                        estado: eliminados ? 'D' : 'A'
                    };
                },
                dataSrc: json => json.status === 'success' ? json.data : []
            },
            columns: [{
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'fecha',
                    class: 'text-center'
                },
                {
                    data: 'registrador',
                    class: 'text-start'
                },
                {
                    data: 'receptor',
                    class: 'text-start'
                },
                {
                    data: 'concepto',
                    class: 'text-start'
                },
                {
                    data: 'monto',
                    class: 'text-end',
                    render: $.fn.dataTable.render.number(',', '.', 2)
                },
                {
                    data: 'numcomprobante',
                    class: 'text-end',
                    render: function(data, type, row) {
                        return data && data.trim() !== '' ? data : 'No registrado';
                    }
                },
                {
                    data: null,
                    class: 'text-center',
                    render: renderOpciones
                }
            ],
            language: {
                lengthMenu: "Mostrar _MENU_ por página",
                zeroRecords: "No hay resultados",
                info: "Mostrando página _PAGE_ de _PAGES_",
                infoEmpty: "No hay registros",
                search: "Buscar:",
                processing: "Cargando...",
                emptyTable: "No hay datos"
            }
        });
    }

    function renderOpciones(data) {
        const id = data.idegreso;
        const recordDate = data.fecha; // ahora es "DD/MM/YYYY"
        // calculamos hoy en DD/MM/YYYY
        const d = new Date();
        const dd = String(d.getDate()).padStart(2, '0');
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const yyyy = d.getFullYear();
        const todayStr = `${dd}/${mm}/${yyyy}`;

        if (!showingDeleted) {
            // botón de detalle siempre
            let html = ``;

            const isToday = recordDate === todayStr;

            html += `
    <button
      class="btn btn-sm ${isToday ? 'btn-danger' : 'btn-secondary'} btn-eliminar"
      data-id="${id}"
      title="Eliminar egreso (solo hoy)"
      ${isToday ? '' : 'disabled'}
    >
      <i class="fa-solid fa-trash"></i>
    </button>
  `;

            return html;

        } else {
            // vista eliminados: botón “ver justificación”
            return `
    <button
      class="btn btn-primary btn-sm btn-view-just"
      data-just="${encodeURIComponent(data.justificacion)}"
      title="Ver justificación"
    >
      <i class="fa-solid fa-eye"></i>
    </button>
  `;
        }

    }




    $(document).ready(() => {
        // inicializar fecha y marcadores
        const hoy = new Date().toISOString().slice(0, 10);
        fechaInput.value = hoy;
        marcarActivo(btnDia);
        cargarTabla(currentModo, hoy, showingDeleted);

        // al hacer clic en día/semana/mes
        filtros.forEach(btn => btn.addEventListener('click', () => {
            currentModo = btn.dataset.modo;
            marcarActivo(btn);
            // Recalculamos la visibilidad según la bandera actual
            if (showingDeleted) {
                $('#tableDia').hide();
                $('#tableEliminados').show();
            } else {
                $('#tableEliminados').hide();
                $('#tableDia').show();
            }
            cargarTabla(currentModo, fechaInput.value, showingDeleted);
        }));

        // al cambiar fecha -> vuelvo a recargar con la bandera actual
        fechaInput.addEventListener('change', () => {
            currentModo = 'dia';
            marcarActivo(btnDia);


            if (showingDeleted) {
                $('#tableDia').hide();
                $('#tableEliminados').show();
            } else {
                $('#tableEliminados').hide();
                $('#tableDia').show();
            }
            cargarTabla(currentModo, fechaInput.value, showingDeleted);
        });


        // toggle ver/ocultar eliminados
        // toggle ver/ocultar eliminados
        btnVerEliminados.addEventListener('click', () => {
            // invertimos bandera
            showingDeleted = !showingDeleted;

            // intercambio tablas
            if (showingDeleted) {
                $('#tableDia').hide();
                $('#tableEliminados').show();

                // cambio estilos y icono: ahora mostramos el ojo (ver)
                btnVerEliminados.classList.replace('btn-secondary', 'btn-warning');
                btnVerEliminados.querySelector('i')
                    .classList.replace('fa-eye-slash', 'fa-eye');
                btnVerEliminados.title = 'Ver activos';
            } else {
                $('#tableEliminados').hide();
                $('#tableDia').show();

                // restauramos botón de eliminar (ojo tachado)
                btnVerEliminados.classList.replace('btn-warning', 'btn-secondary');
                btnVerEliminados.querySelector('i')
                    .classList.replace('fa-eye', 'fa-eye-slash');
                btnVerEliminados.title = 'Ver eliminados';
            }

            // recargo la tabla con el estado adecuado
            cargarTabla(currentModo, fechaInput.value, showingDeleted);
        });

        // eliminación con justificación
        let idToDelete = 0;
        $(document).on('click', '.btn-eliminar', function() {
            idToDelete = $(this).data('id');
            $('#justificacion').val('');
            $('#modalJustificacion').modal('show');
        });
        $('#btnEliminarEgreso').click(async () => {
            const just = $('#justificacion').val().trim();
            if (!just) return alert('Escribe una justificación.');
            const res = await fetch(API, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'delete',
                    idegreso: idToDelete,
                    justificacion: just
                })
            }).then(r => r.json());
            if (res.status === 'success') {
                $('#modalJustificacion').modal('hide');
                // recargo la vista actual (activos o eliminados)
                cargarTabla(currentModo, fechaInput.value, showingDeleted);
            } else {
                alert(res.message || 'Error al eliminar');
            }
        });

        $(document).on('click', '.btn-view-just', function() {
            const just = decodeURIComponent($(this).data('just'));
            $('#textoJustificacion').text(just);
            $('#modalVerJustificacion').modal('show');
        });
    });
</script>