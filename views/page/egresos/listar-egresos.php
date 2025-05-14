<?php
const NAMEVIEW = "Egresos";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>

<div class="container-main mt-5">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
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
                        <input type="date" class="form-control" id="Fecha">
                        <a href="registrar-egresos.php" class="btn btn-success" id="btnRegistrar">Registrar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla egresos activos -->
    <div id="tableDia" class="col-12">
        <table class="table table-striped display" id="tablaegresosdia">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Registrador</th>
                    <th>Receptor</th>
                    <th>Concepto</th>
                    <th class="text-end">Monto</th>
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
                    <th>Hora</th>
                    <th>Registrador</th>
                    <th>Receptor</th>
                    <th>Concepto</th>
                    <th class="text-end">Monto</th>
                    <th class="text-center">Opciones</th>
                </tr>
            </thead>
            <tbody class="text-center"></tbody>
        </table>
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
                <textarea id="justificacion" class="form-control" rows="3" placeholder="Escribe tu justificación..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEliminarEgreso" class="btn btn-danger">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script>
    const API = SERVERURL + 'app/controllers/Egreso.controller.php';
    let tablaEgresos;
    const fechaInput = document.getElementById('Fecha');
    const btnDia = document.querySelector('button[data-modo="dia"]');
    const btnSemana = document.querySelector('button[data-modo="semana"]');
    const btnMes = document.querySelector('button[data-modo="mes"]');
    const filtros = [btnDia, btnSemana, btnMes];
    let currentModo = 'dia';

    function marcarActivo(btn) {
        filtros.forEach(b => b.classList.toggle('active', b === btn));
    }

    function cargarTabla(modo, fecha, eliminados = false) {
        const selector = eliminados ? '#tablaegresoseliminados' : '#tablaegresosdia';
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().destroy();
            $(`${selector} tbody`).empty();
        }
        tablaEgresos = $(selector).DataTable({
            ajax: {
  url: API,
  data: function () {
    return {
      modo: currentModo,
      fecha: fechaInput.value
    };
  },


  dataSrc: json => json.status === 'success' ? json.data : []
}

,
            columns: [
                { data: null, render: (d,t,r,m)=> m.row+1 },
                { data: 'fecha', class: 'text-center' },
                { data: 'hora', class: 'text-center' },
                { data: 'registrador', class: 'text-start' },
                { data: 'receptor', class: 'text-start' },
                { data: 'concepto', class: 'text-start' },
                { data: 'monto', class: 'text-end', render: $.fn.dataTable.render.number(',', '.', 2) },
                { data: null, class: 'text-center', render: renderOpciones }
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
        return `<button class="btn btn-danger btn-sm btn-eliminar" data-id="${id}"><i class="fa-solid fa-trash"></i></button>`;
    }

    $(document).ready(() => {
        // inicializar fecha y tabla
        const hoy = new Date().toISOString().slice(0,10);
        fechaInput.value = hoy;
        marcarActivo(btnDia);
        cargarTabla(currentModo, hoy);

        // filtros
        filtros.forEach(btn=> btn.addEventListener('click', ()=>{
            currentModo = btn.dataset.modo;
            marcarActivo(btn);
            $('#tableEliminados').hide();
            $('#tableDia').show();
            cargarTabla(currentModo, fechaInput.value);
        }));

        // cambio de fecha -> dia
        fechaInput.addEventListener('change', ()=>{
            currentModo = 'dia';
            marcarActivo(btnDia);
            cargarTabla(currentModo, fechaInput.value);
        });

        // ver eliminados
        $('#btnVerEliminados').click(()=>{
            $('#tableDia').hide();
            $('#tableEliminados').show();
            cargarTabla(currentModo, fechaInput.value, true);
        });

        // eliminar con justificación
        let idToDelete = 0;
        $(document).on('click', '.btn-eliminar', function() {
            idToDelete = $(this).data('id');
            $('#justificacion').val('');
            $('#modalJustificacion').modal('show');
        });
        $('#btnEliminarEgreso').click(async ()=>{
            const just = $('#justificacion').val().trim();
            if (!just) return alert('Escribe una justificación.');
            const res = await fetch(API, {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({ action: 'delete', idegreso: idToDelete, justificacion: just })
            }).then(r=>r.json());
            if (res.status==='success') {
                $('#modalJustificacion').modal('hide');
                cargarTabla(currentModo, fechaInput.value);
            } else {
                alert(res.message||'Error al eliminar');
            }
        });
    });
</script>
