<?php

const NAMEVIEW = "Colaborador | Editar";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

$id = isset($_GET['idcolaborador']) ? (int) $_GET['idcolaborador'] : 0;
if ($id <= 0) {
  echo "<div class='container-main'>
  <div class='alert alert-danger'>ID de colaborador inválido.
  </div>
  </div>
  </div>
  </div>";
  require_once "../../partials/_footer.php";
  exit;
}

?>

<div class="container-main">
  <div class="card border">
    <div class="card-body">
      <form id="formEditarColaborador" autocomplete="off">

        <input type="hidden" id="idcolaborador" name="idcolaborador" value="<?= $id ?>">

        <div class="row g-3">

          <!-- Tipo y número de documento -->
          <div class="col-md-2">
            <div class="form-floating">
              <select class="form-select input" id="tipodoc" name="tipodoc" style="color: black;" ReadOnly disabled>
                <option value="DNI">DNI</option>
                <option value="Pasaporte">Pasaporte</option>
                <option value="Carnet">Carnet de extranjería</option>
              </select>
              <label for="tipodoc"><strong>Tipo Documento</strong></label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="text" class="form-control input" id="numdoc" name="numdoc" minlength="6" maxlength="20" pattern="[0-9A-Za-z]+" placeholder="Documento" disabled>
              <label for="numdoc"><strong>N° Documento</strong></label>
            </div>
          </div>

          <!-- Datos personales -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" class="form-control input" id="apellidos" name="apellidos" minlength="2" maxlength="50" placeholder="Apellidos" ReadOnly> 
              <label for="apellidos"><strong>Apellidos</strong></label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" class="form-control input" id="nombres" name="nombres" minlength="2" maxlength="50" placeholder="Nombres" ReadOnly>
              <label for="nombres"><strong>Nombres</strong></label>
            </div>
          </div>

          <!-- Dirección y correo -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" class="form-control input" id="direccion" name="direccion" minlength="5" maxlength="100" placeholder="Dirección">
              <label for="direccion">Dirección</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="email" class="form-control input" id="correo" name="correo" minlength="5" maxlength="100" placeholder="Correo">
              <label for="correo">Correo</label>
            </div>
          </div>

          <!-- Teléfono principal -->
          <div class="col-md-2">
            <div class="form-floating">
              <input type="text" class="form-control input" id="telprincipal" name="telprincipal" minlength="9" maxlength="9" pattern="9\d{8}" placeholder="Tel. principal">
              <label for="telprincipal"><strong>Tel. Principal</strong></label>
            </div>
          </div>

          <!-- Rol -->
          <div class="col-md-2">
            <div class="form-floating">
              <select class="form-select input" id="idrol" name="idrol" style="color: black;" required ReadOnly>
                <option value="">Cargando roles...</option>
              </select>
              <label for="idrol"><strong>Rol</strong></label>
            </div>
          </div>

          <!-- Datos de acceso -->
          <div class="col-md-3">
            <div class="form-floating">
              <input type="text" class="form-control input" name="namuser" id="namuser" minlength="3" maxlength="50" placeholder="Usuario" required>
              <label for="namuser"><strong>Username</strong></label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="password" class="form-control input" name="passuser" id="passuser" minlength="6" maxlength="100" placeholder="Nueva contraseña (dejar en blanco para mantener)">
              <label for="passuser">Nueva contraseña</label>
            </div>
          </div>

          <!-- Fechas de contrato -->
          <div class="col-md-3">
            <div class="form-floating">
              <input type="date" class="form-control input" name="fechainicio" id="fechainicio" disabled>
              <label for="fechainicio"><strong>Fecha Inicio</strong></label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="date" class="form-control input" name="fechafin" id="fechafin">
              <label for="fechafin">Fecha Fin (opcional)</label>
            </div>
          </div>

        </div>

      </form>
    </div>
    <div class="card-footer text-end">
      <a href="listar-colaborador.php" class="btn btn-secondary">Cancelar</a>
      <button id="btnActualizar" class="btn btn-success">Guardar</button>
    </div>
  </div>
</div>
</div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const id = <?= $id ?>;
    const rolSelect = document.getElementById('idrol');
    const tipodocEl = document.getElementById('tipodoc');
    const numdocEl = document.getElementById('numdoc');
    const nombresEl = document.getElementById('nombres');
    const apellidosEl = document.getElementById('apellidos');
    const direccionEl = document.getElementById('direccion');
    const correoEl = document.getElementById('correo');
    const telEl = document.getElementById('telprincipal');
    const fechaIniEl = document.getElementById('fechainicio');
    const fechaFinEl = document.getElementById('fechafin');
    const namuserEl = document.getElementById('namuser');
    
    // 1) Cargar roles y luego seleccionar
    fetch("<?= SERVERURL ?>app/controllers/Rol.controller.php")
      .then(res => res.json())
      .then(data => {
        rolSelect.innerHTML = '<option value="">Seleccione un rol</option>';
        data.forEach(r => {
          const opt = document.createElement('option'); opt.value = r.idrol; opt.textContent = r.rol;
          rolSelect.appendChild(opt);
        });
        // Después de cargar roles, obtén datos colaborador
        return fetch(`<?= SERVERURL ?>app/controllers/colaborador.controller.php?action=get&id=${id}`);
      })
      .then(res => res.json())
      .then(resp => {
        if (resp.status === 'success') {
          const d = resp.data;
          tipodocEl.value = d.tipodoc;
          numdocEl.value = d.numdoc;
          nombresEl.value = d.nombres;
          apellidosEl.value = d.apellidos;
          direccionEl.value = d.direccion;
          correoEl.value = d.correo;
          telEl.value = d.telprincipal;
          fechaIniEl.value = d.fechainicio;
          fechaFinEl.value = d.fechafin || '';
          namuserEl.value = d.username;
          rolSelect.value = d.idrol;
        } else {
          Swal.fire('Error', 'No se pudo cargar datos.', 'error');
        }
      })
      .catch(err => console.error(err));

    // 2) Actualizar
    document.getElementById('btnActualizar').addEventListener('click', () => {
      const data = new FormData(document.getElementById('formEditarColaborador'));
      data.append('action', 'update');
      fetch("<?= SERVERURL ?>app/controllers/colaborador.controller.php", { method: 'POST', body: data })
        .then(res => res.json())
        .then(r => {
          if (r.status) {
            Swal.fire('¡Actualizado!', r.message, 'success')
              .then(() => window.location.href = 'listar-colaborador.php');
          } else {
            Swal.fire('Error', r.message, 'error');
          }
        });
    });
  });
</script>