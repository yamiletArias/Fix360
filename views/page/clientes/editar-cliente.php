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
  const SERVERURL = "<?= SERVERURL ?>";
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
  // views/page/clientes/js/editar-cliente.js

// Se asume que en la página se inyectan estas constantes:
//   const TIPODECLIENTE = "persona" o "empresa";
//   const CLIENTE_ID    = <número>;
//   const SERVERURL     = "<URL base del servidor>";

document.addEventListener("DOMContentLoaded", () => {
  // Referencias a formularios, radios y botón
  const formPersona   = document.getElementById("formPersona");
  const formEmpresa   = document.getElementById("formEmpresa");
  const btnRegistrar  = document.getElementById("btnRegistrar");
  const radioPersona  = document.querySelector('input[name="tipo"][value="persona"]');
  const radioEmpresa  = document.querySelector('input[name="tipo"][value="empresa"]');

  // Muestra u oculta los formularios según el tipo de cliente
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

  // Inicial: marcar radio correspondiente y mostrar el formulario adecuado
  document.querySelector(`input[name="tipo"][value="${TIPODECLIENTE}"]`).checked = true;
  mostrarFormulario(TIPODECLIENTE);

  // 1) Cargar opciones de Contactabilidad en ambos <select>
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
        selP.innerHTML += opt;
        selE.innerHTML += opt;
      });
    })
    .catch(err => console.error("Error al cargar contactabilidad:", err));
  }

  // 2) Traer datos existentes del cliente (persona o empresa) y rellenar campos
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
          // Rellenar formulario de persona
          document.getElementById("tipodoc").value         = reg.tipodoc             || "";
          document.getElementById("numdoc").value         = reg.numdoc              || "";
          document.getElementById("apellidos").value      = reg.apellidos           || "";
          document.getElementById("nombres").value        = reg.nombres             || "";
          document.getElementById("direccion").value      = reg.direccion           || "";
          document.getElementById("telprincipal").value   = reg.telprincipal        || "";
          document.getElementById("telalternativo").value = reg.telalternativo      || "";
          document.getElementById("correo").value         = reg.correo              || "";
          document.getElementById("numruc").value         = reg.numruc              || "";
          document.getElementById("cpersona").value       = reg.idcontactabilidad   || "";

          // Deshabilitar campos inmutables
          ["tipodoc","numdoc","apellidos","nombres"].forEach(id => {
            document.getElementById(id).disabled = true;
          });

          // Guardar el idpersona en data-attribute para el POST
          formPersona.dataset.idpersona = reg.idpersona;
        } else {
          // Rellenar formulario de empresa
          document.getElementById("ruc").value           = reg.ruc                  || "";
          document.getElementById("nomcomercial").value  = reg.nomcomercial         || "";
          document.getElementById("razonsocial").value   = reg.razonsocial          || "";
          document.getElementById("telempresa").value    = reg.telefono             || "";
          document.getElementById("correoemp").value     = reg.correo               || "";
          document.getElementById("cempresa").value      = reg.idcontactabilidad    || "";

          // Deshabilitar campos inmutables
          ["ruc","nomcomercial","razonsocial"].forEach(id => {
            document.getElementById(id).disabled = true;
          });

          // Guardar el idempresa en data-attribute para el POST
          formEmpresa.dataset.idempresa = reg.idempresa;
        }
      })
      .catch(err => console.error("Error al cargar datos del cliente:", err));
  }

  // 3) Validación básica de formulario: los campos required no deben quedar vacíos
  function validarFormulario(formElement) {
    const elementos = Array.from(formElement.querySelectorAll("input, select"));
    for (let el of elementos) {
      if (el.hasAttribute("required") && el.value.trim() === "") {
        el.classList.add("is-invalid");
        el.focus();
        return false;
      }
      el.classList.remove("is-invalid");
    }
    return true;
  }

  // 4) Ejecutar carga encadenada: primero contactabilidad, luego datos del cliente
  cargarContactabilidad()
    .then(cargarDatos)
    .catch(err => console.error(err));

  // 5) Al hacer click en "Aceptar", armar y enviar el body JSON en lugar de FormData
  btnRegistrar.addEventListener("click", async (e) => {
    e.preventDefault();

    const esPersona = formPersona.style.display === 'block';
    const formActual = esPersona ? formPersona : formEmpresa;

    if (!validarFormulario(formActual)) return;

    let payload = {};

    if (esPersona) {
      // Configurar para actualizar persona
      payload.operation     = "updatePersona";
      payload.idpersona     = parseInt(formPersona.dataset.idpersona, 10);
      payload.nombres       = document.getElementById("nombres").value.trim();
      payload.apellidos     = document.getElementById("apellidos").value.trim();
      payload.tipodoc       = document.getElementById("tipodoc").value;
      payload.numdoc        = document.getElementById("numdoc").value.trim();
      payload.numruc        = document.getElementById("numruc").value.trim();
      payload.direccion     = document.getElementById("direccion").value.trim();
      payload.correo        = document.getElementById("correo").value.trim();
      payload.telprincipal  = document.getElementById("telprincipal").value.trim();
      payload.telalternativo= document.getElementById("telalternativo").value.trim();
    } else {
      // Configurar para actualizar empresa
      payload.operation    = "updateEmpresa";
      payload.idempresa    = parseInt(formEmpresa.dataset.idempresa, 10);
      payload.nomcomercial = document.getElementById("nomcomercial").value.trim();
      payload.razonsocial  = document.getElementById("razonsocial").value.trim();
      payload.telefono     = document.getElementById("telempresa").value.trim();
      payload.correo       = document.getElementById("correoemp").value.trim();
    }

    try {
      const respuesta = await fetch(`${SERVERURL}app/controllers/Cliente.controller.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(payload)
      });

      const json = await respuesta.json();

      // Mostrar toast (suponiendo que exista la función showToast)
      showToast(json.message, json.status ? 'SUCCESS' : 'ERROR', 1500);

      if (json.status) {
        // Si todo va bien, redirige a listar-cliente.php al cabo de 1 segundo
        setTimeout(() => {
          window.location.href = "listar-cliente.php";
        }, 1000);
      }
    } catch (error) {
      console.error("Error al actualizar cliente:", error);
      showToast("Error inesperado al actualizar", "ERROR", 1500);
    }
  });

});

</script>