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
        <a href="listar-ventas2.php" class="btn input btn-success btn-sm">
          Mostrar Lista
        </a>
      </div>
    </div>

    <div class="card-body">
      <form action="" method="POST" autocomplete="off" id="formulario-detalle">
        <div class="row g-2">
          <div class="col-md-5">
            <label>
              <input type="radio" name="tipo" value="factura" onclick="inicializarCampos()">
              Factura
            </label>
            <label>
              <input type="radio" name="tipo" value="boleta" onclick="inicializarCampos()" checked>
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
          <div class="col-md-5">
            <div class="autocomplete">
              <div class="form-floating">
                <!-- Campo de búsqueda de Cliente -->
                <input name="cliente" id="cliente" type="text" class="autocomplete-input form-control input"
                  placeholder="Buscar Cliente" required>
                <label for="cliente">Buscar Cliente:</label>
              </div>
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
                <input type="text" class="form-control form-control-sm text-end" id="total" readonly>
              </td>
            </tr>
            <tr>
              <td colspan="4" class="text-end">DSCT</td>
              <td>
                <input type="text" class="form-control form-control-sm text-end" id="totalDescuento" readonly>
              </td>
            </tr>
            <tr>
              <td colspan="4" class="text-end">IGV</td>
              <td>
                <input type="text" class="form-control form-control-sm text-end" id="igv" readonly>
              </td>
            </tr>
            <tr>
              <td colspan="4" class="text-end">NETO</td>
              <td>
                <input type="text" class="form-control form-control-sm text-end" id="neto" readonly>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="mt-4">
          <a type="button" class="btn input btn-success btn-sm" id="btnFinalizarVenta">
            Guardar
          </a>
          <a type="reset" class="btn input btn-secondary btn-sm" id="btnFinalizarVenta">
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

    function calcularTotales() {
      let totalImporte = 0;
      let totalDescuento = 0;

      document.querySelectorAll("#tabla-detalle tbody tr").forEach(fila => {
        const subtotal = parseFloat(fila.querySelector("td:nth-child(6)").textContent) || 0;
        const descuento = parseFloat(fila.querySelector("td:nth-child(5)").textContent) || 0;
        totalImporte += subtotal;
        totalDescuento += descuento;
      });

      // Calcular IGV y Neto
      const igv = totalImporte - (totalImporte / 1.18);
      const neto = totalImporte / 1.18;
      document.getElementById("total").value = totalImporte.toFixed(2);
      document.getElementById("totalDescuento").value = totalDescuento.toFixed(2);
      document.getElementById("igv").value = igv.toFixed(2);
      document.getElementById("neto").value = neto.toFixed(2);
    }

    // Función de autocompletado para clientes
    function mostrarOpcionesCliente(input) {
      cerrarListas();
      if (!input.value) return;
      const searchTerm = input.value;
      fetch(`http://localhost/Fix360/app/controllers/Venta.controller.php?q=${searchTerm}&type=cliente`)
        .then(response => response.json())
        .then(data => {
          const itemsDiv = document.createElement("div");
          itemsDiv.setAttribute("id", "autocomplete-list");
          itemsDiv.setAttribute("class", "autocomplete-items");
          input.parentNode.appendChild(itemsDiv);
          if (data.length === 0) {
            const noResultsDiv = document.createElement("div");
            noResultsDiv.textContent = 'No se encontraron clientes';
            itemsDiv.appendChild(noResultsDiv);
            return;
          }
          data.forEach(function (cliente) {
            const optionDiv = document.createElement("div");
            optionDiv.textContent = cliente.cliente;
            optionDiv.addEventListener("click", function () {
              input.value = cliente.cliente;
              clienteId = cliente.idcliente;
              cerrarListas();
            });
            itemsDiv.appendChild(optionDiv);
          });
        })
        .catch(err => console.error('Error al obtener los clientes: ', err));
    }
    inputCliente.addEventListener("input", function () {
      mostrarOpcionesCliente(this);
    });
    inputCliente.addEventListener("click", function () {
      mostrarOpcionesCliente(this);
    });

    // Función de autocompletado para productos
    function mostrarOpcionesProducto(input) {
      cerrarListas();
      if (!input.value) return;
      const searchTerm = input.value;
      fetch(`http://localhost/Fix360/app/controllers/Venta.controller.php?q=${searchTerm}&type=producto`)
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
              document.getElementById('precio').value = producto.precio;
              document.getElementById('cantidad').value = 1;
              document.getElementById('descuento').value = 0;
              selectedProduct = {
                idproducto: producto.idproducto,
                subcategoria_producto: producto.subcategoria_producto,
                precio: producto.precio
              };
              cerrarListas();
            });
            itemsDiv.appendChild(optionDiv);
          });
        })
        .catch(err => console.error('Error al obtener los productos: ', err));
    }
    inputProductElement.addEventListener("input", function () {
      mostrarOpcionesProducto(this);
    });
    inputProductElement.addEventListener("click", function () {
      mostrarOpcionesProducto(this);
    });
    document.addEventListener("click", function (e) {
      cerrarListas(e.target);
    });
    function cerrarListas(elemento) {
      const items = document.getElementsByClassName("autocomplete-items");
      while (items.length > 0) {
        items[0].parentNode.removeChild(items[0]);
      }
    }

    // Verifica si el producto ya está en el detalle para evitar duplicados
    function estaDuplicado(idproducto = 0) {
      let estado = false;
      let i = 0;
      while (i < detalleVenta.length && !estado) {
        if (detalleVenta[i].idproducto == idproducto) {
          estado = true;
        }
        i++;
      }
      return estado;
    }

    // Agregar producto al detalle de venta
    agregarProductoBtn.addEventListener("click", function () {
      const productoNombre = inputProductElement.value;
      const productoPrecio = parseFloat(document.getElementById('precio').value);
      const productoCantidad = parseFloat(document.getElementById('cantidad').value);
      const productoDescuento = parseFloat(document.getElementById('descuento').value);
      if (!productoNombre || isNaN(productoPrecio) || isNaN(productoCantidad)) {
        alert("Por favor, complete todos los campos correctamente.");
        return;
      }
      if (estaDuplicado(selectedProduct.idproducto)) {
        alert("Este producto ya ha sido agregado.");
        inputProductElement.value = "";
        document.getElementById('precio').value = "";
        document.getElementById('cantidad').value = 1;
        document.getElementById('descuento').value = 0;
        return;
      }
      const importe = (productoPrecio * productoCantidad) - productoDescuento;
      const nuevaFila = document.createElement("tr");
      nuevaFila.innerHTML = `
        <td>${tabla.rows.length + 1}</td>
        <td>${productoNombre}</td>
        <td>${productoPrecio.toFixed(2)}</td>
        <td>${productoCantidad}</td>
        <td>${productoDescuento.toFixed(2)}</td>
        <td>${importe.toFixed(2)}</td>
        <td><button class="btn btn-danger btn-sm">X</button></td>
      `;
      // Al eliminar una fila, además de actualizar números, se deben recalcular los totales
      nuevaFila.querySelector("button").addEventListener("click", function () {
        nuevaFila.remove();
        actualizarNumeros();
        calcularTotales();
      });
      tabla.appendChild(nuevaFila);

      // Agregar al array de detalles
      const detalle = {
        idproducto: selectedProduct.idproducto,
        producto: productoNombre,
        precio: productoPrecio,
        cantidad: productoCantidad,
        descuento: productoDescuento,
        importe: importe.toFixed(2)
      };
      detalleVenta.push(detalle);

      // Limpiar campos de producto
      inputProductElement.value = "";
      document.getElementById('precio').value = "";
      document.getElementById('cantidad').value = 1;
      document.getElementById('descuento').value = 0;

      // ¡Recalcular totales tras agregar!
      calcularTotales();
    });
    function actualizarNumeros() {
      const filas = tabla.getElementsByTagName("tr");
      for (let i = 0; i < filas.length; i++) {
        filas[i].children[0].textContent = i + 1;
      }
    }

    // Funciones para generar números de serie y comprobante
    function generateNumber(type) {
      const randomNumber = Math.floor(Math.random() * 100);
      return `${type}${String(randomNumber).padStart(3, "0")}`;
    }
    function generateComprobanteNumber(type) {
      const randomNumber = Math.floor(Math.random() * 10000000);
      return `${type}-${String(randomNumber).padStart(7, "0")}`;
    }
    function inicializarCampos() {
      const tipoSeleccionado = document.querySelector('input[name="tipo"]:checked').value;
      if (tipoSeleccionado === "boleta") {
        numSerieInput.value = generateNumber("B");
        numComInput.value = generateComprobanteNumber("B");
      } else {
        numSerieInput.value = generateNumber("F");
        numComInput.value = generateComprobanteNumber("F");
      }
    }
    inicializarCampos();
    tipoInputs.forEach((input) => {
      input.addEventListener("change", inicializarCampos);
    });

    // Establecer fecha actual por defecto
    const setFechaDefault = () => {
      const today = new Date();
      const day = String(today.getDate()).padStart(2, '0');
      const month = String(today.getMonth() + 1).padStart(2, '0');
      const year = today.getFullYear();
      fechaInput.value = `${year}-${month}-${day}`;
    };
    setFechaDefault();

    // Script del botón "Guardar"
    btnFinalizarVenta.addEventListener("click", function (e) {
  e.preventDefault();
  btnFinalizarVenta.disabled = true;
  btnFinalizarVenta.textContent = "Guardando...";

  // Habilitar los inputs para que se envíen los valores
  numSerieInput.disabled = false;
  numComInput.disabled = false;

  // Validación de cliente comentada para hacerlo opcional
  // if (!clienteId) {
  //   alert("Por favor, selecciona un cliente.");
  //   btnFinalizarVenta.disabled = false;
  //   btnFinalizarVenta.textContent = "Guardar";
  //   return;
  // }

  if (detalleVenta.length === 0) {
    alert("Por favor, agrega al menos un producto.");
    btnFinalizarVenta.disabled = false;
    btnFinalizarVenta.textContent = "Guardar";
    return;
  }

  // Armar el objeto de datos a enviar
  const data = {
    tipocom: document.querySelector('input[name="tipo"]:checked').value,
    fechahora: fechaInput.value.trim(),
    numserie: numSerieInput.value.trim(),
    numcom: numComInput.value.trim(),
    moneda: monedaSelect.value,
    idcliente: clienteId,  // Si no se selecciona, este valor será null o 0
    productos: detalleVenta
  };

  // Enviar datos al servidor usando fetch...
  fetch("http://localhost/Fix360/app/controllers/Venta.controller.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  })
    .then(response => response.text())
    .then(text => {
      console.log("Respuesta del servidor:", text);
      try {
        const json = JSON.parse(text);
        if (json && json.status === "success") {
          Swal.fire({
            icon: 'success',
            title: '¡Venta registrada con éxito!',
            showConfirmButton: false,
            timer: 1800
          }).then(() => {
            window.location.href = 'listar-ventas2.php';
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error al registrar la venta',
            text: 'Inténtalo nuevamente.',
          });
        }
      } catch (e) {
        console.error("No se pudo parsear JSON:", e);
        Swal.fire({
          icon: 'error',
          title: 'Respuesta inesperada',
          text: 'El servidor no devolvió una respuesta válida.',
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

<!-- js de carga moneda -->
<script src="<?= SERVERURL ?>views/assets/js/tipomoneda.js"></script>
<?php

require_once "../../partials/_footer.php";

?>