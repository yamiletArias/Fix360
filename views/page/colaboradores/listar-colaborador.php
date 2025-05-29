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
        <!-- DataTable la llenará vía AJAX -->
      </tbody>
    </table>
  </div>
</div>
</div>
</div>

<!-- Modal: Detalle de Colaborador -->
<div class="modal fade" id="modalDetalleColaborador" tabindex="-1" aria-labelledby="detalleLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detalleLabel">Detalle de Colaborador</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Tipo de Documento</label>
            <input type="text" id="detalleTipodoc" class="form-control" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">N° Documento</label>
            <input type="text" id="detalleNumdoc" class="form-control" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">RUC</label>
            <input type="text" id="detalleNumruc" class="form-control" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Correo</label>
            <input type="text" id="detalleCorreo" class="form-control" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Teléfono Alternativo</label>
            <input type="text" id="detalleTelAlt" class="form-control" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Dirección</label>
            <input type="text" id="detalleDireccion" class="form-control" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Fecha Inicio Contrato</label>
            <input type="text" id="detalleFInicio" class="form-control" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Fecha Fin Contrato</label>
            <input type="text" id="detalleFFin" class="form-control" disabled>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
      { data: "nombre_rol" },
      {
        data: "telprincipal",
        render: v => v || "No proporcionado"
      },
      { data: "username" },
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
              <i class="fa-solid fa-trash"></i>
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
    $.getJSON(`../../controllers/colaborador.php?action=get&id=${id}`)
      .done(resp => {
        if (resp.status === "success") {
          const d = resp.data;
          $("#detalleTipodoc").val(d.tipodoc);
          $("#detalleNumdoc").val(d.numdoc);
          $("#detalleNumruc").val(d.numruc || "No proporcionado");
          $("#detalleCorreo").val(d.correo || "No proporcionado");
          $("#detalleTelAlt").val(d.telalternativo || "No proporcionado");
          $("#detalleDireccion").val(d.direccion || "No proporcionado");
          $("#detalleFInicio").val(d.fechainicio);
          $("#detalleFFin").val(d.fechafin || "Activo");
          new bootstrap.Modal($("#modalDetalleColaborador")).show();
        }
      });
  };

  // Confirm deactivate
  window.confirmDeactivate = function(id) {
    Swal.fire({
      title: "¿Dar de baja a este colaborador?",
      text: "Se asignará la fecha de fin de contrato al día de hoy.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, dar de baja",
      cancelButtonText: "Cancelar"
    }).then(result => {
      if (result.isConfirmed) {
        const today = new Date().toISOString().split("T")[0];
        $.post("../../controllers/colaborador.php", {
          action: "deactivate",
          idcolaborador: id,
          fechafin: today
        }, resp => {
          if (resp.status) {
            Swal.fire("¡Hecho!", resp.message, "success");
            tabla.ajax.reload();
          } else {
            Swal.fire("Error", resp.message, "error");
          }
        }, "json");
      }
    });
  };
});
</script>
