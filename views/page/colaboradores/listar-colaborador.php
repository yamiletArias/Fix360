<?php

const NAMEVIEW = "Colaboradores";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<div class="container-main">
    <div class="text-end mb-3">

        <a class="btn btn-success" onclick="window.location.href='registrar-colaborador.php'">
            Registrar
</a>
    </div>

    <div class="table-container">
        <table id="tablaColaboradores" class="table table-striped display">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre Completo</th>
                    <th>Rol</th>
                    <th>Teléfono</th>
                    <th>Username</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </div>
</div>
</div>
</div>

<div class="modal fade" id="modalDetalleColaborador" tabindex="-1" aria-labelledby="detalleLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detalleLabel">Detalle de Colaborador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="form-floating input-group">
                            <input type="text"  id="detalleTipodoc" class="form-control input" disabled>
                            <label  for="detalleTipodoc">Tipo de Documento</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-floating input-group">
                            <input type="text" id="detalleNumdoc" class="form-control input" disabled>
                            <label for="detalleNumdoc">N° Documento</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-floating input-group">
                            <input type="text" id="detalleCorreo" class="form-control input" disabled>
                            <label for="detalleCorreo">Correo</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-floating input-group">
                            <input type="text" id="detalleDireccion" class="form-control input" disabled>
                            <label for="detalleDireccion">Dirección</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-floating input-group">
                            <input type="text" id="detalleFInicio" class="form-control input" disabled>
                            <label for="detalleFInicio">Fecha Inicio Contrato</label>
                        </div>
                        </div>
                    <div class="form-group">
                        <div class="form-floating input-group">
                            <input type="text" id="detalleFFin" class="form-control input" disabled>
                            <label class="form-label">Fecha Fin Contrato</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Initialize DataTable
    const tabla = $("#tablaColaboradores").DataTable({
        ajax: {
            url: "<?= SERVERURL ?>app/controllers/Colaborador.controller.php?action=list",
            dataSrc: "data"
        },
        columns: [
            {
                data: null,
                render: (d, t, r, m) => m.row + 1
            },
            {
                data: null,
                render: row => `${row.nombres} ${row.apellidos}`
            },
            { data: "nombre_rol",
                render: r => r || "No proporcionado"
             },
            {
                data: "telprincipal",
                render: v => v || "No proporcionado"
            },
            {   data: "username",
                defaultContent: "sin cuenta" },
            {
                data: null,
                orderable: false,
                render: row => {
                    return `
                        <a href="editar-colaborador.php?idcolaborador=${row.idcolaborador}"
                            class="btn btn-warning btn-sm" title="Editar">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <button class="btn btn-info btn-sm" title="Ver detalle"
                                onclick="verDetalleColaborador(${row.idcolaborador})">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" title="Dar de baja"
                                onclick="confirmDeactivate(${row.idcolaborador})">
                            <i class="fa-solid fa-user-xmark"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No hay resultados",
            info: "Página _PAGE_ de _PAGES_",
            infoEmpty: "No hay registros",
            infoFiltered: "(filtrado de _MAX_ registros)",
            search: "Buscar:"
        }
    });

    // Show detail modal
window.verDetalleColaborador = function(id) {
    $.getJSON(`<?= SERVERURL?>app/controllers/Colaborador.controller.php?action=get&id=${id}`)
        .done(resp => {
            if (resp.status === "success") {
                const d = resp.data;
                $("#detalleTipodoc").val(d.tipodoc);
                $("#detalleNumdoc").val(d.numdoc);
                $("#detalleCorreo").val(d.correo || "No proporcionado");
                $("#detalleDireccion").val(d.direccion || "No proporcionado");
                $("#detalleFInicio").val(d.fechainicio);
                $("#detalleFFin").val(d.fechafin || "No proporcionado");
                new bootstrap.Modal($("#modalDetalleColaborador")).show();
            } else {
                Swal.fire("Error", resp.message, "error");
            }
        })
        .fail((_, status, err) => {
            console.error("AJAX Error:", status, err);
        });


};
    // Confirm deactivate
    window.confirmDeactivate = async function(id) { // Agrega async aquí
        const confirmar = await ask("¿Dar de baja a este colaborador?", "Colaboradores");
        if (confirmar) {
            const today = new Date().toISOString().split("T")[0];
            $.post("<?= SERVERURL?>app/controllers/colaborador.controller.php", {
                action: "deactivate",
                idcolaborador: id,
                fechafin: today
            }, resp => {
                if (resp.status) {
                    showToast("Colaborador desactivado correctamente", "SUCCESS",1500);
                    tabla.ajax.reload();
                } else {
                    showToast("Ocurrio un error","ERROR",1500);
                }
            }, "json");
        }
    };
});
</script>