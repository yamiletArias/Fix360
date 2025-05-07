<?php
const NAMEVIEW = "Compras | Registro";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>

<div class="container-main mt-5">
  <div class="card border">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h3 class="mb-0">Complete los datos</h3>
      </div>
      <div>
        <a href="listar-compras.php" class="btn btn-success">
          Mostrar Lista
        </a>
      </div>
    </div>
    <div class="card-body">
      <form action="" method="POST" autocomplete="off" id="formulario-detalle">
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
              <select class="form-select" id="proveedor" name="proveedor" style="color: black;" required>
                <option value="" selected>Selecciona proveedor</option>
                <!-- Se llenará dinámicamente vía AJAX -->
              </select>
              <label for="proveedor">Proveedor</label>
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
                <!-- Aquí se insertan dinámicamente el resto de monedas -->
              </select>
              <label for="moneda">Moneda:</label>
            </div>
          </div>
        </div>

        <!-- Sección Producto, Precio, Cantidad y Descuento -->
        <div class="row g-2 mt-3">
          <div class="col-md-5">
            <div class="form-floating input-group mb-3">
              <!-- Campo de búsqueda de Producto -->
              <input name="producto" id="producto" type="text" class="autocomplete-input form-control input"
                placeholder="Buscar Producto" required>
              <label for="producto">Buscar Producto: </label>
              <input type="hidden" id="hiddenIdCliente" />
              <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                data-bs-target="#miModal">
                <i class="fas fa-plus-square"></i>
              </button>
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
              <label for="precio">Precio</label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control input" name="cantidad" id="cantidad" placeholder="Cantidad"
                required />
              <label for="cantidad">Cantidad</label>
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

  <!-- seccion de detalles de la compra -->
  <div class="container-main-2 mt-4">
    <div class="card border">
      <div class="card-body p-3">
        <table class="table table-striped table-sm mb-0" id="tabla-detalle-compra">
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
            <!-- se agregan los detalles del producto -->
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
          <a href="" type="button" class="btn input btn-success" id="btnFinalizarCompra">
            Guardar
          </a>
          <a href="" type="reset" class="btn input btn-secondary" id="btnCancelarCompra">
            Cancelar
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal de registrar producto (versión compacta con estilos) -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md" style="margin-top: 60px;">
    <div class="modal-content" style="background-color: #fff; color: #000;">
      <div class="modal-header">
        <h5 class="modal-title">Registrar producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="form-nuevo-producto">
          <div class="row g-3">
            <div class="col-12">
              <div class="form-floating">
                <select class="form-select" id="marca" name="idmarca" required
                  style="background-color: white; color: black;">
                  <option>Seleccione una opción</option>
                </select>
                <label for="marca">Marca:</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <select class="form-select" id="categoria" name="categoria" required
                  style="background-color: white; color: black;">
                  <option>Seleccione una opción</option>
                </select>
                <label for="categoria">Categoría:</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <select class="form-select" name="subcategoria" id="subcategoria" required
                  style="background-color: white; color: black;">
                  <option value="">Seleccione una opción</option>
                </select>
                <label for="subcategoria">Subcategoría:</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Descripción"
                  style="height: 70px; background-color: white; color: black;"></textarea>
                <label for="descripcion">Descripción:</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <input type="text" class="form-control" id="presentacion" name="presentacion" placeholder="Presentación"
                  style="background-color: white; color: black;" />
                <label for="presentacion">Presentación:</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-floating">
                <input type="number" class="form-control" id="cantidad" name="cantidad" placeholder="Cantidad"
                  style="background-color: white; color: black;" />
                <label for="cantidad">Cantidad:</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="undmedida" name="undmedida" placeholder="Unidad de Medida"
                  style="background-color: white; color: black;" />
                <label for="undmedida">Und. Medida:</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <input type="number" class="form-control" id="precioModal" name="precioModal" placeholder="Precio"
                  style="background-color: white; color: black;" />
                <label for="precioModal">Precio:</label>
              </div>
            </div>
            <div class="col-12">
              <label for="img" class="form-label" style="color: black;">Imagen del producto:</label>
              <input type="file" class="form-control" name="img" id="img" accept="image/png, image/jpeg"
                style="background-color: white; color: black;" />
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary" id="btnRegistrarProducto">Guardar</button>
      </div>
    </div>
  </div>
</div>

</div>
</div>
</body>

</html>

<script>
  // 1) Declárala UNA sola vez, arriba de todo:
  let selectedProduct = {};
  const detalleCompra = [];
</script>

<script>
  document.getElementById("btnRegistrarProducto").addEventListener("click", function (e) {
    e.preventDefault();

    const form = document.getElementById("form-nuevo-producto");
    const formData = new FormData(form);

    fetch("http://localhost/fix360/app/controllers/producto.controller.php", {
      method: "POST",
      body: formData
    })
      .then(response => response.json())
      .then(resp => {
        if (resp.rows > 0) {
          showToast('Producto registrado exitosamente.', 'SUCCESS', 1500);

          // Obtener subcategoría y descripción para formar el nombre del producto
          const subcategoriaText = document.getElementById("subcategoria")
            .options[document.getElementById("subcategoria").selectedIndex].text;
          const descripcion = document.getElementById("descripcion").value;
          const inputBusqueda = document.getElementById("producto");
          if (inputBusqueda) {
            inputBusqueda.value = `${subcategoriaText} ${descripcion}`;
          }
          // ← aquí las dos líneas nuevas:
          document.getElementById("cantidad").value = 1;
          document.getElementById("descuento").value = 0;

          // **** Actualización clave: asignar el id retornado al objeto global selectedProduct ****
          // Se asume que la respuesta JSON ahora incluye la propiedad "idproducto" obtenida en PHP.
          selectedProduct.idproducto = resp.idproducto;
          selectedProduct.subcategoria_producto = `${subcategoriaText} ${descripcion}`;
          selectedProduct.precio = document.getElementById("precioModal").value;

          // Cerrar el modal correctamente.
          const modalEl = document.getElementById('miModal');
          let modalInstance = bootstrap.Modal.getInstance(modalEl);
          if (!modalInstance) {
            modalInstance = new bootstrap.Modal(modalEl);
          }
          modalInstance.hide();

          // Eliminar backdrop manualmente.
          const backdrop = document.querySelector('.modal-backdrop');
          if (backdrop) backdrop.remove();

          // Quitar la clase modal-open y restaurar el scroll.
          document.body.classList.remove('modal-open');
          document.body.style.overflow = '';

          // Limpiar el formulario del modal
          form.reset();
        } else {
          showToast('Hubo un error al registrar el producto.', 'ERROR', 1500);
        }
      })
      .catch(err => {
        console.error("Error en la solicitud:", err);
        showToast('Error de conexión al registrar.', 'ERROR', 1500);
      });
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const marcaSelect = document.getElementById("marca");
    const categoriaSelect = document.getElementById("categoria");
    const subcategoriaSelect = document.getElementById("subcategoria");

    fetch("http://localhost/fix360/app/controllers/marca.controller.php?task=getAllMarcaProducto")
      .then(response => response.json())
      .then(data => {
        data.forEach(item => {
          const option = document.createElement("option");
          option.value = item.idmarca;
          option.textContent = item.nombre;
          marcaSelect.appendChild(option);
        });
      })
      .catch(error => console.error("Error al cargar las marcas:", error));
    fetch("http://localhost/fix360/app/controllers/categoria.controller.php?task=getAll")
      .then(response => response.json())
      .then(data => {
        data.forEach(item => {
          const option = document.createElement("option");
          option.value = item.idcategoria;
          option.textContent = item.categoria;
          categoriaSelect.appendChild(option);
        });
      })
      .catch(error => console.error("Error al cargar categorias:", error));

    function cargarSubcategorias() {
      const categoria = categoriaSelect.value;
      subcategoriaSelect.innerHTML = '<option value="">Seleccione una opcion</option>';
      if (categoria) {
        fetch(`http://localhost/fix360/app/controllers/subcategoria.controller.php?task=getSubcategoriaByCategoria&idcategoria=${encodeURIComponent(categoria)}`)
          .then(response => response.json())
          .then(data => {
            data.forEach(item => {
              const option = document.createElement("option");
              option.value = item.idsubcategoria;
              option.textContent = item.subcategoria;
              subcategoriaSelect.appendChild(option);
            });
          })
          .catch(error => console.error("Error al cargar subcategorias:", error));
      }
    }
    categoriaSelect.addEventListener("change", cargarSubcategorias);
  });
</script>

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

    // Nuevos elementos de input para los detalles del producto
    const inputStock = document.getElementById("stock");
    const inputPrecio = document.getElementById("precio");
    const inputCantidad = document.getElementById("cantidad");
    const inputDescuento = document.getElementById("descuento");

    function calcularTotales() {
      let totalImporte = 0;
      let totalDescuento = 0;

      document.querySelectorAll("#tabla-detalle-compra tbody tr").forEach(fila => {
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

    // Manejador del botón "Agregar" para añadir producto al detalle de compra
    agregarProductoBtn.addEventListener("click", function () {
      const nomProducto = inputProductElement.value;
      const precioProducto = parseFloat(inputPrecio.value);
      const cantidadProducto = parseFloat(inputCantidad.value);
      const descuentoProducto = parseFloat(inputDescuento.value);

      if (!nomProducto || isNaN(precioProducto) || isNaN(cantidadProducto)) {
        alert("Por favor, complete todos los campos correctamente.");
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
        calcularTotales();
      });
      tabla.appendChild(nuevaFila);

      const detalle = {
        idproducto: selectedProduct.idproducto,
        producto: nomProducto,
        precio: precioProducto,
        cantidad: cantidadProducto,
        descuento: descuentoProducto,
        importe: importe.toFixed(2)
      };
      detalleCompra.push(detalle);

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

    // Navegación con Enter para ir de campo en campo (productos, precio, cantidad y descuento)
    inputProductElement.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        inputPrecio.focus();
      }
    });

    inputPrecio.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        inputCantidad.focus();
      }
    });

    inputCantidad.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        inputDescuento.focus();
      }
    });

    inputDescuento.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        // Opcional: puedes mover el foco al botón de agregar o ejecutar su acción directamente
        agregarProductoBtn.focus();
        // agregarProductoBtn.click();  // Si prefieres ejecutar la acción
      }
    });

    // Evento del botón "Guardar" para enviar la compra
    btnFinalizarCompra.addEventListener('click', function (e) {
      e.preventDefault();

      if (
        !proveedorSelect.value ||
        proveedorSelect.value === 'Selecciona proveedor'
      ) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error', // ← cambia 'success' por 'error' para que sea rojo
          title: 'Por favor selecciona un proveedor',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
        return;
      }

      // toast si no hay productos
      if (detalleCompra.length === 0) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'warning',
          title: 'Agrega al menos un producto',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
        return;
      }

      // Confirmación SweetAlert
      Swal.fire({
        title: '¿Deseas guardar la compra?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',    // verde
        cancelButtonColor: '#d33'
      }).then(result => {
        if (result.isConfirmed) {
          // Si confirma, envía y redirige directamente
          btnFinalizarCompra.disabled = true;
          btnFinalizarCompra.textContent = 'Guardando...';

          fetch('http://localhost/Fix360/app/controllers/Compra.controller.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              tipocom: document.querySelector('input[name="tipo"]:checked').value,
              fechacompra: fechaInput.value,
              numserie: numSerieInput.value,
              numcom: numComInput.value,
              moneda: monedaSelect.value,
              idproveedor: proveedorSelect.value,
              productos: detalleCompra
            })
          })
            .then(res => res.json())
            .then(json => {
              if (json.status === 'success') {
                // Mostrar el toast verde
                Swal.fire({
                  toast: true,
                  position: 'top-end',
                  icon: 'success',
                  title: 'Compra registrada',
                  showConfirmButton: false,
                  timer: 2000,
                  timerProgressBar: true
                });

                // Espera 2 segundos antes de redirigir
                setTimeout(() => {
                  window.location.href = 'listar-compras.php';
                }, 2000);
              } else {
                Swal.fire('Error', 'No se pudo registrar la compra.', 'error');
              }
            })
            .catch(() => Swal.fire('Error', 'Fallo de conexión.', 'error'))
            .finally(() => {
              btnFinalizarCompra.disabled = false;
              btnFinalizarCompra.textContent = 'Guardar';
            });
        }
        // si cancela, no hace nada
      });
    });
  });
</script>
<!-- <script src="<?= SERVERURL ?>views/page/compras/js/registrar-compras.js"></script> -->
<!-- js de carga moneda -->
<script src="<?= SERVERURL ?>views/assets/js/moneda.js"></script>
<?php
require_once "../../partials/_footer.php";
?>