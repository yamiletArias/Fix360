<?php

const NAMEVIEW = "Colaborador | Registro";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<div class="container-main">
  <div class="card border">
    <div class="card-body">
      <form id="formColaborador" autocomplete="off">

        <div class="row g-3">

          <!-- Tipo y número de documento -->
          <div class="col-md-2">
            <div class="form-floating">
              <select class="form-select input" id="tipodoc" name="tipodoc" style="color: black;" autofocus required>
                <option value="DNI">DNI</option>
                <option value="Pasaporte">Pasaporte</option>
                <option value="Carnet">Carnet de extranjería</option>
              </select>
              <label for="tipodoc"><strong>Tipo Documento</strong></label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="text" class="form-control input" id="numdoc" name="numdoc" minlength="6" maxlength="20"
                pattern="[0-9A-Za-z]+" placeholder="Documento" required>
              <label for="numdoc"><strong>N° Documento</strong></label>
            </div>
          </div>

          <!-- Datos personales -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" class="form-control input" id="apellidos" name="apellidos" minlength="2" maxlength="50"
                placeholder="Apellidos" required>
              <label for="apellidos"><strong>Apellidos</strong></label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" class="form-control input" id="nombres" name="nombres" minlength="2" maxlength="50"
                placeholder="Nombres" required>
              <label for="nombres"><strong>Nombres</strong></label>
            </div>
          </div>

          <!-- Dirección y correo -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" class="form-control input" id="direccion" name="direccion" minlength="5"
                maxlength="100" placeholder="Dirección">
              <label for="direccion">Dirección</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="email" class="form-control input" id="correo" name="correo" minlength="5" maxlength="100"
                placeholder="Correo">
              <label for="correo">Correo</label>
            </div>
          </div>

          <!-- Teléfono principal -->
          <div class="col-md-2">
            <div class="form-floating">
              <input type="text" class="form-control input" id="telprincipal" name="telprincipal" minlength="9"
                maxlength="9" pattern="9\d{8}" placeholder="Tel. principal" required>
              <label for="telprincipal"><strong>Tel. Principal</strong></label>
            </div>
          </div>

          <!-- Rol -->
          <div class="col-md-2">
            <div class="form-floating">
              <select class="form-select input" id="idrol" name="idrol" style="color: black;" required>
                <option value="">Cargando roles...</option>
              </select>
              <label for="idrol"><strong>Rol</strong></label>
            </div>
          </div>

          <!-- Datos de acceso -->
          <div class="col-md-3">
            <div class="form-floating">
              <input type="text" class="form-control input" name="namuser" id="namuser" minlength="3" maxlength="50"
                placeholder="Usuario" required>
              <label for="namuser"><strong>Username</strong></label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="password" class="form-control input" name="passuser" id="passuser" minlength="6"
                maxlength="100" placeholder="Contraseña" required>
              <label for="passuser"><strong>Contraseña</strong></label>
            </div>
          </div>

          <!-- Fechas de contrato -->
          <div class="col-md-3">
            <div class="form-floating">
              <input type="date" class="form-control input" name="fechainicio" id="fechainicio" value="<?= date('Y-m-d') ?>" required disabled>
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
    fetch("<?= SERVERURL ?>app/controllers/Rol.controller.php")
      .then(res => res.json())
      .then(data => {
        const sel = document.getElementById("idrol");
        sel.innerHTML = '<option value="">Seleccione un rol</option>';
        data.forEach(r => {
          sel.innerHTML += `<option value="${r.idrol}">${r.rol}</option>`;
        });
      });

    // Elements
    const tipodoc = document.getElementById('tipodoc');
    const numdoc = document.getElementById('numdoc');
    const nombres = document.getElementById('nombres');
    const apellidos = document.getElementById('apellidos');
    const form = document.getElementById('formColaborador');

    // API DNI lookup on blur
    numdoc.addEventListener('blur', async () => {
      if (tipodoc.value === 'DNI' && numdoc.value.trim().length === 8) {
        try {
          const resp = await fetch(`<?= SERVERURL ?>app/api/consultaDni.php?dni=${numdoc.value.trim()}`);
          const data = await resp.json();
          if (data.nombres) {
            nombres.value = data.nombres;
            apellidos.value = `${data.apellidoPaterno} ${data.apellidoMaterno}`;
            nombres.readOnly = true;
apellidos.readOnly = true;;
          }
        } catch (e) {
          console.error('Error DNI API:', e);
          nombres.disabled = false;
          apellidos.disabled = false;
        }
      }
    });

    // Validation helper
    function validar() {
      // Ejemplo: validar correo
      const correo = document.getElementById('correo').value.trim();
      if (correo && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
        showToast('Correo invalido', 'ERROR', 1500); return false;
      }
      // Validar DNI formato
      if (tipodoc.value === 'DNI' && !/^\d{8}$/.test(numdoc.value.trim())) {
        showToast('El DNI debe de contar con 8 digitos', 'ERROR', 1500); return false;
      }
      // Validar celular
      const tel = document.getElementById('telprincipal').value.trim();
      if (!/^[9]\d{8}$/.test(tel)) {
        showToast('El Telefono debe tener 9 dígitos y comenzar con 9', 'ERROR', 1500); return false;
      }
      return true;
    }

    // Registrar
    document.getElementById('btnRegistrar').addEventListener('click', async e => {
      e.preventDefault();
      if (!validar()) return;
      const fd = new FormData(form);
      fd.append('action', 'create');
      const resp = await fetch("<?= SERVERURL ?>app/controllers/colaborador.controller.php", { method: 'POST', body: fd });
      const result = await resp.json();
      if (result.status) {
        showToast('Colaborador registrado exitosamente.', 'SUCCESS', 1000);
        setTimeout(() => window.location.href = 'listar-colaborador.php', 1500);
      } else {
        Swal.fire('Error', result.message, 'error');
      }
    });
  });
</script>