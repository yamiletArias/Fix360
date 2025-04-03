<?php

const NAMEVIEW = "Registro de clientes";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<div class="container-main">
  <div class="card border">
    <div class="card-header">
      <h3>Tipo de cliente:</h3>
      <label>
        <input type="radio" name="tipo" value="persona" onclick="mostrarFormulario('persona')" checked>
        Persona
      </label>
      <label>
        <input type="radio" name="tipo" value="empresa" onclick="mostrarFormulario('empresa')">
        Empresa
      </label>
    </div>
    <div class="card-body">
      <!-- Formulario para registrar una persona -->
      <form action="" id="formPersona" style="display: block;">
        <div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-floating">
                <select class="form-select" required name="tipodoc" id="tipodoc" style="color: black;">
                  <option value="DNI">DNI</option>
                  <option value="Pasaporte">Pasaporte</option>
                  <option value="cde">Carnet de extranjeria</option>
                </select>
                <label for="tipodoc">Tipo de Documento:</label>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control" name="numdoc" id="numdoc" minlength="8" maxlength="20" placeholder="parece que sin placeholder no tiene ese efecto" required>
                <label for="numdoc">N° de Documento</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" id="telprincipal" class="form-control" minlength="9" maxlength="9" name="telprincipal" placeholder="celular">
                <label for="telprincipal">Tel. principal</label>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="form-floating">
                <input type="text" id="nombres" class="form-control" name="nombres" minlength="2" maxlength="100" required placeholder="nombrealazar">
                <label for="nombres">Nombres</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" name="apellidos" class="form-control" id="apellidos" minlength="2" maxlength="100" placeholder="apellidos" required>
                <label for="apellidos">Apellidos</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-floating">
                <input type="text" class="form-control" name="direccion" id="direccion" minlength="5" maxlength="100" placeholder="mi casa">
                <label for="direccion">Direccion</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-floating">
                <input type="email" name="correo" class="form-control" id="correo" minlength="10" maxlength="100" placeholder="thepunisher2000@gmail.com">
                <label for="correo">Correo</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-floating">
                <input type="text" id="telalternativo" name="telalternativo" class="form-control" minlength="9" maxlength="9" placeholder="956633983">
                <label for="telalternativo">Tel. alternativo</label>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-floating">
                <!-- Usamos id "cpersona" en el select para el formulario de persona -->
                <select class="form-select" id="cpersona" name="cpersona" style="color: black;" required>
                  <option value="">Seleccione una opción</option>
                </select>
                <label for="cpersona">Contactabilidad:</label>
              </div>
            </div>

          </div>
        </div>
      </form>
      <!-- Formulario Empresa -->
      <form action="" id="formEmpresa" style="display: none;">
        <div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <div class="form-floating">
                <input type="text" name="ruc" class="form-control" placeholder="rucdelaempresa" minlength="11" maxlength="11" required>
                <label for="ruc">RUC</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" name="nomcomercial" class="form-control" placeholder="nomcomercial" minlength="5" maxlength="100" required>
                <label for="nomcomercial">Nombre Comercial</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" name="razonsocial" class="form-control" placeholder="razonsocia0l" minlength="5" maxlength="100" required>
                <label for="razonsocial">Razón Social</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" name="telempresa" class="form-control" placeholder="telempresa" minlength="9" maxlength="9">
                <label for="telempresa">Teléfono</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="email" name="correoemp" class="form-control" placeholder="coreoemp" minlength="10" maxlength="100">
                <label for="correoEmp">Correo</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <!-- Usamos id "cempresa" para el formulario de empresa -->
                <select class="form-select" id="cempresa" name="cempresa" style="color: black;" required>
                  <option value="">Seleccione una opción</option>
                </select>
                <label for="cempresa">Contactabilidad:</label>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="card-footer text-end">
      <button type="button" class="btn btn-secondary">Cancelar</button>
      <button type="button" id="btnRegistrar" class="btn btn-success">Aceptar</button>
    </div>
  </div>
</div>

</div>
<!-- Formulario Persona -->
</div>
</div>
</div>

</body>

</html>


<script>
  // Función para mostrar/ocultar formularios según el tipo de cliente
  function mostrarFormulario(tipo) {
    let formPersona = document.getElementById("formPersona");
    let formEmpresa = document.getElementById("formEmpresa");
    if (tipo === "persona") {
      formPersona.style.display = "block";
      formEmpresa.style.display = "none";
    } else {
      formPersona.style.display = "none";
      formEmpresa.style.display = "block";
    }
  }

  // Función para mostrar notificaciones usando tu SweetAlert personalizada
  // (ya la tienes definida como showToast)

  // Función de validación que recibe el formulario activo
  function validarFormulario(formulario) {
    let esValido = true;

    // Validaciones para el formulario de persona
    if (formulario.id === "formPersona") {
      // Verificar campos obligatorios
      if (!document.querySelector('#cpersona').value.trim()) {
        esValido = false;
        showToast('El campo "Contactabilidad" es obligatorio', 'ERROR', 3000);
      }
      if (!document.querySelector('#apellidos').value.trim()) {
        esValido = false;
        showToast('El campo "Apellidos" es obligatorio', 'ERROR', 3000);
      }
      if (!document.querySelector('#nombres').value.trim()) {
        esValido = false;
        showToast('El campo "Nombres" es obligatorio', 'ERROR', 3000);
      }
      if (!document.querySelector('#tipodoc').value.trim()) {
        esValido = false;
        showToast('El campo "Tipo de documento" es obligatorio', 'ERROR', 3000);
      }
      if (!document.querySelector('#numdoc').value.trim()) {
        esValido = false;
        showToast('El campo "Número de documento" es obligatorio', 'ERROR', 3000);
      }

      // Restricción según el tipo de documento
      let tipodoc = document.querySelector('#tipodoc').value.trim();
      let numdoc = document.querySelector('#numdoc').value.trim();
      if (tipodoc === "DNI") {
        if (!/^\d{8}$/.test(numdoc)) {
          esValido = false;
          showToast('El DNI debe tener exactamente 8 dígitos', 'ERROR', 3000);
        }
      } else if (tipodoc === "Pasaporte" || tipodoc === "cde") {
        if (numdoc.length < 9) {
          esValido = false;
          showToast('El Número de documento para Pasaporte/Carnet de extranjería debe tener al menos 9 caracteres', 'ERROR', 3000);
        }
      }

      // Validación de teléfono principal (si tiene valor)
      let telprincipal = document.querySelector('#telprincipal').value.trim();
      if (telprincipal && !/^[9]\d{8}$/.test(telprincipal)) {
        esValido = false;
        showToast('El Tel. principal debe tener 9 dígitos y comenzar con 9', 'ERROR', 3000);
      }

      // Validación de teléfono alternativo (si tiene valor)
      let telalternativo = document.querySelector('#telalternativo').value.trim();
      if (telalternativo && !/^[9]\d{8}$/.test(telalternativo)) {
        esValido = false;
        showToast('El Tel. alternativo debe tener 9 dígitos y comenzar con 9', 'ERROR', 3000);
      }

      // Validación de correo (si tiene valor)
      let correo = document.querySelector('#correo').value.trim();
      if (correo && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
        esValido = false;
        showToast('El correo electrónico es inválido', 'ERROR', 3000);
      }
    }
    // Validaciones para el formulario de empresa
    else if (formulario.id === "formEmpresa") {
      if (!document.querySelector('[name="razonsocial"]').value.trim()) {
        esValido = false;
        showToast('El campo "Razón Social" es obligatorio', 'ERROR', 3000);
      }
      if (!document.querySelector('#cempresa').value.trim()) {
        esValido = false;
        showToast('El campo "Contactabilidad" es obligatorio', 'ERROR', 3000);
      }
      if (!document.querySelector('[name="nomcomercial"]').value.trim()) {
        esValido = false;
        showToast('El campo "Nombre Comercial" es obligatorio', 'ERROR', 3000);
      }
      if (!document.querySelector('[name="ruc"]').value.trim()) {
        esValido = false;
        showToast('El campo "RUC" es obligatorio', 'ERROR', 3000);
      }

      // Restricción para el RUC: 11 dígitos y comenzar con 10 o 20
      let ruc = document.querySelector('[name="ruc"]').value.trim();
      if (!/^(10|20)\d{9}$/.test(ruc)) {
        esValido = false;
        showToast('El RUC debe tener 11 dígitos y comenzar con 10 o 20', 'ERROR', 3000);
      }

      // Validación de correo (si tiene valor)
      let correoEmp = document.querySelector('[name="correoemp"]').value.trim();
      if (correoEmp && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correoEmp)) {
        esValido = false;
        showToast('El correo electrónico es inválido', 'ERROR', 3000);
      }

      // Validación de teléfono (si tiene valor) para la empresa
      let telempresa = document.querySelector('[name="telempresa"]').value.trim();
      if (telempresa && !/^[9]\d{8}$/.test(telempresa)) {
        esValido = false;
        showToast('El Teléfono debe tener 9 dígitos y comenzar con 9', 'ERROR', 3000);
      }
    }
    return esValido;
  }


  // Función para registrar el cliente en el servidor
  async function registrarCliente(datos, tipo) {
    // Mostrar confirmación con SweetAlert (función ask que ya tienes)
    const confirmacion = await ask("¿Estás seguro de registrar este cliente?", "Registro de Cliente");
    if (!confirmacion) {
      showToast('Registro cancelado.', 'WARNING', 3000);
      return;
    }

    // URL del controlador en el servidor (reemplaza con tu ruta real)
    const url = 'http://localhost/fix360/app/controllers/Cliente.controller.php';

    // Agregar el tipo al objeto de datos
    const clienteData = {
      tipo,
      ...datos
    };

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(clienteData)
      });
      const data = await response.json();
      if (data.rows > 0) {
        showToast('Cliente registrado exitosamente.', 'SUCCESS', 3000);
        // Opcional: limpiar los campos o redirigir a otra página
      } else {
        showToast('Hubo un error al registrar al cliente. Intenta nuevamente.', 'ERROR', 3000);
      }
    } catch (error) {
      console.error('Error al registrar el cliente:', error);
      showToast('Error al realizar la solicitud. Intenta nuevamente.', 'ERROR', 3000);
    }
  }

  // Asignar el event listener al botón "Aceptar"
  document.addEventListener("DOMContentLoaded", function() {
    document.querySelector("#btnRegistrar").addEventListener("click", async (e) => {
      e.preventDefault();

      // Determinar qué formulario está visible
      let formPersona = document.getElementById('formPersona');
      let formEmpresa = document.getElementById('formEmpresa');
      let formularioVisible = (formPersona.style.display === 'block') ? formPersona : formEmpresa;

      // Validar el formulario visible
      if (!validarFormulario(formularioVisible)) return;

      // Construir el objeto de datos según el formulario activo
      let datosCliente = {};
      if (formularioVisible.id === "formPersona") {
        datosCliente = {
          nombres: document.querySelector('#nombres').value.trim(),
          apellidos: document.querySelector('#apellidos').value.trim(),
          tipodoc: document.querySelector('#tipodoc').value.trim(),
          numdoc: document.querySelector('#numdoc').value.trim(),
          direccion: document.querySelector('#direccion').value.trim(),
          correo: document.querySelector('#correo').value.trim(),
          telprincipal: document.querySelector('#telprincipal').value.trim(),
          telalternativo: document.querySelector('#telalternativo').value.trim(),
          idcontactabilidad: document.querySelector('#cpersona').value.trim()
        };
        await registrarCliente(datosCliente, 'persona');
      } else {
        datosCliente = {
          ruc: document.querySelector('[name="ruc"]').value.trim(),
          nomcomercial: document.querySelector('[name="nomcomercial"]').value.trim(),
          razonsocial: document.querySelector('[name="razonsocial"]').value.trim(),
          telefono: document.querySelector('[name="telempresa"]').value.trim(),
          correo: document.querySelector('[name="correoemp"]').value.trim(),
          idcontactabilidad: document.querySelector('#cempresa').value.trim()
        };
        await registrarCliente(datosCliente, 'empresa');
      }
    });
  });

  // Código para cargar las opciones de contactabilidad (sin cambios significativos)
  document.addEventListener("DOMContentLoaded", function() {
    fetch("http://localhost/fix360/app/controllers/Contactabilidad.controller.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "operation=getContactabilidad",
      })
      .then(response => response.json())
      .then(data => {
        console.log("Respuesta del servidor:", data);
        if (!Array.isArray(data)) {
          console.error("Error: La respuesta del servidor no es un array", data);
          return;
        }
        let selectPersona = document.getElementById("cpersona");
        let selectEmpresa = document.getElementById("cempresa");
        selectPersona.innerHTML = "<option value=''>Seleccione una opción</option>";
        selectEmpresa.innerHTML = "<option value=''>Seleccione una opción</option>";
        data.forEach(item => {
          let option1 = document.createElement("option");
          let option2 = document.createElement("option");
          option1.value = item.idcontactabilidad;
          option1.textContent = item.contactabilidad;
          option2.value = item.idcontactabilidad;
          option2.textContent = item.contactabilidad;
          selectPersona.appendChild(option1);
          selectEmpresa.appendChild(option2);
        });
      })
      .catch(error => console.error("Error:", error));
  });
</script>

<?php

require_once "../../partials/_footer.php";

?>