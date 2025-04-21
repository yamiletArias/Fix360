document.addEventListener("DOMContentLoaded", function() {
  // Elementos del modal de asignar vehículo
  const tipovSelect = document.getElementById("tipov");
  const marcavSelect = document.getElementById("marcav");
  const modeloSelect = document.getElementById("modelo");
  const hiddenIdCliente = document.getElementById("hiddenIdCliente");
  const btnRegistrar = document.getElementById("btnRegistrarVehiculo");

  // Si no estamos en la vista de asignar vehículo, salimos
  if (!tipovSelect || !marcavSelect || !modeloSelect || !btnRegistrar) return;

  // Carga de tipos de vehículo
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
    })
    .catch(err => console.error("Error al cargar tipos de vehículo:", err));

  // Carga de marcas de vehículo
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
    })
    .catch(err => console.error("Error al cargar marcas de vehículo:", err));

  // Función para cargar modelos según tipo y marca
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
          opt.value = item.idmodelo;
          opt.textContent = item.modelo;
          modeloSelect.appendChild(opt);
        });
      })
      .catch(err => console.error("Error al cargar modelos:", err));
  }
  tipovSelect.addEventListener("change", cargarModelos);
  marcavSelect.addEventListener("change", cargarModelos);

  // Show.bs.modal: pintar nombre e id del cliente en el formulario
  const modalEl = document.getElementById('ModalAsignarVehiculo');
  modalEl.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const idCliente = button.getAttribute('data-idcliente');
    const nombreCliente = button.getAttribute('data-nombrecliente');
    console.log('[DEBUG] Abrir modal, idCliente=', idCliente, 'nombreCliente=', nombreCliente);
    document.getElementById('floatingInput').value = nombreCliente || '';
    hiddenIdCliente.value = idCliente || '';
  });

  // Envío del formulario dentro del modal
  btnRegistrar.addEventListener("click", function(e) {
    e.preventDefault();
    const data = {
      idmodelo: modeloSelect.value,
      placa: document.getElementById("fplaca").value.trim().toUpperCase(),
      anio: document.getElementById("fanio").value.trim(),
      numserie: document.getElementById("fnumserie").value.trim(),
      color: document.getElementById("fcolor").value.trim(),
      tipocombustible: document.getElementById("ftcombustible").value,
      idcliente: hiddenIdCliente.value
    };
    // Validación básica
    if (!data.idmodelo || !data.placa || !data.anio || !data.idcliente) {
      alert("Completa todos los campos obligatorios antes de guardar.");
      return;
    }

    fetch("http://localhost/fix360/app/controllers/Vehiculo.controller.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(resp => {
      if (resp.rows > 0) {
        alert("Vehículo asignado exitosamente.");
        // Opcional: recargar la tabla de clientes o cerrar modal manualmente
        modal.hide();
      } else {
        alert("Error al asignar vehículo.");
      }
    })
    .catch(err => console.error("Error en la solicitud:", err));
  });
});


