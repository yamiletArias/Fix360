
  document.addEventListener("DOMContentLoaded", function() {
  // Cacheo de selectores
  const fplacaInput = document.getElementById('fplaca');
  // ... (otros selectores ya existentes)

  // Consulta a la API de placas al salir del campo (blur)
  if (fplacaInput) {
    fplacaInput.addEventListener('blur', async function() {
      const placa = fplacaInput.value.trim().toUpperCase(); // Convertir a mayúsculas, si es necesario
      // Valida, por ejemplo, que la placa tenga 6 caracteres (ajusta según tu caso)
      if (placa.length === 6) {
        try {
          // Llamada al endpoint intermedio que creaste (consultaPlaca.php)
          const response = await fetch(`http://localhost/fix360/app/api/consultaPlaca.php?placa=${encodeURIComponent(placa)}`);
          const data = await response.json();
          // Supongamos que la API retorna datos como "marca", "modelo", "anio", etc.
          if (data && data.marca) {
            // Por ejemplo, asigna el valor a un select o input correspondiente
            const marcavSelect = document.getElementById("marcav");
            if (marcavSelect) {
              marcavSelect.value = data.marca; // Asigna la marca
            }
            // También podrías completar otros campos como modelo o año:
            const modeloSelect = document.getElementById("modelo");
            if (modeloSelect && data.modelo) {
              modeloSelect.value = data.modelo;
            }
            // Si la API indica que la placa ya existe, podrías mostrar un showToast:
            if (data.duplicado) {
              showToast('La placa ya está registrada.', 'WARNING', 3000);
            }
          } else {
            // Si la API no retorna datos válidos, puedes limpiar o notificar
            showToast('No se encontró información para esa placa.', 'INFO', 3000);
          }
        } catch (error) {
          console.error("Error al consultar la API de placas:", error);
          showToast('Error al consultar información de la placa.', 'ERROR', 3000);
        }
      }
    });
  }

  // ... (resto de tu código, por ejemplo, listeners para DNI, RUC, registro del vehículo, etc.)
});

fetch("http://localhost/fix360/app/controllers/Tcombustible.controller.php")
.then(res => res.json())
.then(data => {
  const tcombustibleSelect = document.getElementById("ftcombustible");
  tcombustibleSelect.innerHTML = `<option value="">Seleccione una opción</option>`;
  data.forEach(item => {
    const opt = document.createElement("option");
    opt.value   = item.idtcombustible;
    opt.textContent = item.tcombustible;
    tcombustibleSelect.appendChild(opt);
  });
})

  // Función para actualizar las opciones del select según el tipo de propietario (persona/empresa)
  function actualizarOpciones() {
    const select = document.getElementById("selectMetodo");
    const personaSeleccionada = document.getElementById("rbtnpersona").checked;
    // Limpiar opciones actuales
    select.innerHTML = "";
    if (personaSeleccionada) {
      select.innerHTML += `<option value="dni">DNI</option>`;
      select.innerHTML += `<option value="nombre">Nombre</option>`;
    } else {
      select.innerHTML += `<option value="ruc">RUC</option>`;
      select.innerHTML += `<option value="razonsocial">Razón Social</option>`;
    }
  }

  // Función para buscar propietarios en el modal y llenar la tabla de resultados
  function buscarPropietario() {
    const tipo = document.getElementById("rbtnpersona").checked ? "persona" : "empresa";
    const metodo = document.getElementById("selectMetodo").value;
    const valor = document.getElementById("vbuscado").value.trim();

    // Si no se ingresa valor, limpia la tabla
    if (valor === "") {
      document.querySelector("#tabla-resultado tbody").innerHTML = "";
      return;
    }

    // Construir la URL de la consulta (ajusta la ruta según tu estructura)
    const url = `http://localhost/fix360/app/controllers/Propietario.controller.php?tipo=${encodeURIComponent(tipo)}&metodo=${encodeURIComponent(metodo)}&valor=${encodeURIComponent(valor)}`;

    fetch(url)
      .then(response => response.json())
      .then(data => {
        const tbody = document.querySelector("#tabla-resultado tbody");
        tbody.innerHTML = "";
        // Crear filas para cada resultado
        data.forEach((item, index) => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
          <td>${index + 1}</td>
          <td>${item.nombre}</td>
          <td>${item.documento}</td>
          <td>
            <button type="button" class="btn btn-success btn-sm btn-confirmar" data-id="${item.idcliente}" data-bs-dismiss="modal">
  <i class="fa-solid fa-circle-check"></i>
</button>
          </td>
        `;
          tbody.appendChild(tr);
        });
      })
      .catch(error => console.error("Error en búsqueda:", error));
  }

  // Cuando se hace clic en el botón "Confirmar" del modal
  document.querySelector("#tabla-resultado").addEventListener("click", function(e) {
    if (e.target.closest(".btn-confirmar")) {
      const btn = e.target.closest(".btn-confirmar");
      const idcliente = btn.getAttribute("data-id");

      // Obtener el nombre desde la fila (segunda columna)
      const fila = btn.closest("tr");
      const nombre = fila.cells[1].textContent;

      // Guardar el id y el nombre en los inputs correspondientes
      document.getElementById("hiddenIdCliente").value = idcliente;
      document.getElementById("floatingInput").value = nombre;

      // Cerrar el modal después de un pequeño delay
      setTimeout(() => {
        const modalEl = document.getElementById("miModal");
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.hide();
      }, 100);
    }
  });

  // Escuchar el input de búsqueda para disparar la consulta (puedes agregar debounce para evitar llamadas excesivas)
  document.getElementById("vbuscado").addEventListener("keyup", buscarPropietario);

  // Actualizar opciones del select y disparar búsqueda al cambiar los radio buttons
  document.getElementById("rbtnpersona").addEventListener("click", function() {
    actualizarOpciones();
    buscarPropietario();
  });
  document.getElementById("rbtnempresa").addEventListener("click", function() {
    actualizarOpciones();
    buscarPropietario();
  });

  // Inicializar las opciones del select al cargar el modal
  document.addEventListener("DOMContentLoaded", actualizarOpciones);

  // --- Registro de Vehículo y Propietario ---
  // Escuchar el botón "Aceptar" del formulario de vehículo
  document.getElementById("btnRegistrarVehiculo").addEventListener("click", function(e) {
    e.preventDefault();

    // Recopilar los datos del formulario de vehículo
    const data = {
      idmodelo: document.getElementById("modelo").value,
      placa: document.getElementById("fplaca").value.trim(),
      anio: document.getElementById("fanio").value.trim(),
      numserie: document.getElementById("fnumserie").value.trim(),
      color: document.getElementById("fcolor").value.trim(),
      tipocombustible: document.getElementById("ftcombustible").value,
      // Enviar directamente el idcliente obtenido del modal
      idcliente: document.getElementById("hiddenIdCliente").value
    };

    // Validar que se tenga seleccionado un propietario
    if (!data.idmodelo || !data.placa || !data.anio || !data.numserie || !data.idcliente) {
      alert("Por favor, completa todos los campos obligatorios y selecciona un propietario.");
      return;
    }

    // Enviar la información al controlador (ajusta la URL según tu estructura)
    fetch("http://localhost/fix360/app/controllers/Vehiculo.controller.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(resp => {
        if (resp.rows > 0) {
          alert("Registro exitoso.");
          // Aquí puedes limpiar el formulario o redirigir
        } else {
          console.log("Error en el registro.");
          err => {
            console.error("Error en la solicitud:", err);
          }

        }
      })
      .catch(err => {
        console.error("Error en la solicitud:", err);
      });

  });



  document.addEventListener("DOMContentLoaded", function() {
    const tipovSelect = document.getElementById("tipov");
    const marcavSelect = document.getElementById("marcav");
    const modeloSelect = document.getElementById("modelo");

    // Cargar select de tipo de vehículo
    fetch("http://localhost/fix360/app/controllers/Tipov.controller.php")
      .then(response => response.json())
      .then(data => {
        // Asumiendo que cada registro trae campos 'idtipov' y 'tipov'
        data.forEach(item => {
          const option = document.createElement("option");
          option.value = item.idtipov; // Ajusta según el nombre del campo real
          option.textContent = item.tipov; // Ajusta según el nombre del campo real
          tipovSelect.appendChild(option);
        });
      })
      .catch(error => console.error("Error al cargar tipos de vehículo:", error));

    // Cargar select de marcas
    fetch("http://localhost/fix360/app/controllers/Marca.controller.php?task=getAllMarcaVehiculo")
      .then(response => response.json())
      .then(data => {
        // Asumiendo que cada registro trae campos 'idmarca' y 'marca'
        data.forEach(item => {
          const option = document.createElement("option");
          option.value = item.idmarca; // Ajusta según el nombre del campo real
          option.textContent = item.nombre; // Ajusta según el nombre del campo real
          marcavSelect.appendChild(option);
        });
      })
      .catch(error => console.error("Error al cargar marcas:", error));

    // Función para cargar modelos basado en tipo y marca seleccionados
    function cargarModelos() {
      const idtipov = tipovSelect.value;
      const idmarca = marcavSelect.value;

      // Limpiar el select de modelos y agregar opción por defecto
      modeloSelect.innerHTML = '<option value="">Seleccione una opcion</option>';

      // Solo llamar al controller si ambos selects tienen un valor
      if (idtipov && idmarca) {
        fetch(`http://localhost/fix360/app/controllers/Modelo.controller.php?idtipov=${encodeURIComponent(idtipov)}&idmarca=${encodeURIComponent(idmarca)}`)
          .then(response => response.json())
          .then(data => {
            // Asumiendo que cada registro trae campos 'idmodelo' y 'modelo'
            data.forEach(item => {
              const option = document.createElement("option");
              option.value = item.idmodelo;
              option.textContent = item.modelo;
              modeloSelect.appendChild(option);
            });
          })
          .catch(error => console.error("Error al cargar modelos:", error));
      }
    }

    // Agregar event listeners para que cuando cambie el tipo o la marca se actualice el select de modelos
    tipovSelect.addEventListener("change", cargarModelos);
    marcavSelect.addEventListener("change", cargarModelos);
  });
