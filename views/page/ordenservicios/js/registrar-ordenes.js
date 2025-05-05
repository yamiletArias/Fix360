// registrar-ordenes.js
let detalleArr = [];

document.addEventListener("DOMContentLoaded", () => {
  // Modales: propietario y cliente
  document.querySelector("#tabla-resultado").addEventListener("click", function (e) {
    const btn = e.target.closest(".btn-confirmar");
    if (!btn) return;
    const id = btn.getAttribute("data-id");
    const nombre = btn.closest("tr").cells[1].textContent;
    document.getElementById("hiddenIdPropietario").value = id;
    document.getElementById("propietario").value = nombre;

    cargarVehiculos();
    setTimeout(() => bootstrap.Modal.getOrCreateInstance(document.getElementById("miModal")).hide(), 100);
  });

  document
  .querySelector("#tabla-resultado-cliente tbody")
  .addEventListener("click", function (e) {
    // 1) Buscamos específicamente un <button> que tenga data-id
    const btn = e.target.closest("button[data-id]");
    if (!btn) return;

    // 2) Leemos el valor mediante el DOM dataset
    const id = btn.dataset.id;
    console.log("Seleccioné cliente id=", id);

    // 3) Guardamos en el hidden y pintamos el nombre
    document.getElementById("hiddenIdCliente").value = id;
    document.getElementById("cliente").value = btn
      .closest("tr")
      .cells[1].textContent;

    // 4) Cerramos el modal
    setTimeout(
      () =>
        bootstrap
          .Modal.getOrCreateInstance(
            document.getElementById("ModalCliente")
          )
          .hide(),
      100
    );
  });


  // Después de inicializar tu código en DOMContentLoaded…
const miModalEl = document.getElementById('miModal');
miModalEl.addEventListener('shown.bs.modal', () => {
  // Al mostrarse el modal, ponemos foco en el input
  document.getElementById('vbuscado').focus();
});

// Y lo mismo para el modal de cliente…
const modalClienteEl = document.getElementById('ModalCliente');
modalClienteEl.addEventListener('shown.bs.modal', () => {
  document.getElementById('vbuscadoCliente').focus();
});


  // Inputs del modal propietario
  document.getElementById("rbtnpersona").addEventListener("click", () => { actualizarOpciones(); buscarPropietario(); });
  document.getElementById("rbtnempresa").addEventListener("click", () => { actualizarOpciones(); buscarPropietario(); });
  document.getElementById("vbuscado").addEventListener("keyup", buscarPropietario);

  // Inputs del modal cliente
  let clienteTimer;
  const vbuscadoC = document.getElementById("vbuscadoCliente");
  const selectMetodoC = document.getElementById("selectMetodoCliente");
  vbuscadoC.addEventListener("input", () => { clearTimeout(clienteTimer); clienteTimer = setTimeout(buscarCliente, 300); });
  selectMetodoC.addEventListener("change", () => { clearTimeout(clienteTimer); clienteTimer = setTimeout(buscarCliente, 300); });

  // Inicializaciones de UI
  actualizarOpciones();
  setFechaDefault();

  // Carga selects de subcategoria, mecánico
  cargarSubcategorias();
  cargarMecanicos();

  // Eventos de selects
  document.getElementById("subcategoria").addEventListener("change", cargarServicio);
  document.getElementById("hiddenIdCliente").addEventListener("change", cargarVehiculos);

  // Botones de detalle y Aceptar
  document.getElementById("btnAgregar").addEventListener("click", onAgregarDetalle);
  document.getElementById("btnAceptarOrden").addEventListener("click", onAceptarOrden);
});

// Funciones auxiliares
function actualizarOpciones() {
  const select = document.getElementById("selectMetodo");
  const isPersona = document.getElementById("rbtnpersona").checked;
  select.innerHTML = isPersona
    ? `<option value="dni">DNI</option><option value="nombre">Apellidos y nombres</option>`
    : `<option value="ruc">RUC</option><option value="razonsocial">Razón Social</option>`;
}

function buscarPropietario() {
  const tipo    = document.getElementById("rbtnpersona").checked ? "persona" : "empresa";
  const metodo  = document.getElementById("selectMetodo").value;
  const valor   = document.getElementById("vbuscado").value.trim();
  if (!valor) return document.querySelector("#tabla-resultado tbody").innerHTML = "";

  fetch(`http://localhost/fix360/app/controllers/Propietario.controller.php?tipo=${tipo}&metodo=${metodo}&valor=${encodeURIComponent(valor)}`)
    .then(res => res.json())
    .then(data => {
      const tbody = document.querySelector("#tabla-resultado tbody");
      tbody.innerHTML = data.map((item, i) =>
        `<tr>
           <td>${i + 1}</td>
           <td>${item.nombre}</td>
           <td>${item.documento}</td>
           <td>
             <button
               class="btn btn-success btn-sm btn-confirmar"
               data-id="${item.idcliente}"
               data-bs-dismiss="modal">
               <i class="fa-solid fa-circle-check"></i>
             </button>
           </td>
         </tr>`
      ).join('');
    })
    .catch(console.error);
}

function buscarCliente() {
  const tipo    = document.getElementById("tipoBusquedaCliente").value;
  const metodo  = document.getElementById("selectMetodoCliente").value;
  const valor   = document.getElementById("vbuscadoCliente").value.trim();
  if (!valor) return document.querySelector("#tabla-resultado-cliente tbody").innerHTML = "";

  fetch(`http://localhost/fix360/app/controllers/propietario.controller.php?task=buscarPropietario&tipo=${tipo}&metodo=${metodo}&valor=${encodeURIComponent(valor)}`)
    .then(res => res.json())
    .then(data => {
      const tbody = document.querySelector("#tabla-resultado-cliente tbody");
      tbody.innerHTML = data.map((item, i) =>
        `<tr>
           <td>${i + 1}</td>
           <td>${item.nombre}</td>
           <td>${item.documento}</td>
           <td>
             <button
               class="btn btn-sm btn-success"
               data-id="${item.idcliente}"
               data-bs-dismiss="modal">
               <i class="fa-solid fa-circle-check"></i>
             </button>
           </td>
         </tr>`
      ).join('');
    })
    .catch(console.error);
}


function setFechaDefault() {
  const input = document.getElementById('fechaIngreso');
  const now = new Date(); const pad=n=>String(n).padStart(2,'0');
  const yyyy=now.getFullYear(), MM=pad(now.getMonth()+1), dd=pad(now.getDate());
  const hh=pad(now.getHours()), mm=pad(now.getMinutes());
  input.value = `${yyyy}-${MM}-${dd}T${hh}:${mm}`;
  const twoDaysAgo=new Date(now); twoDaysAgo.setDate(now.getDate()-2);
  input.min = `${twoDaysAgo.getFullYear()}-${pad(twoDaysAgo.getMonth()+1)}-${pad(twoDaysAgo.getDate())}T00:00`;
  input.max = `${yyyy}-${MM}-${dd}T23:59`;
}

function cargarSubcategorias() {
  fetch("http://localhost/fix360/app/controllers/subcategoria.controller.php?task=getServicioSubcategoria")
    .then(r=>r.json())
    .then(data=> data.forEach(item=> document.getElementById("subcategoria").insertAdjacentHTML('beforeend',
      `<option value="${item.idsubcategoria}">${item.subcategoria}</option>`
    ))).catch(console.error);
}

function cargarMecanicos() {
  fetch("http://localhost/fix360/app/controllers/mecanico.controller.php?task=getAllMecanico")
    .then(r=>r.json())
    .then(data=> data.forEach(item=> document.getElementById("mecanico").insertAdjacentHTML('beforeend',
      `<option value="${item.idcolaborador}">${item.nombres}</option>`
    ))).catch(console.error);
}

function cargarServicio() {
  const subc = document.getElementById("subcategoria").value;
  const sel = document.getElementById("servicio");
  sel.innerHTML = '<option value="">Seleccione una opción</option>';
  if (!subc) return;
  fetch(`http://localhost/fix360/app/controllers/servicio.controller.php?task=getServicioBySubcategoria&idsubcategoria=${subc}`)
    .then(r=>r.json())
    .then(data=> data.forEach(item=> sel.insertAdjacentHTML('beforeend',
      `<option value="${item.idservicio}">${item.servicio}</option>`
    ))).catch(console.error);
}



function cargarVehiculos() {
  const id = document.getElementById("hiddenIdPropietario").value;
  const sel = document.getElementById("vehiculo");
  sel.innerHTML = '<option value="">Seleccione una opción</option>';
  if (!id) return;
  fetch(`http://localhost/fix360/app/controllers/vehiculo.controller.php?task=getVehiculoByCliente&idcliente=${id}`)
    .then(r => r.json())
    .then(data => {
      data.forEach(item => {
        sel.insertAdjacentHTML('beforeend',
          `<option value="${item.idvehiculo}">${item.vehiculo}</option>`
        );
      });
    })
    .catch(console.error);
}


function onAgregarDetalle() {
  const serv   = document.getElementById('servicio');
  const mec    = document.getElementById('mecanico');
  const precio = parseFloat(document.getElementById('precio').value);
  const idServ = +serv.value;

  if (!idServ || !+mec.value) {
    return alert('Selecciona servicio y mecánico');
  }
  if (detalleArr.some(d => d.idservicio === idServ)) {
    return alert('Este servicio ya está en la lista');
  }

  detalleArr.push({ idservicio: idServ, idmecanico: +mec.value, precio });

  const option = serv.querySelector(`option[value="${idServ}"]`);
  if (option) option.disabled = true;

  const tbody = document.querySelector('#tabla-detalle tbody');
  const idx   = tbody.children.length + 1;
  const tr    = document.createElement('tr');
  tr.innerHTML = `
    <td>${idx}</td>
    <td>${serv.selectedOptions[0].text}</td>
    <td>${mec.selectedOptions[0].text}</td>
    <td>${precio.toFixed(2)}</td>
    <td><button class="btn btn-sm btn-danger">X</button></td>
  `;

  tr.querySelector('button').onclick = () => {
    const removeIndex = detalleArr.findIndex(d => d.idservicio === idServ);
    if (removeIndex > -1) detalleArr.splice(removeIndex, 1);
    tr.remove();
    if (option) option.disabled = false;
    recalcular();
  };

  tbody.appendChild(tr);
  recalcular();
}

function recalcular() {
  const total = detalleArr.reduce((s,i)=>s+i.precio,0);
  const igv= total*0.18;
  document.getElementById('subtotal').value = (total-igv).toFixed(2);
  document.getElementById('igv').value = igv.toFixed(2);
  document.getElementById('neto').value = total.toFixed(2);
}

function onAceptarOrden(e) {
  
  e.preventDefault();
  
  const confirmar = window.confirm('¿Estás seguro de que quieres registrar esta orden de servicio?');
  if (!confirmar) return; // si pulsa Cancelar, no hace nada

  // 1) Validar que haya al menos un servicio

  if (detalleArr.length===0) return alert('Agrega al menos un servicio');
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
  console.log('payload:', payload);
  fetch('http://localhost/fix360/app/controllers/OrdenServicio.controller.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  })
    .then(r => r.json())
    .then(js => {
      if (js.status === 'success') {
        showToast('Orden registrada exitosamente', 'SUCCESS', 1500);
        // Después de 1 segundo (una vez visible el toast) redirige:
        setTimeout(() => {
          window.location.href = 'listar-ordenes.php';
        }, 1000);
      } else {
        showToast('Error al registrar la orden de servicio', 'ERROR', 1500);
      }
    })
    .catch(err => {
      console.error('Fetch error:', err);
      showToast('Error de red o servidor','ERROR',2000);
    });
}  
