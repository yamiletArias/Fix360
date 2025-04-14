<?php
const NAMEVIEW = "Registrar Cotización";

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
        <a href="listar-cotizacion.php" class="btn btn-sm btn-success">
          Mostrar Lista
        </a>
      </div>
    </div>

    <div class="card-body">
      <form action="" method="POST" autocomplete="off" id="formulario-detalle">
        <div class="row g-2">
          <div class="col-md-6">
            <div class="form-floating">
              <input type="date" class="form-control input" name="fecha" id="fecha" required />
              <label for="fecha">Fecha de Cotización</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="date" class="form-control input" name="vigenciadias" id="vigenciadias" required />
              <label for="vigenciadias">Vigencia</label>
            </div>
          </div>
        </div>
        <!-- Sección Cliente, Fecha y Moneda -->
        <div class="row g-2 mt-3">
          <div class="col-md-6">
            <div class="form-floating">
              <input name="cliente" id="cliente" type="text" class=" form-control input" placeholder="Producto"
                required />
              <label for="cliente">Cliente</label>
            </div>
          </div>
          <div class="col-md-6">
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
          <!-- Aquí se agregarán los detalles de los productos -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- Botón para finalizar la venta -->
  <div class="btn-container text-end mt-3">
    <button id="btnFinalizarCotizacion" type="button" class="btn btn-success">
      Guardar
    </button>
  </div>
</div>
<!-- fin de cotizacion -->
</div>
</div>
</body>

</html>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const inputCliente = document.getElementById("cliente");
    const inputProductElement = document.getElementById("producto");
    let clienteId = null;
    let selectedProduct = {};
    const agregarProductoBtn = document.getElementById("agregarProducto");
    const tabla = document.querySelector("#tabla-detalle tbody");
    const detalleCotizacion = [];
    const fechaInput = document.getElementById("fecha");
    const vigenciaDiasInput = document.getElementById("vigenciadias");
    const monedaSelect = document.getElementById('moneda');
    const btnFinalizarVenta = document.getElementById('btnFinalizarCotizacion');

    // Función de autocompletado para clientes
    function mostrarOpcionesCliente(input) {
      cerrarListas();
      if (!input.value) return;
      const searchTerm = input.value;
      fetch(`http://localhost/Fix360/app/controllers/Cotizacion.controller.php?q=${searchTerm}&type=cliente`)
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
      fetch(`http://localhost/Fix360/app/controllers/Cotizacion.controller.php?q=${searchTerm}&type=producto`)
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

    // Establecer fecha actual por defecto en ambos campos
    const setFechaDefault = () => {
      const today = new Date();
      const day = String(today.getDate()).padStart(2, '0');
      const month = String(today.getMonth() + 1).padStart(2, '0');
      const year = today.getFullYear();
      const formattedDate = `${year}-${month}-${day}`;

      fechaInput.value = formattedDate;
      vigenciaDiasInput.value = formattedDate;
    };
    setFechaDefault();

    // Variable para almacenar días de vigencia calculados
    let diasVigencia = 0;

    // Evento para calcular días de vigencia al cambiar la fecha
    vigenciaDiasInput.addEventListener("change", function () {
      const fechaCotizacion = new Date(fechaInput.value);
      const fechaVigencia = new Date(vigenciaDiasInput.value);

      // Calcula diferencia en milisegundos y convierte a días
      const diffTime = fechaVigencia - fechaCotizacion;
      diasVigencia = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      if (diasVigencia < 0) {
        alert("La vigencia no puede ser menor a la fecha de cotización.");
        vigenciaDiasInput.value = fechaInput.value; // Restablece la fecha
        diasVigencia = 0;
      } else {
        console.log(`Días de vigencia: ${diasVigencia}`);
      }
    });

    // Verifica si el producto ya está en el detalle para evitar duplicados
    function estaDuplicado(idproducto = 0) {
      let estado = false;
      let i = 0;
      while (i < detalleCotizacion.length && !estado) {
        if (detalleCotizacion[i].idproducto == idproducto) {
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
      nuevaFila.querySelector("button").addEventListener("click", function () {
        nuevaFila.remove();
        actualizarNumeros();
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
      detalleCotizacion.push(detalle);
      inputProductElement.value = "";
      document.getElementById('precio').value = "";
      document.getElementById('cantidad').value = 1;
      document.getElementById('descuento').value = 0;
    });

    //boton Guardar
    btnFinalizarCotizacion.addEventListener("click", function (e) {
      e.preventDefault();
      btnFinalizarCotizacion.disabled = true;
      btnFinalizarCotizacion.textContent = "Guardando...";

      // Validaciones
      if (!clienteId) {
        alert("Por favor, selecciona un cliente.");
        btnFinalizarCotizacion.disabled = false;
        btnFinalizarCotizacion.textContent = "Guardar";
        return;
      }
      if (detalleCotizacion.length === 0) {
        alert("Por favor, agrega al menos un producto.");
        btnFinalizarCotizacion.disabled = false;
        btnFinalizarCotizacion.textContent = "Guardar";
        return;
      }

      // Armar objeto de datos a enviar
      const data = {
        fechahora: fechaInput.value.trim(),
        vigenciadias: diasVigencia,
        moneda: monedaSelect.value,
        idcliente: clienteId,
        productos: detalleCotizacion
      };

      // Envío de datos al servidor
      fetch("http://localhost/Fix360/app/controllers/Cotizacion.controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      })
        .then(response => response.text())
        .then(text => {
          // Procesamiento de la respuesta
          console.log("Respuesta del servidor:", text);
          try {
            const json = JSON.parse(text);
            if (json && json.status === "success") {
              Swal.fire({
                icon: 'success',
                title: '¡Cotizacion registrada con éxito!',
                showConfirmButton: false,
                timer: 1800
              }).then(() => {
                window.location.href = 'listar-cotizacion.php';
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error al registrar la Cotizacion',
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
          btnFinalizarCotizacion.disabled = false;
          btnFinalizarCotizacion.textContent = "Guardar";
        });
    });

  });
</script>

<!-- js de carga moneda -->
<script src="<?= SERVERURL ?>views/assets/js/tipomoneda.js"></script>
<?php

require_once "../../partials/_footer.php";

?>