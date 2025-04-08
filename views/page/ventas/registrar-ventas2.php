<?php

const NAMEVIEW = "Registro de Ventas 2";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-DQvkBjpPgn7RC31MCQoOeC9TI2kdqa4+BSgNMNj8v77fdC77Kj5zpWFTJaaAoMbC" crossorigin="anonymous">

  <style>
    .autocomplete {
      position: relative;
      display: inline-block;
      width: 100%;
      max-width: 600px;
    }

    /* .autocomplete-input {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 4px;
    } */

    .autocomplete-items {
      position: absolute;
      border: 1px solid #d4d4d4;
      border-top: none;
      z-index: 99;
      top: 100%;
      left: 0;
      right: 0;
      border-radius: 0 0 4px 4px;
      max-height: 200px;
      overflow-y: auto;
    }

    .autocomplete-items div {
      padding: 10px;
      cursor: pointer;
      background-color: #fff;
    }

    .autocomplete-items div:hover {
      background-color: #4e99e9;
      color: #ffffff;
    }

    .autocomplete-active {
      background-color: #4e99e9 !important;
      color: #ffffff;
    }

    .autocomplete-items .default-option {
      background-color: #4e99e9;
      color: #ffffff;
    }

    #numserie,
    #numcom {
      margin-right: 10px;
      /* Agregar un margen derecho al campo de N° serie */
    }
  </style>

</head>
<div class="container">
  <form action="" method="POST" autocomplete="off" id="formulario-detalle">
    <div class="card mt-5">
      <div class="card-header">Registro</div>
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-5">
            <label>
              <input type="radio" name="tipo" value="factura" onclick="inicializarCampos('factura')">
              Factura
            </label>
            <label>
              <input type="radio" name="tipo" value="boleta" onclick="inicializarCampos('boleta')" checked>
              Boleta
            </label>
          </div>
          <!-- N° serie y N° comprobante -->
          <div class="col-md-7 d-flex justify-content-end">
            <label for="">N° serie: </label>
            <input type="text" class="form-control form-control-sm w-25 ms-2" name="numserie" id="numserie" required
              disabled />
            <label for="">N° comprobante:</label>
            <input type="text" name="numcomprobante" id="numcom" class="form-control form-control-sm w-25 ms-2" required
              disabled />
          </div>
        </div>
        <!-- Cliente, Fecha y Moneda -->
        <div class="row g-2 mt-3">
          <div class="col-md-4">
            <div class="autocomplete">
              <div class="form-floating">
                <input id="cliente" name="cliente" type="text" class="autocomplete-input form-control"
                  placeholder="Buscar Cliente">
                <label for="cliente">Buscar cliente:</label>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="date" class="form-control" name="fecha" id="fecha" required />
              <label for="fecha">Fecha:</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <select class="form-select" id="moneda" name="moneda" style="color: black;" required>
                <option value="">Seleccione una opción</option>
              </select>
              <label for="moneda">Moneda:</label>
            </div>
          </div>
        </div>
        <!-- Producto, Precio, Cantidad, Descuento -->
        <div class="row g-2 mt-3">
          <div class="col-md-4">
            <div class="autocomplete">
              <div class="form-floating">
                <input name="producto" id="producto" type="text" class="autocomplete-input form-control" required>
                <label for="producto">Buscar Producto:</label>
              </div>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control" name="precio" id="precio" required />
              <label for="precio">Precio</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control" name="cantidad" id="cantidad" required />
              <label for="cantidad">Cantidad</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control" name="descuento" id="descuento" />
              <label for="descuento">Descuento</label>
            </div>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-success w-100" id="agregarProducto">
              AGREGAR
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="card mt-2">
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
          <!-- Se agregarán los detalles -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- Botón para finalizar la venta -->
  <div class="btn-container text-end mt-3">
    <button id="btnFinalizarVenta" type="button" class="btn btn-success">
      Guardar
    </button>
  </div>
</div>
</div>
<!-- Formulario Persona -->

</body>

</html>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Variables y elementos
    const inputElement = document.getElementById("cliente");
    const inputProductElement = document.getElementById("producto");
    let clienteId = null;
    let selectedProduct = {};
    const numSerieInput = document.getElementById("numserie");
    const numComInput = document.getElementById("numcom");
    const tipoInputs = document.querySelectorAll('input[name="tipo"]');
    const agregarProductoBtn = document.querySelector("#agregarProducto");
    const tabla = document.querySelector("#tabla-detalle tbody");
    const detalleVenta = [];
    const btnFinalizarVenta = document.getElementById('btnFinalizarVenta');
    const fechaInput = document.getElementById("fecha");
    const monedaSelect = document.getElementById('moneda');

    // Función para cargar las monedas
    function cargarMonedas() {
      fetch('http://localhost/Fix360/app/controllers/Venta.controller.php?type=moneda')
        .then(response => response.json())
        .then(data => {
          if (data.length > 0) {
            monedaSelect.innerHTML = '<option value="">Seleccione una opción</option>';
            data.forEach(moneda => {
              const option = document.createElement('option');
              option.value = moneda.moneda;
              option.textContent = moneda.moneda;
              monedaSelect.appendChild(option);
            });
          }
        })
        .catch(error => {
          console.error('Error al cargar las monedas:', error);
        });
    }
    cargarMonedas();

    // Funciones de autocompletado para clientes y productos (se mantienen iguales)
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
    inputElement.addEventListener("input", function () {
      mostrarOpcionesCliente(this);
    });
    inputElement.addEventListener("click", function () {
      mostrarOpcionesCliente(this);
    });

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
              document.getElementById('precio').value = producto.precioventa;
              $("#cantidad").val(1);
              $("#descuento").val(0);
              selectedProduct = {
                idproducto: producto.idproducto,
                subcategoria_producto: producto.subcategoria_producto,
                precio: producto.precioventa
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
      for (let i = 0; i < items.length; i++) {
        if (elemento !== items[i] && elemento !== inputElement) {
          items[i].parentNode.removeChild(items[i]);
        }
      }
    }

    // Verificar si el producto ya está en el detalle
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
        <td><button class="btn btn-danger">X</button></td>
      `;
      nuevaFila.querySelector("button").addEventListener("click", function () {
        nuevaFila.remove();
        actualizarNumeros();
      });
      tabla.appendChild(nuevaFila);
      // Agregar al array de detalles
      const detalle = {
        idproducto: selectedProduct.idproducto,
        producto: productoNombre,
        precioventa: productoPrecio,
        cantidad: productoCantidad,
        descuento: productoDescuento,
        importe: importe.toFixed(2)
      };
      detalleVenta.push(detalle);
      inputProductElement.value = "";
      document.getElementById('precio').value = "";
      document.getElementById('cantidad').value = 1;
      document.getElementById('descuento').value = 0;
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
    // Establecer fecha actual
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

      // Validar la selección de cliente y productos
      if (!clienteId) {
        alert("Por favor, selecciona un cliente.");
        btnFinalizarVenta.disabled = false;
        btnFinalizarVenta.textContent = "Guardar";
        return;
      }
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
        idcliente: clienteId,
        productos: detalleVenta
      };

      // Enviar datos al servidor usando fetch
      fetch("http://localhost/Fix360/app/controllers/Venta.controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      })
        .then(response => response.text()) // <-- temporalmente en vez de .json()
        .then(text => {
          console.log("Respuesta del servidor:", text);
          try {
            const json = JSON.parse(text);
            // Aquí sigues tu lógica normal si quieres
          } catch (e) {
            console.error("No se pudo parsear JSON:", e);
          }
        })
        .finally(() => {
          btnFinalizarVenta.disabled = false;
          btnFinalizarVenta.textContent = "Guardar";
        });
    });
  });
</script>

<?php

require_once "../../partials/_footer.php";

?>