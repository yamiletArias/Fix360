<?php
const NAMEVIEW = "Registro de Ventas";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>

<div class="container-main mt-5">
  <div class="card border">
    <div class="card-header d-flex justify-content-between align-items-center">
      <!-- Título a la izquierda -->
      <div>
        <h3 class="mb-0">Complete los datos</h3>
      </div>
      <!-- Botón a la derecha -->
      <div>
        <a href="listar-ventas.php" class="btn input btn-success">
          Mostrar Lista
        </a>
      </div>
    </div>

    <div class="card-body">
      <form action="" method="POST" autocomplete="off" id="formulario-detalle">
        <div class="row g-2">
          <div class="col-md-5">
            <label>
              <input class="form-check-input text-start" type="radio" name="tipo" value="factura"
                onclick="inicializarCampos()">
              Factura
            </label>
            <label style="padding-left: 10px;">
              <input class="form-check-input text-start" type="radio" name="tipo" value="boleta"
                onclick="inicializarCampos()" checked>
              Empresa
            </label>
            <!-- <label>
              <input type="radio" name="tipo" value="factura" onclick="inicializarCampos()">
              Factura
            </label>
            <label>
              <input type="radio" name="tipo" value="boleta" onclick="inicializarCampos()" checked>
              Boleta
            </label> -->
          </div>
          <!-- N° serie y N° comprobante -->
          <div class="col-md-7 d-flex align-items-center justify-content-end">
            <label for="numserie" class="mb-0">N° serie:</label>
            <input type="text" class="form-control input text-center form-control-sm w-25 ms-2" name="numserie"
              id="numserie" required disabled />
            <label for="numcom" class="mb-0 ms-2">N° comprobante:</label>
            <input type="text" name="numcomprobante" id="numcom"
              class="form-control text-center input form-control-sm w-25 ms-2" required disabled />
          </div>
        </div>
        <!-- Sección Cliente, Fecha y Moneda -->
        <div class="row g-2 mt-3">
          <div class="col-md-5">
            <div class="form-floating">
              <input name="cliente" id="cliente" type="text" class=" form-control input" placeholder="Producto"
                required />
              <label for="cliente">Cliente</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="date" class="form-control input" name="fecha" id="fecha" required />
              <label for="fecha">Fecha de venta:</label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <select class="form-select input" id="moneda" name="moneda" style="color: black;" required>
                <option value="soles" selected>Soles</option>
                <!-- Aquí se insertan dinámicamente el resto de monedas -->
              </select>
              <label for="moneda">Moneda:</label>
            </div>
          </div>
        </div>

        <!-- Sección Producto, Precio, Cantidad y Descuento -->
        <div class="row g-2 mt-3">
          <div class="col-md-5">
            <div class="autocomplete">
              <div class="form-floating">
                <!-- Campo de búsqueda de Producto -->
                <input name="producto" id="producto" type="text" class="autocomplete-input form-control input"
                  placeholder="Buscar Producto" required>
                <label for="producto">Buscar Producto:</label>
              </div>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control input" name="precio" id="precio" required />
              <label for="precio">Precio</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control input" name="cantidad" id="cantidad" required />
              <label for="cantidad">Cantidad</label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="input-group">
              <div class="form-floating">
                <input type="number" class="form-control input" name="descuento" id="descuento" required />
                <label for="descuento">Descuento</label>
              </div>
              <button type="button" class="btn btn-success" id="agregarProducto">Agregar</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Sección de Detalles de la Venta -->
  <div class="container-main-2 mt-4">
    <div class="card border">

      <div class="card-body p-3">
        <table class="table table-striped table-sm mb-0" id="tabla-detalle">
          <thead>
            <tr>
              <th>#</th>
              <th>Producto</th>
              <th>Precio</th>
              <th>Cantidad</th>
              <th>Dsct</th>
              <th>Importe</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <!-- Aquí se agregarán los detalles de los productos -->
          </tbody>
        </table>
      </div>
      <div class="card-footer text-end">
        <table class="tabla table-sm">
          <colgroup>
            <col style="width: 10%;">
            <col style="width: 60%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col style="width: 5%;">
          </colgroup>
          <tbody>
            <tr>
              <td colspan="4" class="text-end">Importe</td>
              <td>
                <input type="text" class="form-control input form-control-sm text-end" id="total" readonly>
              </td>
            </tr>
            <tr>
              <td colspan="4" class="text-end">DSCT</td>
              <td>
                <input type="text" class="form-control input form-control-sm text-end" id="totalDescuento" readonly>
              </td>
            </tr>
            <tr>
              <td colspan="4" class="text-end">IGV</td>
              <td>
                <input type="text" class="form-control input form-control-sm text-end" id="igv" readonly>
              </td>
            </tr>
            <tr>
              <td colspan="4" class="text-end">NETO</td>
              <td>
                <input type="text" class="form-control input form-control-sm text-end" id="neto" readonly>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="mt-4">
          <a href="" type="button" class="btn input btn-success" id="btnFinalizarVenta">
            Guardar
          </a>
          <a href="" type="reset" class="btn input btn-secondary" id="btnCancelarVenta">
            Cancelar
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<!-- Formulario Venta -->
</body>
</html>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Variables y elementos
    const inputCliente = document.getElementById("cliente");
    const inputProductElement = document.getElementById("producto");
    const inputPrecio = document.getElementById("precio");
    const inputCantidad = document.getElementById("cantidad");
    const inputDescuento = document.getElementById("descuento");
    let clienteId = null;
    let selectedProduct = {};
    const numSerieInput = document.getElementById("numserie");
    const numComInput = document.getElementById("numcom");
    const tipoInputs = document.querySelectorAll('input[name="tipo"]');
    const agregarProductoBtn = document.getElementById("agregarProducto");
    const tabla = document.querySelector("#tabla-detalle tbody");
    const detalleVenta = [];
    const btnFinalizarVenta = document.getElementById('btnFinalizarVenta');
    const fechaInput = document.getElementById("fecha");
    const monedaSelect = document.getElementById('moneda');

    // --- Funciones auxiliares ---

    function calcularTotales() {
      let totalImporte = 0, totalDescuento = 0;
      document.querySelectorAll("#tabla-detalle tbody tr").forEach(fila => {
        totalImporte += parseFloat(fila.children[5].textContent) || 0;
        totalDescuento += parseFloat(fila.children[4].textContent) || 0;
      });
      const igv = totalImporte - (totalImporte / 1.18);
      const neto = totalImporte / 1.18;
      document.getElementById("total").value = totalImporte.toFixed(2);
      document.getElementById("totalDescuento").value = totalDescuento.toFixed(2);
      document.getElementById("igv").value = igv.toFixed(2);
      document.getElementById("neto").value = neto.toFixed(2);
    }

    function actualizarNumeros() {
      [...tabla.rows].forEach((fila, i) => fila.cells[0].textContent = i + 1);
    }

    function estaDuplicado(idproducto = 0) {
      return detalleVenta.some(d => d.idproducto == idproducto);
    }

    function debounce(func, delay) {
      let timeout;
      return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
      };
    }

    function agregaNavegacion(input, itemsDiv) {
      let currentFocus = -1;
      input.addEventListener("keydown", function (e) {
        const items = itemsDiv.getElementsByTagName("div");
        if (e.key === "ArrowDown") {
          currentFocus++;
          addActive(items);
        } else if (e.key === "ArrowUp") {
          currentFocus--;
          addActive(items);
        } else if (e.key === "Enter") {
          e.preventDefault();
          if (currentFocus > -1 && items[currentFocus]) {
            items[currentFocus].click();
          }
        }
      });
      function addActive(items) {
        if (!items) return false;
        removeActive(items);
        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = items.length - 1;
        items[currentFocus].classList.add("autocomplete-active");
      }
      function removeActive(items) {
        Array.from(items).forEach(item => item.classList.remove("autocomplete-active"));
      }
    }

    function cerrarListas(excepto) {
      document.querySelectorAll(".autocomplete-items").forEach(div => {
        if (div !== excepto) div.remove();
      });
    }

    // --- Autocompletado Clientes ---

    function mostrarOpcionesCliente(input) {
      cerrarListas();
      if (!input.value) return;
      fetch(`http://localhost/Fix360/app/controllers/Venta.controller.php?q=${encodeURIComponent(input.value)}&type=cliente`)
        .then(res => res.json())
        .then(data => {
          const itemsDiv = document.createElement("div");
          itemsDiv.id = "autocomplete-list-cliente";
          itemsDiv.className = "autocomplete-items";
          input.parentNode.appendChild(itemsDiv);

          if (data.length === 0) {
            const noRes = document.createElement("div");
            noRes.textContent = 'No se encontraron clientes';
            itemsDiv.appendChild(noRes);
          } else {
            data.forEach(cliente => {
              const optionDiv = document.createElement("div");
              optionDiv.textContent = cliente.cliente;
              optionDiv.addEventListener("click", () => {
                input.value = cliente.cliente;
                clienteId = cliente.idcliente;
                cerrarListas(itemsDiv);
              });
              itemsDiv.appendChild(optionDiv);
            });
            agregaNavegacion(input, itemsDiv);
          }
        })
        .catch(err => console.error('Error al obtener los clientes:', err));
    }
    const debouncedClientes = debounce(mostrarOpcionesCliente, 300);
    inputCliente.addEventListener("input", () => debouncedClientes(inputCliente));
    inputCliente.addEventListener("click", () => debouncedClientes(inputCliente));
    document.addEventListener("click", e => cerrarListas(e.target));

    // --- Autocompletado Productos ---

    function mostrarOpcionesProducto(input) {
      cerrarListas();
      if (!input.value) return;
      fetch(`http://localhost/Fix360/app/controllers/Venta.controller.php?q=${encodeURIComponent(input.value)}&type=producto`)
        .then(res => res.json())
        .then(data => {
          const itemsDiv = document.createElement("div");
          itemsDiv.id = "autocomplete-list-producto";
          itemsDiv.className = "autocomplete-items";
          input.parentNode.appendChild(itemsDiv);

          if (data.length === 0) {
            const noRes = document.createElement("div");
            noRes.textContent = 'No se encontraron productos';
            itemsDiv.appendChild(noRes);
          } else {
            data.forEach(prod => {
              const optionDiv = document.createElement("div");
              optionDiv.textContent = prod.subcategoria_producto;
              optionDiv.addEventListener("click", () => {
                inputProductElement.value = prod.subcategoria_producto;
                inputPrecio.value = prod.precio;
                inputCantidad.value = 1;
                inputDescuento.value = 0;
                selectedProduct = {
                  idproducto: prod.idproducto,
                  subcategoria_producto: prod.subcategoria_producto,
                  precio: prod.precio
                };
                cerrarListas(itemsDiv);
              });
              itemsDiv.appendChild(optionDiv);
            });
            agregaNavegacion(input, itemsDiv);
          }
        })
        .catch(err => console.error('Error al obtener productos:', err));
    }
    const debouncedProductos = debounce(mostrarOpcionesProducto, 300);
    inputProductElement.addEventListener("input", () => debouncedProductos(inputProductElement));
    inputProductElement.addEventListener("click", () => debouncedProductos(inputProductElement));

    // --- Agregar Producto al Detalle ---

    agregarProductoBtn.addEventListener("click", () => {
      const nombre = inputProductElement.value;
      const precio = parseFloat(inputPrecio.value);
      const cantidad = parseFloat(inputCantidad.value);
      const descuento = parseFloat(inputDescuento.value);
      if (!nombre || isNaN(precio) || isNaN(cantidad)) {
        return alert("Completa todos los campos correctamente.");
      }
      if (estaDuplicado(selectedProduct.idproducto)) {
        alert("Este producto ya ha sido agregado.");
        inputProductElement.value = "";
        inputPrecio.value = "";
        inputCantidad.value = 1;
        inputDescuento.value = 0;
        return;
      }
      const importe = (precio * cantidad) - descuento;
      const fila = document.createElement("tr");
      fila.innerHTML = `
            <td>${tabla.rows.length + 1}</td>
            <td>${nombre}</td>
            <td>${precio.toFixed(2)}</td>
            <td>${cantidad}</td>
            <td>${descuento.toFixed(2)}</td>
            <td>${importe.toFixed(2)}</td>
            <td><button class="btn btn-danger btn-sm">X</button></td>
        `;
      fila.querySelector("button").addEventListener("click", () => {
        fila.remove();
        actualizarNumeros();
        calcularTotales();
      });
      tabla.appendChild(fila);

      detalleVenta.push({
        idproducto: selectedProduct.idproducto,
        producto: nombre,
        precio, cantidad, descuento,
        importe: importe.toFixed(2)
      });

      // Reset campos
      inputProductElement.value = "";
      inputPrecio.value = "";
      inputCantidad.value = 1;
      inputDescuento.value = 0;

      calcularTotales();
    });

    // --- Generación de Serie y Comprobante ---

    function generateNumber(type) {
      return `${type}${String(Math.floor(Math.random() * 100)).padStart(3, "0")}`;
    }
    function generateComprobanteNumber(type) {
      return `${type}-${String(Math.floor(Math.random() * 1e7)).padStart(7, "0")}`;
    }
    function inicializarCampos() {
      const tipo = document.querySelector('input[name="tipo"]:checked').value;
      if (tipo === "boleta") {
        numSerieInput.value = generateNumber("B");
        numComInput.value = generateComprobanteNumber("B");
      } else {
        numSerieInput.value = generateNumber("F");
        numComInput.value = generateComprobanteNumber("F");
      }
    }
    tipoInputs.forEach(i => i.addEventListener("change", inicializarCampos));
    inicializarCampos();

    // --- Fecha por defecto ---

    (function setFechaDefault() {
      const t = new Date();
      fechaInput.value = `${t.getFullYear()}-${String(t.getMonth() + 1).padStart(2, "0")}-${String(t.getDate()).padStart(2, "0")}`;
    })();

    // --- Navegación con Enter entre campos de producto ---

    inputProductElement.addEventListener("keydown", e => { if (e.key === "Enter") { e.preventDefault(); inputPrecio.focus(); } });
    inputPrecio.addEventListener("keydown", e => { if (e.key === "Enter") { e.preventDefault(); inputCantidad.focus(); } });
    inputCantidad.addEventListener("keydown", e => { if (e.key === "Enter") { e.preventDefault(); inputDescuento.focus(); } });
    inputDescuento.addEventListener("keydown", e => {
      if (e.key === "Enter") {
        e.preventDefault();
        agregarProductoBtn.focus();
        // o bien: agregarProductoBtn.click();
      }
    });

    // --- Guardar Venta ---

    btnFinalizarVenta.addEventListener("click", function (e) {
      e.preventDefault();
      btnFinalizarVenta.disabled = true;
      btnFinalizarVenta.textContent = "Guardando...";
      numSerieInput.disabled = numComInput.disabled = false;

      if (detalleVenta.length === 0) {
        alert("Agrega al menos un producto.");
        btnFinalizarVenta.disabled = false;
        btnFinalizarVenta.textContent = "Guardar";
        return;
      }

      const data = {
        tipocom: document.querySelector('input[name="tipo"]:checked').value,
        fechahora: fechaInput.value.trim(),
        numserie: numSerieInput.value.trim(),
        numcom: numComInput.value.trim(),
        moneda: monedaSelect.value,
        idcliente: clienteId,
        productos: detalleVenta
      };

      fetch("http://localhost/Fix360/app/controllers/Venta.controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      })
        .then(r => r.text())
        .then(text => {
          try {
            const json = JSON.parse(text);
            if (json.status === "success") {
              Swal.fire({
                icon: 'success',
                title: '¡Venta registrada con éxito!',
                showConfirmButton: false,
                timer: 1800
              }).then(() => window.location.href = 'listar-ventas.php');
            } else {
              throw new Error();
            }
          } catch {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Respuesta inesperada del servidor.'
            });
          }
        })
        .finally(() => {
          btnFinalizarVenta.disabled = false;
          btnFinalizarVenta.textContent = "Guardar";
        });
    });
  });

</script>
<!-- <script src="<?= SERVERURL ?>views/page/ventas/js/registrar-ventas.js"></script> -->
<!-- js de carga moneda -->
<script src="<?= SERVERURL ?>views/assets/js/tipomoneda.js"></script>
<?php
require_once "../../partials/_footer.php";
?>