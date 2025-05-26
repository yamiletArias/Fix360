<?php

const NAMEVIEW = "Kardex";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>

<div class="container-main">
  <div class="col-md-4 mb-3">
    <div class="btn-group" role="group">
      <button type="button" data-modo="dia" class="btn btn-primary active">Día</button>
      <button type="button" data-modo="semana" class="btn btn-primary">Semana</button>
      <button type="button" data-modo="mes" class="btn btn-primary">Mes</button>
      <button type="button" id="btnPdfKardex" class="btn btn-danger text-white">
        <i class="fa-solid fa-file-pdf"></i>
      </button>
    </div>
  </div>

  <div class="row align-items-center">
    <div class="col-md-4 mb-3">
      <div class="form-floating">
        <input type="text" id="producto" class="form-control input" placeholder="busca producto..." autocomplete="off" autofocus>
        <div id="autocomplete-producto" class="autocomplete-items"></div>
        <label for="producto">Producto</label>
      </div>
    </div>

    <div class="col-md-2 mb-3">
      <div class="form-floating ">
        <input type="number" class="form-control input" step="0.1" id="stock_actual" name="stock_actual" placeholder="stock actual" min="0" disabled />
        <label for="stock_actual">Stock Actual</label>
      </div>
    </div>
    <div class="col-md-2 mb-3">
      <div class="form-floating ">
        <input type="number" class="form-control input" step="0.1" id="stock_min" name="stock_min" placeholder="stock min" min="0" disabled />
        <label for="stock_min">Stock min.</label>
      </div>
    </div>
    <div class="col-md-2 mb-3">
      <div class="form-floating ">
        <input type="number" class="form-control input" step="0.1" id="stock_max" name="stock_max" placeholder="stock max" min="0" disabled />
        <label for="stock_max">Stock max.</label>
      </div>
    </div>

    <div class="col-md-2 text-end mb-3">
      <input type="date" id="fecha" class="form-control input">
    </div>

    <div class="table-responsive w-100">
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

<?php require_once "../../partials/_footer.php"; ?>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const inpProducto = document.getElementById('producto');
    const listaAuto = document.getElementById('autocomplete-producto');
    const fechaInput = document.getElementById('fecha');
    const botones = document.querySelectorAll('button[data-modo]');
    const tablaBody = document.querySelector('#miTabla tbody');
    const API_MOV = 'http://localhost/Fix360/app/controllers/Movimiento.controller.php';
    const API_STOCK = 'http://localhost/Fix360/app/controllers/Kardex.Controller.php?task=getStock';

    let selectedProduct = {
      idproducto: null
    };
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
        if (res.ok && res.headers.get('Content-Type')?.includes('application/json')) {
          return await res.json();
        }
      } catch (e) {
        console.error('fetchProductos error:', e);
      }
      return [];
    }

    function initWithFirstProduct() {
      fetchProductos('').then(all => {
        if (all.length) selectProd(all[0]);
        else mostrarMensajeTabla('Selecciona un producto para visualizar sus movimientos');
      });
    }

    // Si no existe el input, iniciamos con primer producto y salimos
    if (!inpProducto) {
      console.warn('Elemento #producto no encontrado, cargando primer producto automáticamente.');
      initWithFirstProducto();
      return;
    }

    async function mostrarOpcionesProducto(input) {
      listaAuto.innerHTML = '';
      const termino = input.value.trim();
      if (!termino) return;

      const data = await fetchProductos(termino);
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
    }


    const showAuto = debounce(async () => {
      const q = inpProducto.value.trim();
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

    // 1) Sólo para búsqueda manual con debounce (letras o espacios)
    inpProducto.addEventListener('input', function() {
      const q = this.value.trim();
      const esSoloDigitos = /^\d+$/.test(q);
      if (!esSoloDigitos) {
        showAuto();
      }
      // si es sólo dígitos, esperamos al ENTER del scanner
    });

    // 2) Para el lector de código de barras (ENTER)
    // 2) Para el lector de código de barras (ENTER):
    inpProducto.addEventListener('keyup', function(e) {
      if (e.key !== 'Enter') return;
      e.preventDefault();

      const codigo = this.value.trim();
      if (!codigo) return;

      // Cancelamos cualquier dropdown pendiente
      listaAuto.innerHTML = '';

      // Hacemos fetch inmediato con el código escaneado
      fetchProductos(codigo).then(data => {
        if (Array.isArray(data) && data.length) {
          // Selecciona el producto
          selectProd(data[0]);

          // En lugar de borrar el valor,
          // dejamos el nombre y seleccionamos todo el texto:
          inpProducto.select();
        } else {
          mostrarMensajeTabla('No se encontraron productos para ese código');
          // si quisieras borrar en este caso:
          // this.value = '';
        }
      });
    });


    document.addEventListener('click', e => {
      if (!listaAuto.contains(e.target) && e.target !== inpProducto) {
        listaAuto.innerHTML = '';
      }
    });

    function selectProd(prod) {
      selectedProduct = prod;
      if (inpProducto) inpProducto.value = prod.subcategoria_producto;
      listaAuto.innerHTML = '';
      cargarStock();
      cargarMovimientos();
    }

    // ---- Botones de periodo ----
    botones.forEach(btn => btn.addEventListener('click', () => {
      currentModo = btn.dataset.modo;
      botones.forEach(b => b.classList.toggle('active', b === btn));
      cargarMovimientos();
    }));

    // ---- Cargar stock y llenar inputs ----
    async function cargarStock() {
      if (!selectedProduct.idproducto) return;
      try {
        const res = await fetch(`${API_STOCK}&idproducto=${selectedProduct.idproducto}`);
        const json = await res.json();
        const actual = json.stock_actual ?? '';
        const min = json.stockmin ?? '';
        const max = json.stockmax ?? '';

        // Rellenar los campos
        document.getElementById('stock_actual').value = actual;
        document.getElementById('stock_min').value = min;
        document.getElementById('stock_max').value = max;

        // Verificar niveles y mostrar alert
        const a = parseFloat(actual);
        const mn = parseFloat(min);
        const mx = parseFloat(max);

        if (!isNaN(a) && !isNaN(mn) && a < mn) {
          showToast(`¡Atención! El stock actual (${a}) ha bajado por debajo del mínimo (${mn}).`, 'WARNING', 5000);
        }
        if (!isNaN(a) && !isNaN(mx) && a > mx) {
          showToast(`¡Atención! El stock actual (${a}) ha superado el máximo (${mx}).`, 'WARNING', 5000);
        }

      } catch (e) {
        console.error('cargarStock error:', e);
      }
    }


    // ---- Cargar movimientos ----
    async function cargarMovimientos() {
      if (!selectedProduct.idproducto) {
        mostrarMensajeTabla('Buscar un producto para ver sus movimientos');
        return;
      }
      try {
        const url = `${API_MOV}?idproducto=${selectedProduct.idproducto}&modo=${currentModo}&fecha=${fechaInput.value}`;
        const res = await fetch(url);
        const contentType = res.headers.get('Content-Type') || '';
        if (!res.ok || !contentType.includes('application/json')) {
          console.error('Respuesta no JSON:', await res.text());
          return;
        }
        const json = await res.json();
        if (json.status !== 'success') {
          mostrarMensajeTabla('No hay movimientos para este periodo');
          return;
        }
        pintar(json.data);
      } catch (e) {
        console.error('cargarMovimientos error:', e);
      }
    }

    function mostrarMensajeTabla(msg) {
      if ($.fn.DataTable.isDataTable('#miTabla')) {
        $('#miTabla').DataTable().destroy();
      }
      $('#miTabla').DataTable({
        data: [],
        columns: [{
            title: '#'
          },
          {
            title: 'Fecha'
          },
          {
            title: 'Flujo'
          },
          {
            title: 'Tipo Movimiento'
          },
          {
            title: 'Cantidad'
          },
          {
            title: 'Precio Unitario'
          },
          {
            title: 'Saldo'
          }
        ],
        paging: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
          emptyTable: msg
        }
      });
    }

    // ---- Pintar tabla ----
    function fmtFecha(iso) {
      if (!iso) return '';
      const d = new Date(iso);
      const pad = v => String(v).padStart(2, '0');
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
            <td>${m.preciounit}</td>
            <td>${m.saldo_restante}</td>
          </tr>
        `);
      });
      $('#miTabla').DataTable({
        paging: true,
        info: true,
        search: false,
        columnDefs: [{
          orderable: false,
          targets: -1
        }],
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
    initWithFirstProduct();

    fechaInput.addEventListener('change', cargarMovimientos);

    document.getElementById('btnPdfKardex').addEventListener('click', () => {
      if (!selectedProduct.idproducto) {
        alert('Selecciona primero un producto.');
        return;
      }
      const idp = selectedProduct.idproducto;
      const fecha = fechaInput.value;
      const modo = currentModo;
      const nombre = selectedProduct.subcategoria_producto;

      const url = `http://localhost/Fix360/app/reports/reportekardex.php` +
        `?idproducto=${encodeURIComponent(idp)}` +
        `&fecha=${encodeURIComponent(fecha)}` +
        `&modo=${encodeURIComponent(modo)}` +
        `&nombre=${encodeURIComponent(nombre)}`; // ← lo agregamos aquí

      window.open(url, '_blank');
    });


  });
</script>