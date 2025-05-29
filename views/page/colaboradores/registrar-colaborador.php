<?php

const NAMEVIEW = "Registro de Colaborador";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<div class="container-main">
  <div class="card border">
    <div class="card-header">
      <h3>Registrar Colaborador</h3>
    </div>
    <div class="card-body">
      <form id="formColaborador">

        <div class="row g-3">
          <!-- Datos de acceso -->
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" class="form-control" name="namuser" id="namuser" placeholder="Usuario" required>
              <label for="namuser"><strong>Username</strong></label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="password" class="form-control" name="passuser" id="passuser" placeholder="Contraseña" required>
              <label for="passuser"><strong>Contraseña</strong></label>
            </div>
          </div>

          <!-- Rol / Contrato -->
          <div class="col-md-4">
            <div class="form-floating">
              <select class="form-select" id="idrol" name="idrol" required>
                <option value="">Cargando roles...</option>
              </select>
              <label for="idrol"><strong>Rol</strong></label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="date" class="form-control" name="fechainicio" id="fechainicio" required>
              <label for="fechainicio"><strong>Fecha Inicio</strong></label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="date" class="form-control" name="fechafin" id="fechafin">
              <label for="fechafin"><strong>Fecha Fin (opcional)</strong></label>
            </div>
          </div>

          <!-- Datos personales -->
          <div class="col-md-3">
            <div class="form-floating">
              <select class="form-select" id="tipodoc" name="tipodoc" required>
                <option value="DNI">DNI</option>
                <option value="Pasaporte">Pasaporte</option>
                <option value="Carnet">Carnet de extranjería</option>
              </select>
              <label for="tipodoc"><strong>Tipo Documento</strong></label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="text" class="form-control" id="numdoc" name="numdoc" minlength="6" maxlength="20" placeholder="Documento" required>
              <label for="numdoc"><strong>N° Documento</strong></label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="text" class="form-control" id="apellidos" name="apellidos" minlength="2" maxlength="50" placeholder="Apellidos" required>
              <label for="apellidos"><strong>Apellidos</strong></label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="text" class="form-control" id="nombres" name="nombres" minlength="2" maxlength="50" placeholder="Nombres" required>
              <label for="nombres"><strong>Nombres</strong></label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" class="form-control" id="direccion" name="direccion" minlength="5" maxlength="100" placeholder="Dirección">
              <label for="direccion"><strong>Dirección</strong></label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="text" class="form-control" id="telprincipal" name="telprincipal" minlength="7" maxlength="20" placeholder="Tel. principal" required>
              <label for="telprincipal"><strong>Tel. Principal</strong></label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="text" class="form-control" id="telalternativo" name="telalternativo" minlength="7" maxlength="20" placeholder="Tel. alternativo">
              <label for="telalternativo"><strong>Tel. Alternativo</strong></label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <input type="email" class="form-control" id="correo" name="correo" minlength="5" maxlength="100" placeholder="Correo">
              <label for="correo"><strong>Correo</strong></label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="text" class="form-control" id="numruc" name="numruc" minlength="11" maxlength="11" placeholder="RUC">
              <label for="numruc"><strong>RUC</strong></label>
            </div>
          </div>
        </div>

      </form>
    </div>

    <div class="card-footer text-end">
      <a href="lista-colaborador.php" class="btn btn-secondary">Cancelar</a>
      <button id="btnRegistrar" class="btn btn-success">Guardar</button>
    </div>
  </div>
</div>
</div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    // Cargar roles en el select
    fetch("<?= rtrim(SERVERURL, '/') ?>/app/controllers/rol.php")
      .then(res => res.json())
      .then(data => {
        const sel = document.getElementById("idrol");
        sel.innerHTML = '<option value="">Seleccione un rol</option>';
        data.forEach(r => {
          sel.innerHTML += `<option value="${r.idrol}">${r.rol}</option>`;
        });
      });

    // Registrar colaborador
    document.getElementById("btnRegistrar").addEventListener("click", () => {
      const form = document.getElementById("formColaborador");
      const fd = new FormData(form);
      // asignar action para controller
      fd.append("action", "create");

      fetch("<?= rtrim(SERVERURL, '/') ?>/app/controllers/colaborador.php", {
        method: "POST",
        body: fd
      })
      .then(res => res.json())
      .then(resp => {
        if (resp.status) {
          Swal.fire("¡Registrado!", "Colaborador creado correctamente.", "success")
            .then(() => window.location.href = "lista-colaborador.php");
        } else {
          Swal.fire("Error", resp.message, "error");
        }
      });
    });
  });
</script>
