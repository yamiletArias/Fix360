<?php
const NAMEVIEW = "Ventas | Registro";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<div class="container-main mt-5">
  <div class="card border">
    <!--     <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h3 class="mb-0">Complete los datos</h3>
      </div>
      <div>
        <a href="listar-ventas.php" class="btn input btn-success">
          Mostrar Lista
        </a>
      </div>
    </div> -->

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
              Boleta
            </label>
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
          <div class="col-md-4">
            <div class="form-floating input-group mb-3">
              <input type="text" disabled class="form-control input" id="propietario" placeholder="Propietario" />
              <label for="propietario"><strong>Propietario</strong></label>
              <input type="hidden" id="hiddenIdCliente" />
              <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                data-bs-target="#miModal">
                ...
              </button>
            </div>
            <!-- <div class="form-floating">
              <input name="cliente" id="cliente" type="text" class=" form-control input" placeholder="Producto"
                required />
              <label for="cliente">Cliente</label>
            </div> -->
          </div>
          <div class="col-md-3 mb-3">
            <div class="form-floating">
              <select class="form-select" id="vehiculo" name="vehiculo" style="color:black;">
                <option selected>Sin vehiculo</option>
              </select>
              <label for="vehiculo"><strong>Eliga un vehículo</strong></label>
            </div>
          </div>
          <div class="col-md-2 mb-3">
            <div class="form-floating">
              <input type="number" step="0.1" class="form-control input" id="kilometraje" placeholder="201">
              <label for="kilometraje"><strong>Kilometraje</strong></label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="date" class="form-control input" name="fechaIngreso" id="fechaIngreso" required />
              <label for="fechaIngreso">Fecha de venta:</label>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-floating">
              <select class="form-select input" id="moneda" name="moneda" style="color: black;" required>
                <!-- “Soles” siempre estático y seleccionado -->
                <option value="Soles" selected>Soles</option>
                <!-- Aquí sólo meteremos el resto -->
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
                <label for="producto"><strong>Buscar Producto:</strong></label>
              </div>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-floating">
              <input type="number" class="form-control input" name="stock" id="stock" placeholder="Stock" required
                readonly />
              <label for="stock">Stock</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control input" name="precio" id="precio" placeholder="Precio" required />
              <label for="precio"><strong>Precio</strong></label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control input" name="cantidad" id="cantidad" placeholder="Cantidad"
                required />
              <label for="cantidad"><strong>Cantidad</strong></label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="input-group">
              <div class="form-floating">
                <input type="number" class="form-control input" name="descuento" id="descuento" placeholder="DSCT"
                  required />
                <label for="descuento">DSCT</label>
              </div>
              <button type="button" class="btn btn-sm btn-success" id="agregarProducto">Agregar</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Sección de Detalles de la Venta -->
  <div class="card mt-2 border">
    <!-- <div class="card border"> -->
    <div class="card-body">
      <table class="table table-striped table-sm" id="tabla-detalle">
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
  </div>
  
  <div class="card mt-2 border">
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
            <td colspan="4" class="text-end">NETO</td>
            <td>
              <input type="text" class="form-control input form-control-sm text-end" id="neto" readonly>
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
            <td colspan="4" class="text-end">Importe</td>
            <td>
              <input type="text" class="form-control input form-control-sm text-end" id="total" readonly>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="mt-4">
        <!-- <a href="" type="button" class="btn input btn-success" id="btnFinalizarVenta">
            Aceptar
          </a> -->
        <button id="btnFinalizarVenta" type="button" class="btn btn-success text-end">Aceptar</button>
        <a href="" type="reset" class="btn btn-secondary" id="btnCancelarVenta">
          Cancelar
        </a>
      </div>
    </div>
  </div>
</div>
</div>
</div>

<div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="miModalLabel">Seleccionar Propietario</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Fila para Tipo de Propietario -->
        <div class="row mb-3">
          <div class="col">
            <label><strong>Tipo de propietario:</strong></label>
            <!-- Contenedor de radio buttons -->
            <div style="display: flex; align-items: center; gap: 10px; margin-left:20px;">
              <div class="form-check form-check-inline" style="margin-right:40px;">
                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnpersona"
                  onclick="actualizarOpciones(); buscarPropietario();" checked>
                <label class="form-check-label" for="rbtnpersona" style="margin-left:5px;">Persona</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnempresa"
                  onclick="actualizarOpciones(); buscarPropietario();">
                <label class="form-check-label" for="rbtnempresa" style="margin-left:5px;">Empresa</label>
              </div>
            </div>
          </div>
        </div>
        <!-- Fila para Método de Búsqueda -->
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <select id="selectMetodo" class="form-select" style="color: black;">
                <!-- Se actualizarán las opciones según el tipo (persona/empresa) -->
              </select>
              <label for="selectMetodo">Método de búsqueda:</label>
            </div>
          </div>
        </div>
        <!-- Fila para Valor Buscado -->
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <input type="text" class="form-control input" id="vbuscado" style="background-color: white;"
                placeholder="Valor buscado" style="accent-color:white;" autofocus />
              <label for="vbuscado">Valor buscado</label>
            </div>
          </div>
        </div>
        <!-- Tabla de Resultados -->
        <p class="mt-3"><strong>Resultado:</strong></p>
        <div class="table-responsive">
          <table id="tabla-resultado" class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Confirmar</th>
              </tr>
            </thead>
            <tbody>
              <!-- Se llenará dinámicamente -->
            </tbody>
          </table>
        </div>
      </div>
      <!-- Pie del Modal -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- Formulario Venta -->
</body>

</html>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const hiddenIdCliente = document.getElementById("hiddenIdCliente");
    const vehiculoSelect = document.getElementById("vehiculo");
    let clienteId = null;

    // 1) Función para cargar vehículos tras seleccionar cliente
    function cargarVehiculos() {
      const id = hiddenIdCliente.value;
      vehiculoSelect.innerHTML = '<option value="">Eliga un vehículo</option>';
      if (!id) return;
      fetch(`http://localhost/fix360/app/controllers/vehiculo.controller.php?task=getVehiculoByCliente&idcliente=${encodeURIComponent(id)}`)
        .then(res => res.json())
        .then(data => {
          data.forEach(item => {
            const option = document.createElement("option");
            option.value = item.idvehiculo;
            option.textContent = item.vehiculo;
            vehiculoSelect.appendChild(option);
          });
        })
        .catch(err => console.error("Error al cargar vehículos:", err));
    }

    // 2) Al cambiar el hiddenIdCliente, recarga vehículos
    hiddenIdCliente.addEventListener("change", cargarVehiculos);

    // 3) Cuando confirmas en el modal, además de poner el input, actualiza clienteId y dispara change
    document.querySelector("#tabla-resultado").addEventListener("click", function (e) {
      const btn = e.target.closest(".btn-success");
      if (!btn) return;
      const id = btn.getAttribute("data-id");
      const fila = btn.closest("tr");
      const nombre = fila.cells[1].textContent;

      hiddenIdCliente.value = id;
      document.getElementById("propietario").value = nombre;
      clienteId = id;                        // ← actualizar variable interna
      hiddenIdCliente.dispatchEvent(new Event("change"));  // ← dispara carga de vehículos
    });

    // … resto de tu código de ventas …
  });

</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Variables y elementos
    /* const inputCliente = document.getElementById("cliente"); */
    const inputProductElement = document.getElementById("producto");
    const inputStock = document.getElementById("stock");
    const inputPrecio = document.getElementById("precio");
    const inputCantidad = document.getElementById("cantidad");
    const inputDescuento = document.getElementById("descuento");
    let selectedProduct = {};
    const numSerieInput = document.getElementById("numserie");
    const numComInput = document.getElementById("numcom");
    const tipoInputs = document.querySelectorAll('input[name="tipo"]');
    const agregarProductoBtn = document.getElementById("agregarProducto");
    const tabla = document.querySelector("#tabla-detalle tbody");
    const detalleVenta = [];
    const vehiculoSelect = document.getElementById("vehiculo");
    const btnFinalizarVenta = document.getElementById('btnFinalizarVenta');
    
    function initDateField(id) {
      const el = document.getElementById(id);
      if (!el) return;               // si no existe, no hace nada
      const today = new Date();
      const twoAgo = new Date();
      twoAgo.setDate(today.getDate() - 2);
      const fmt = d => d.toISOString().split('T')[0];
      el.value = fmt(today);
      el.min = fmt(twoAgo);
      el.max = fmt(today);
    }

    initDateField('fechaIngreso');
    const fechaInput = document.getElementById("fechaIngreso");
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
      inputStock.value = "";
      inputCantidad.value = 1;
      inputDescuento.value = 0;

      calcularTotales();
    });

    function actualizarNumeros() {
      const filas = tabla.getElementsByTagName("tr");
      for (let i = 0; i < filas.length; i++) {
        filas[i].children[0].textContent = i + 1;
      }
    }

    // Función de debounce para evitar demasiadas llamadas en tiempo real
    function debounce(func, delay) {
      let timeout;
      return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
      };
    }

    // Función de navegación con el teclado para autocompletar
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
        for (let i = 0; i < items.length; i++) {
          items[i].classList.remove("autocomplete-active");
        }
      }
    }

    // Función para mostrar opciones de productos (autocompletado)
    function mostrarOpcionesProducto(input) {
      cerrarListas();
      if (!input.value) return;
      const searchTerm = input.value;
      fetch(`http://localhost/Fix360/app/controllers/Compra.controller.php?q=${searchTerm}&type=producto`)
        .then(response => response.json())
        .then(data => {
          const itemsDiv = document.createElement("div");
          itemsDiv.setAttribute("id", "autocomplete-list-producto");
          itemsDiv.setAttribute("class", "autocomplete-items");
          input.parentNode.appendChild(itemsDiv);
          if (data.length === 0) {
            const noResultsDiv = document.createElement("div");
            noResultsDiv.textContent = 'No se encontraron productos';
            itemsDiv.appendChild(noResultsDiv);
            return;
          }
          data.forEach(function (producto) {
            const optionDiv = document.createElement("div");
            optionDiv.textContent = producto.subcategoria_producto;
            optionDiv.addEventListener("click", function () {
              input.value = producto.subcategoria_producto;
              inputPrecio.value = producto.precio;
              inputStock.value = producto.stock;
              inputCantidad.value = 1;
              inputDescuento.value = 0;
              selectedProduct = {
                idproducto: producto.idproducto,
                subcategoria_producto: producto.subcategoria_producto,
                precio: producto.precio
              };
              cerrarListas();
            });
            itemsDiv.appendChild(optionDiv);
          });
          // Habilitar navegación por teclado en la lista de productos
          agregaNavegacion(input, itemsDiv);
        })
        .catch(err => console.error('Error al obtener los productos: ', err));
    }

    // Función para cerrar las listas de autocompletado
    function cerrarListas(elemento) {
      const items = document.getElementsByClassName("autocomplete-items");
      for (let i = 0; i < items.length; i++) {
        if (elemento !== items[i] && elemento !== inputProductElement) {
          items[i].parentNode.removeChild(items[i]);
        }
      }
    }

    // Listeners para el autocompletado de productos usando debounce
    const debouncedMostrarOpcionesProducto = debounce(mostrarOpcionesProducto, 500);
    inputProductElement.addEventListener("input", function () {
      debouncedMostrarOpcionesProducto(this);
    });
    inputProductElement.addEventListener("click", function () {
      debouncedMostrarOpcionesProducto(this);
    });
    document.addEventListener("click", function (e) {
      cerrarListas(e.target);
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


    btnFinalizarVenta.addEventListener("click", async function (e) {
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

      const idVehiculo = vehiculoSelect.value ? parseInt(vehiculoSelect.value) : null;
      /* const idVehiculo = parseInt(vehiculoSelect.value);
      if (!idVehiculo) {
        alert("Selecciona un vehículo.");
        btnFinalizarVenta.disabled = false;
        btnFinalizarVenta.textContent = "Guardar";
        return;
      } */

      const km = parseFloat(document.getElementById("kilometraje").value) || 0;

      const data = {
        tipocom: document.querySelector('input[name="tipo"]:checked').value,
        fechahora: fechaInput.value.trim(),
        numserie: numSerieInput.value.trim(),
        numcom: numComInput.value.trim(),
        moneda: monedaSelect.value,
        idcliente: hiddenIdCliente.value,
        idvehiculo: idVehiculo, // puede ser null
        kilometraje: km,         // puede ser 0 o null
        productos: detalleVenta
      };

      const confirmacion = await ask("¿Estás seguro de registrar esta venta?", "Confirmar Venta");
      if (!confirmacion) {
        showToast('Registro cancelado.', 'WARNING', 1500);
        btnFinalizarVenta.disabled = false;
        btnFinalizarVenta.textContent = "Guardar";
        return;
      }

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
<script src="<?= SERVERURL ?>views/page/ordenservicios/js/registrar-ordenes.js"></script>
<!-- js de carga moneda -->
<script src="<?= SERVERURL ?>views/assets/js/moneda.js"></script>
<?php
require_once "../../partials/_footer.php";
?>