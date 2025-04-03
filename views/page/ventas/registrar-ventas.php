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

  <style>
    .container-ventas {
      background: transparent;
      padding: 30px;
      border-radius: 8px;
      box-shadow: none;
      width: 1500px;
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

    table {
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
    }

    .btn-container {
      display: flex;
      justify-content: flex-end;
      margin-top: 40px;
    }

    .btn-finalizar {
      background: green;
      color: white;
      padding: 12px;
      border: none;
      cursor: pointer;
      font-size: 16px;
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

    .options li:hover {
      background-color: #f1f1f1;
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
    <form action="Venta.controller.php" method="POST">
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
          <input id="myInput" type="text" class="autocomplete-input" placeholder="Buscar Cliente">
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
          <input id="myInputProduct" type="text" class="autocomplete-input" placeholder="Buscar Producto">
        </div>

        <input type="number" class="small-input" name="precio" id="precio" placeholder="Precio" required />
        <input type="number" class="small-input" name="cantidad" id="cantidad" placeholder="Cantidad" required />
        <input type="number" class="small-input" name="descuento" id="descuento" placeholder="Descuento" />
        <button type="button" class="btn btn-success" id="agregarProducto">
          AGREGAR
        </button>
      </div>

      <!-- Tabla de productos agregados -->
      <div class="table-container">
        <table id="miTabla" class="table table-striped display">
          <thead>
            <tr>
              <th>PRODUCTO</th>
              <th>PRECIO</th>
              <th>CANTIDAD</th>
              <th>DSCT</th>
              <th>Importe</th>
              <th>Acción</th>
              <!-- <th>
                <button class="btn btn-danger btn-sm">
                  <i class="fas fa-times"></i>
                </button>
              </th> -->
            </tr>
          </thead>
          <tbody>
            <!-- productos agregados dinámicamente -->
          </tbody>
        </table>
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
      const inputElement = document.getElementById("myInput");
      const inputProductElement = document.getElementById("myInputProduct");
      let currentFocus = -1;

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
                  input.value = cliente.cliente;  // Esto es el nombre del cliente
                  clienteId = cliente.idcliente; // Obtén el idcliente
                  cerrarListas();
              });

              itemsDiv.appendChild(optionDiv);
            });
          })
          .catch(err => console.error('Error al obtener los clientes: ', err));
      }

      // Función para mostrar las opciones de autocompletado para productos
      // Mostrar opciones para productos
      // Función para mostrar las opciones de autocompletado para productos
      function mostrarOpcionesProducto(input) {
        // Cerrar cualquier lista abierta de valores autocompletados
        cerrarListas();

        // No mostrar nada si el input está vacío
        if (!input.value) return;

        // Petición al servidor para obtener los productos
        const searchTerm = input.value;
        fetch(`http://localhost/Fix360/app/controllers/Venta.controller.php?q=${searchTerm}&type=producto`)
          .then(response => response.json())
          .then(data => {
            console.log(data);
            const itemsDiv = document.createElement("div");
            itemsDiv.setAttribute("id", "autocomplete-list-producto");
            itemsDiv.setAttribute("class", "autocomplete-items");
            input.parentNode.appendChild(itemsDiv);

            // Si no hay coincidencias, no hacer nada
            if (data.length === 0) {
              const noResultsDiv = document.createElement("div");
              noResultsDiv.textContent = 'No se encontraron productos';
              itemsDiv.appendChild(noResultsDiv);
              return;
            }

            // Mostrar los resultados obtenidos
            data.forEach(function (producto) {
              const optionDiv = document.createElement("div");
              optionDiv.textContent = producto.subcategoria_producto;

              // Evento de clic para seleccionar el producto
              optionDiv.addEventListener("click", function () {

                input.value = producto.subcategoria_producto;
                document.getElementById('precio').value = producto.precio;
                $("#cantidad").val(1);
                $("#descuento").val(0);

                // Asegúrate de que el idproducto esté correctamente asignado
                selectedProduct = {
                  idproducto: producto.idproducto,
                  subcategoria_producto: producto.subcategoria_producto,
                  precio: producto.precio
                };
                //console.log(selectedProduct);  

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
      // Inicializa el DataTable solo una vez.
      const tabla = $('#miTabla').DataTable();
      if (tabla.settings()[0]) {
        tabla.destroy(); // Destruir si ya existe
      }
      $('#miTabla').DataTable(); // Inicializar el DataTable
    });
  </script>

  <script>
    $(document).ready(function () {
      const fechaActual = new Date().toISOString().split("T")[0];
      $("#fecha").val(fechaActual);
    });
  </script>

  <script>
    $(document).ready(function () {
      const tabla = $('#miTabla').DataTable();

      $("#agregarProducto").click(function () {
        const producto = selectedProduct;  // Asegúrate de que selectedProduct tenga el idproducto
        const precio = $("#precio").val();
        const cantidad = $("#cantidad").val();
        const descuento = $("#descuento").val();

        // Verificar si los campos de producto, precio y cantidad son válidos
        if (!producto || !precio || !cantidad || isNaN(precio) || isNaN(cantidad)) {
          alert("Por favor, complete todos los campos (producto, precio, cantidad).");
          return;
        }

        const importe = (parseFloat(precio) * parseInt(cantidad)) - parseFloat(descuento);

        // Agregar la fila a la tabla y asociar el idproducto con el atributo data-idproducto
        tabla.row.add([
          selectedProduct.subcategoria_producto,
          precio,
          cantidad,
          descuento,
          importe.toFixed(2),
          '<button class="btn btn-danger btn-sm eliminarProducto"><i class="fas fa-times"></i></button>'
        ]).draw(false);
        $('#miTabla tbody tr:last').data('idproducto', selectedProduct.idproducto);
        // Asignar el idproducto al data-idproducto de la fila
        const lastRow = $("#miTabla tbody tr:last-child");
        lastRow.data('idproducto', producto.idproducto);

        // Limpiar campos
        $("#myInputProduct").val("");
        $("#precio").val("");
        $("#cantidad").val("");
        $("#descuento").val("");
      });


      $(document).on("click", ".eliminarProducto", function () {
        $(this).closest("tr").remove();
      });
    });
  </script>

  <script>
    $("#finalizarBtn").click(function () {
      const tipoComprobante = $("input[name='tipo']:checked").val();
      const numSerie = $("#numserie").val();
      const numComprobante = $("#numcom").val();
      const cliente = $("#myInput").val(); // Nombre del cliente
      const fecha = $("#fecha").val();
      const moneda = $("#tipomoneda").val();

      // Verifica si el clienteId está definido antes de enviarlo
      if (!clienteId) {
        alert("Debe seleccionar un cliente.");
        return;
      }

      // Obtener los productos de la tabla
      const productos = [];
      $('#miTabla tbody tr').each(function () {
        const producto = $(this).find('td').eq(0).text();
        const precio = $(this).find('td').eq(1).text();
        const cantidad = $(this).find('td').eq(2).text();
        const descuento = $(this).find('td').eq(3).text();
        const importe = $(this).find('td').eq(4).text();
        const idproducto = $(this).data('idproducto');  // Asegúrate de obtener el idproducto correctamente

        if (!idproducto) {
          console.error("Falta el idproducto en este producto.");
        }

        productos.push({
          idproducto: idproducto,  // Asegúrate de que idproducto esté correctamente asignado
          precio: parseFloat(precio),
          cantidad: parseInt(cantidad),
          descuento: parseFloat(descuento),
          importe: parseFloat(importe)
        });
      });

      console.log({
          tipo: tipoComprobante,
          numserie: numSerie,
          numcomprobante: numComprobante,
          cliente: clienteId,
          fecha: fecha,
          tipomoneda: moneda,
          productos: productos
      });


      $.ajax({
    url: 'http://localhost/Fix360/app/controllers/Venta.controller.php',
    method: 'POST',
    data: {
        tipo: tipoComprobante,
        numserie: numSerie,
        numcomprobante: numComprobante,
        cliente: clienteId,  // Verifica que clienteId esté correctamente definido
        fecha: fecha,
        tipomoneda: moneda,
        productos: JSON.stringify(productos)  // Envía los productos correctamente
    },
    success: function (response) {
        console.log(response);
        alert("Venta registrada exitosamente");
    },
    error: function (error) {
        console.error(error);
        alert("Hubo un error al registrar la venta");
    }
});
    });

  </script>


  <!-- <script>
    $(document).ready(function () {
      $(document).ready(function () {
        // Inicializa el DataTable solo una vez.
        const tabla = $('#miTabla').DataTable();
        if (tabla.settings()[0]) {
          tabla.destroy(); // Destruir si ya existe
        }
        $('#miTabla').DataTable(); // Inicializar el DataTable

        $("#myInputProduc").on("input", function () {
          const producto = $("#myInputProduc").val();
          if (myInputProduc) {
            $("#cantidad").val(1);
            $("#descuento").val(0);
          } else {
            $("#cantidad").val("");
            $("#descuento").val("");
          }
        });

        $("#agregarProducto").click(function () {
          const producto = $("#myInputProduc").val();
          const precio = $("#precio").val();
          const cantidad = $("#cantidad").val() || 1;
          const descuento = $("#descuento").val() || 0;

          // Verificar que los campos de producto y precio no estén vacíos
          if (!producto || !precio || isNaN(precio) || isNaN(cantidad)) {
            alert("Por favor, complete todos los campos (producto, precio).");
            return;
          }

          const importe = parseFloat(precio) * parseInt(cantidad) - parseFloat(descuento);
          const idproducto = $("#myInputProduc").data("idproducto"); // Asegúrate de que este campo tiene el ID del producto.

          // Agregar la fila a la tabla
          const tabla = $("#miTabla").DataTable();
          tabla.row.add([
            myInputProduc,
            precio,
            cantidad,
            descuento,
            importe.toFixed(2),
            '<button class="btn btn-danger btn-sm eliminarProducto"><i class="fas fa-times"></i></button>'
          ]).draw(false);

          // Limpiar los campos después de agregar el producto
          $("#myInputProduc").val("");
          $("#precio").val("");
          $("#cantidad").val("");
          $("#descuento").val("");
        });

        $(document).on("click", ".eliminarProducto", function () {
          $(this).closest("tr").remove();
        });
      });
    });
  </script> -->

  <!-- <script>
    $(document).ready(function () {
      const urlVenta = 'http://localhost/fix360/app/controllers/Venta.controller.php';

      $("#finalizarBtn").click(function () {
        const tipoComprobante = $("input[name='tipo']:checked").val();
        const numSerie = $("#numserie").val();
        const numCom = $("#numcom").val();
        const nombreCliente = $("#nomcliente").val(); // Asegúrate de que esto sea el nombre del cliente
        const fecha = $("#fecha").val();
        const moneda = $("select[name='tipomoneda']").val();

        const productos = [];
        const tabla = $("#miTabla").DataTable();
        tabla.rows().every(function () {
          const data = this.data();
          productos.push({
            idproducto: data[0],  // Assuming this is the product ID
            precioventa: data[1], // Price of the product
            cantidad: data[2],    // Quantity
            descuento: data[3],   // Discount
            producto: data[4]     // Description of the product (no HTML)
          });
        });

        const ventaData = {
          nomcliente: nombreCliente,
          tipocom: tipoComprobante,
          numserie: numSerie,
          numcom: numCom,
          fechahora: fecha,
          moneda: moneda,
          productos: productos
        };

        console.log("Venta Data to be sent:", JSON.stringify(ventaData));


        $.ajax({
          url: urlVenta,
          type: 'POST',
          contentType: 'application/json', // Especifica que estamos enviando JSON
          data: JSON.stringify(ventaData),  // Convierte el objeto JS a JSON
          success: function (response) {
            console.log("Respuesta del servidor:", response);
            if (response.status === 'success') {
              alert(response.message);
            } else {
              alert(response.message);
            }
          },

          error: function (xhr, status, error) {
            console.log("Error: " + error);
          }
        });


      });
    });

  </script> -->


  <!-- <script>
    $("#finalizarBtn").click(function () {
      var cliente = $("#nomcliente").val(); // Nombre del cliente (opcional si usas el ID)
      var idcliente = $("#nomcliente").data('idcliente'); // Aquí obtenemos el ID del cliente
      var tipoComprobante = $('input[name="tipo"]:checked').val();
      var numSerie = $("#numserie").val();
      var numComprobante = $("#numcom").val();
      var fecha = $("#fecha").val();
      var moneda = $("select[name='tipomoneda']").val();

      // Verificar que todos los campos estén completos
      if (!idcliente || !tipoComprobante || !numSerie || !numComprobante || !fecha || !moneda) {
        alert("Por favor, complete todos los campos.");
        return;
      }

      var productos = [];
      $("#miTabla tbody tr").each(function () {
        var producto = {
          idproducto: $(this).find("td:eq(0)").text(), // ID del producto
          precioventa: parseFloat($(this).find("td:eq(1)").text()),
          cantidad: parseInt($(this).find("td:eq(2)").text()),
          descuento: parseFloat($(this).find("td:eq(3)").text()) || 0,
        };
        productos.push(producto);
      });

      // Verifica los datos de la venta antes de enviarlos
      console.log("Venta Data:", {
        cliente,
        tipoComprobante,
        numSerie,
        numComprobante,
        fecha,
        moneda,
        productos
      });

      if (productos.length === 0) {
        alert("Debe agregar al menos un producto.");
        return;
      }

      var ventaData = {
        idcliente: idcliente,  // Usamos el ID del cliente
        tipocom: tipoComprobante,
        numserie: numSerie,
        numcom: numComprobante,
        fechahora: fecha,
        moneda: moneda,
        productos: productos,
      };

      // Verifica el objeto de venta antes de enviarlo
      //console.log("Venta Data (JSON):", JSON.stringify(ventaData));

      $.ajax({
        url: "http://localhost/Fix360/app/controllers/Venta.controller.php",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(ventaData),
        success: function (response) {
          console.log("Respuesta del servidor:", response); // Imprime la respuesta del servidor
          if (response.status === "success") {
            alert("Venta registrada con éxito. ID de venta: " + response.idventa);
          } else {
            alert("Hubo un error al guardar la venta.");
          }
        },
        error: function (error) {
          console.error("Error en la solicitud AJAX:", error);
          alert("Hubo un error al guardar la venta.");
        },
      });

    });

  </script> -->

  <!-- <script>
      $("#nomcliente").on("input", function () {
        var search = $(this).val();
    
        if (search.length > 2) {
          // Verifica que la ruta esté correcta
          $.getJSON("/fix360/app/models/buscar_cliente.php", { q: search })
            .done(function (data) {
              // Limpiar los resultados previos
              $("#clientesResultado").empty();
    
              // Mostrar los resultados
              if (data.length > 0) {
                data.forEach((cliente) => {
                  const li = document.createElement("li");
                  li.textContent = cliente.nombre; // Asegúrate de que el campo sea 'nombre' o el que corresponda
                  li.addEventListener("click", function () {
                    nomClienteInput.value = cliente.nombre; // Usar 'cliente.nombre' o el campo correcto
                    clientesResultado.innerHTML = ""; // Limpiar resultados
                  });
                  clientesResultado.appendChild(li);
                });
              } else {
                $("#clientesResultado").append(
                  "<li>No se encontraron resultados</li>"
                );
              }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
              console.error("Error en la solicitud:", textStatus, errorThrown);
            });
        } else {
          $("#clientesResultado").empty(); // Limpiar resultados si la búsqueda tiene menos de 2 caracteres
        }
      });
    </script> -->

  <!-- <script>
      $(document).ready(function () {
        $("#producto").on("input", function () {
          var search = $(this).val();
          if (search.length > 2) {
            $.getJSON(
              "../../app/models/buscar_producto.php",
              { search: search },
              function (data) {
                if (data && !data.error) {
                  var suggestions = data
                    .map(function (producto) {
                      return (
                        '<option value="' +
                        producto.nombre +
                        '" data-id="' +
                        producto.idproducto +
                        '" data-precio="' +
                        producto.precio +
                        '">'
                      ); // Agregamos el precio al option
                    })
                    .join("");
                  $("#producto").after(
                    '<datalist id="productosDataList">' +
                      suggestions +
                      "</datalist>"
                  );
                  $("#producto").attr("list", "productosDataList");
                }
              }
            );
          }
        });

        $("#producto").on("change", function () {
          var selected = $(this).find(":selected");
          var precio = selected.data("precio");
          var cantidad = 1; // Establecemos la cantidad a 1
          var descuento = 0; // Establecemos el descuento a 0

          $("#precio").val(precio);
          $("#cantidad").val(cantidad);
          $("#descuento").val(descuento);
        });
      });
    </script> -->

  <!-- <script>
        // Lógica para agregar productos dinámicamente a la tabla
        $(document).ready(function () {
          $("#agregarProducto").click(function () {
            const producto = $("input[name='producto[]']").last().val();
            const precio = $("input[name='precio[]']").last().val();
            const cantidad = $("input[name='cantidad[]']").last().val();
            const descuento = $("input[name='descuento[]']").last().val();
            
            if (producto && precio && cantidad) {
              const importe =
              parseFloat(precio) * parseInt(cantidad) -
              (parseFloat(descuento) || 0);
              
              $("#miTabla tbody").append(`
              <tr>
                <td>${producto}</td>
                <td>${precio}</td>
                <td>${cantidad}</td>
                <td>${descuento}</td>
                <td>${importe.toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm eliminarProducto">Eliminar</button></td>
              </tr>
              `);
            }
          });
          
          $(document).on("click", ".eliminarProducto", function () {
            $(this).closest("tr").remove();
          });
        });
      </script> -->
  <!-- <script>
        document
          .getElementById("finalizarBtn")
          .addEventListener("click", function () {
            // Obtener los datos que necesitas enviar (ejemplo con variables)
            const tipo = "boleta"; // Tipo de venta
            const numserie = "V00001"; // Número de serie
            const numcomprobante = "0001"; // Número de comprobante
            const nomcliente = "Juan Pérez"; // Nombre del cliente
            const fecha = "2025-03-27"; // Fecha
            const tipomoneda = "S/.";
      
            const productos = JSON.stringify([1, 2, 3]); // Ejemplo de ID de productos
            const precios = JSON.stringify([100, 200, 150]); // Precios de los productos
            const cantidades = JSON.stringify([1, 2, 3]); // Cantidades de los productos
            const descuentos = JSON.stringify([0, 10, 5]); // Descuentos de los productos
      
            // Usar Fetch API para enviar los datos al servidor
            fetch("Venta.controller.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({
                tipo: tipo,
                numserie: numserie,
                numcomprobante: numcomprobante,
                nomcliente: nomcliente,
                fecha: fecha,
                tipomoneda: tipomoneda,
                productos: productos,
                precios: precios,
                cantidades: cantidades,
                descuentos: descuentos,
              }),
            })
              .then((response) => response.json())
              .then((data) => {
                // Aquí puedes manejar la respuesta del servidor
                if (data.success) {
                  alert("Venta finalizada con éxito!");
                } else {
                  alert("Hubo un error al finalizar la venta.");
                }
              })
              .catch((error) => {
                console.error("Error:", error);
                alert("Error al enviar la solicitud.");
              });
          });
      </script> -->
  <!-- <script>
      $(document).ready(function () {
        $('#agregarProducto').click(function () {
          var producto = $('#producto').val();
          var precio = $('#precio').val();
          var cantidad = $('#cantidad').val();
          var descuento = $('#descuento').val();
          
          if (producto && precio && cantidad) {
            var importe = (parseFloat(precio) * parseInt(cantidad)) - (parseFloat(descuento) || 0);
            $('#miTabla tbody').append(`
            <tr>
              <td>${producto}</td>
              <td>${precio}</td>
              <td>${cantidad}</td>
              <td>${descuento}</td>
              <td>${importe.toFixed(2)}</td>
              <td><button type="button" class="btn btn-danger btn-sm eliminarProducto">Eliminar</button></td>
            </tr>
            `);
          }
        });
        
        $(document).on('click', '.eliminarProducto', function () {
          $(this).closest('tr').remove();
        });
      });
    </script> -->
</body>

</html>