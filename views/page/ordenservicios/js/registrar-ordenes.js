// views/page/ordenservicios/js/registrar-ordenes.js
// Script unificado para registro de orden y manejo de modales

document.addEventListener('DOMContentLoaded', () => {
    // —— ELEMENTOS COMUNES ——
    const tiposervicioSelect = document.getElementById('subcategoria');
    const servicioSelect     = document.getElementById('servicio');
    const mecanicoSelect     = document.getElementById('mecanico');
    const vehiculoSelect     = document.getElementById('vehiculo');
    const hiddenIdCliente    = document.getElementById('hiddenIdCliente');
    const precioInput        = document.getElementById('precio-input');
    const btnAgregar         = document.getElementById('btnAgregarDetalle');
    const btnAceptar         = document.getElementById('btnAceptarOrden');
    const tbody              = document.querySelector('#tabla-detalle tbody');
    const kmInput            = document.getElementById('kilometraje');
    const fechaIn            = document.getElementById('fechaIngreso');
    const obsInput           = document.getElementById('observaciones');
    const ingGruaChk         = document.getElementById('ingresogrua');
    const detalleArr         = [];
  
    // —— MODAL PROPIETARIO ——
    function actualizarOpcionesProp() {
      const select = document.getElementById('selectMetodo');
      const persona = document.getElementById('rbtnpersona').checked;
      select.innerHTML = persona
        ? '<option value="dni">DNI</option><option value="nombre">Nombre</option>'
        : '<option value="ruc">RUC</option><option value="razonsocial">Razón Social</option>';
    }
    async function buscarPropietario() {
      const tipo   = document.getElementById('rbtnpersona').checked ? 'persona' : 'empresa';
      const metodo = document.getElementById('selectMetodo').value;
      const valor  = document.getElementById('vbuscado').value.trim();
      const tbodyR = document.querySelector('#tabla-resultado tbody');
      if (!valor) { tbodyR.innerHTML = ''; return; }
      const url = `/app/controllers/Propietario.controller.php?tipo=${tipo}&metodo=${metodo}&valor=${encodeURIComponent(valor)}`;
      try {
        const res  = await fetch(url);
        const data = await res.json();
        tbodyR.innerHTML = data.map((it,i) => `
          <tr>
            <td>${i+1}</td>
            <td>${it.nombre}</td>
            <td>${it.documento}</td>
            <td><button class="btn btn-success btn-sm btn-confirmar" data-id="${it.idcliente}"><i class="fa-solid fa-circle-check"></i></button></td>
          </tr>
        `).join('');
      } catch(e) { console.error(e); }
    }
    document.getElementById('vbuscado').addEventListener('keyup', buscarPropietario);
    document.getElementById('rbtnpersona').addEventListener('click', () => { actualizarOpcionesProp(); buscarPropietario(); });
    document.getElementById('rbtnempresa').addEventListener('click', () => { actualizarOpcionesProp(); buscarPropietario(); });
    document.querySelector('#tabla-resultado').addEventListener('click', e => {
      const btn = e.target.closest('.btn-confirmar'); if (!btn) return;
      hiddenIdCliente.value = btn.dataset.id;
      document.getElementById('propietario').value = btn.closest('tr').cells[1].textContent;
      setTimeout(() => bootstrap.Modal.getOrCreateInstance('#miModal').hide(), 100);
    });
    actualizarOpcionesProp();
  
    // —— MODAL CLIENTE ——
    let clienteTimer;
    const vbuscadoCliente = document.getElementById('vbuscadoCliente');
    const selectMetodoCl  = document.getElementById('selectMetodoCliente');
    async function buscarCliente() {
      const tipo   = document.getElementById('tipoBusquedaCliente').value;
      const metodo = selectMetodoCl.value;
      const valor  = vbuscadoCliente.value.trim();
      const tbodyC = document.querySelector('#tabla-resultado-cliente tbody');
      if (!valor) { tbodyC.innerHTML = ''; return; }
      const url = `/app/controllers/propietario.controller.php?task=buscarPropietario&tipo=${tipo}&metodo=${metodo}&valor=${encodeURIComponent(valor)}`;
      try {
        const data = await (await fetch(url)).json();
        tbodyC.innerHTML = data.map((it,i) => `
          <tr>
            <td>${i+1}</td>
            <td>${it.nombre}</td>
            <td>${it.documento}</td>
            <td><button class="btn btn-success btn-sm" onclick="seleccionarCliente(${it.idcliente}, '${it.nombre}')"><i class="fa-solid fa-circle-check"></i></button></td>
          </tr>
        `).join('');
      } catch(e) { console.error(e); }
    }
    vbuscadoCliente.addEventListener('input', () => { clearTimeout(clienteTimer); clienteTimer = setTimeout(buscarCliente, 300); });
    selectMetodoCl.addEventListener('change', () => { clearTimeout(clienteTimer); clienteTimer = setTimeout(buscarCliente, 300); });
  
    // —— CARGAR SUBCATEGORÍA, SERVICIOS, MECÁNICOS, VEHÍCULOS ——
    fetch('/app/controllers/subcategoria.controller.php?task=getServicioSubcategoria')
      .then(r => r.json()).then(data => {
        tiposervicioSelect.innerHTML = '<option selected>Eliga un tipo de servicio</option>';
        data.forEach(it => tiposervicioSelect.append(new Option(it.subcategoria, it.idsubcategoria)));
      });
    fetch('/app/controllers/mecanico.controller.php?task=getAllMecanico')
      .then(r => r.json()).then(data => {
        mecanicoSelect.innerHTML = '<option selected>Eliga un mecánico</option>';
        data.forEach(it => mecanicoSelect.append(new Option(it.nombres, it.idcolaborador)));
      });
    tiposervicioSelect.addEventListener('change', () => {
      servicioSelect.innerHTML = '<option value="">Seleccione una opción</option>';
      const idSub = tiposervicioSelect.value; if (!idSub) return;
      fetch(`/app/controllers/servicio.controller.php?task=getServicioBySubcategoria&idsubcategoria=${idSub}`)
        .then(r => r.json()).then(data => {
          data.forEach(it => servicioSelect.append(new Option(it.servicio, it.idservicio)));
        });
    });
    hiddenIdCliente.addEventListener('change', () => {
      vehiculoSelect.innerHTML = '<option value="">Seleccione un vehículo</option>';
      const idCli = hiddenIdCliente.value; if (!idCli) return;
      fetch(`/app/controllers/vehiculo.controller.php?task=getVehiculoByCliente&idcliente=${idCli}`)
        .then(r => r.json()).then(data => {
          data.forEach(it => vehiculoSelect.append(new Option(it.vehiculo, it.idvehiculo)));
        });
    });
  
    // —— FECHA MÍN/MÁX y default ——
    const today = new Date();
    const y = today.getFullYear(), m = String(today.getMonth()+1).padStart(2,'0'), d = String(today.getDate()).padStart(2,'0');
    fechaIn.value = `${y}-${m}-${d}`;
    fechaIn.min   = `${y}-${m}-${String(today.getDate()-2).padStart(2,'0')}`;
    fechaIn.max   = `${y}-${m}-${d}`;
  
    // —— AGREGAR DETALLE ——
    function recalcular() {
      const sub = detalleArr.reduce((s,i) => s + i.precio, 0);
      const igv = sub - sub/1.18;
      const net = sub/1.18;
      document.getElementById('subtotal').value = sub.toFixed(2);
      document.getElementById('igv').value      = igv.toFixed(2);
      document.getElementById('neto').value     = net.toFixed(2);
    }
    btnAgregar.addEventListener('click', () => {
      const idserv = +servicioSelect.value;
      const mecId  = +mecanicoSelect.value;
      console.log('botón Agregar es:', btnAgregar);
console.log('input precio existe:', precioInput);
console.log('input precio.value:', precioInput.value);

      const precio = parseFloat(precioInput.value);
      if (!idserv || !mecId) return alert('Selecciona servicio y mecánico');
      if (isNaN(precio) || precio <= 0) return alert('Precio inválido');
      detalleArr.push({ idservicio: idserv, precio });
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${tbody.children.length+1}</td>
        <td>${servicioSelect.selectedOptions[0].textContent}</td>
        <td>${mecanicoSelect.selectedOptions[0].textContent}</td>
        <td>${precio.toFixed(2)}</td>
        <td><button class="btn btn-sm btn-danger">X</button></td>
      `;
      tr.querySelector('button').onclick = () => {
        detalleArr.splice([...tbody.children].indexOf(tr), 1);
        tr.remove();
        recalcular();
      };
      tbody.appendChild(tr);
      recalcular();
      precioInput.value = '';
    });
  
    // —— GUARDAR ORDEN ——
    btnAceptar.addEventListener('click', async e => {
      e.preventDefault();
      if (detalleArr.length === 0) return alert('Agrega al menos un servicio');
      const payload = {
        idmecanico:        +mecanicoSelect.value,
        idpropietario:     +hiddenIdCliente.value,
        idcliente:         +hiddenIdCliente.value,
        idvehiculo:        +vehiculoSelect.value,
        kilometraje:       parseFloat(kmInput.value),
        observaciones:     obsInput?.value || '',
        ingresogrua:       ingGruaChk?.checked || false,
        fechaingreso:      fechaIn.value,
        fecharecordatorio: null,
        detalle:           detalleArr
      };
      try {
        const res = await fetch('/app/controllers/ordenservicio.controller.php', {
          method: 'POST', headers: {'Content-Type':'application/json'},
          body: JSON.stringify(payload)
        });
        const js = await res.json();
        if (js.status === 'success') showToast('Orden registrada','SUCCESS',1000);
        else showToast('Error al registrar','ERROR',1500);
      } catch (err) {
        console.error(err);
        showToast('Error de conexión','ERROR',1500);
      }
    });
  });
  
  // Función auxiliar para el modal cliente
  function seleccionarCliente(id, nombre) {
    document.getElementById('hiddenIdCliente').value = id;
    document.getElementById('cliente').value = nombre;
    setTimeout(() => bootstrap.Modal.getOrCreateInstance('#ModalCliente').hide(), 100);
  }
  