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
                pattern="[0-9A-Za-z]+" placeholder="Documento" autocomplete="off" required>
              <label for="numdoc"><strong>N° Documento</strong></label>
            </div>
          </div>

          <!-- Datos personales -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" class="form-control input" id="apellidos" name="apellidos" minlength="2" maxlength="50" autocomplete="off"
                placeholder="Apellidos" required>
              <label for="apellidos"><strong>Apellidos</strong></label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" class="form-control input" id="nombres" name="nombres" minlength="2" maxlength="50" autocomplete="off"
                placeholder="Nombres" required>
              <label for="nombres"><strong>Nombres</strong></label>
            </div>
          </div>

          <!-- Dirección y correo -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" class="form-control input" id="direccion" name="direccion" minlength="5" autocomplete="off"
                maxlength="100" placeholder="Dirección">
              <label for="direccion">Dirección</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="email" class="form-control input" id="correo" name="correo" minlength="5" maxlength="100" autocomplete="off"
                placeholder="Correo">
              <label for="correo">Correo</label>
            </div>
          </div>

          <!-- Teléfono principal -->
          <div class="col-md-2">
            <div class="form-floating">
              <input type="text" class="form-control input" id="telprincipal" name="telprincipal" minlength="9" autocomplete="off"
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
              <input type="text" class="form-control input" name="namuser" id="namuser" minlength="3" maxlength="50" autocomplete="off"
                placeholder="Usuario">
              <label for="namuser">Username</label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="password" class="form-control input" name="passuser" id="passuser" minlength="6" autocomplete="off"
                maxlength="100" placeholder="Contraseña">
              <label for="passuser">Contraseña</label>
            </div>
          </div>

          <!-- Fechas de contrato -->
          <div class="col-md-3">
            <div class="form-floating">
              <input type="date" class="form-control input" name="fechainicio" id="fechainicio" value="<?= date('Y-m-d') ?>" required readonly>
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
      <button id="btnRegistrar" class="btn btn-success" disabled>Guardar</button>
    </div>
  </div>
</div>
</div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // Elementos
  const tipodoc       = document.getElementById('tipodoc');
  const numdoc        = document.getElementById('numdoc');
  const nombres       = document.getElementById('nombres');
  const apellidos     = document.getElementById('apellidos');
  const direccion     = document.getElementById('direccion');
  const correo        = document.getElementById('correo');
  const telprincipal  = document.getElementById('telprincipal');
  const idrol         = document.getElementById('idrol');
  const namuser       = document.getElementById('namuser');
  const passuser      = document.getElementById('passuser');
  const btnRegistrar  = document.getElementById('btnRegistrar');
  const form          = document.getElementById('formColaborador');

  // 1) Cargar roles en el select
  fetch("<?= SERVERURL ?>app/controllers/Rol.controller.php")
    .then(res => res.json())
    .then(data => {
      idrol.innerHTML = '<option value="">Seleccione un rol</option>';
      data.forEach(r => {
        idrol.innerHTML += `<option value="${r.idrol}">${r.rol}</option>`;
      });
      updateBtnState();
    });

  // API DNI lookup on blur
  numdoc.addEventListener('blur', async () => {
    if (tipodoc.value === 'DNI' && /^\d{8}$/.test(numdoc.value.trim())) {
      try {
        const resp = await fetch(`<?= SERVERURL ?>app/api/consultaDni.php?dni=${numdoc.value.trim()}`);
        const data = await resp.json();
        if (data.nombres) {
          nombres.value   = data.nombres;
          apellidos.value = `${data.apellidoPaterno} ${data.apellidoMaterno}`;
          nombres.readOnly   = true;
          apellidos.readOnly = true;
        }
      } catch (e) {
        console.error('Error DNI API:', e);
        nombres.readOnly   = false;
        apellidos.readOnly = false;
      }
    }
    updateBtnState();
  });

  // Validation helpers
  function validarCorreo() {
    const c = correo.value.trim();
    return !c || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(c);
  }
  function validarDNI() {
    return tipodoc.value !== 'DNI' || /^\d{8}$/.test(numdoc.value.trim());
  }
  function validarTelefono() {
    return /^[9]\d{8}$/.test(telprincipal.value.trim());
  }

  // Campos básicos obligatorios
  function camposBasicosValidos() {
    if (!idrol.value)                   return false;
    if (!nombres.value.trim())          return false;
    if (!apellidos.value.trim())        return false;
    if (!validarDNI())                  return false;
    return true;
  }

  // Regla de credenciales: si uno tiene valor, el otro también
  function credencialesValidas() {
    const u = namuser.value.trim();
    const p = passuser.value.trim();
    if (!u && !p)       return true;   // ambos vacíos => OK
    if (u.length < 3)   return false;  // user mínimo 3
    if (p.length < 6)   return false;  // pass mínimo 6
    return true;
  }

  // Actualiza estado del botón
  function updateBtnState() {
    btnRegistrar.disabled = !(camposBasicosValidos() && credencialesValidas());
  }

  // Escuchar cambios en todos los campos
  [
    tipodoc, numdoc, nombres, apellidos, direccion,
    correo, telprincipal, idrol, namuser, passuser
  ].forEach(el => el.addEventListener('input', updateBtnState));

  // Inicial
  updateBtnState();

  // Manejo de clic en Guardar
  btnRegistrar.addEventListener('click', async e => {
    e.preventDefault();

    // Validaciones de seguridad
    if (!camposBasicosValidos()) {
      showToast('Completa todos los campos obligatorios correctamente.', 'ERROR', 1500);
      return;
    }
    if (!credencialesValidas()) {
      showToast('Si pones username, la contraseña (mín. 6c) es obligatoria, y viceversa.', 'ERROR', 2000);
      return;
    }

    // Confirmación
    const confirmado = await ask(
      "¿Está seguro de registrar este colaborador?",
      "Colaboradores"
    );
    if (!confirmado) return;

    // Preparar y enviar datos
    const fd = new FormData(form);
    fd.append('action', 'create');

    try {
      const resp = await fetch("<?= SERVERURL ?>app/controllers/colaborador.controller.php", {
        method: 'POST',
        body: fd
      });
      const result = await resp.json();

      if (result.status) {
        showToast('Colaborador registrado exitosamente.', 'SUCCESS', 1000);
        setTimeout(() => {
          window.location.href = 'listar-colaborador.php';
        }, 1500);
      } else {
        showToast(result.message || 'Ocurrió un error inesperado.', 'ERROR', 2000);
      }
    } catch (err) {
      console.error('Fetch error:', err);
      showToast('Error de servidor. Intenta nuevamente.', 'ERROR', 2000);
    }
  });
});
</script>


