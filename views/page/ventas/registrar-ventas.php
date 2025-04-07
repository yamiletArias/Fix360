<?php
const NAMEVIEW = "Registro de ventas";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SESSION VENTAS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet"
  integrity="sha384-DQvkBjpPgn7RC31MCQoOeC9TI2kdqa4+BSgNMNj8v77fdC77Kj5zpWFTJaaAoMbC" crossorigin="anonymous">


  <style>
    .container-ventas {
      background: transparent;
      padding: 30px;
      border-radius: 8px;
      box-shadow: none;
      width: 1400px;
      min-height: 700px;
      margin-left: 80px;
      margin-top: 50px;
    }

    .form-group {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      margin-top: 25px;
      gap: 15px;
    }

    .form-group label {
      margin-right: 15px;
    }

    /* Mantener el borde visible en todo momento incluso al enfocar el campo */
    input:focus,
    select:focus {
      outline: none;
      border: 1px solid #ccc;
      box-shadow: none;
    }

    input,
    select,
    button {
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    input[type="text"],
    select {
      flex: 1;
    }

    input[type="date"] {
      width: 160px;
    }

    .small-input {
      width: 130px;
    }

    .medium-input {
      width: 200px;
    }

    /* Aseguramos que todos los campos en el formulario tengan el mismo tamaño */
    input[type="text"].medium-input,
    input[type="date"].medium-input,
    select.medium-input {
      width: 200px;
    }

    /* Estilo para los productos */
    input[type="text"].medium-input,
    input[type="number"].small-input {
      width: 200px;
      margin-right: 10px;
    }

    /* Tabla de productos */
    .table-container {
      margin-top: 30px;
    }

    /* table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    table,
    th,
    td {
      border: 1px solid #ccc;
      text-align: center;
      padding: 10px;
    } */

    .btn-container {
      display: flex;
      justify-content: flex-end;
      margin-top: 40px;
    }


    .header-group {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .right-group {
      display: flex;
      gap: 10px;
    }

    .form-check {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .form-check-input {
      margin-right: 2px;
    }

    /* Estilo para el select con búsqueda */
    /* Estilo para el select con búsqueda */
    .select-box {
      position: relative;
      width: 200px;
    }

    .select-options input {
      cursor: pointer;
      padding: 12px;
      background-color: transparent;
      border: 1px solid #ccc;
      border-radius: 4px;
      width: 100%;
      font-size: 14px;
    }

    .content {
      position: absolute;
      top: 40px;
      left: 0;
      width: 100%;
      z-index: 100;
    }

    .search input {
      display: none;
      /* Ocultamos el campo de búsqueda inicialmente */
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
      background-color: #fff;
    }

    .search input:focus {
      border-color: #007bff;
      /* Resaltar el borde al hacer clic */
    }

    .options {
      position: absolute;
      top: 35px;
      width: 100%;
      max-height: 200px;
      overflow-y: auto;
      border: 1px solid #ccc;
      border-radius: 4px;
      background-color: white;
      z-index: 9999;
      display: none;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .options li {
      padding: 12px;
      cursor: pointer;
      font-size: 14px;
    }


    /* Mostrar el fondo blanco del input de búsqueda cuando se hace clic */
    .select-options input:focus+.content .search input {
      display: block;
      background-color: #fff;
    }

    /* Estilo adicional cuando se selecciona un cliente de la lista */
    .options li.selected {
      background-color: #007bff;
      color: white;
    }

    .autocomplete {
      position: relative;
      display: inline-block;
      width: 100%;
      max-width: 600px;
    }

    .autocomplete-input {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

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
  </style>
</head>

<body>



  <div class="container-ventas">
    <form action="" method="" id="formulario-detalleventa">
      <div class="header-group">
        <div class="form-group">
          <!-- Radio buttons para seleccionar el tipo de comprobante -->
          <label>
            <input name="tipo" type="radio" id="factura" value="factura">
            Factura
          </label>
          <label>
            <input name="tipo" type="radio" id="boleta" value="boleta" checked>
            Boleta
          </label>
        </div>
        <div class="right-group">
          <input type="text" class="small-input" name="numserie" id="numserie" placeholder="N° serie" required
            disabled />
          <input type="text" name="numcomprobante" id="numcom" class="small-input" placeholder="N° comprobante" required
            disabled />
        </div>
      </div>

      <!-- Cliente, fecha y moneda -->
      <div class="form-group">
        <!-- Campo de búsqueda de cliente con la misma estructura que el select -->
        <div class="autocomplete">
          <input id="cliente" name="cliente" type="text" class="autocomplete-input" placeholder="Buscar Cliente">
        </div>

        <!-- Campo de fecha con la misma estructura de tamaño -->
        <input type="date" class="medium-input" name="fecha" id="fecha" required />

        <!-- Select de moneda con la misma estructura de tamaño -->
        <select class="medium-input" name="tipomoneda" id="tipomoneda" required>
          <option value="Soles">Soles</option>
          <option value="Dolares">Dólares</option>
        </select>
      </div>

      <!-- Productos -->
      <div class="form-group">
        <div class="autocomplete">
          <input name="producto" id="producto" type="text" class="autocomplete-input" placeholder="Buscar Producto">
        </div>

        <input type="number" class="small-input" name="precio" id="precio" placeholder="Precio" required />
        <input type="number" class="small-input" name="cantidad" id="cantidad" placeholder="Cantidad" required />
        <input type="number" class="small-input" name="descuento" id="descuento" placeholder="Descuento" />
        <button type="button" class="btn btn-success" id="agregarProducto">
          AGREGAR
        </button>
      </div>

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
      <div class="btn-container">
        <button id="finalizarBtn" type="button" class="btn btn-success">
          FINALIZAR
        </button>
      </div>
    </form>
  </div>

  <!--FIN VENTAS-->
  <!--FIN VENTAS-->
  </div>
  </div>

  <!-- plugins:js -->
  <script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="../../assets/js/off-canvas.js"></script>
  <script src="../../assets/js/hoverable-collapse.js"></script>
  <script src="../../assets/js/misc.js"></script>
  <script src="../../assets/js/settings.js"></script>
  <script src="../../assets/js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page -->
  <!-- End custom js for this page -->

  <!-- jQuery (necesario para DataTables) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const inputElement = document.getElementById("cliente");
      const inputProductElement = document.getElementById("producto");
      let currentFocus = -1;

      let clienteId = null;
      let selectedProduct = {}; // Definir aquí para evitar errores más tarde

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

              // Guardar el idcliente al seleccionar el cliente
              optionDiv.addEventListener("click", function () {
                input.value = cliente.cliente;  // Esto es el nombre del cliente
                clienteId = cliente.idcliente; // Ahora se asigna el idcliente correctamente
                console.log("Cliente seleccionado: ", cliente.cliente, cliente.idcliente); // Imprimir cliente en consola
                cerrarListas();
              });

              itemsDiv.appendChild(optionDiv);
            });
          })
          .catch(err => console.error('Error al obtener los clientes: ', err));
      }

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
      const agregarProductoBtn = document.querySelector("#agregarProducto");
      const tabla = document.querySelector("#tabla-detalle tbody");
      const detalleVenta = [];

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
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const numSerieInput = document.getElementById("numserie");
      const numComInput = document.getElementById("numcom");
      const tipoInputs = document.querySelectorAll('input[name="tipo"]');

      function generateNumber(type) {
        const randomNumber = Math.floor(Math.random() * 100);
        return `${type}${String(randomNumber).padStart(3, "0")}`;
      }

      function generateComprobanteNumber(type) {
        const randomNumber = Math.floor(Math.random() * 1000000);
        return `${type}-${String(randomNumber).padStart(7, "0")}`;
      }

      if (document.getElementById("boleta").checked) {
        numSerieInput.value = generateNumber("B");
        numComInput.value = generateComprobanteNumber("B");
      } else {
        numSerieInput.value = generateNumber("F");
        numComInput.value = generateComprobanteNumber("F");
      }

      tipoInputs.forEach((input) => {
        input.addEventListener("change", function () {
          if (this.value === "boleta") {
            numSerieInput.value = generateNumber("B");
            numComInput.value = generateComprobanteNumber("B");
          } else {
            numSerieInput.value = generateNumber("F");
            numComInput.value = generateComprobanteNumber("F");
          }
        });
      });
    });
  </script>

  <script>
    $(document).ready(function () {
      const fechaActual = new Date().toISOString().split("T")[0];
      $("#fecha").val(fechaActual);
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      let clienteId = null;
      const finalizarBtn = document.getElementById('finalizarBtn');
      const formularioDetalle = document.getElementById('formulario-detalleventa');
      const tablaDetalle = document.querySelector("#tabla-detalle tbody");

      // Mostrar opciones de autocompletado para cliente
      const inputElement = document.getElementById("cliente");
      inputElement.addEventListener("input", function () {
        mostrarOpcionesCliente(this);
      });

      inputElement.addEventListener("click", function () {
        mostrarOpcionesCliente(this);
      });

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

              // Guardar el idcliente al seleccionar el cliente
              optionDiv.addEventListener("click", function () {
                input.value = cliente.cliente;
                clienteId = cliente.idcliente;
                console.log("Cliente seleccionado: ", cliente.cliente, cliente.idcliente); 
                cerrarListas();
              });

              itemsDiv.appendChild(optionDiv);
            });
          })
          .catch(err => console.error('Error al obtener los clientes: ', err));
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

      // Evento del botón Finalizar
      finalizarBtn.addEventListener("click", function () {
        if (!clienteId) {
          alert("Por favor, selecciona un cliente antes de finalizar la venta.");
          return;
        }

        // Recopilar los datos del formulario
        const tipoComprobante = document.querySelector('input[name="tipo"]:checked').value; // 'factura' o 'boleta'
        const numSerie = document.getElementById("numserie").value;
        const numComprobante = document.getElementById("numcom").value;
        const clienteNombre = document.getElementById("cliente").value;
        const fecha = document.getElementById("fecha").value;
        const moneda = document.getElementById("tipomoneda").value;

        // Recopilar los productos del detalle de venta
        const detalleVenta = [];
        const filas = tablaDetalle.querySelectorAll("tr");
        filas.forEach((fila, index) => {
          const celdas = fila.querySelectorAll("td");
          detalleVenta.push({
            idproducto: celdas[1].textContent,
            precio: parseFloat(celdas[2].textContent),
            cantidad: parseInt(celdas[3].textContent),
            descuento: parseFloat(celdas[4].textContent),
            importe: parseFloat(celdas[5].textContent)
          });
        });

        // Verificar que haya productos en el detalle
        if (detalleVenta.length === 0) {
          alert("Debe agregar al menos un producto para finalizar la venta.");
          return;
        }

        // Preparar los datos para enviar al servidor
        const ventaData = {
          idcliente: clienteId,
          tipocom: tipoComprobante,
          fechahora: fecha,
          numserie: numSerie,
          numcom: numComprobante,
          moneda: moneda,
          detalle: detalleVenta
        };

        // Enviar los datos al servidor usando fetch()
        fetch("http://localhost/Fix360/app/controllers/Venta.controller.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(ventaData)
        })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              alert('Venta registrada correctamente');
              window.location.href = '/ventas/listar';
            } else {
              alert('Error al registrar la venta: ' + data.message);
            }
          })
          .catch(error => {
            console.error("Error al enviar la solicitud:", error);
            alert('Hubo un error al registrar la venta. Inténtalo nuevamente.');
          });
      });
    });
  </script>

</body>

</html>