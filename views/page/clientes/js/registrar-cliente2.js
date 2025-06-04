// archivo: views/page/clientes/js/registrar-cliente.js
document.addEventListener("DOMContentLoaded", function () {
  const rucInput = document.getElementById('ruc');
  const numdocInput = document.getElementById('numdoc');
  const tipodocInput = document.getElementById('tipodoc');
  const nombresInput = document.getElementById('nombres');
  const apellidosInput = document.getElementById('apellidos');
  const formPersona = document.getElementById("formPersona");
  const formEmpresa = document.getElementById("formEmpresa");
  const btnRegistrar = document.querySelector("#btnRegistrar");

  // ------------- 1) Consulta API RUC al blur -------------
  if (rucInput) {
    rucInput.addEventListener('blur', async function () {
      const ruc = rucInput.value.trim();
      if (ruc.length === 11) {
        try {
          const response = await fetch(`http://localhost/Fix360/app/api/consultaRuc.php?ruc=${encodeURIComponent(ruc)}`);
          const data = await response.json();
          const inputRazonSocial = document.querySelector('#razonsocial');
          if (data && data.razonSocial && inputRazonSocial) {
            inputRazonSocial.value = data.razonSocial;
            inputRazonSocial.disabled = true;
          } else if (inputRazonSocial) {
            inputRazonSocial.disabled = false;
          }
        } catch (error) {
          console.error("Error al consultar API de RUC:", error);
          const inputRazonSocial = document.querySelector('#razonsocial');
          if (inputRazonSocial) inputRazonSocial.disabled = false;
        }
      }
    });
  }

  // ------------- 2) Consulta API DNI al blur -------------
  if (numdocInput && tipodocInput) {
    numdocInput.addEventListener('blur', async function () {
      if (tipodocInput.value === "DNI") {
        const dni = numdocInput.value.trim();
        if (dni.length === 8) {
          try {
            const response = await fetch(`http://localhost/Fix360/app/api/consultaDni.php?dni=${encodeURIComponent(dni)}`);
            const data = await response.json();
            if (data && data.nombres) {
              nombresInput.value = data.nombres;
              apellidosInput.value = `${data.apellidoPaterno} ${data.apellidoMaterno}`;
              nombresInput.disabled = true;
              apellidosInput.disabled = true;
            } else {
              nombresInput.disabled = false;
              apellidosInput.disabled = false;
            }
          } catch (error) {
            console.error("Error al consultar la API de DNI:", error);
            nombresInput.disabled = false;
            apellidosInput.disabled = false;
          }
        }
      }
    });
  }

  // ------------- 3) Mostrar/ocultar formularios Persona/Empresa -------------
  window.mostrarFormulario = function (tipo) {
    if (tipo === "persona") {
      formPersona.style.display = "block";
      formEmpresa.style.display = "none";
      numdocInput && numdocInput.focus();
    } else {
      formPersona.style.display = "none";
      formEmpresa.style.display = "block";
      rucInput && rucInput.focus();
    }
  };

  // ------------- 4) Validación de formularios -------------
  function validarFormulario(formulario) {
    let esValido = true;

    if (formulario.id === "formPersona") {
      const cpersona = document.querySelector('#cpersona')?.value.trim();
      const apellidos = document.querySelector('#apellidos')?.value.trim();
      const nombres = document.querySelector('#nombres')?.value.trim();
      const tipodoc = tipodocInput.value.trim();
      const numdoc = numdocInput.value.trim();

      if (!cpersona) {
        esValido = false;
        showToast('El campo "Contactabilidad" es obligatorio', 'ERROR', 1500);
      }
      if (!apellidos) {
        esValido = false;
        showToast('El campo "Apellidos" es obligatorio', 'ERROR', 1500);
      }
      if (!nombres) {
        esValido = false;
        showToast('El campo "Nombres" es obligatorio', 'ERROR', 1500);
      }
      if (!tipodoc) {
        esValido = false;
        showToast('El campo "Tipo de documento" es obligatorio', 'ERROR', 1500);
      }
      if (!numdoc) {
        esValido = false;
        showToast('El campo "Número de documento" es obligatorio', 'ERROR', 1500);
      }

      const numruc = document.querySelector('#numruc')?.value.trim();
      if (numruc && !/^10\d{9}$/.test(numruc)) {
        esValido = false;
        showToast('El N° de RUC debe comenzar con 10 y tener 11 dígitos', 'ERROR', 1500);
      }

      if (tipodoc === "DNI") {
        if (!/^\d{8}$/.test(numdoc)) {
          esValido = false;
          showToast('El DNI debe tener exactamente 8 dígitos', 'ERROR', 1500);
        }
      } else if (tipodoc === "Pasaporte" || tipodoc === "cde") {
        if (numdoc.length < 9) {
          esValido = false;
          showToast('El Número de documento para Pasaporte/Carnet de extranjería debe tener al menos 9 caracteres', 'ERROR', 1500);
        }
      }

      const telprincipal = document.querySelector('#telprincipal')?.value.trim();
      if (telprincipal && !/^[9]\d{8}$/.test(telprincipal)) {
        esValido = false;
        showToast('El Tel. principal debe tener 9 dígitos y comenzar con 9', 'ERROR', 1500);
      }

      const telalternativo = document.querySelector('#telalternativo')?.value.trim();
      if (telalternativo && !/^[9]\d{8}$/.test(telalternativo)) {
        esValido = false;
        showToast('El Tel. alternativo debe tener 9 dígitos y comenzar con 9', 'ERROR', 1500);
      }

      const correo = document.querySelector('#correo')?.value.trim();
      if (correo && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
        esValido = false;
        showToast('El correo electrónico es inválido', 'ERROR', 1500);
      }

    } else if (formulario.id === "formEmpresa") {
      const razonsocial = document.querySelector('#razonsocial')?.value.trim();
      const cempresa = document.querySelector('#cempresa')?.value.trim();
      const nomcomercial = document.querySelector('#nomcomercial')?.value.trim();
      const rucVal = document.querySelector('#ruc')?.value.trim();

      if (!razonsocial) {
        esValido = false;
        showToast('El campo "Razón Social" es obligatorio', 'ERROR', 1500);
      }
      if (!cempresa) {
        esValido = false;
        showToast('El campo "Contactabilidad" es obligatorio', 'ERROR', 1500);
      }
      if (!nomcomercial) {
        esValido = false;
        showToast('El campo "Nombre Comercial" es obligatorio', 'ERROR', 1500);
      }
      if (!rucVal) {
        esValido = false;
        showToast('El campo "RUC" es obligatorio', 'ERROR', 1500);
      }
      if (!/^(20)\d{9}$/.test(rucVal)) {
        esValido = false;
        showToast('El RUC debe tener 11 dígitos y comenzar con 20', 'ERROR', 1500);
      }

      const correoEmp = document.querySelector('#correoemp')?.value.trim();
      if (correoEmp && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correoEmp)) {
        esValido = false;
        showToast('El correo electrónico es inválido', 'ERROR', 1500);
      }

      const telempresa = document.querySelector('#telempresa')?.value.trim();
      if (telempresa && !/^[9]\d{8}$/.test(telempresa)) {
        esValido = false;
        showToast('El Teléfono debe tener 9 dígitos y comenzar con 9', 'ERROR', 1500);
      }
    }

    return esValido;
  }

  // ------------- 5) Función para registrar el cliente -------------
  async function registrarCliente(datos, tipo) {
    const confirmacion = await ask("¿Estás seguro de registrar este cliente?", "Registro de Cliente");
    if (!confirmacion) {
      showToast('Registro cancelado.', 'WARNING', 1500);
      return;
    }

    const url = 'http://localhost/fix360/app/controllers/Cliente.controller.php';
    const clienteData = { tipo, ...datos };

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(clienteData)
      });
      const resData = await response.json();

      if (resData.rows > 0) {
        showToast('Cliente registrado exitosamente.', 'SUCCESS', 1000);

        // Asigna los datos al formulario de Propietario
        const campoHiddenProp = document.getElementById('hiddenIdPropietario');
        const inputPropietario = document.getElementById('propietario');
        if (campoHiddenProp && inputPropietario) {
          campoHiddenProp.value = resData.idcliente;
          inputPropietario.value = resData.propietario;
        }

        // Cierra el modal de "Registrar Cliente"
        const modalRegistrarCliente = bootstrap.Modal.getInstance(document.getElementById('modalRegistrarCliente'));
        modalRegistrarCliente.hide();

        // Muestra un mensaje exitoso
        showToast('Cliente asignado como Propietario.', 'SUCCESS', 1500);
      } else {
        showToast('Hubo un error al registrar al cliente. Intenta nuevamente.', 'ERROR', 1500);
      }
    } catch (error) {
      console.error('Error al registrar el cliente:', error);
      showToast('Error al realizar la solicitud. Intenta nuevamente.', 'ERROR', 1500);
    }
  }

  // ------------- 6) Listener del botón “Aceptar” -------------
  if (btnRegistrar) {
    btnRegistrar.addEventListener("click", async (e) => {
      e.preventDefault(); // Evita el comportamiento por defecto (submit)
      const formularioVisible = (formPersona.style.display === 'block') ? formPersona : formEmpresa;
      if (!validarFormulario(formularioVisible)) return;

      let datosCliente = {};
      if (formularioVisible.id === "formPersona") {
        datosCliente = {
          nombres: nombresInput.value.trim(),
          apellidos: apellidosInput.value.trim(),
          tipodoc: tipodocInput.value.trim(),
          numdoc: numdocInput.value.trim(),
          numruc: document.querySelector('#numruc')?.value.trim(),
          direccion: document.querySelector('#direccion')?.value.trim(),
          correo: document.querySelector('#correo')?.value.trim(),
          telprincipal: document.querySelector('#telprincipal')?.value.trim(),
          telalternativo: document.querySelector('#telalternativo')?.value.trim(),
          idcontactabilidad: document.querySelector('#cpersona')?.value.trim()
        };
        await registrarCliente(datosCliente, 'persona');
      } else {
        datosCliente = {
          ruc: document.querySelector('#ruc')?.value.trim(),
          nomcomercial: document.querySelector('#nomcomercial')?.value.trim(),
          razonsocial: document.querySelector('#razonsocial')?.value.trim(),
          telefono: document.querySelector('#telempresa')?.value.trim(),
          correo: document.querySelector('#correoemp')?.value.trim(),
          idcontactabilidad: document.querySelector('#cempresa')?.value.trim()
        };
        await registrarCliente(datosCliente, 'empresa');
      }
    });
  }

  // ------------- 7) Cargar opciones de Contactabilidad -------------
  fetch("http://localhost/fix360/app/controllers/Contactabilidad.controller.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "operation=getContactabilidad",
  })
    .then(response => response.json())
    .then(data => {
      if (!Array.isArray(data)) {
        console.error("Error: La respuesta del servidor no es un array", data);
        return;
      }
      const selectPersona = document.getElementById("cpersona");
      const selectEmpresa = document.getElementById("cempresa");
      if (selectPersona) selectPersona.innerHTML = "<option value=''>Seleccione una opción</option>";
      if (selectEmpresa) selectEmpresa.innerHTML = "<option value=''>Seleccione una opción</option>";
      data.forEach(item => {
        const option1 = document.createElement("option");
        const option2 = document.createElement("option");
        option1.value = item.idcontactabilidad;
        option1.textContent = item.contactabilidad;
        option2.value = item.idcontactabilidad;
        option2.textContent = item.contactabilidad;
        if (selectPersona) selectPersona.appendChild(option1);
        if (selectEmpresa) selectEmpresa.appendChild(option2);
      });
    })
    .catch(error => console.error("Error:", error));
});