<?php

const NAMEVIEW = "Editar Colaborador";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

$id = isset($_GET['idcolaborador']) ? (int)$_GET['idcolaborador'] : 0;
if ($id <= 0) {
    echo "<div class='alert alert-danger'>ID de colaborador inválido.</div>";
    require_once "../../partials/_footer.php";
    exit;
}

?>

<div class="container-main">
  <div class="card border">
    <div class="card-header">
      <h3>Editar Colaborador</h3>
    </div>
    <div class="card-body">
      <form id="formEditarColaborador">

        <input type="hidden" id="idcolaborador" name="idcolaborador" value="<?= $id ?>">

        <div class="row g-3">
          <!-- Datos de persona -->
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" class="form-control" name="nombres" id="nombres" placeholder="Nombres" required>
              <label for="nombres"><strong>Nombres</strong></label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" class="form-control" name="apellidos" id="apellidos" placeholder="Apellidos" required>
              <label for="apellidos"><strong>Apellidos</strong></label>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-floating">
              <select class="form-select" name="tipodoc" id="tipodoc" required>
                <option value="DNI">DNI</option>
                <option value="Pasaporte">Pasaporte</option>
                <option value="Carnet de extranjería">Carnet de extranjería</option>
              </select>
              <label for="tipodoc"><strong>Tipo de Documento</strong></label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" class="form-control" name="numdoc" id="numdoc" placeholder="N° Documento" required>
              <label for="numdoc"><strong>N° de Documento</strong></label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" class="form-control" name="numruc" id="numruc" placeholder="RUC">
              <label for="numruc"><strong>RUC</strong> <small>(opcional)</small></label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <input type="email" class="form-control" name="correo" id="correo" placeholder="Correo">
              <label for="correo"><strong>Correo</strong> <small>(opcional)</small></label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="text" class="form-control" name="telprincipal" id="telprincipal" placeholder="Tel. Principal" required>
              <label for="telprincipal"><strong>Tel. Principal</strong></label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="text" class="form-control" name="telalternativo" id="telalternativo" placeholder="Tel. Alternativo">
              <label for="telalternativo"><strong>Tel. Alternativo</strong> <small>(opcional)</small></label>
            </div>
          </div>

          <div class="col-12">
            <div class="form-floating">
              <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Dirección">
              <label for="direccion"><strong>Dirección</strong> <small>(opcional)</small></label>
            </div>
          </div>

          <!-- Datos de contrato / rol -->
          <div class="col-md-4">
            <div class="form-floating">
              <select class="form-select" id="idrol" name="idrol" required>
                <option value="">Cargando roles…</option>
              </select>
              <label for="idrol"><strong>Rol</strong></label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="date" class="form-control" name="fechainicio" id="fechainicio" placeholder="Fecha Inicio" required>
              <label for="fechainicio"><strong>Fecha Inicio</strong></label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="date" class="form-control" name="fechafin" id="fechafin" placeholder="Fecha Fin">
              <label for="fechafin"><strong>Fecha Fin</strong> <small>(opcional)</small></label>
            </div>
          </div>

          <!-- Datos de acceso -->
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" class="form-control" name="namuser" id="namuser" placeholder="Username" required>
              <label for="namuser"><strong>Username</strong></label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="password" class="form-control" name="passuser" id="passuser" placeholder="Nueva Contraseña">
              <label for="passuser"><strong>Contraseña</strong> <small>(dejar vacío para no cambiar)</small></label>
            </div>
          </div>

          <!-- Estado -->
          <div class="col-md-3">
            <div class="form-check mt-3">
              <input class="form-check-input" type="checkbox" id="estado" name="estado">
              <label class="form-check-label" for="estado"><strong>Activo</strong></label>
            </div>
          </div>
        </div>

      </form>
    </div>
    <div class="card-footer text-end">
      <a href="colaboradores.php" class="btn btn-secondary">Cancelar</a>
      <button id="btnActualizar" class="btn btn-primary">Guardar Cambios</button>
    </div>
  </div>
</div>
</div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const id = <?= $id ?>;
  const rolSelect = $("#idrol");

  // 1) Cargar lista de roles
  $.getJSON("<?= rtrim(SERVERURL,'/') ?>/app/controllers/rol.php")
    .done(list => {
      rolSelect.empty().append('<option value="">Seleccione un rol</option>');
      list.forEach(r => rolSelect.append(
        `<option value="${r.idrol}">${r.rol}</option>`
      ));
    });

  // 2) Obtener datos del colaborador
  $.getJSON("../../controllers/colaborador.php?action=get&id=" + id)
    .done(resp => {
      if (resp.status === "success") {
        const d = resp.data;
        $("#nombres").val(d.nombres);
        $("#apellidos").val(d.apellidos);
        $("#tipodoc").val(d.tipodoc);
        $("#numdoc").val(d.numdoc);
        $("#numruc").val(d.numruc);
        $("#correo").val(d.correo);
        $("#telprincipal").val(d.telprincipal);
        $("#telalternativo").val(d.telalternativo);
        $("#direccion").val(d.direccion);
        $("#fechainicio").val(d.fechainicio);
        $("#fechafin").val(d.fechafin);
        $("#namuser").val(d.username);
        $("#estado").prop("checked", d.usuario_activo == 1);
        rolSelect.val(d.idrol);
      } else {
        alert("No se pudo cargar datos.");
      }
    });

  // 3) Enviar actualización
  $("#btnActualizar").click(() => {
    const form = $("#formEditarColaborador")[0];
    const data = {
      action: "update",
      idcolaborador: id,
      nombres: $("#nombres").val(),
      apellidos: $("#apellidos").val(),
      tipodoc: $("#tipodoc").val(),
      numdoc: $("#numdoc").val(),
      numruc: $("#numruc").val(),
      correo: $("#correo").val(),
      telprincipal: $("#telprincipal").val(),
      telalternativo: $("#telalternativo").val(),
      direccion: $("#direccion").val(),
      idrol: $("#idrol").val(),
      fechainicio: $("#fechainicio").val(),
      fechafin: $("#fechafin").val() || null,
      namuser: $("#namuser").val(),
      passuser: $("#passuser").val(),
      estado: $("#estado").is(":checked") ? 1 : 0
    };

    $.post("../../controllers/colaborador.php", data, resp => {
      if (resp.status) {
        Swal.fire("¡Actualizado!", resp.message, "success")
          .then(() => window.location.href = "colaboradores.php");
      } else {
        Swal.fire("Error", resp.message, "error");
      }
    }, "json");
  });
});
</script>
