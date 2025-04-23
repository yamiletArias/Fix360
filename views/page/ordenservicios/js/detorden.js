document.addEventListener('DOMContentLoaded', () => {
  // --- Variables del DOM ---
  const rbtnPersona    = document.getElementById('rbtnpersona');
  const rbtnEmpresa    = document.getElementById('rbtnempresa');
  const selectMetodo   = document.getElementById('selectMetodo');
  const vbuscado       = document.getElementById('vbuscado');
  const tbodyProp      = document.querySelector('#tabla-resultado tbody');
  const hiddenCliente  = document.getElementById('hiddenIdCliente');
  const propietarioIn  = document.getElementById('propietario');

  const subcatSelect   = document.getElementById('subcategoria');
  const servSelect     = document.getElementById('servicio');
  const mecSelect      = document.getElementById('mecanico');
  const vehSelect      = document.getElementById('vehiculo');

  const fechaInput     = document.getElementById('fechaIngreso');
  const kmInput        = document.getElementById('kilometraje');
  const obsInput       = document.getElementById('observaciones');
  const gruaCheckbox   = document.getElementById('ingresogrua');

  const btnAgregar     = document.getElementById('btnAgregarDetalle');
  const btnAceptar     = document.getElementById('btnAceptarOrden');

  const detalleArr     = [];

  // --- Funciones Propietario Modal ---
  function actualizarOpciones() {
    selectMetodo.innerHTML = '';
    if (rbtnPersona.checked) {
      selectMetodo.innerHTML = '<option value="dni">DNI</option><option value="nombre">Nombre</option>';
    } else {
      selectMetodo.innerHTML = '<option value="ruc">RUC</option><option value="razonsocial">Razón Social</option>';
    }
  }

  function buscarPropietario() {
    const tipo   = rbtnPersona.checked ? 'persona' : 'empresa';
    const metodo = selectMetodo.value;
    const valor  = vbuscado.value.trim();
    if (!valor) { tbodyProp.innerHTML = ''; return; }

    fetch(`http://localhost/fix360/app/controllers/Propietario.controller.php?tipo=${encodeURIComponent(tipo)}&metodo=${encodeURIComponent(metodo)}&valor=${encodeURIComponent(valor)}`)
      .then(res => res.json())
      .then(data => {
        tbodyProp.innerHTML = '';
        data.forEach((item,i) => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${i+1}</td>
            <td>${item.nombre}</td>
            <td>${item.documento}</td>
            <td><button class="btn btn-success btn-sm btn-confirmar" data-id="${item.idcliente}" data-bs-dismiss="modal"><i class="fa-solid fa-circle-check"></i></button></td>
          `;
          tbodyProp.appendChild(tr);
        });
      })
      .catch(console.error);
  }

  tbodyProp.addEventListener('click', e => {
    const btn = e.target.closest('.btn-confirmar');
    if (!btn) return;
    const id    = btn.dataset.id;
    const name  = btn.closest('tr').cells[1].textContent;
    hiddenCliente.value = id;
    propietarioIn.value = name;
    setTimeout(() => bootstrap.Modal.getOrCreateInstance(document.getElementById('miModal')).hide(), 100);
  });

  // Propietario modal events
  rbtnPersona.addEventListener('click', () => { actualizarOpciones(); buscarPropietario(); });
  rbtnEmpresa.addEventListener('click', () => { actualizarOpciones(); buscarPropietario(); });
  vbuscado.addEventListener('keyup', () => { clearTimeout(window._propTimer); window._propTimer = setTimeout(buscarPropietario, 300); });
  document.addEventListener('DOMContentLoaded', actualizarOpciones);

  // --- Inicializar Fecha ---
  (function setFechaDefault() {
    const t = new Date();
    fechaInput.value = `${t.getFullYear()}-${String(t.getMonth()+1).padStart(2,'0')}-${String(t.getDate()).padStart(2,'0')}`;
  })();


  fetch('http://localhost/fix360/app/controllers/mecanico.controller.php?task=getAllMecanico')
    .then(r=>r.json()).then(list=>{
      mecSelect.innerHTML = '<option value="">Eliga un mecánico</option>';
      list.forEach(it=> mecSelect.append(new Option(it.nombres, it.idcolaborador)));
    }).catch(console.error);

  // Servicios por subcategoria
  subcatSelect.addEventListener('change', () => {
    servSelect.innerHTML = '<option value="">Seleccione una opción</option>';
    const id = subcatSelect.value;
    if (!id) return;
    fetch(`http://localhost/fix360/app/controllers/servicio.controller.php?task=getServicioBySubcategoria&idsubcategoria=${id}`)
      .then(r=>r.json()).then(list=>{
        list.forEach(it => {
          const opt = new Option(it.servicio, it.idservicio);
          opt.dataset.precio = it.precio;
          servSelect.append(opt);
        });
      }).catch(console.error);
  });

  // Vehículos por cliente
  hiddenCliente.addEventListener('change', () => {
    vehSelect.innerHTML = '<option value="">Seleccione un vehículo</option>';
    const id = hiddenCliente.value;
    if (!id) return;
    fetch(`http://localhost/fix360/app/controllers/vehiculo.controller.php?task=getVehiculoByCliente&idcliente=${id}`)
      .then(r=>r.json()).then(list=>{
        list.forEach(it => vehSelect.append(new Option(it.vehiculo, it.idvehiculo)));
      }).catch(console.error);
  });

  // --- Detalle de Orden ---
  function recalcular() {
    const sub = detalleArr.reduce((s,i)=>s+i.precio,0);
    const igv = sub - sub/1.18;
    const net = sub/1.18;
    document.getElementById('subtotal').value = sub.toFixed(2);
    document.getElementById('igv').value      = igv.toFixed(2);
    document.getElementById('neto').value     = net.toFixed(2);
  }

  btnAgregar.addEventListener('click', () => {
    const idS  = +servSelect.value;
    const txtS = servSelect.selectedOptions[0]?.textContent || '';
    const pr   = parseFloat(servSelect.selectedOptions[0]?.dataset.precio || 0);
    const idM  = +mecSelect.value;
    const txtM = mecSelect.selectedOptions[0]?.textContent || '';
    if (!idS || !idM) return alert('Selecciona servicio y mecánico');
    detalleArr.push({idservicio:idS,precio:pr});
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${tbodyProp.rows.length+1}</td><td>${txtS}</td><td>${txtM}</td><td>${pr.toFixed(2)}</td><td><button class="btn btn-sm btn-danger">X</button></td>`;
    tr.querySelector('button').onclick = () => {
      const idx = Array.from(tbodyProp.rows).indexOf(tr);
      detalleArr.splice(idx,1); tr.remove(); recalcular();
    };
    document.querySelector('#tabla-detalle tbody').appendChild(tr);
    recalcular();
  });

  // --- Enviar Orden ---
  btnAceptar.addEventListener('click', e => {
    e.preventDefault();
    if (detalleArr.length === 0) return alert('Agrega al menos un servicio');
    const payload = {
      idmecanico:     +mecSelect.value,
      idpropietario:  +hiddenCliente.value,
      idcliente:      +hiddenCliente.value,
      idvehiculo:     +vehSelect.value,
      kilometraje:    parseFloat(kmInput.value),
      observaciones:  obsInput.value,
      ingresogrua:    gruaCheckbox.checked,
      fechaingreso:   fechaInput.value,
      fecharecordatorio: null,
      detalle:        detalleArr
    };
    fetch('http://localhost/fix360/app/controllers/OrdenServicio.controller.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    })
    .then(r=>r.json())
    .then(js=>{
      if (js.status==='success') {
        Swal.fire({icon:'success',title:'¡Orden guardada!',timer:1500})
          .then(()=> window.location.href='listar-ordenes.php');
      } else {
        Swal.fire({icon:'error',title:'Error',text:js.message});
      }
    });
  });
});
