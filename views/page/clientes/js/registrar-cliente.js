document.addEventListener("DOMContentLoaded", function () {
  const rucInput = document.getElementById('ruc');
  const numdocInput = document.getElementById('numdoc');
  const tipodocInput = document.getElementById('tipodoc');
  const nombresInput = document.getElementById('nombres');
  const apellidosInput = document.getElementById('apellidos');
  const formPersona = document.getElementById("formPersona");
  const formEmpresa = document.getElementById("formEmpresa");
  const btnRegistrar = document.querySelector("#btnRegistrar");

  // Consulta a API RUC al salir del campo
  if (rucInput) {
    rucInput.addEventListener('blur', async function () {
      const ruc = rucInput.value.trim();
      if (ruc.length === 11) {
        try {
          const response = await fetch(`http://localhost/fix360/app/api/consultaRuc.php?ruc=${encodeURIComponent(ruc)}`);
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

  // Consulta a API DNI al salir del campo (solo si es DNI)
  if (numdocInput && tipodocInput) {
    numdocInput.addEventListener('blur', async function () {
      if (tipodocInput.value === "DNI") {
        const dni = numdocInput.value.trim();
        if (dni.length === 8) {
          try {
            const response = await fetch(`http://localhost/fix360/app/api/consultaDni.php?dni=${encodeURIComponent(dni)}`);
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

  // Muestra/oculta formularios Persona/Empresa
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

  // Validación de campos
  function validarFormulario(formulario) {
  let esValido = true;

  if (formulario.id === "formPersona") {
    // Campos específicos de Persona:
    const cpersona      = document.querySelector('#cpersona')?.value.trim();
    const apellidos     = document.querySelector('#apellidos')?.value.trim();
    const nombres       = document.querySelector('#nombres')?.value.trim();
    const tipodoc       = document.querySelector('#tipodoc')?.value.trim();
    const numdoc        = document.querySelector('#numdoc')?.value.trim();
    const numrucPersona = document.querySelector('#numruc')?.value.trim();

    // 1) Contactabilidad
    if (!cpersona) {
      esValido = false;
      showToast('El campo "Contactabilidad" es obligatorio', 'ERROR', 1500);
    }
    // 2) Apellidos
    if (!apellidos) {
      esValido = false;
      showToast('El campo "Apellidos" es obligatorio', 'ERROR', 1500);
    }
    // 3) Nombres
    if (!nombres) {
      esValido = false;
      showToast('El campo "Nombres" es obligatorio', 'ERROR', 1500);
    }
    // 4) Tipo de documento
    if (!tipodoc) {
      esValido = false;
      showToast('El campo "Tipo de documento" es obligatorio', 'ERROR', 1500);
    }
    // 5) Número de documento (DNI/Pasaporte/cde)
    if (!numdoc) {
      esValido = false;
      showToast('El campo "Número de documento" es obligatorio', 'ERROR', 1500);
    } else {
      if (tipodoc === "DNI" && !/^\d{8}$/.test(numdoc)) {
        esValido = false;
        showToast('El DNI debe tener exactamente 8 dígitos', 'ERROR', 1500);
      }
      if ((tipodoc === "Pasaporte" || tipodoc === "cde") && numdoc.length < 9) {
        esValido = false;
        showToast('El documento (Pasaporte/Carnet) debe tener al menos 9 caracteres', 'ERROR', 1500);
      }
    }

    // 6) Validación de RUC (solo si el usuario escribió algo en #numruc y no está vacío)
    //    - Debe ser exactamente 11 dígitos, empezar por “10”
    if (numrucPersona) {
      if (!/^(?:10)\d{9}$/.test(numrucPersona)) {
        esValido = false;
        showToast('Para PERSONA, el RUC debe tener 11 dígitos y comenzar con 10', 'ERROR', 1500);
      }
    }

    // 7) Validación de teléfonos (si los ingresa, deben empezar con 9 y tener 9 dígitos)
    const telprincipal  = document.querySelector('#telprincipal')?.value.trim();
    const telalternativo = document.querySelector('#telalternativo')?.value.trim();
    if (telprincipal && !/^[9]\d{8}$/.test(telprincipal)) {
      esValido = false;
      showToast('El Tel. principal debe tener 9 dígitos y comenzar con 9', 'ERROR', 1500);
    }
    if (telalternativo && !/^[9]\d{8}$/.test(telalternativo)) {
      esValido = false;
      showToast('El Tel. alternativo debe tener 9 dígitos y comenzar con 9', 'ERROR', 1500);
    }
    // 8) Validación de correo (si lo ingresa, que tenga formato válido)
    const correoP = document.querySelector('#correo')?.value.trim();
    if (correoP && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correoP)) {
      esValido = false;
      showToast('El correo electrónico es inválido', 'ERROR', 1500);
    }

  } else if (formulario.id === "formEmpresa") {
    // Campos específicos de Empresa:
    const rucEmpresa    = document.querySelector('#ruc')?.value.trim();
    const nomcomercial  = document.querySelector('#nomcomercial')?.value.trim();
    const razonsocial   = document.querySelector('#razonsocial')?.value.trim();
    const correoE       = document.querySelector('#correoemp')?.value.trim();
    const telempresa    = document.querySelector('#telempresa')?.value.trim();
    const cempresa      = document.querySelector('#cempresa')?.value.trim();

    // 1) RUC (obligatorio y debe empezar con “20”)
    if (!rucEmpresa) {
      esValido = false;
      showToast('El campo "RUC" es obligatorio', 'ERROR', 1500);
    } else {
      if (!/^(?:20)\d{9}$/.test(rucEmpresa)) {
        esValido = false;
        showToast('Para EMPRESA, el RUC debe tener 11 dígitos y comenzar con 20', 'ERROR', 1500);
      }
    }
    // 2) Razón Social obligatorio
    if (!razonsocial) {
      esValido = false;
      showToast('El campo "Razón Social" es obligatorio', 'ERROR', 1500);
    }
    // 3) Nombre Comercial obligatorio
    if (!nomcomercial) {
      esValido = false;
      showToast('El campo "Nombre Comercial" es obligatorio', 'ERROR', 1500);
    }
    // 4) Contactabilidad obligatorio
    if (!cempresa) {
      esValido = false;
      showToast('El campo "Contactabilidad" es obligatorio', 'ERROR', 1500);
    }
    // 5) Validación de correo (si lo ingresa)
    if (correoE && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correoE)) {
      esValido = false;
      showToast('El correo electrónico es inválido', 'ERROR', 1500);
    }
    // 6) Teléfono de empresa (si lo ingresa, iniciar en 9 y 9 dígitos)
    if (telempresa && !/^[9]\d{8}$/.test(telempresa)) {
      esValido = false;
      showToast('El Teléfono de empresa debe tener 9 dígitos y comenzar con 9', 'ERROR', 1500);
    }
  }

  return esValido;
}

  // Función para registrar el cliente en el servidor
  async function registrarCliente(datos, tipo) {
    // Pregunta de confirmación (opcional)
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

    if (resData.status === true && resData.idcliente) {
      // 1) Rellenamos el formulario padre (propietario) con el nuevo cliente
      document.getElementById("hiddenIdPropietario").value = resData.idcliente;
      document.getElementById("propietario").value = resData.nombre;

      // 2) Cerramos el modal de registro
      bootstrap.Modal.getInstance(document.getElementById('modalRegistrarCliente')).hide();

      // 3) También cerramos el modal de búsqueda (si está abierto)
      const busquedaModal = document.getElementById('miModal');
      if (busquedaModal) {
        bootstrap.Modal.getInstance(busquedaModal)?.hide();
      }

      // 4) Opcional: recarga vehículos del propietario
      if (typeof cargarVehiculos === 'function') {
        setTimeout(() => cargarVehiculos(), 300);
      }
    } else {
      showToast(resData.message || 'Error al registrar el cliente.', 'ERROR', 1500);
    }
  } catch (error) {
    console.error('Error al registrar el cliente:', error);
    showToast('Error al realizar la solicitud. Intenta nuevamente.', 'ERROR', 1500);
  }
  }

  // Listener para el botón "Aceptar" del modal de registrar cliente
  if (btnRegistrar) {
    btnRegistrar.addEventListener("click", async (e) => {
      e.preventDefault();
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

  // Cargar opciones de contactabilidad
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