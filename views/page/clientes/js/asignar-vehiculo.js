(function () {
  document.addEventListener("DOMContentLoaded", () => {
    // ——— 1) Referencias a los selects del modal padre ———
    const fplaca = document.getElementById("fplaca");
    const fanio = document.getElementById("fanio");
    const fnumserie = document.getElementById("fnumserie");
    const fcolor = document.getElementById("fcolor");
    const vin = document.getElementById("vin");
    const numchasis = document.getElementById("numchasis");
    const tipovSelect = document.getElementById("tipov");
    const marcavSelect = document.getElementById("marcav");
    const modeloSelect = document.getElementById("modelo");
    const tcombustibleSelect = document.getElementById("ftcombustible");
    const hiddenIdCliente = document.getElementById("hiddenIdCliente");
    const btnRegistrar = document.getElementById("btnRegistrarVehiculo");
    const modalAsignarEl = document.getElementById("ModalAsignarVehiculo");
    const modalVehiculosEl = document.getElementById("ModalVehiculos");

    // Validar que existan
    if (!tipovSelect || !marcavSelect || !modeloSelect) {
      console.error("Faltan selects de marca/tipo/modelo en el DOM");
      return;
    }

    // ——— 2) Función para cargar modelos según tipo+marca ———
    function cargarModelos() {
      modeloSelect.innerHTML = `<option value="">Seleccione una opción</option>`;
      if (!tipovSelect.value || !marcavSelect.value) return;
      fetch(`http://localhost/fix360/app/controllers/Modelo.controller.php?` +
        `idtipov=${encodeURIComponent(tipovSelect.value)}` +
        `&idmarca=${encodeURIComponent(marcavSelect.value)}`)
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
        tcombustibleSelect.innerHTML = `<option value="">Seleccione una opción</option>`;
        data.forEach(i => {
          const o = new Option(i.tcombustible, i.idtcombustible);
          tcombustibleSelect.append(o);
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

    // ——— 5) Modales secundarios ———
    const btnNuevaMarca = document.getElementById("btnNuevaMarca");
    const btnNuevoModelo = document.getElementById("btnNuevoModelo");
    const formRegistrarMarca = document.getElementById("formRegistrarMarca");
    const formRegistrarModelo = document.getElementById("formRegistrarModelo");
    const inputMarcaNueva = document.getElementById("inputMarcaNueva");
    const inputModeloNueva = document.getElementById("inputModeloNuevo");
    const selTipoModelo = document.getElementById("inputTipoModelo");
    const selMarcaModelo = document.getElementById("inputMarcaModelo");

    const btnNuevoTcombustible = document.getElementById("btnNuevoTcombustible");
    const formRegistrarTcombustible = document.getElementById("formRegistrarTcombustible");
    const inputTcombustibleNuevo = document.getElementById("inputTcombustibleNuevo");
    const ftcombustibleSelect = document.getElementById("ftcombustible");
    const bsModalTcombustible = new bootstrap.Modal(document.getElementById("ModalRegistrarTcombustible"));
    const bsModalMarca = new bootstrap.Modal(document.getElementById("ModalRegistrarMarca"));
    const bsModalModelo = new bootstrap.Modal(document.getElementById("ModalRegistrarModelo"));

    btnNuevoTcombustible.addEventListener("click", () => {
      inputTcombustibleNuevo.value = "";
      bsModalTcombustible.show();
    });

    // 5.1 Abrir modal de nueva marca
    btnNuevaMarca.addEventListener("click", () => {
      inputMarcaNueva.value = "";
      bsModalMarca.show();
    });
    formRegistrarMarca.addEventListener("submit", async e => {
      e.preventDefault();
      const nombre = inputMarcaNueva.value.trim();
      if (!nombre) return;
      try {
        const res = await fetch("http://localhost/fix360/app/controllers/Marca.controller.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ task: "registerMarcaVehiculo", nombre })
        });
        const j = await res.json();
        if (j.success) {
          const o = new Option(nombre, j.idmarca);
          marcavSelect.append(o);
          marcavSelect.value = j.idmarca;
          cargarModelos();
          bsModalMarca.hide();
        } else {
          alert("Error al crear marca");
        }
      } catch (err) {
        console.error(err);
        alert("Error de red");
      }
    });

    // Cuando se abra el modal de Marca, enfoca el campo de texto
document.getElementById("ModalRegistrarMarca")
  .addEventListener("shown.bs.modal", () => {
    document.getElementById("inputMarcaNueva").focus();
  });

// Cuando se abra el modal de Modelo, enfoca el campo de modelo
document.getElementById("ModalRegistrarModelo")
  .addEventListener("shown.bs.modal", () => {
    document.getElementById("inputModeloNuevo").focus();
  });

// Cuando se abra el modal de Tipo de Combustible, enfoca ese input
document.getElementById("ModalRegistrarTcombustible")
  .addEventListener("shown.bs.modal", () => {
    document.getElementById("inputTcombustibleNuevo").focus();
  });

    

    // 5.2 Abrir modal de nuevo modelo
    btnNuevoModelo.addEventListener("click", () => {
      // Precargar y deshabilitar selects en el modal de modelo
      selTipoModelo.innerHTML = `<option>${tipovSelect.selectedOptions[0]?.text || ""}</option>`;
      selMarcaModelo.innerHTML = `<option>${marcavSelect.selectedOptions[0]?.text || ""}</option>`;
      selTipoModelo.disabled = true;
      selMarcaModelo.disabled = true;
      inputModeloNueva.value = "";
      bsModalModelo.show();
    });

    formRegistrarTcombustible.addEventListener("submit", async e => {
  e.preventDefault();
  const texto = inputTcombustibleNuevo.value.trim();
  if (!texto) return;

  try {
    const res = await fetch("http://localhost/fix360/app/controllers/Tcombustible.controller.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ tcombustible: texto })
    });
    const j = await res.json();
    if (j.success) {
      // Añade la nueva opción al select
      const opt = new Option(texto, j.idtcombustible);
      ftcombustibleSelect.append(opt);
      ftcombustibleSelect.value = j.idtcombustible;
      bsModalTcombustible.hide();
    } else {
      alert("No se pudo crear el tipo de combustible");
    }
  } catch (err) {
    console.error("Error al crear combustible:", err);
    alert("Error de red");
  }
});
    formRegistrarModelo.addEventListener("submit", async e => {
      e.preventDefault();

      const modelo = inputModeloNueva.value.trim();
      const idtipov = tipovSelect.value;
      const idmarca = marcavSelect.value;
      if (!modelo) return;
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
        } else {
          alert("Error al crear modelo");
        }
      } catch (err) {
        console.error(err);
        alert("Error de red al crear modelo");
      }
    });

    // ——— 6) Listener para mostrar vehiculos ———
    const bsModalVehiculos = new bootstrap.Modal(modalVehiculosEl);
    modalVehiculosEl.addEventListener("show.bs.modal", evt => {
      const btn = evt.relatedTarget;
      const id = btn.dataset.idcliente;
      const nom = btn.dataset.nombrecliente;
      document.getElementById("nombreCliente").textContent = nom;
      cargarTablaVehiculos(id);
    });
    function cargarTablaVehiculos(idcliente) {
      const tbody = modalVehiculosEl.querySelector("tbody");
      tbody.innerHTML = `<tr><td colspan="6">Cargando...</td></tr>`;
      fetch(`http://localhost/fix360/app/controllers/Vehiculo.controller.php?task=getVehiculoByCliente&idcliente=${idcliente}`)
        .then(r => r.json())
        .then(data => {
          tbody.innerHTML = "";
          if (Array.isArray(data) && data.length) {
            data.forEach((it, i) => {
              const tr = document.createElement("tr");
              tr.innerHTML = `<td>${i + 1}</td>
                              <td>${it.tipov || "N/A"}</td>
                              <td>${it.nombre || "N/A"}</td>
                              <td>${it.modelo || "N/A"}</td>
                              <td>${it.placa || "N/A"}</td>
                              <td>${it.color || "N/A"}</td>`;
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

    // Envío del formulario al hacer click en "Guardar" - SIN CAMBIOS
    btnRegistrar.addEventListener("click", async e => {
      e.preventDefault();
      if (!confirm("¿Estás seguro de que quieres asignar este vehículo?")) return;

      const payload = {
        task: "registerVehiculo",
        idmodelo: modeloSelect.value,
        idtcombustible: tcombustibleSelect.value,
        placa: fplaca.value.trim().toUpperCase(),
        anio: fanio.value.trim(),
        numserie: fnumserie.value.trim(),
        color: fcolor.value.trim(),
        vin: vin.value.trim(),
        numchasis: numchasis.value.trim(),
        idcliente: hiddenIdCliente.value
      };
      console.log("🛰 Enviando payload:", payload);

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
        console.log("🛰 Respuesta raw:", resp);

        if (resp.rows > 0) {
          // cerrar modal
          const btnCerrar = modalAsignarEl.querySelector('button[data-bs-dismiss="modal"]');
          if (btnCerrar) btnCerrar.click();
          alert("Vehículo asignado exitosamente.");
        } else {
          alert(resp.message || "No se pudo asignar el vehículo.");
        }
      } catch (err) {
        console.error("Error en la asignación del vehículo:", err);
        alert(`Error en el servidor: ${err.message}`);
      }
    });

  });  // cierra DOMContentLoaded
})();  // cierra IIFE

