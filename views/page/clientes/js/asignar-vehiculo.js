document.addEventListener("DOMContentLoaded", function() {
  // Elementos del modal de asignar vehículo
  const tipovSelect      = document.getElementById("tipov");
  const marcavSelect     = document.getElementById("marcav");
  const modeloSelect     = document.getElementById("modelo");
  const hiddenIdCliente  = document.getElementById("hiddenIdCliente");
  const btnRegistrar     = document.getElementById("btnRegistrarVehiculo");
  const formVehiculo     = document.getElementById("FormVehiculo");
  const modalEl          = document.getElementById("ModalAsignarVehiculo");
  const bsModal          = new bootstrap.Modal(modalEl);

  // Si no estamos en la vista de asignar vehículo, salimos
  if (!tipovSelect || !marcavSelect || !modeloSelect || !btnRegistrar) return;

  // 1) Carga de tipos de vehículo
  fetch("http://localhost/fix360/app/controllers/Tipov.controller.php")
    .then(res => res.json())
    .then(data => {
      tipovSelect.innerHTML = `<option value="">Seleccione una opción</option>`;
      data.forEach(item => {
        const opt = document.createElement("option");
        opt.value   = item.idtipov;
        opt.textContent = item.tipov;
        tipovSelect.appendChild(opt);
      });
    })
    .catch(err => console.error("Error al cargar tipos de vehículo:", err));

  // 2) Carga de marcas de vehículo
  fetch("http://localhost/fix360/app/controllers/Marca.controller.php?task=getAllMarcaVehiculo")
    .then(res => res.json())
    .then(data => {
      marcavSelect.innerHTML = `<option value="">Seleccione una opción</option>`;
      data.forEach(item => {
        const opt = document.createElement("option");
        opt.value   = item.idmarca;
        opt.textContent = item.nombre;
        marcavSelect.appendChild(opt);
      });
    })
    .catch(err => console.error("Error al cargar marcas de vehículo:", err));

  // 3) Función para cargar modelos según tipo y marca
  function cargarModelos() {
    const idtipov = tipovSelect.value;
    const idmarca = marcavSelect.value;
    modeloSelect.innerHTML = `<option value="">Seleccione una opción</option>`;
    if (!idtipov || !idmarca) return;

    fetch(`http://localhost/fix360/app/controllers/Modelo.controller.php?idtipov=${encodeURIComponent(idtipov)}&idmarca=${encodeURIComponent(idmarca)}`)
      .then(res => res.json())
      .then(data => {
        data.forEach(item => {
          const opt = document.createElement("option");
          opt.value   = item.idmodelo;
          opt.textContent = item.modelo;
          modeloSelect.appendChild(opt);
        });
      })
      .catch(err => console.error("Error al cargar modelos:", err));
  }
  tipovSelect.addEventListener("change", cargarModelos);
  marcavSelect.addEventListener("change", cargarModelos);

  // 4) Pintar nombre e ID de cliente al abrir el modal
  modalEl.addEventListener("show.bs.modal", function(event) {
    const button        = event.relatedTarget;
    const idCliente     = button.getAttribute("data-idcliente");
    const nombreCliente = button.getAttribute("data-nombrecliente");
    document.getElementById("floatingInput").value = nombreCliente || "";
    hiddenIdCliente.value = idCliente || "";
  });

  // 5) Envío del formulario dentro del modal
  btnRegistrar.addEventListener("click", async function(e) {
    e.preventDefault();

    const data = {
      idmodelo:        modeloSelect.value,
      placa:           document.getElementById("fplaca").value.trim().toUpperCase(),
      anio:            document.getElementById("fanio").value.trim(),
      numserie:        document.getElementById("fnumserie").value.trim(),
      color:           document.getElementById("fcolor").value.trim(),
      tipocombustible: document.getElementById("ftcombustible").value,
      idcliente:       hiddenIdCliente.value
    };

    // Validación básica
    if (!data.idmodelo || !data.placa || !data.anio || !data.idcliente) {
      return alert("Completa todos los campos obligatorios antes de guardar.");
    }

    try {
      const res  = await fetch("http://localhost/fix360/app/controllers/Vehiculo.controller.php", {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify(data)
      });
      const resp = await res.json();

      if (resp.rows > 0) {
        // 1) Simular click en el botón de cerrar
        const closeBtn = modalEl.querySelector('button[data-bs-dismiss="modal"]');
        if (closeBtn) closeBtn.click();
      
        // 2) Resetear el formulario
        formVehiculo.reset();
      
        // 3) (Opcional) Recargar DataTable
        // $('#tablaPersona').DataTable().ajax.reload();
      
        // 4) Notificar al usuario
        alert("Vehículo asignado exitosamente.");
      } else {
        alert("Error al asignar vehículo.");
      }
    } catch (err) {
      console.error("Error en la solicitud:", err);
      alert("Ocurrió un error de comunicación con el servidor.");
    }
  });
});
