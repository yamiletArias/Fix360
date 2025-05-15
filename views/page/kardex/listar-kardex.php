<?php

const NAMEVIEW = "Kardex";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>

<div class="container-main">
  <div class="card border mb-4">
    <div class="card-header">
      <div class="row align-items-center">
        <div class="col-md-6 mb-2 mb-md-0">
          <div class="input-group">
            <span class="input-group-text input">Producto:</span>
            <input type="text" id="producto" class="form-control input" placeholder="busca producto..." Autocomplete="off">
            <div id="autocomplete-producto" class="autocomplete-items"></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="btn-group" role="group">
            <button type="button" data-modo="dia" class="btn btn-primary">Día</button>
            <button type="button" data-modo="semana" class="btn btn-primary">Semana</button>
            <button type="button" data-modo="mes" class="btn btn-primary">Mes</button>
            <button type="button" class="btn btn-danger text-white">
              <i class="fa-solid fa-file-pdf"></i>
            </button>
          </div>
        </div>
        <div class="col-md-2 text-end">
          <input type="date" id="fecha" class="form-control input">
        </div>
      </div>
    </div>


<div class="card-body">
  <div class="table-responsive">
    <table id="miTabla" class="table table-striped display">
      <thead>
        <tr>
          <th>#</th>
          <th>Fecha</th>
          <th>Flujo</th>
          <th>Tipo Movimiento</th>
          <th>Cantidad</th>
          <th>Saldo</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>


  </div>
</div>
</div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const inpProducto = document.getElementById('producto');
  const listaAuto   = document.getElementById('autocomplete-producto');
  const fechaInput  = document.getElementById('fecha');
  const botones     = document.querySelectorAll('button[data-modo]');
  const tablaBody   = document.querySelector('#miTabla tbody');
  const API_MOV     = 'http://localhost/Fix360/app/controllers/movimiento.controller.php';

  let selectedProduct = { idproducto: null };
  let currentModo = 'dia';

  // ---- Interfaz de fecha ----
  const hoy = new Date().toISOString().split('T')[0];
  fechaInput.value = hoy;

  // ---- Debounce ----
  function debounce(fn, delay) {
    let timeout;
    return function(...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => fn.apply(this, args), delay);
    }
  }

  // ---- Autocomplete productos ----
  async function fetchProductos(q) {
    try {
      const res = await fetch(`http://localhost/Fix360/app/controllers/Venta.controller.php?q=${encodeURIComponent(q)}&type=producto`);
      if (res.ok && res.headers.get('Content-Type').includes('application/json')) {
        return await res.json();
      }
    } catch (e) {
      console.error('fetchProductos error:', e);
    }
    return [];
  }

  const showAuto = debounce(async () => {
    const q = inpProducto.value.trim();
    // siempre buscar aunque sea string vacío para mostrar todos
    const data = await fetchProductos(q);
    listaAuto.innerHTML = '';
    if (!data.length) {
      listaAuto.innerHTML = '<div>No se encontraron productos</div>';
      return;
    }
    data.forEach(prod => {
      const div = document.createElement('div');
      div.textContent = prod.subcategoria_producto;
      div.tabIndex = 0;
      div.addEventListener('click', () => selectProd(prod));
      listaAuto.appendChild(div);
    });
  }, 300);

  inpProducto.addEventListener('input', showAuto);
  inpProducto.addEventListener('focus', showAuto);
  document.addEventListener('click', e => {
    if (!listaAuto.contains(e.target) && e.target !== inpProducto) {
      listaAuto.innerHTML = '';
    }
  });

  function selectProd(prod) {
    selectedProduct = prod;
    inpProducto.value = prod.subcategoria_producto;
    listaAuto.innerHTML = '';
    cargarMovimientos();
  }

  // ---- Botones de periodo ----
  botones.forEach(btn => btn.addEventListener('click', () => {
    currentModo = btn.dataset.modo;
    botones.forEach(b => b.classList.toggle('active', b === btn));
    cargarMovimientos();
  }));

  // ---- Cargar movimientos ----
  async function cargarMovimientos() {
    if (!selectedProduct.idproducto) return;
    try {
      const url = `${API_MOV}?idproducto=${selectedProduct.idproducto}&modo=${currentModo}&fecha=${fechaInput.value}`;
      const res = await fetch(url);
      const contentType = res.headers.get('Content-Type') || '';
      if (!res.ok || !contentType.includes('application/json')) {
        console.error('Respuesta no JSON:', await res.text());
        return;
      }
      const json = await res.json();
      if (json.status !== 'success') return;
      pintar(json.data);
    } catch (e) {
      console.error('cargarMovimientos error:', e);
    }
  }

  // ---- Pintar tabla ----
  function fmtFecha(iso) {
    if (!iso) return '';
    const d = new Date(iso);
    const pad = v => String(v).padStart(2,'0');
    return `${pad(d.getDate())}/${pad(d.getMonth()+1)}/${d.getFullYear()}`;
  }

  function pintar(data) {
    if ($.fn.DataTable.isDataTable('#miTabla')) {
      $('#miTabla').DataTable().destroy();
    }
    tablaBody.innerHTML = '';
    data.forEach((m, i) => {
      tablaBody.insertAdjacentHTML('beforeend', `
        <tr>
          <td>${i+1}</td>
          <td>${fmtFecha(m.fecha)}</td>
          <td>${m.flujo}</td>
          <td>${m.tipo_movimiento}</td>
          <td>${m.cantidad}</td>
          <td>${m.saldo_restante}</td>
        </tr>
      `);
    });
    $('#miTabla').DataTable({
      paging: true,
      info: true,
      columnDefs: [{ orderable: false, targets: -1 }],
      language: {
        lengthMenu: "Mostrar _MENU_ por página",
        zeroRecords: "No hay resultados",
        info: "Mostrar _PAGE_ de _PAGES_",
        infoEmpty: "0 de 0",
        emptyTable: "No hay datos"
      }
    });
  }

  // ---- Inicialización inicial ----
  (async () => {
    const all = await fetchProductos('');
    if (all.length) selectProd(all[0]);
  })();

  fechaInput.addEventListener('change', cargarMovimientos);
});
</script>
