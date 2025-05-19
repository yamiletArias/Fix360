
document.addEventListener('DOMContentLoaded', () => {
  // — Elementos del formulario —
  const propietarioText = document.getElementById('propietario');
  const inputIdPropietario = document.getElementById('idpropietario');
  const hiddenIdCliente = document.getElementById('hiddenIdCliente');
  const clienteModalBtn = document.querySelector('#miModal .btn-close');

  const vehiculoSelect = document.getElementById('vehiculo');
  const kilometrajeInput = document.getElementById('kilometraje');
  const observacionesInput = document.getElementById('observaciones');
  const ingresogruaCheckbox = document.getElementById('ingresogrua');
  const fechaVentaInput = document.getElementById('fechaVenta');
  const monedaSelect = document.getElementById('moneda');
  const numserieInput = document.getElementById('numserie');
  const numcomInput   = document.getElementById('numcom');
  const tipoRadios    = document.querySelectorAll('input[name="tipo"]');

  // — Tablas de detalle —
  const tbodyProductos = document.querySelector('#tabla-detalle tbody');
  const tbodyServicios = document.querySelector('#tabla-servicios tbody');
  let detalleProductos = [];
  let detalleServicios = [];

  // — Autocompletado y agregado Productos —
  let productoSeleccionado = null;
  const inpProducto   = document.getElementById('producto');
  const inpStock      = document.getElementById('stock');
  const inpPrecio     = document.getElementById('precio');
  const inpCantidad   = document.getElementById('cantidad');
  const inpDescuento  = document.getElementById('descuento');
  const btnAgregarProd = document.getElementById('agregarProducto');

  function debounce(fn, delay=300){
    let t;
    return (...a)=>{ clearTimeout(t); t = setTimeout(()=>fn(...a),delay) };
  }

  inpProducto.addEventListener('input', debounce(() => {
    const q = inpProducto.value.trim();
    if(!q) return;
    fetch(`../controllers/Venta.controller.php?q=${encodeURIComponent(q)}&type=producto`)
      .then(r=>r.json())
      .then(arr=>{
        cerrarAutocompletes('producto');
        const list = document.createElement('div');
        list.id = 'autocomplete-producto';
        list.className='autocomplete-items';
        inpProducto.parentNode.append(list);
        if(!arr.length){
          list.innerHTML=`<div>No encontrado</div>`;
        } else arr.forEach(prod=>{
          const div = document.createElement('div');
          div.textContent = prod.subcategoria_producto;
          div.addEventListener('click', ()=>{
            productoSeleccionado = prod;
            inpProducto.value = prod.subcategoria_producto;
            inpPrecio.value   = prod.precio;
            inpStock.value    = prod.stock;
            inpCantidad.value = 1;
            inpDescuento.value= 0;
            cerrarAutocompletes('producto');
          });
          list.append(div);
        });
      });
  }), false);

  function cerrarAutocompletes(prefijo){
    document.querySelectorAll(`.autocomplete-items`).forEach(x=>x.remove());
  }
  document.addEventListener('click', e=>cerrarAutocompletes());

  btnAgregarProd.addEventListener('click', ()=>{
    if(!productoSeleccionado) { alert('Selecciona un producto válido'); return; }
    const cantidad  = parseFloat(inpCantidad.value)||0;
    const descuento = parseFloat(inpDescuento.value)||0;
    const precio    = parseFloat(inpPrecio.value)||0;
    if(cantidad<1||precio<=0){ alert('Datos incorrectos'); return; }
    if(detalleProductos.some(d=>d.idproducto===productoSeleccionado.idproducto)){
      alert('Ya agregado'); return;
    }
    const netoUnit = precio - descuento;
    const importe = parseFloat((netoUnit*cantidad).toFixed(2));
    const tr = document.createElement('tr');
    tr.dataset.idproducto = productoSeleccionado.idproducto;
    tr.innerHTML=`
      <td></td>
      <td>${productoSeleccionado.subcategoria_producto}</td>
      <td>${precio.toFixed(2)}</td>
      <td>${cantidad}</td>
      <td>${descuento.toFixed(2)}</td>
      <td>${importe.toFixed(2)}</td>
      <td><button class="btn btn-sm btn-danger">X</button></td>`;
    tr.querySelector('button').addEventListener('click', ()=>{
      detalleProductos = detalleProductos.filter(d=>d.idproducto!==productoSeleccionado.idproducto);
      tr.remove(); renumerar(tbodyProductos); calcularTotales();
    });
    detalleProductos.push({
      idproducto: productoSeleccionado.idproducto,
      cantidad, descuento, precioventa: precio
    });
    tbodyProductos.append(tr);
    renumerar(tbodyProductos);
    calcularTotales();
    // limpiar inputs
    inpProducto.value=''; inpStock.value=''; inpPrecio.value=''; inpCantidad.value=1; inpDescuento.value=0;
    productoSeleccionado = null;
  });

  // — Agregado de Servicios —
  const subcatSelect = document.getElementById('subcategoria');
  const servicioSelect = document.getElementById('servicio');
  const mecanicoSelect  = document.getElementById('mecanico');
  const precioSrvInput  = document.createElement('input');
        precioSrvInput.type='number';
        precioSrvInput.step='0.1';
        precioSrvInput.placeholder='Precio';
        precioSrvInput.className='form-control form-control-sm w-25 ms-2';
  servicioSelect.after(precioSrvInput);
  const btnAgregarSrv = document.getElementById('btnAgregar');

  // Cuando cambia subcategoría cargamos servicios
  subcatSelect.addEventListener('change', ()=>{
    const idsc = subcatSelect.value;
    servicioSelect.innerHTML=`<option value="">Elige servicio</option>`;
    if(!idsc) return;
    fetch(`../controllers/Servicio.controller.php?subcategoria=${idsc}`)
      .then(r=>r.json())
      .then(arr=>{
        arr.forEach(s=>{
          servicioSelect.add(new Option(s.servicio, s.idservicio));
        });
      });
  });

  btnAgregarSrv.addEventListener('click', ()=>{
    const idserv = parseInt(servicioSelect.value,10);
    const idmec  = parseInt(mecanicoSelect.value,10);
    const precio = parseFloat(precioSrvInput.value)||0;
    if(!idserv||!idmec||precio<=0){ alert('Completa servicio, mecánico y precio'); return;}
    if(detalleServicios.some(d=>d.idservicio===idserv && d.idmecanico===idmec)){
      alert('Servicio duplicado'); return;
    }
    const tr = document.createElement('tr');
    tr.dataset.idservicio = idserv;
    tr.dataset.idmecanico = idmec;
    tr.innerHTML=`
      <td></td>
      <td>${servicioSelect.selectedOptions[0].text}</td>
      <td>${mecanicoSelect.selectedOptions[0].text}</td>
      <td class="text-end">${precio.toFixed(2)}</td>
      <td><button class="btn btn-sm btn-danger">X</button></td>`;
    tr.querySelector('button').addEventListener('click', ()=>{
      detalleServicios = detalleServicios.filter(d=>!(d.idservicio===idserv&&d.idmecanico===idmec));
      tr.remove(); renumerar(tbodyServicios);
    });
    detalleServicios.push({ idservicio:idserv, idmecanico:idmec, precio });
    tbodyServicios.append(tr);
    renumerar(tbodyServicios);
    precioSrvInput.value='';
  });

  // — Renumerar filas —
  function renumerar(tbody){
    Array.from(tbody.children).forEach((tr,i)=>{
      tr.children[0].textContent = i+1;
    });
  }

  // — Cálculo de totales —
  function calcularTotales(){
    let totalImp=0, totalDesc=0;
    detalleProductos.forEach(d=>{
      totalImp  += (d.precioventa - d.descuento)*d.cantidad;
      totalDesc += d.descuento*d.cantidad;
    });
    const igv  = totalImp - (totalImp/1.18);
    const neto = totalImp/1.18;
    document.getElementById('neto').value = neto.toFixed(2);
    document.getElementById('totalDescuento').value = totalDesc.toFixed(2);
    document.getElementById('igv').value = igv.toFixed(2);
    document.getElementById('total').value = totalImp.toFixed(2);
  }

  // — Propietario: búsqueda y selección en modal —
  const tablaRes = document.querySelector('#tabla-resultado tbody');
  const vbuscado = document.getElementById('vbuscado');
  const selectMetodo = document.getElementById('selectMetodo');
  const tipoBusquedaRadios = document.getElementsByName('tipoBusqueda');

  function actualizarOpciones(){
    const isEmp = document.getElementById('rbtnempresa').checked;
    selectMetodo.innerHTML = isEmp
      ? '<option value="ruc">RUC</option><option value="razonsocial">Razón Social</option>'
      : '<option value="dni">DNI</option><option value="nombre">Apellidos y Nombres</option>';
  }
  actualizarOpciones();

  function buscarPropietario(){
    const tipo = document.getElementById('rbtnempresa').checked ? 'empresa':'persona';
    const metodo = selectMetodo.value;
    const valor = vbuscado.value.trim();
    if(!valor){ tablaRes.innerHTML=''; return; }
    fetch(`../controllers/propietario.controller.php?task=buscarPropietario&tipo=${tipo}&metodo=${metodo}&valor=${encodeURIComponent(valor)}`)
      .then(r=>r.json())
      .then(arr=>{
        tablaRes.innerHTML='';
        arr.forEach((it,i)=>{
          const tr=document.createElement('tr');
          tr.innerHTML=`
            <td>${i+1}</td>
            <td>${it.nombre}</td>
            <td>${it.documento}</td>
            <td><button class="btn btn-sm btn-success" data-id="${it.idcliente}">✔</button></td>`;
          tablaRes.append(tr);
        });
      });
  }
  let timerProp;
  vbuscado.addEventListener('input', ()=>{ clearTimeout(timerProp); timerProp=setTimeout(buscarPropietario,300);});
  selectMetodo.addEventListener('change', ()=>{ clearTimeout(timerProp); timerProp=setTimeout(buscarPropietario,300);});
  document.getElementById('tabla-resultado').addEventListener('click', e=>{
    if(!e.target.matches('button')) return;
    const id = e.target.dataset.id;
    const nombre = e.target.closest('tr').cells[1].textContent;
    inputIdPropietario.value = id;
    hiddenIdCliente.value    = id;
    propietarioText.value    = nombre;
    clienteModalBtn.click(); // cierra modal
    cargarVehiculos();
  });
  document.getElementsByName('tipoBusqueda').forEach(rb=>rb.addEventListener('change', ()=>{ actualizarOpciones(); buscarPropietario(); }));

  // — Carga vehículos tras elegir propietario —
  function cargarVehiculos(){
    vehiculoSelect.innerHTML = '<option value="">Sin vehículo</option>';
    const idc = hiddenIdCliente.value;
    if(!idc) return;
    fetch(`../controllers/vehiculo.controller.php?task=getVehiculoByCliente&idcliente=${idc}`)
      .then(r=>r.json()).then(arr=>{
        arr.forEach(v=>{
          vehiculoSelect.add(new Option(v.placa, v.idvehiculo));
        });
      });
  }

  // — Generar serie y comprobante aleatorios —
  function genSerie(pref){ return `${pref}${Math.floor(Math.random()*900+100)}`; }
  function genComprobante(pref){ return `${pref}-${Math.floor(Math.random()*1e7).toString().padStart(7,'0')}`; }
  function inicializarCampos(){
    const t = document.querySelector('input[name="tipo"]:checked').value;
    const p = t==='boleta'?'B':'F';
    numserieInput.value = genSerie(p);
    numcomInput.value   = genComprobante(p);
  }
  tipoRadios.forEach(r=>r.addEventListener('change', inicializarCampos));
  inicializarCampos();

  // — Fecha por defecto hoy —
  fechaVentaInput.value = new Date().toISOString().slice(0,16);

  // — Botón Finalizar Venta —
  document.getElementById('btnFinalizarVenta').addEventListener('click', e=>{
    e.preventDefault();
    if(detalleProductos.length===0){
      alert('Agrega al menos un producto.'); return;
    }
    const servicios = detalleServicios; // array ya poblado
    const data = {
      conOrden:      servicios.length>0?1:0,
      idcolaborador: <?= $_SESSION['login']['idcolaborador'] ?? 0 ?>,
      idpropietario: parseInt(inputIdPropietario.value,10),
      idcliente:     parseInt(hiddenIdCliente.value,10),
      idvehiculo:    vehiculoSelect.value?parseInt(vehiculoSelect.value,10):null,
      kilometraje:   parseFloat(kilometrajeInput.value)||0,
      observaciones: observacionesInput.value.trim(),
      ingresogrua:   ingresogruaCheckbox.checked?1:0,
      fechaingreso:  fechaVentaInput.value,
      tipocom:       document.querySelector('input[name="tipo"]:checked').value,
      fechahora:     fechaVentaInput.value,
      numserie:      numserieInput.value,
      numcom:        numcomInput.value,
      moneda:        monedaSelect.value,
      productos:     detalleProductos,
      servicios:     detalleServicios
    };
    fetch('../controllers/Venta.controller.php', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify(data)
    })
    .then(r=>r.json())
    .then(j=>{
      if(j.status==='success'){
        alert('Venta registrada OK');
        window.location='listar-ventas.php';
      } else {
        alert('Error: '+j.message);
      }
    })
    .catch(err=>{ console.error(err); alert('Error de conexión'); });
  });

});

