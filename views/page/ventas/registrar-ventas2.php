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
              <input type="radio" name="tipo" value="factura" onclick="mostrarFormulario('factura')">
              Factura
            </label>
            <label>
              <input type="radio" name="tipo" value="boleta" onclick="mostrarFormulario('boleta')" checked>
              Boleta
            </label>
          </div>

          <!-- N° serie y N° comprobante con espacio entre ellos -->
          <div class="col-md-7 d-flex justify-content-end">
            <label for="">N° serie: </label>
            <input type="text" class="form-control form-control-sm w-25 ms-2" name="numserie" id="numserie" required
              disabled />
            <label for="">N° comprobante:</label>
            <input type="text" name="numcomprobante" id="numcom" class="form-control form-control-sm w-25 ms-2" required
              disabled />
          </div>
        </div> <!-- ./row -->
        <!-- Cliente, Fecha y Moneda -->
        <div class="row g-2 mt-3">
          <div class="col-md-4">
            <div class="autocomplete">
              <div class="form-floating">
                <input id="cliente" name="cliente" type="text" class="autocomplete-input form-control"
                  placeholder="Buscar Cliente">
                <label for="cliente">Bucar cliente:</label>
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
        </div> <!-- ./row -->
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
              <label for="cantidad">Precio</label>
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

        </div> <!-- ./row -->
      </div> <!-- ./card-body -->
    </div> <!-- ./card -->
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
            <th>Acciónes</th>
          </tr>
        </thead>
        <tbody>
          <!-- Datos asíncronos -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- Botón para finalizar la venta -->
  <div class="btn-container text-end mt-3">
    <button id="finalizarBtn" type="button" class="btn btn-success">
      FINALIZAR
    </button>
  </div>
</div>
</div>
<!-- Formulario Persona -->

</body>

</html>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const inputElement = document.getElementById("cliente");
    const inputProductElement = document.getElementById("producto");
    let currentFocus = -1;
    let clienteId = null;
    let selectedProduct = {}; // Definir aquí para evitar errores más tarde
    const numSerieInput = document.getElementById("numserie");
    const numComInput = document.getElementById("numcom");
    const tipoInputs = document.querySelectorAll('input[name="tipo"]');
    const agregarProductoBtn = document.querySelector("#agregarProducto");
    const tabla = document.querySelector("#tabla-detalle tbody");
    const detalleVenta = [];
    const finalizarBtn = document.getElementById('finalizarBtn');
    const formularioDetalle = document.getElementById('formulario-detalle');
    const tablaDetalle = document.querySelector("#tabla-detalle tbody");
    const fechaInput = document.getElementById("fecha");
    const monedaSelect = document.getElementById('moneda');

    // Función para cargar las monedas desde el servidor
    function cargarMonedas() {
      fetch('http://localhost/Fix360/app/controllers/Venta.controller.php?type=moneda')
        .then(response => response.json())
        .then(data => {
          if (data.length > 0) {
            // Limpiar las opciones existentes
            monedaSelect.innerHTML = '<option value="">Seleccione una opción</option>';

            // Llenar las opciones del select con las monedas
            data.forEach(moneda => {
              const option = document.createElement('option');
              option.value = moneda.moneda; // Asignar el valor de la moneda
              option.textContent = moneda.moneda; // Mostrar la moneda en el desplegable
              monedaSelect.appendChild(option);
            });
          }
        })
        .catch(error => {
          console.error('Error al cargar las monedas:', error);
        });
    }
    // Llamar a la función para cargar las monedas
    cargarMonedas();

    // Función para mostrar las opciones de autocompletado para clientes
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
              input.value = cliente.cliente;  // Cliente name
              clienteId = cliente.idcliente; // Set the clienteId
              console.log("Cliente seleccionado: ", cliente.cliente, cliente.idcliente); // Log selected client
              cerrarListas();  // Close autocomplete list
            });

            itemsDiv.appendChild(optionDiv);
          });
        })
        .catch(err => console.error('Error al obtener los clientes: ', err));
    }
    // Attach the event listener to the input for client search
    inputElement.addEventListener("input", function () {
      mostrarOpcionesCliente(this);
    });

    // Finalizar Button Click Event
    finalizarBtn.addEventListener("click", function () {
      if (!clienteId) {
        alert("Debe seleccionar un cliente.");
        return; // Exit if no client is selected
      }

      // Further logic for finalizing the sale
      console.log("Venta finalizada con cliente:", clienteId);
    });

    // Función para mostrar las opciones de autocompletado para productos
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

              console.log("Producto seleccionado: ", selectedProduct); // Imprimir producto en consola
              cerrarListas();
            });

            itemsDiv.appendChild(optionDiv);
          });
        })
        .catch(err => console.error('Error al obtener los productos: ', err));
    }

    // Función para cerrar todas las listas de autocompletado
    function cerrarListas(elemento) {
      const items = document.getElementsByClassName("autocomplete-items");
      for (let i = 0; i < items.length; i++) {
        if (elemento !== items[i] && elemento !== inputElement) {
          items[i].parentNode.removeChild(items[i]);
        }
      }
    }

    // Manejar eventos de input para mostrar opciones para clientes
    inputElement.addEventListener("input", function () {
      mostrarOpcionesCliente(this);
    });

    // Evento de clic para seleccionar cliente
    inputElement.addEventListener("click", function () {
      mostrarOpcionesCliente(this);
    });

    // Manejar eventos de input para mostrar opciones para productos
    inputProductElement.addEventListener("input", function () {
      mostrarOpcionesProducto(this);
    });

    // Mostrar opciones al hacer clic en el input para productos
    inputProductElement.addEventListener("click", function () {
      mostrarOpcionesProducto(this);
    });

    // Cerrar lista cuando se hace clic fuera
    document.addEventListener("click", function (e) {
      cerrarListas(e.target);
    });

    // Función para verificar si el producto ya está en el detalle de venta
    function estaDuplicado(idproducto = 0) {
      let estado = false;
      let i = 0;

      if (detalleVenta.length > 0) {
        while (i < detalleVenta.length && !estado) {
          if (detalleVenta[i].idproducto == idproducto) {
            estado = true; // El producto ya está en el detalle
          }
          i++;
        }
      }

      return estado;
    }

    // Manejar el formulario y agregar productos al detalle de venta
    agregarProductoBtn.addEventListener("click", function () {
      const productoNombre = inputProductElement.value;
      const productoPrecio = parseFloat(document.getElementById('precio').value);
      const productoCantidad = parseFloat(document.getElementById('cantidad').value);
      const productoDescuento = parseFloat(document.getElementById('descuento').value);

      if (!productoNombre || isNaN(productoPrecio) || isNaN(productoCantidad)) {
        alert("Por favor, complete todos los campos correctamente.");
        return;
      }

      // Verificar si el producto ya ha sido agregado
      if (estaDuplicado(selectedProduct.idproducto)) {
        alert("Este producto ya ha sido agregado.");

        // Limpiar los campos de entrada si el producto es duplicado
        inputProductElement.value = ""; // Limpiar el nombre del producto
        document.getElementById('precio').value = ""; // Limpiar el precio
        document.getElementById('cantidad').value = 1; // Restablecer la cantidad a 1
        document.getElementById('descuento').value = 0; // Restablecer el descuento a 0

        return;
      }

      // Calcular el importe
      const importe = (productoPrecio * productoCantidad) - productoDescuento;

      // Crear una nueva fila en la tabla
      const nuevaFila = document.createElement("tr");

      const celdaNumero = document.createElement("td");
      celdaNumero.textContent = tabla.rows.length + 1;
      nuevaFila.appendChild(celdaNumero);

      const celdaProducto = document.createElement("td");
      celdaProducto.textContent = productoNombre;
      nuevaFila.appendChild(celdaProducto);

      const celdaPrecio = document.createElement("td");
      celdaPrecio.textContent = productoPrecio.toFixed(2);
      nuevaFila.appendChild(celdaPrecio);

      const celdaCantidad = document.createElement("td");
      celdaCantidad.textContent = productoCantidad;
      nuevaFila.appendChild(celdaCantidad);

      const celdaDescuento = document.createElement("td");
      celdaDescuento.textContent = productoDescuento.toFixed(2);
      nuevaFila.appendChild(celdaDescuento);

      const celdaImporte = document.createElement("td");
      celdaImporte.textContent = importe.toFixed(2);
      nuevaFila.appendChild(celdaImporte);

      const celdaAcciones = document.createElement("td");
      const btnEliminar = document.createElement("button");
      btnEliminar.textContent = "X";
      btnEliminar.classList.add("btn", "btn-danger");
      btnEliminar.addEventListener("click", function () {
        nuevaFila.remove();
        actualizarNumeros();
      });
      celdaAcciones.appendChild(btnEliminar);
      nuevaFila.appendChild(celdaAcciones);

      tabla.appendChild(nuevaFila);

      // Agregar el producto al detalle de venta
      const detalle = {
        idproducto: selectedProduct.idproducto,
        producto: productoNombre,
        precio: productoPrecio,
        cantidad: productoCantidad,
        descuento: productoDescuento,
        importe: importe.toFixed(2)
      };

      // Añadir el detalle de venta al array
      detalleVenta.push(detalle);

      // Limpiar los campos de entrada después de agregar
      inputProductElement.value = "";
      document.getElementById('precio').value = "";
      document.getElementById('cantidad').value = 1;
      document.getElementById('descuento').value = 0;
    });

    // Actualizar los números de fila en la tabla
    function actualizarNumeros() {
      const filas = tabla.getElementsByTagName("tr");
      for (let i = 0; i < filas.length; i++) {
        filas[i].children[0].textContent = i + 1; // Actualizar el número de la fila
      }
    }

    // Función para generar el número de serie
    function generateNumber(type) {
      const randomNumber = Math.floor(Math.random() * 100); // Generar un número aleatorio entre 0 y 99
      return `${type}${String(randomNumber).padStart(3, "0")}`; // Formato B001, F023, etc.
    }

    // Función para generar el número de comprobante
    function generateComprobanteNumber(type) {
      const randomNumber = Math.floor(Math.random() * 10000000); // Generar un número aleatorio entre 0 y 9999999
      return `${type}-${String(randomNumber).padStart(7, "0")}`; // Formato B-1234567 o F-2345678
    }

    function inicializarCampos() {
      const tipoSeleccionado = document.querySelector('input[name="tipo"]:checked').value;

      if (tipoSeleccionado === "boleta") {
        numSerieInput.value = generateNumber("B"); // Genera número de serie con "B"
        numComInput.value = generateComprobanteNumber("B"); // Genera número de comprobante con "B"
      } else {
        numSerieInput.value = generateNumber("F"); // Genera número de serie con "F"
        numComInput.value = generateComprobanteNumber("F"); // Genera número de comprobante con "F"
      }
    }
    inicializarCampos();

    tipoInputs.forEach((input) => {
      input.addEventListener("change", function () {
        inicializarCampos();
      });
    });

    const setFechaDefault = () => {
      const today = new Date();
      const day = String(today.getDate()).padStart(2, '0'); // Asegura que el día tenga 2 dígitos
      const month = String(today.getMonth() + 1).padStart(2, '0'); // Asegura que el mes tenga 2 dígitos
      const year = today.getFullYear();
      fechaInput.value = `${year}-${month}-${day}`;
    };

    setFechaDefault();
    finalizarBtn.addEventListener("click", function () {
      if (!clienteId) {
        alert("Debe seleccionar un cliente.");
        return;
      }
      alert("Venta finalizada");
    });
  });


</script>

<?php

require_once "../../partials/_footer.php";

?>