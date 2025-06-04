
(function () {
  document.addEventListener("DOMContentLoaded", () => {
    // ——— 1) Referencias a los inputs y selects del modal padre ———
    const fplaca          = document.getElementById("fplaca");
    const fanio           = document.getElementById("fanio");
    const fnumserie       = document.getElementById("fnumserie");
    const fcolor          = document.getElementById("fcolor");
    const vin             = document.getElementById("vin");
    const numchasis       = document.getElementById("numchasis");
    const tipovSelect     = document.getElementById("tipov");
    const marcavSelect    = document.getElementById("marcav");
    const modeloSelect    = document.getElementById("modelo");
    const tcombustibleSel = document.getElementById("ftcombustible");
    const hiddenIdCliente = document.getElementById("hiddenIdCliente");
    const btnRegistrar    = document.getElementById("btnRegistrarVehiculo");
    const modalAsignarEl  = document.getElementById("ModalAsignarVehiculo");
    const modalVehiculosEl= document.getElementById("ModalVehiculos");

    if (!tipovSelect || !marcavSelect || !modeloSelect) {
      console.error("Faltan selects obligatorios en el DOM");
      return;
    }

    // ——— 2) Función para cargar modelos según tipo+marca ———
    function cargarModelos() {
      modeloSelect.innerHTML = `<option value="">Seleccione una opción</option>`;
      if (!tipovSelect.value || !marcavSelect.value) return;
      fetch(
        `http://localhost/fix360/app/controllers/Modelo.controller.php?` +
        `idtipov=${encodeURIComponent(tipovSelect.value)}` +
        `&idmarca=${encodeURIComponent(marcavSelect.value)}`
      )
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

    // ——— 3) Carga inicial de opciones ———
    fetch("http://localhost/fix360/app/controllers/Tipov.controller.php?task=getAllTipoVehiculo")
      .then(r => r.json())
      .then(data => {
        tipovSelect.innerHTML = `<option value="">Seleccione una opción</option>`;
        data.forEach(i => {
          const o = new Option(i.tipov, i.idtipov);
          tipovSelect.append(o);
        });
      });
    fetch("http://localhost/fix360/app/controllers/Tcombustible.controller.php?task=getAllTcombustible")
      .then(r => r.json())
      .then(data => {
        tcombustibleSel.innerHTML = `<option value="">Seleccione una opción</option>`;
        data.forEach(i => {
          const o = new Option(i.tcombustible, i.idtcombustible);
          tcombustibleSel.append(o);
        });
      });
    fetch("http://localhost/fix360/app/controllers/Marca.controller.php?task=getAllMarcaVehiculo")
      .then(r => r.json())
      .then(data => {
        marcavSelect.innerHTML = `<option value="">Seleccione una opción</option>`;
        data.forEach(i => {
          const o = new Option(i.nombre, i.idmarca);
          marcavSelect.append(o);
        });
      });

    // Al cambiar tipo o marca, recargar modelos:
    tipovSelect.addEventListener("change", cargarModelos);
    marcavSelect.addEventListener("change", cargarModelos);

    // ——— 4) Listener para abrir el modal de asignar ———
    const bsModalAsignar = new bootstrap.Modal(modalAsignarEl);
    modalAsignarEl.addEventListener("show.bs.modal", evt => {
      const btn = evt.relatedTarget;
      hiddenIdCliente.value = btn.dataset.idcliente || "";
      document.getElementById("floatingInput").value = btn.dataset.nombrecliente || "";
    });

    // ——— 5) Modales secundarios (marca/modelo/combustible) ———
    const btnNuevaMarca          = document.getElementById("btnNuevaMarca");
    const btnNuevoModelo         = document.getElementById("btnNuevoModelo");
    const formRegistrarMarca     = document.getElementById("formRegistrarMarca");
    const formRegistrarModelo    = document.getElementById("formRegistrarModelo");
    const inputMarcaNueva        = document.getElementById("inputMarcaNueva");
    const inputModeloNueva       = document.getElementById("inputModeloNuevo");
    const selTipoModelo          = document.getElementById("inputTipoModelo");
    const selMarcaModelo         = document.getElementById("inputMarcaModelo");
    const btnNuevoTcombustible   = document.getElementById("btnNuevoTcombustible");
    const formRegistrarTcombustible = document.getElementById("formRegistrarTcombustible");
    const inputTcombustibleNuevo = document.getElementById("inputTcombustibleNuevo");
    const bsModalTcombustible    = new bootstrap.Modal(document.getElementById("ModalRegistrarTcombustible"));
    const bsModalMarca           = new bootstrap.Modal(document.getElementById("ModalRegistrarMarca"));
    const bsModalModelo          = new bootstrap.Modal(document.getElementById("ModalRegistrarModelo"));

    // Abrir modal de tipo combustible:
    btnNuevoTcombustible.addEventListener("click", () => {
      inputTcombustibleNuevo.value = "";
      bsModalTcombustible.show();
    });
    formRegistrarTcombustible.addEventListener("submit", async e => {
      e.preventDefault();
      const texto = inputTcombustibleNuevo.value.trim();
      if (!texto) {
        showToast("Ingrese un tipo de combustible.", "ERROR", 1500);
        return;
      }
      try {
        const res = await fetch(
          "http://localhost/fix360/app/controllers/Tcombustible.controller.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ tcombustible: texto })
          }
        );
        const j = await res.json();
        if (j.success) {
          const opt = new Option(texto, j.idtcombustible);
          tcombustibleSel.append(opt);
          tcombustibleSel.value = j.idtcombustible;
          bsModalTcombustible.hide();
          showToast("Tipo de combustible registrado.", "SUCCESS", 1500);
        } else {
          showToast("No se pudo crear el tipo de combustible.", "ERROR", 1500);
        }
      } catch (err) {
        console.error("Error al crear combustible:", err);
        showToast("Error de red al crear combustible.", "ERROR", 1500);
      }
    });
    document.getElementById("ModalRegistrarTcombustible")
      .addEventListener("shown.bs.modal", () => {
        inputTcombustibleNuevo.focus();
      });

    // Abrir modal de nueva marca:
    btnNuevaMarca.addEventListener("click", () => {
      inputMarcaNueva.value = "";
      bsModalMarca.show();
    });
    formRegistrarMarca.addEventListener("submit", async e => {
      e.preventDefault();
      const nombre = inputMarcaNueva.value.trim();
      if (!nombre) {
        showToast("Ingrese el nombre de la marca.", "ERROR", 1500);
        return;
      }
      try {
        const res = await fetch(
          "http://localhost/fix360/app/controllers/Marca.controller.php?task=registerMarcaVehiculo",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ nombre })
          }
        );
        const j = await res.json();
        if (j.success) {
          const o = new Option(nombre, j.idmarca);
          marcavSelect.append(o);
          marcavSelect.value = j.idmarca;
          cargarModelos();
          bsModalMarca.hide();
          showToast("Marca registrada.", "SUCCESS", 1500);
        } else {
          showToast("Error al crear marca.", "ERROR", 1500);
        }
      } catch (err) {
        console.error(err);
        showToast("Error de red al crear marca.", "ERROR", 1500);
      }
    });
    document.getElementById("ModalRegistrarMarca")
      .addEventListener("shown.bs.modal", () => {
        inputMarcaNueva.focus();
      });

    // Abrir modal de nuevo modelo:
    btnNuevoModelo.addEventListener("click", () => {
      selTipoModelo.innerHTML  = `<option>${tipovSelect.selectedOptions[0]?.text || ""}</option>`;
      selMarcaModelo.innerHTML = `<option>${marcavSelect.selectedOptions[0]?.text || ""}</option>`;
      selTipoModelo.disabled  = true;
      selMarcaModelo.disabled = true;
      inputModeloNueva.value   = "";
      bsModalModelo.show();
    });
    formRegistrarModelo.addEventListener("submit", async e => {
      e.preventDefault();
      const modelo   = inputModeloNueva.value.trim();
      const idtipov  = tipovSelect.value;
      const idmarca  = marcavSelect.value;
      if (!modelo) {
        showToast("Ingrese el nombre del modelo.", "ERROR", 1500);
        return;
      }
      try {
        const res = await fetch("http://localhost/fix360/app/controllers/Modelo.controller.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ idtipov, idmarca, modelo })
        });
        const j = await res.json();
        if (j.success) {
          const o = new Option(modelo, j.idmodelo);
          modeloSelect.append(o);
          modeloSelect.value = j.idmodelo;
          bsModalModelo.hide();
          showToast("Modelo registrado.", "SUCCESS", 1500);
        } else {
          showToast("Error al crear modelo.", "ERROR", 1500);
        }
      } catch (err) {
        console.error(err);
        showToast("Error de red al crear modelo.", "ERROR", 1500);
      }
    });
    document.getElementById("ModalRegistrarModelo")
      .addEventListener("shown.bs.modal", () => {
        inputModeloNueva.focus();
      });

    // ——— 6) Listener para mostrar vehículos ———
    const bsModalVehiculos = new bootstrap.Modal(modalVehiculosEl);
    modalVehiculosEl.addEventListener("show.bs.modal", evt => {
      const btn = evt.relatedTarget;
      const id  = btn.dataset.idcliente;
      const nom = btn.dataset.nombrecliente;
      document.getElementById("nombreCliente").textContent = nom;
      cargarTablaVehiculos(id);
    });
    function cargarTablaVehiculos(idcliente) {
      const tbody = modalVehiculosEl.querySelector("tbody");
      tbody.innerHTML = `<tr><td colspan="6">Cargando...</td></tr>`;
      fetch(
        `http://localhost/fix360/app/controllers/Vehiculo.controller.php?task=getVehiculoByCliente&idcliente=${idcliente}`
      )
        .then(r => r.json())
        .then(data => {
          tbody.innerHTML = "";
          if (Array.isArray(data) && data.length) {
            data.forEach((it, i) => {
              const tr = document.createElement("tr");
              tr.innerHTML = `
                <td>${i + 1}</td>
                <td>${it.tipov || "N/A"}</td>
                <td>${it.nombre || "N/A"}</td>
                <td>${it.modelo || "N/A"}</td>
                <td>${it.placa || "N/A"}</td>
                <td>${it.color || "N/A"}</td>
              `;
              tbody.append(tr);
            });
          } else {
            tbody.innerHTML = `<tr><td colspan="6">No hay vehículos.</td></tr>`;
          }
        })
        .catch(e => {
          console.error(e);
          tbody.innerHTML = `<tr><td colspan="6" class="text-danger">Error al cargar.</td></tr>`;
        });
    }

    // ——— 7) Envío del formulario al hacer click en "Guardar" ———
    btnRegistrar.addEventListener("click", async e => {
      e.preventDefault();

      // 7.1) Validaciones previas antes de preguntar:
      if (!tipovSelect.value) {
        showToast("Seleccione un tipo de vehículo.", "ERROR", 1500);
        return;
      }
      if (!marcavSelect.value) {
        showToast("Seleccione una marca.", "ERROR", 1500);
        return;
      }
      if (!modeloSelect.value) {
        showToast("Seleccione un modelo.", "ERROR", 1500);
        return;
      }
      if (!tcombustibleSel.value) {
        showToast("Seleccione un tipo de combustible.", "ERROR", 1500);
        return;
      }
      if (!fplaca.value.trim()) {
        showToast("La placa no puede estar vacía.", "ERROR", 1500);
        return;
      }
      if (!fanio.value.trim()) {
        showToast("El año no puede estar vacío.", "ERROR", 1500);
        return;
      }
      
      if (!fcolor.value.trim()) {
        showToast("El color no puede estar vacío.", "ERROR", 1500);
        return;
      }


      // 7.2) Preguntar confirmación con SweetAlert2 (ask)
      const confirmado = await ask(
        "¿Estás seguro de que quieres asignar este vehículo?",
        "Vehículos"
      );
      if (!confirmado) {
        return; // El usuario cancela
      }

      // 7.3) Construir payload y enviarlo
      const payload = {
        task: "registerVehiculo",
        idmodelo: modeloSelect.value,
        idtcombustible: tcombustibleSel.value,
        placa: fplaca.value.trim().toUpperCase(),
        anio: fanio.value.trim(),
        numserie: fnumserie.value.trim(),
        color: fcolor.value.trim(),
        vin: vin.value.trim(),
        numchasis: numchasis.value.trim(),
        idcliente: hiddenIdCliente.value
      };

      try {
        const res = await fetch(
          "http://localhost/fix360/app/controllers/Vehiculo.controller.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
          }
        );
        if (!res.ok) throw new Error(`HTTP ${res.status}: ${await res.text()}`);
        const resp = await res.json();

        if (resp.rows > 0) {
          // 7.4) Si todo salió bien, cerrar modal y mostrar Toast de éxito
          bsModalAsignar.hide();
          showToast("Vehículo asignado exitosamente.", "SUCCESS", 1500);
          // Opcional: recargar tabla de vehículos tras asignar
          const idClienteActual = hiddenIdCliente.value;
          setTimeout(() => {
            cargarTablaVehiculos(idClienteActual);
          }, 1600);
        } else {
          // 7.5) Si el backend devolvió error lógico
          showToast(resp.message || "No se pudo asignar el vehículo.", "ERROR", 2000);
        }
      } catch (err) {
        console.error("Error en la asignación del vehículo:", err);
        showToast(`Error de servidor: ${err.message}`, "ERROR", 2000);
      }
    });
  });  // cierra DOMContentLoaded
})();  // cierra IIFE

