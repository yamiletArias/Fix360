(function() {
  // Espera a que el DOM esté listo
  document.addEventListener("DOMContentLoaded", function() {
    // Elementos del formulario y modales (igual que el original)
    const tipovSelect        = document.getElementById("tipov");
    const marcavSelect       = document.getElementById("marcav");
    const modeloSelect       = document.getElementById("modelo");
    const tcombustibleSelect = document.getElementById("ftcombustible");
    const hiddenIdCliente    = document.getElementById("hiddenIdCliente");
    const formVehiculo       = document.getElementById("FormVehiculo");
    const btnRegistrar       = document.getElementById("btnRegistrarVehiculo");
    const modalAsignar       = document.getElementById("ModalAsignarVehiculo");
    const modalVehiculos     = document.getElementById("ModalVehiculos"); // Se necesita para el nuevo listener

    // Si faltan elementos clave, salimos (igual que el original)
    if (!modalAsignar || !modalVehiculos) {
        console.error("Error: No se encontraron los elementos de los modales requeridos.");
        return;
    }

    // Carga inicial de selects (tipos, combustible, marcas) - SIN CAMBIOS
    fetch("http://localhost/fix360/app/controllers/Tipov.controller.php")
      .then(res => res.json())
      .then(data => {
        tipovSelect.innerHTML = `<option value="">Seleccione una opción</option>`;
        data.forEach(item => {
          const opt = document.createElement("option");
          opt.value = item.idtipov;
          opt.textContent = item.tipov;
          tipovSelect.appendChild(opt);
        });
      });

    fetch("http://localhost/fix360/app/controllers/Tcombustible.controller.php")
      .then(res => res.json())
      .then(data => {
        tcombustibleSelect.innerHTML = `<option value="">Seleccione una opción</option>`;
        data.forEach(item => {
          const opt = document.createElement("option");
          opt.value = item.idtcombustible;
          opt.textContent = item.tcombustible;
          tcombustibleSelect.appendChild(opt);
        });
      });

    fetch("http://localhost/fix360/app/controllers/Marca.controller.php?task=getAllMarcaVehiculo")
      .then(res => res.json())
      .then(data => {
        marcavSelect.innerHTML = `<option value="">Seleccione una opción</option>`;
        data.forEach(item => {
          const opt = document.createElement("option");
          opt.value = item.idmarca;
          opt.textContent = item.nombre;
          marcavSelect.appendChild(opt);
        });
      });

    // Carga de modelos al cambiar tipo o marca - SIN CAMBIOS
    function cargarModelos() {
      modeloSelect.innerHTML = `<option value="">Seleccione una opción</option>`;
      if (!tipovSelect.value || !marcavSelect.value) return;
      fetch(
        `http://localhost/fix360/app/controllers/Modelo.controller.php?idtipov=${encodeURIComponent(tipovSelect.value)}&idmarca=${encodeURIComponent(marcavSelect.value)}`
      )
      .then(res => res.json())
      .then(data => {
        data.forEach(item => {
          const opt = document.createElement("option");
          opt.value = item.idmodelo;
          opt.textContent = item.modelo;
          modeloSelect.appendChild(opt);
        });
      });
    }
    tipovSelect.addEventListener("change", cargarModelos);
    marcavSelect.addEventListener("change", cargarModelos);

    // Asignar datos al modal de asignar vehículo - SIN CAMBIOS
    modalAsignar.addEventListener("show.bs.modal", function(evt) {
      const button = evt.relatedTarget;
      hiddenIdCliente.value = button.getAttribute("data-idcliente") || "";
      document.getElementById("floatingInput").value = button.getAttribute("data-nombrecliente") || "";
    });

    // --- Listener ÚNICO y MEJORADO para mostrar vehículos ---
    // REEMPLAZA el bloque $("#ModalVehiculos").off().on(...) del código original.
    modalVehiculos.addEventListener("show.bs.modal", function(evt) {
        console.log("[DEBUG] Evento show.bs.modal para ModalVehiculos disparado."); // Log

        const button = evt.relatedTarget; // El botón que disparó el modal
        if (!button) {
            console.error("Error: No se pudo determinar el botón que abrió el modal de vehículos.");
             const tbody = modalVehiculos.querySelector("#tabla-vehiculos tbody");
             if(tbody) tbody.innerHTML = '<tr><td colspan="6">Error: No se pudo identificar el cliente.</td></tr>';
            return;
        }

        const id     = button.getAttribute("data-idcliente");
        const nombre = button.getAttribute("data-nombrecliente");

        const nombreClienteSpan = document.getElementById("nombreCliente");
        if (nombreClienteSpan) {
            nombreClienteSpan.textContent = nombre || 'Cliente Desconocido';
        } else {
             console.error("Error: Elemento con ID 'nombreCliente' no encontrado.");
        }

        if (id) {
             console.log(`[DEBUG] Llamando a cargarTablaVehiculos con idcliente: ${id}`); // Log
             cargarTablaVehiculos(id); // Llama a la función para cargar los datos
        } else {
             console.error("Error: El botón no tiene el atributo data-idcliente.");
             const tbody = modalVehiculos.querySelector("#tabla-vehiculos tbody");
             if(tbody) tbody.innerHTML = '<tr><td colspan="6">Error: Falta el ID del cliente.</td></tr>';
        }
    });

    // --- Función MEJORADA para poblar la tabla de vehículos ---
    // REEMPLAZA la función cargarTablaVehiculos original.
    function cargarTablaVehiculos(idcliente) {
      console.log(`[DEBUG] Dentro de cargarTablaVehiculos, iniciando fetch para id: ${idcliente}`); // Log
      const tbody = document.querySelector("#tabla-vehiculos tbody");
      if (!tbody) {
        console.error("Error: Elemento tbody de #tabla-vehiculos no encontrado.");
        return;
      }

      // Mostrar estado de carga
      tbody.innerHTML = '<tr><td colspan="6" class="text-center">Cargando vehículos...</td></tr>'; // Puedes añadir un icono si quieres

      fetch(
        `http://localhost/fix360/app/controllers/vehiculo.controller.php?task=getVehiculoByCliente&idcliente=${encodeURIComponent(idcliente)}`
      )
      .then(res => {
        if (!res.ok) {
          // Si la respuesta no es exitosa (ej. 404, 500), lanza un error
          throw new Error(`Error HTTP ${res.status} - ${res.statusText}`);
        }
        // Verificar si el Content-Type es JSON antes de intentar parsear
        const contentType = res.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error(`Respuesta inesperada del servidor: Se esperaba JSON pero se recibió ${contentType}`);
        }
        return res.json(); // Intenta parsear la respuesta como JSON
      })
      .then(data => {
        tbody.innerHTML = ""; // Limpiar la tabla (quita el mensaje de carga)

        if (data && Array.isArray(data) && data.length > 0) {
          data.forEach((item, i) => {
            const tr = document.createElement("tr");
            // Usar 'N/A' o similar si un campo viene vacío o null
            tr.innerHTML = `
              <td>${i + 1}</td>
              <td>${item.tipov || 'N/A'}</td>
              <td>${item.nombre || 'N/A'}</td>
              <td>${item.modelo || 'N/A'}</td>
              <td>${item.placa || 'N/A'}</td>
              <td>${item.color || 'N/A'}</td>
            `;
            tbody.appendChild(tr);
          });
        } else if (data && !Array.isArray(data)) {
            // Si la respuesta es JSON pero no un array (podría ser un objeto de error del backend)
            console.warn("Se recibió una respuesta JSON pero no era un array:", data);
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Respuesta inesperada del servidor.</td></tr>';
        }
         else {
          // Si no hay datos o la respuesta no es un array válido
          tbody.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron vehículos asignados a este cliente.</td></tr>';
        }
      })
      .catch(error => {
        // Captura errores de red o errores lanzados en .then()
        console.error("Error al cargar los vehículos:", error);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error al cargar los vehículos: ${error.message}.</td></tr>`;
      });
    }

    // Envío del formulario al hacer click en "Guardar" - SIN CAMBIOS
    btnRegistrar.addEventListener("click", async function(e) {
      e.preventDefault();
      const payload = {
        idmodelo:        modeloSelect.value,
        placa:           document.getElementById("fplaca").value.trim().toUpperCase(),
        anio:            document.getElementById("fanio").value.trim(),
        numserie:        document.getElementById("fnumserie").value.trim(),
        color:           document.getElementById("fcolor").value.trim(),
        vin:             document.getElementById("vin").value.trim(),
        numchasis:       document.getElementById("numchasis").value.trim(),
        tipocombustible: tcombustibleSelect.value,
        idcliente:       hiddenIdCliente.value
      };
      if (!payload.idmodelo || !payload.placa || !payload.anio || !payload.idcliente) {
        // Considera usar una librería de notificaciones o un modal en lugar de alert
        return alert("Completa todos los campos obligatorios (Marca, Tipo, Modelo, Placa, Año).");
      }
      try {
        const res  = await fetch("http://localhost/fix360/app/controllers/Vehiculo.controller.php", {
          method:  "POST",
          headers: { "Content-Type": "application/json" },
          body:    JSON.stringify(payload)
        });
        // Es buena práctica verificar si la respuesta fue exitosa antes de parsear JSON
        if (!res.ok) {
             const errorText = await res.text(); // Intenta obtener más detalles del error
             throw new Error(`Error del servidor: ${res.status} - ${res.statusText}. ${errorText}`);
        }
        const resp = await res.json();
        if (resp.rows > 0) { // Asumiendo que tu backend devuelve { rows: n } en caso de éxito
          bootstrap.Modal.getInstance(modalAsignar).hide();
          formVehiculo.reset(); // Limpia el formulario
          modeloSelect.innerHTML = `<option value="">Seleccione una opción</option>`; // Resetea el select de modelos también
          alert("Vehículo asignado exitosamente.");
          // Opcional: Podrías recargar la tabla de vehículos si el modal estuviera visible
          // o actualizar la tabla principal si es necesario.
        } else {
          // Si el backend devuelve rows: 0 u otra estructura en caso de error conocido
           alert(resp.message || "Error: No se pudo asignar el vehículo. Verifique los datos."); // Muestra mensaje del backend si existe
        }
      } catch (err) {
        console.error("Error en la asignación del vehículo:", err);
        alert(`Ocurrió un error de comunicación o en el servidor: ${err.message}`);
      }
    });

  }); // Fin de DOMContentLoaded
})();