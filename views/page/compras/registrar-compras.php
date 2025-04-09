<?php

const NAMEVIEW = "Registro de Compras";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<html lang="en">

<head>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-DQvkBjpPgn7RC31MCQoOeC9TI2kdqa4+BSgNMNj8v77fdC77Kj5zpWFTJaaAoMbC" crossorigin="anonymous">

</head>

<body>
  <div class="container">
    <form action="" method="POST" autocomplete="off" id="formulario-detalle-compra">
      <div class="card mt-5">
        <div class="card-header">
          <div class="row">
            <div class="col"><strong>Registrar</strong></div>
            <div class="col text-end"><a href="listar-compras.php" class="btn btn-sm btn-success"
                style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">Mostrar Lista</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <!-- Tipo de comprobante -->
          <div class="row g-2">
            <div class="col-md-5">
              <label>
                <input type="radio" name="tipo" value="factura" onclick="inicializarCampos()" checked>
                Factura
              </label>
              <label>
                <input type="radio" name="tipo" value="boleta" onclick="inicializarCampos()">
                Boleta
              </label>
            </div>
            <!-- N° serie y N° comprobante -->
            <div class="col-md-7 d-flex justify-content-end">
              <label for="">N° serie:</label>
              <input type="text" class="form-control form-control-sm w-25 ms-2" name="numserie" id="numserie" required
                disabled />
              <label for="">N° comprobante:</label>
              <input type="text" name="numcomprobante" id="numcom" class="form-control form-control-sm w-25 ms-2"
                required disabled />
            </div>
          </div>
          <!-- Proveedor, fecha y moneda -->
          <div class="row g-3 mt-2">
            <div class="col-md-4">
              <div class="form-floating">
                <select class="form-select" id="proveedor" name="proveedor" style="color: black;" required>
                  <option selected>Selecciona proveedor</option>
                  <!-- Se llenará dinámicamente vía AJAX -->
                </select>
                <label for="proveedor">Proveedor</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="date" class="form-control" id="fecha" name="fecha" required>
                <label for="fecha">Fecha de compra</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <select class="form-select" id="moneda" name="moneda" style="color: black;" required>
                  <option value="soles" selected>Soles</option>
                  <!-- Aquí se insertan dinámicamente el resto de monedas -->
                </select>
                <label for="moneda">Moneda:</label>
              </div>
            </div>
          </div>
          <!-- Producto, Precio, Cantidad, Descuento -->
          <div class="row g-2 mt-3">
            <div class="col-md-5">
              <div class="autocomplete">
                <div class="form-floating">
                  <input name="producto" id="producto" type="text" class="autocomplete-input form-control" required>
                  <label for="producto">Buscar Producto:</label>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-floating">
                <input type="number" class="form-control" name="precio" id="precio" required readonly>
                <label for="precio">Precio</label>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-floating">
                <input type="number" class="form-control" name="cantidad" id="cantidad" required>
                <label for="cantidad">Cantidad</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <div class="form-floating">
                  <input type="number" class="form-control" name="descuento" id="descuento" required>
                  <label for="descuento">Descuento</label>
                </div>
                <button type="button" class="btn btn-success" id="agregarProducto" type="submit">Agregar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>

    <!-- Tabla de detalle de compra -->
    <div class="card mt-2">
      <div class="card-body">
        <table class="table table-striped table-sm" id="tabla-detalle-compra">
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

    <!-- Botón para finalizar la compra -->
    <div class="btn-container text-end mt-3">
      <button id="btnFinalizarCompra" type="button" class="btn btn-success">
        Guardar
      </button>
    </div>
  </div>
  </div>
  </div>

</body>

</html>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Variables y elementos
    const proveedorSelect = document.getElementById('proveedor');
    const inputProductElement = document.getElementById("producto");
    const numSerieInput = document.getElementById("numserie");
    const numComInput = document.getElementById("numcom");
    const fechaInput = document.getElementById('fecha');
    const monedaSelect = document.getElementById('moneda');
    const tipoInputs = document.querySelectorAll('input[name="tipo"]');
    const agregarProductoBtn = document.querySelector("#agregarProducto");
    const tabla = document.querySelector("#tabla-detalle-compra tbody");
    const btnFinalizarCompra = document.getElementById('btnFinalizarCompra');
    let selectedProduct = {};
    const detalleCompra = [];

    // Función para evitar duplicados en productos
    function estaDuplicado(idproducto = 0) {
      let duplicado = false;
      let i = 0;
      while (i < detalleCompra.length && !duplicado) {
        if (detalleCompra[i].idproducto == idproducto) {
          duplicado = true;
        }
        i++;
      }
      return duplicado;
    }

    // Función para agregar producto al detalle de compra
    agregarProductoBtn.addEventListener("click", function () {
      const nomProducto = inputProductElement.value;
      const precioProducto = parseFloat(document.getElementById('precio').value);
      const cantidadProducto = parseFloat(document.getElementById('cantidad').value);
      const descuentoProducto = parseFloat(document.getElementById('descuento').value);
      if (!nomProducto || isNaN(precioProducto) || isNaN(cantidadProducto)) {
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
      const importe = (precioProducto * cantidadProducto) - descuentoProducto;
      const nuevaFila = document.createElement("tr");
      nuevaFila.innerHTML = `
          <td>${tabla.rows.length + 1}</td>
          <td>${nomProducto}</td>
          <td>${precioProducto.toFixed(2)}</td>
          <td>${cantidadProducto}</td>
          <td>${descuentoProducto.toFixed(2)}</td>
          <td>${importe.toFixed(2)}</td>
          <td><button class="btn btn-danger btn-sm">X</button></td>
        `;
      nuevaFila.querySelector("button").addEventListener("click", function () {
        nuevaFila.remove();
        actualizarNumeros();
      });
      tabla.appendChild(nuevaFila);
      const detalle = {
        idproducto: selectedProduct.idproducto,
        producto: nomProducto,
        preciocompra: precioProducto,
        cantidad: cantidadProducto,
        descuento: descuentoProducto,
        importe: importe.toFixed(2)
      };
      detalleCompra.push(detalle);
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
              document.getElementById('precio').value = producto.preciocompra;
              // Se establece cantidad 1 y descuento 0
              document.getElementById('cantidad').value = 1;
              document.getElementById('descuento').value = 0;
              selectedProduct = {
                idproducto: producto.idproducto,
                subcategoria_producto: producto.subcategoria_producto,
                precio: producto.preciocompra
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
        if (elemento !== items[i] && elemento !== inputProductElement) {
          items[i].parentNode.removeChild(items[i]);
        }
      }
    }

    // Funciones para generar número de serie y de comprobante
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
      if (tipoSeleccionado === "factura") {
        numSerieInput.value = generateNumber("F");
        numComInput.value = generateComprobanteNumber("F");
      } else {
        numSerieInput.value = generateNumber("B");
        numComInput.value = generateComprobanteNumber("B");
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

    // Carga de proveedores vía AJAX
    fetch('http://localhost/Fix360/app/controllers/Compra.controller.php?type=proveedor')
      .then(response => response.json())
      .then(data => {
        proveedorSelect.innerHTML = '<option selected>Selecciona proveedor</option>';
        if (data.error) {
          console.error('Error:', data.error);
          return;
        }
        data.forEach(proveedor => {
          const option = document.createElement('option');
          option.value = proveedor.idproveedor;
          option.textContent = proveedor.nombre_empresa;
          proveedorSelect.appendChild(option);
        });
      })
      .catch(error => console.error('Error al cargar los proveedores:', error));

    // Evento del botón "Guardar" para enviar la compra
    btnFinalizarCompra.addEventListener("click", function (e) {
      e.preventDefault();
      btnFinalizarCompra.disabled = true;
      btnFinalizarCompra.textContent = "Guardando...";

      // Habilitar los inputs de num. serie y comprobante para enviar sus valores
      numSerieInput.disabled = false;
      numComInput.disabled = false;

      // Validar que se haya seleccionado un proveedor y agregado productos
      if (proveedorSelect.value === "" || proveedorSelect.value === "Selecciona proveedor") {
        alert("Por favor, selecciona un proveedor.");
        btnFinalizarCompra.disabled = false;
        btnFinalizarCompra.textContent = "Guardar";
        return;
      }
      if (detalleCompra.length === 0) {
        alert("Por favor, agrega al menos un producto.");
        btnFinalizarCompra.disabled = false;
        btnFinalizarCompra.textContent = "Guardar";
        return;
      }

      // Armar el objeto de datos a enviar
      const dataCompra = {
        tipocom: document.querySelector('input[name="tipo"]:checked').value,
        fechacompra: fechaInput.value.trim(),
        numserie: numSerieInput.value.trim(),
        numcom: numComInput.value.trim(),
        moneda: monedaSelect.value,
        idproveedor: proveedorSelect.value,
        productos: detalleCompra
      };

      // Enviar datos al servidor mediante fetch
      fetch("http://localhost/Fix360/app/controllers/Compra.controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(dataCompra)
      })
        .then(response => response.text())
        .then(text => {
          console.log("Respuesta del servidor:", text);
          try {
            const json = JSON.parse(text);
            // Aquí puedes procesar la respuesta según tus necesidades
          } catch (e) {
            console.error("No se pudo parsear JSON:", e);
          }
        })
        .finally(() => {
          btnFinalizarCompra.disabled = false;
          btnFinalizarCompra.textContent = "Guardar";
        });
    });
  });
</script>
<?php
require_once "../../partials/_footer.php";
?>