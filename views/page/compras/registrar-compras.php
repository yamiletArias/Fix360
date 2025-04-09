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
        <div class="card-header">Registro compra</div>
        <div class="card-body">
          <div class="row g-2">
            <div class="col-md-5">
              <label>
                <input type="radio" name="tipo" value="factura" onclick="inicializarCampos('factura')" checked>
                Factura
              </label>
              <label>
                <input type="radio" name="tipo" value="boleta" onclick="inicializarCampos('boleta')">
                Boleta
              </label>
            </div>
            <!-- N° serie y N° comprobante -->
            <div class="col-md-7 d-flex justify-content-end">
              <label for="">N° serie: </label>
              <input type="text" class="form-control form-control-sm w-25 ms-2" name="numserie" id="numserie" required
                disabled />
              <label for="">N° comprobante:</label>
              <input type="text" name="numcomprobante" id="numcom" class="form-control form-control-sm w-25 ms-2"
                required disabled />
            </div>
          </div>
          <!-- Proveedor, fecha, moneda -->
          <div class="row g-3 mt-2">
            <div class="col-md-4">
              <div class="form-floating">
                <select class="form-select" id="proveedor" name="proveedor" style="color: black;" required>
                  <option selected>Selecciona proveedor</option>
                  <!-- opciones dinámicas -->
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

    <div class="btn-container text-end mt-3">
      <button id="btnFinalizarVenta" type="button" class="btn btn-success">
        Guardar
      </button>
    </div>
    <!-- Fin de registro compra -->

  </div>
  </div>
  </div>

</body>

</html>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Selector del campo de proveedor
    const proveedorSelect = document.getElementById('proveedor');
    let selectedProduct = {};
    const fechaInput = document.getElementById('fecha');
    const inputProductElement = document.getElementById("producto");
    const monedaSelect = document.getElementById('moneda');
    const detalleCompra = [];
    const agregarProductoBtnCompras = document.querySelector("#agregarProducto");
    const tabla = document.querySelector("#tabla-detalle-compra tbody");

    //duplicado de productos
    function estaDuplicado(idproducto = 0) {
      let estado = false;
        let i = 0;
        while (i < detalleCompra.length && !estado){
          if(detalleCompra[i].idproducto == idproducto){
            estado = true;
          }
          i++;
        }
        return estado;
    }

    //boton de agregar productos
    agregarProductoBtnCompras.addEventListener("click", function(){
      const nomProduct = inputProductElement.value;
      const precioProduct = parseFloat(document.getElementById('precio').value);
      const cantidadProduct = parseFloat(document.getElementById('cantidad').value);
      const descuentoProduct = parseFloat(document.getElementById('descuento').value);
      if(!nomProduct || isNaN(precioProduct) || isNaN(cantidadProduct)){
        alert("Por favor, complete todos los campos correctamente.");
        return; 
      }
      if(estaDuplicado(selectedProduct.idproducto)){
        alert("Este producto ya ha sido agregado.");
        inputProductElement.value = "";
        document.getElementById('precio').value = 1;
        document.getElementById('cantidad').value = 0;
        return;
      }
      const importe = (precioProduct * cantidadProduct) - descuentoProduct;
      const nuevaFila = document.createElement("tr");
      nuevaFila.innerHTML = `
        <td>${tabla.rows.length + 1}</td>
        <td>${nomProduct}</td>
        <td>${precioProduct.toFixed(2)}</td>
        <td>${cantidadProduct}</td>
        <td>${descuentoProduct.toFixed(2)}</td>
        <td>${importe.toFixed(2)}</td>
        <td><button class="btn btn-danger sm">X</button></td>
      `;
      nuevaFila.querySelector("button").addEventListener("click", function(){
        nuevaFila.remove();
        actualizarNumeros();
      });
      tabla.appendChild(nuevaFila);
      const detalle = {
        idproducto: selectedProduct.idproducto,
        producto: nomProduct,
        precioventa: precioProduct,
        cantidad: cantidadProduct,
        descuento: descuentoProduct,
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

    // Función para mostrar las opciones de productos
    function mostrarOpcionesProducto(input) {
      cerrarListas();  // Cerrar las listas anteriores
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
              $("#cantidad").val(1);
              $("#descuento").val(0);
              selectedProduct = {
                idproducto: producto.idproducto,
                subcategoria_producto: producto.subcategoria_producto,
                precio: producto.preciocompra
              };
              cerrarListas();  // Cerrar la lista de opciones después de seleccionar el producto
            });
            itemsDiv.appendChild(optionDiv);
          });
        })
        .catch(err => {
          console.error('Error al obtener los productos: ', err);
        });
    }

    inputProductElement.addEventListener("input", function () {
      mostrarOpcionesProducto(this);
    });
    inputProductElement.addEventListener("click", function () {
      mostrarOpcionesProducto(this);
    });

    // Evento para cerrar las listas cuando se hace clic fuera
    document.addEventListener("click", function (e) {
      if (e.target !== inputProductElement && !inputProductElement.contains(e.target)) {
        cerrarListas(e.target);
      }
    });

    // Función para cerrar las listas de autocompletado
    function cerrarListas(elemento) {
      const items = document.getElementsByClassName("autocomplete-items");
      for (let i = 0; i < items.length; i++) {
        if (elemento !== items[i] && elemento !== inputProductElement) {
          items[i].parentNode.removeChild(items[i]);
        }
      }
    }

    // Establecer la fecha actual por defecto
    const setFechaDefault = () => {
      const today = new Date();
      const day = String(today.getDate()).padStart(2, '0');
      const month = String(today.getMonth() + 1).padStart(2, '0');
      const year = today.getFullYear();
      fechaInput.value = `${year}-${month}-${day}`;
    };
    setFechaDefault();

    // Realizamos la solicitud AJAX para cargar proveedores
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
  });
</script>
<?php
require_once "../../partials/_footer.php";
?>