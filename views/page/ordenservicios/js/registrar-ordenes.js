let detalleArr = [];
function fetchHandler() {
  document.getElementById("kilometraje").value = "";
  if (this.value) fetchUltimoKilometraje(this.value);
}

// --------------------------------------------------
// Inicialización de UI y eventos tras cargar el DOM
// --------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
  console.log("DOMContentLoaded fired");

  // Verificar existencia del select de vehículo y registrar listener
  const vehiculoSelect = document.getElementById("vehiculo");
  console.log("→ #vehiculo existe?", vehiculoSelect);
  if (vehiculoSelect) {
    vehiculoSelect.addEventListener("change", fetchHandler);
    console.log("→ listener 'change' attached on #vehiculo");
  } else {
    console.warn("! No se encontró elemento #vehiculo para bindear listener");
  }

  // 1) Selección de Propietario
  const tablaRes = document.querySelector("#tabla-resultado");
  if (tablaRes) {
    tablaRes.addEventListener("click", e => {
      const btn = e.target.closest(".btn-confirmar");
      if (!btn) return;
      const id = btn.dataset.id;
      const nombre = btn.closest("tr").cells[1].textContent;
      document.getElementById("hiddenIdPropietario").value = id;
      document.getElementById("propietario").value = nombre;
      cargarVehiculos();
      setTimeout(() => bootstrap.Modal.getOrCreateInstance(document.getElementById("miModal")).hide(), 100);
    });
  }

   const kmInput = document.getElementById("kilometraje");
  if (kmInput) {
    kmInput.addEventListener("change", () => {
      const nuevo = parseFloat(kmInput.value);
      if (prevKilometraje !== null && nuevo < prevKilometraje) {
        alert(`El kilometraje no puede ser menor que el último registrado (${prevKilometraje}).`);
        kmInput.value = prevKilometraje;  // restablecer al valor válido
      }
    });
  }

  // 2) Selección de Cliente
  const tablaCli = document.querySelector("#tabla-resultado-cliente tbody");
  if (tablaCli) {
    tablaCli.addEventListener("click", e => {
      const btn = e.target.closest("button[data-id]");
      if (!btn) return;
      const id = btn.dataset.id;
      const nombre = btn.closest("tr").cells[1].textContent;
      document.getElementById("hiddenIdCliente").value = id;
      document.getElementById("cliente").value = nombre;
      setTimeout(() => bootstrap.Modal.getOrCreateInstance(document.getElementById("ModalCliente")).hide(), 100);
    });
  }

  // 3) Focus inputs al mostrar modales
  const miModal = document.getElementById("miModal");
  const modalCli = document.getElementById("ModalCliente");
  if (miModal) miModal.addEventListener("shown.bs.modal", () => document.getElementById("vbuscado").focus());
  if (modalCli) modalCli.addEventListener("shown.bs.modal", () => document.getElementById("vbuscadoCliente").focus());

  // 4) Búsquedas en modales
  const rpersona = document.getElementById("rbtnpersona");
  const rempresa = document.getElementById("rbtnempresa");
  const vbus = document.getElementById("vbuscado");
  if (rpersona && rempresa && vbus) {
    rpersona.addEventListener("click", () => { actualizarOpciones(); buscarPropietario(); });
    rempresa.addEventListener("click", () => { actualizarOpciones(); buscarPropietario(); });
    vbus.addEventListener("keyup", buscarPropietario);
  }

  let clienteTimer;
  const vbuscadoC = document.getElementById("vbuscadoCliente");
  const selectMetodoC = document.getElementById("selectMetodoCliente");
  if (vbuscadoC && selectMetodoC) {
    vbuscadoC.addEventListener("input", () => { clearTimeout(clienteTimer); clienteTimer = setTimeout(buscarCliente, 300); });
    selectMetodoC.addEventListener("change", () => { clearTimeout(clienteTimer); clienteTimer = setTimeout(buscarCliente, 300); });
  }

  // 5) Inicializaciones de UI
  actualizarOpciones();
  setFechaDefault();
  cargarSubcategorias();
  cargarMecanicos();

  // 6) Eventos de selects
  const subc = document.getElementById("subcategoria");
  const hidCli = document.getElementById("hiddenIdCliente");
  if (subc) subc.addEventListener("change", cargarServicio);
  if (hidCli) hidCli.addEventListener("change", cargarVehiculos);

  // 7) DETALLE y envío de formulario
  const btnAdd = document.getElementById("btnAgregar");
  const btnAceptar = document.getElementById("btnAceptarOrden");
  if (btnAdd) btnAdd.addEventListener("click", onAgregarDetalle);
  if (btnAceptar) btnAceptar.addEventListener("click", onAceptarOrden);
});

// -----------------------------
// Funciones auxiliares externas
// -----------------------------

async function fetchUltimoKilometraje(idvehiculo) {
  console.log("🔍 fetchKilometraje(", idvehiculo, ")");
  try {
    const url = `${SERVERURL}app/controllers/vehiculo.controller.php?task=getUltimoKilometraje&idvehiculo=${idvehiculo}`;
    console.log("→ Fetch URL:", url);
    const res = await fetch(url);
    if (!res.ok) throw new Error(res.status);
    const data = await res.json();
    const ultimo = data.ultimo_kilometraje ?? 0;
    prevKilometraje = parseFloat(ultimo); 
    console.log("SP response:", data);
    document.getElementById("kilometraje").value = data.ultimo_kilometraje ?? "";
  } catch (err) {
    console.error("Error al cargar último kilometraje:", err);
  }
}


/**
 * Actualiza los métodos de búsqueda en modal propietario.
 */
function actualizarOpciones() {
  const select = document.getElementById("selectMetodo");
  const isPersona = document.getElementById("rbtnpersona").checked;
  select.innerHTML = isPersona
    ? `<option value="dni">DNI</option><option value="nombre">Apellidos y nombres</option>`
    : `<option value="ruc">RUC</option><option value="razonsocial">Razón Social</option>`;
}

/**
 * Busca propietarios (persona/empresa) y pinta resultados.
 */
function buscarPropietario() {
  const tipo = document.getElementById("rbtnpersona").checked ? "persona" : "empresa";
  const metodo = document.getElementById("selectMetodo").value;
  const valor = document.getElementById("vbuscado").value.trim();
  if (!valor) {
    document.querySelector("#tabla-resultado tbody").innerHTML = "";
    return;
  }
  fetch(
    `${SERVERURL}app/controllers/Propietario.controller.php?tipo=${tipo}&metodo=${metodo}&valor=${encodeURIComponent(valor)}`
  )
    .then(r => r.json())
    .then(data => {
      const tbody = document.querySelector("#tabla-resultado tbody");
      tbody.innerHTML = data.map((item, i) => `
        <tr>
          <td>${i+1}</td>
          <td>${item.nombre}</td>
          <td>${item.documento}</td>
          <td>
            <button class="btn btn-success btn-sm btn-confirmar" data-id="${item.idcliente}" data-bs-dismiss="modal">
              <i class="fa-solid fa-circle-check"></i>
            </button>
          </td>
        </tr>
      `).join('');
    })
    .catch(console.error);
}

/**
 * Busca clientes en modal cliente y pinta resultados.
 */
function buscarCliente() {
  const tipo = document.getElementById("tipoBusquedaCliente").value;
  const metodo = document.getElementById("selectMetodoCliente").value;
  const valor = document.getElementById("vbuscadoCliente").value.trim();
  if (!valor) {
    document.querySelector("#tabla-resultado-cliente tbody").innerHTML = "";
    return;
  }
  fetch(
    `${SERVERURL}app/controllers/propietario.controller.php?task=buscarPropietario&tipo=${tipo}&metodo=${metodo}&valor=${encodeURIComponent(valor)}`
  )
    .then(r => r.json())
    .then(data => {
      const tbody = document.querySelector("#tabla-resultado-cliente tbody");
      tbody.innerHTML = data.map((item, i) => `
        <tr>
          <td>${i+1}</td>
          <td>${item.nombre}</td>
          <td>${item.documento}</td>
          <td>
            <button class="btn btn-sm btn-success" data-id="${item.idcliente}" data-bs-dismiss="modal">
              <i class="fa-solid fa-circle-check"></i>
            </button>
          </td>
        </tr>
      `).join('');
    })
    .catch(console.error);
}

/**
 * Setea fecha de ingreso por defecto (hoy) y rango mínimo.
 */
function setFechaDefault() {
  const input = document.getElementById('fechaIngreso');
  const now = new Date(); const pad = n => String(n).padStart(2,'0');
  const yyyy = now.getFullYear(), MM = pad(now.getMonth()+1), dd = pad(now.getDate());
  const hh = pad(now.getHours()), mm = pad(now.getMinutes());
  input.value = `${yyyy}-${MM}-${dd}T${hh}:${mm}`;

  const twoDaysAgo = new Date(now);
  twoDaysAgo.setDate(now.getDate()-2);
  input.min = `${twoDaysAgo.getFullYear()}-${pad(twoDaysAgo.getMonth()+1)}-${pad(twoDaysAgo.getDate())}T00:00`;
  input.max = `${yyyy}-${MM}-${dd}T23:59`;
}

/**
 * Carga subcategorías en el select.
 */
function cargarSubcategorias() {
  fetch(`${SERVERURL}app/controllers/subcategoria.controller.php?task=getServicioSubcategoria`)
    .then(r => r.json())
    .then(data => data.forEach(item =>
      document.getElementById('subcategoria').insertAdjacentHTML('beforeend',
        `<option value="${item.idsubcategoria}">${item.subcategoria}</option>`
      )
    ))
    .catch(console.error);
}

/**
 * Carga mecánicos en el select.
 */
function cargarMecanicos() {
  fetch(`${SERVERURL}app/controllers/mecanico.controller.php?task=getAllMecanico`)
    .then(r => r.json())
    .then(data => data.forEach(item =>
      document.getElementById('mecanico').insertAdjacentHTML('beforeend',
        `<option value="${item.idcolaborador}">${item.nombres}</option>`
      )
    ))
    .catch(console.error);
}

/**
 * Carga servicios según subcategoría.
 */
function cargarServicio() {
  const subc = document.getElementById('subcategoria').value;
  const sel = document.getElementById('servicio');
  sel.innerHTML = '<option value="">Seleccione una opción</option>';
  if (!subc) return;
  fetch(
    `${SERVERURL}app/controllers/servicio.controller.php?task=getServicioBySubcategoria&idsubcategoria=${subc}`
  )
    .then(r => r.json())
    .then(data => data.forEach(item =>
      sel.insertAdjacentHTML('beforeend', `<option value="${item.idservicio}">${item.servicio}</option>`)
    ))
    .catch(console.error);
}

/**
 * Carga vehículos del propietario.
 */
function cargarVehiculos() {
  const idProp = document.getElementById('hiddenIdPropietario').value;
  const sel    = document.getElementById('vehiculo');
  sel.innerHTML = '<option value="">Seleccione una opción</option>';
  if (!idProp) return;

  fetch(
    `${SERVERURL}app/controllers/vehiculo.controller.php?task=getVehiculoByCliente&idcliente=${idProp}`
  )
  .then(r => r.json())
  .then(data => {
    data.forEach(item => {
      sel.insertAdjacentHTML('beforeend',
        `<option value="${item.idvehiculo}">${item.vehiculo}</option>`
      );
    });
    // ——> auto-carga del primero, opcional:
    if (sel.options.length > 1) {
      sel.selectedIndex = 1;
      sel.dispatchEvent(new Event('change'));
    }
  })
  .catch(console.error);
}

/**
 * Agrega un servicio al detalle y recalcula totales.
 */
function onAgregarDetalle() {
  const serv = document.getElementById('servicio');
  const mec  = document.getElementById('mecanico');
  const precio = parseFloat(document.getElementById('precio').value);
  const idServ = +serv.value;

   if (isNaN(precio) || precio <= 0) {
    return alert('El precio debe ser un número mayor a cero');
  }
  

  if (!idServ || !+mec.value) return alert('Selecciona servicio y mecánico');
  if (detalleArr.some(d => d.idservicio === idServ)) return alert('Este servicio ya está en la lista');

  detalleArr.push({ idservicio: idServ, idmecanico: +mec.value, precio });
  const option = serv.querySelector(`option[value="${idServ}"]`);
  if (option) option.disabled = true;

  const tbody = document.querySelector('#tabla-detalle tbody');
  const idx = tbody.children.length + 1;
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td>${idx}</td>
    <td>${serv.selectedOptions[0].text}</td>
    <td>${mec.selectedOptions[0].text}</td>
    <td>${precio.toFixed(2)}</td>
    <td><button class="btn btn-sm btn-danger">X</button></td>
  `;
  tr.querySelector('button').onclick = () => {
    detalleArr = detalleArr.filter(d => d.idservicio !== idServ);
    tr.remove();
    if (option) option.disabled = false;
    recalcular();
  };
  tbody.appendChild(tr);
  recalcular();
}

/**
 * Recalcula subtotal, igv y neto.
 */
function recalcular() {
  const total = detalleArr.reduce((s, i) => s + i.precio, 0);
  const igv = total * 0.18;
  document.getElementById('subtotal').value = (total - igv).toFixed(2);
  document.getElementById('igv').value = igv.toFixed(2);
  document.getElementById('neto').value = total.toFixed(2);
}

/**
 * Envía la orden al backend.
 */
function onAceptarOrden(e) {
  e.preventDefault();

    const kmInput = document.getElementById('kilometraje');
  const nuevo = parseFloat(kmInput.value);
  if (prevKilometraje !== null && nuevo < prevKilometraje) {
    return alert(`No puedes enviar un kilometraje menor que ${prevKilometraje}.`);
  }
  
  if (!window.confirm('¿Estás seguro de que quieres registrar esta orden de servicio?')) return;
  if (detalleArr.length === 0) return alert('Agrega al menos un servicio');

  const payload = {
    idmecanico: +document.getElementById('mecanico').value,
    idpropietario: +document.getElementById('hiddenIdPropietario').value,
    idcliente: +document.getElementById('hiddenIdCliente').value,
    idvehiculo: +document.getElementById('vehiculo').value,
    kilometraje: parseFloat(document.getElementById('kilometraje').value),
    observaciones: document.getElementById('observaciones').value,
    ingresogrua: document.getElementById('ingresogrua').checked,
    fechaingreso: document.getElementById('fechaIngreso').value,
    fecharecordatorio: null,
    detalle: detalleArr
  };

  fetch(`${SERVERURL}app/controllers/OrdenServicio.controller.php`, {
    method: 'POST',
    headers: { 'Content-Type':'application/json' },
    body: JSON.stringify(payload)
  })
    .then(r => r.json())
    .then(js => {
      if (js.status === 'success') {
        showToast('Orden registrada exitosamente', 'SUCCESS', 1500);
        setTimeout(() => window.location.href = 'listar-ordenes.php', 1000);
      } else {
        showToast('Error al registrar la orden de servicio', 'ERROR', 1500);
      }
    })
    .catch(err => {
      console.error('Fetch error:', err);
      showToast('Error de red o servidor','ERROR',2000);
    });
}
