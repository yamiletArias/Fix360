<?php
const NAMEVIEW = "Editar datos del cliente";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

// Determinar tipo e ID
$tipo = "";
$id   = 0;
if (isset($_GET['idpersona'])) {
  $tipo = 'persona';
  $id   = intval($_GET['idpersona']);
} elseif (isset($_GET['idempresa'])) {
  $tipo = 'empresa';
  $id   = intval($_GET['idempresa']);
}
?>
<script>
  // Inyectamos en JS
  const TIPODECLIENTE = "<?= $tipo ?>";
  const CLIENTE_ID = <?= $id ?>;
  const SERVERURL     = "<?= SERVERURL ?>"; 
</script>

<div class="container-main">
  <div class="card border">
    <div class="card-header">
      <h3>Tipo de cliente:</h3>
      <label>
        <input class="form-check-input" type="radio" name="tipo" value="persona" onclick="mostrarFormulario('persona')">
        Persona
      </label>
      <label style="margin-left:1rem;">
        <input class="form-check-input" type="radio" name="tipo" value="empresa" onclick="mostrarFormulario('empresa')">
        Empresa
      </label>
    </div>
    <div class="card-body">
      <!-- FORMULARIO PERSONA -->
      <form id="formPersona" style="display:none;">
        <div class="row g-3">
          <div class="col-md-2">
            <div class="form-floating">
              <select id="tipodoc" name="tipodoc" class="form-select input" style="color: black;" required>
                <option value="DNI">DNI</option>
                <option value="Pasaporte">Pasaporte</option>
                <option value="cde">Carnet de extranjería</option>
              </select>
              <label for="tipodoc">Tipo de Documento</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input id="numdoc" name="numdoc" type="text" class="form-control input" placeholder="N° documento" minlength="8" maxlength="20" required>
              <label for="numdoc">N° Documento</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input id="apellidos" name="apellidos" type="text" class="form-control input" placeholder="Apellidos" minlength="2" maxlength="100" required>
              <label for="apellidos">Apellidos</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input id="nombres" name="nombres" type="text" class="form-control input" placeholder="Nombres" minlength="2" maxlength="100" required>
              <label for="nombres">Nombres</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-floating">
              <input id="direccion" name="direccion" type="text" class="form-control input" placeholder="Dirección" minlength="5" maxlength="100">
              <label for="direccion">Dirección</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input id="telprincipal" name="telprincipal" type="text" class="form-control input" placeholder="Tel. principal" minlength="9" maxlength="9">
              <label for="telprincipal">Tel. principal</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input id="telalternativo" name="telalternativo" type="text" class="form-control input" placeholder="Tel. alternativo" minlength="9" maxlength="9">
              <label for="telalternativo">Tel. alternativo</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-floating">
              <input id="correo" name="correo" type="email" class="form-control input" placeholder="Correo" minlength="10" maxlength="100">
              <label for="correo">Correo</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input id="numruc" name="numruc" type="text" class="form-control input" placeholder="N° RUC" minlength="11" maxlength="11">
              <label for="numruc">N° RUC</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <select id="cpersona" name="cpersona" class="form-select input" required>
                <option value="">-- Contactabilidad --</option>
              </select>
              <label for="cpersona">Contactabilidad</label>
            </div>
          </div>
        </div>
      </form>

      <!-- FORMULARIO EMPRESA -->
      <form id="formEmpresa" style="display:none;">
        <div class="row g-3">
          <div class="col-md-4">
            <div class="form-floating">
              <input id="ruc" name="ruc" type="text" class="form-control input" placeholder="RUC" minlength="11" maxlength="11" required>
              <label for="ruc">RUC</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input id="nomcomercial" name="nomcomercial" type="text" class="form-control input" placeholder="Nombre Comercial" minlength="5" maxlength="100" required>
              <label for="nomcomercial">Nombre Comercial</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input id="razonsocial" name="razonsocial" type="text" class="form-control input" placeholder="Razón Social" minlength="5" maxlength="100" required>
              <label for="razonsocial">Razón Social</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input id="telempresa" name="telempresa" type="text" class="form-control input" placeholder="Teléfono" minlength="9" maxlength="9">
              <label for="telempresa">Teléfono</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input id="correoemp" name="correoemp" type="email" class="form-control input" placeholder="Correo" minlength="10" maxlength="100">
              <label for="correoemp">Correo</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <select id="cempresa" name="cempresa" class="form-select input" required>
                <option value="">-- Contactabilidad --</option>
              </select>
              <label for="cempresa">Contactabilidad</label>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="card-footer text-end">
      <a type="button" class="btn btn-secondary" href="javascript:history.back()">Cancelar</a>
      <button id="btnRegistrar" class="btn btn-success">Aceptar</button>
    </div>
  </div>
</div>
</div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script>
  console.log({ TIPODECLIENTE, CLIENTE_ID, SERVERURL });

  // views/page/clientes/js/editar-cliente.js
// Lógica para editar cliente (persona o empresa)
document.addEventListener("DOMContentLoaded", () => {
  // 1) Referencias
  const formPersona  = document.getElementById("formPersona");
  const formEmpresa  = document.getElementById("formEmpresa");
  const btnRegistrar = document.getElementById("btnRegistrar");
  const radioPersona = document.querySelector('input[name="tipo"][value="persona"]');
  const radioEmpresa = document.querySelector('input[name="tipo"][value="empresa"]');

  // 2) Función para mostrar/ocultar formularios y deshabilitar el radio opuesto
  function mostrarFormulario(tipo) {
    if (tipo === "persona") {
      formPersona.style.display = "block";
      formEmpresa.style.display = "none";
      radioPersona.disabled = false;
      radioEmpresa.disabled = true;
    } else {
      formPersona.style.display = "none";
      formEmpresa.style.display = "block";
      radioPersona.disabled = true;
      radioEmpresa.disabled = false;
    }
  }

  // 3) Inicialización: marcar radio y mostrar formulario
  document.querySelector(`input[name="tipo"][value="${TIPODECLIENTE}"]`).checked = true;
  mostrarFormulario(TIPODECLIENTE);

  // 4) Cargar contactabilidad y luego datos existentes
  function cargarContactabilidad() {
    return fetch(`${SERVERURL}app/controllers/Contactabilidad.controller.php`, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "operation=getContactabilidad"
    })
    .then(res => res.json())
    .then(lista => {
      const selP = document.getElementById("cpersona");
      const selE = document.getElementById("cempresa");
      selP.innerHTML = "<option value=''>-- Contactabilidad --</option>";
      selE.innerHTML = "<option value=''>-- Contactabilidad --</option>";
      lista.forEach(item => {
        const opt = `<option value="${item.idcontactabilidad}">${item.contactabilidad}</option>`;
        selP && (selP.innerHTML += opt);
        selE && (selE.innerHTML += opt);
      });
    });
  }

  function cargarDatos() {
    if (CLIENTE_ID <= 0) return;
    const url = TIPODECLIENTE === 'persona'
      ? `${SERVERURL}app/controllers/Cliente.controller.php?task=getById&tipo=persona&idpersona=${CLIENTE_ID}`
      : `${SERVERURL}app/controllers/Cliente.controller.php?task=getById&tipo=empresa&idempresa=${CLIENTE_ID}`;

    return fetch(url)
      .then(res => res.json())
      .then(resp => {
        const reg = Array.isArray(resp) ? resp[0] : resp;
        if (!reg) return;

        if (TIPODECLIENTE === "persona") {
          // Poblar persona
          ["tipodoc","numdoc","apellidos","nombres","direccion","telprincipal","telalternativo","correo","numruc"].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = reg[id] || '';
          });
          document.getElementById("cpersona").value = reg.idcontactabilidad;
          // Deshabilitar inmutables
          ["tipodoc","numdoc","apellidos","nombres"].forEach(id => {
            document.getElementById(id).disabled = true;
          });
          formPersona.dataset.idpersona = reg.idpersona;
        } else {
          // Poblar empresa
          document.getElementById('ruc').value          = reg.ruc || '';
          document.getElementById('nomcomercial').value = reg.nomcomercial || '';
          document.getElementById('razonsocial').value  = reg.razonsocial || '';
          document.getElementById('telempresa').value   = reg.telefono || '';
          document.getElementById('correoemp').value    = reg.correo || '';
          document.getElementById('cempresa').value     = reg.idcontactabilidad;
          // Deshabilitar inmutables
          ['ruc','nomcomercial','razonsocial'].forEach(id => {
            document.getElementById(id).disabled = true;
          });
          formEmpresa.dataset.idempresa = reg.idempresa;
        }
      });
  }

  // 5) Ejecutar carga encadenada: primero contactabilidad, luego datos
  cargarContactabilidad().then(cargarDatos);

  // 6) Envío de actualización
  btnRegistrar.addEventListener("click", async (e) => {
  e.preventDefault();
  const form = formPersona.style.display === 'block' ? formPersona : formEmpresa;
  if (!validarFormulario(form)) return;

  const fd = new FormData(form);

  // *** IMPORTANTE: *** agregar el campo “tipo” para que el controller lo reciba:
  fd.append('tipo', TIPODECLIENTE);
  fd.append('operation', 'update');

  if (TIPODECLIENTE === 'persona') 
       fd.append('idpersona', form.dataset.idpersona);
  else fd.append('idempresa', form.dataset.idempresa);

  const resp = await fetch(`${SERVERURL}app/controllers/Cliente.controller.php`, {
    method: 'POST',
    body: fd
  }).then(r => r.json());

  showToast(resp.message, resp.status ? 'SUCCESS' : 'ERROR', 1500);
  if (resp.status) {
    setTimeout(() => window.location.href = 'listar-cliente.php', 1000);
  }
});


});

</script>
