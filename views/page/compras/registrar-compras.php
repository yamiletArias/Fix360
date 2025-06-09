<?php
const NAMEVIEW = "Compras | Registro";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<div class="container-main mt-5">
  <div class="card border">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div></div>
      <!-- Botón a la derecha -->
      <div>
        <a href="listar-compras.php" class="btn btn-sm btn-success">
          Mostrar Lista
        </a>
      </div>
    </div>
    <div class="card-body">
      <form action="" method="POST" autocomplete="off" id="formulario-detalle">
        <div class="row g-2">
          <div class="col-md-5">
            <label>
              <input class="form-check-input text-start" type="radio" name="tipo" value="factura"
                onclick="inicializarCampos()" checked>
              Factura
            </label>
            <label style="padding-left: 10px;">
              <input class="form-check-input text-start" type="radio" name="tipo" value="boleta"
                onclick="inicializarCampos()">
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
            <div class="form-floating input-group mb-3">
              <select class="form-select" id="proveedor" name="proveedor" style="color: black;" required>
                <option selected>Selecciona proveedor</option>
                <!-- Se llenará dinámicamente vía AJAX -->
              </select>
              <label for="proveedor"><strong>Proveedor</strong></label>
              <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                data-bs-target="#modalNuevoProveedor">
                <i class="fas fa-plus-square"></i>
              </button>
            </div>
          </div>
          <div class="col-md-4 d-flex">
            <!-- Input con flex-grow para que sea más largo -->
            <div class="form-floating flex-grow-1 ">
              <input type="datetime-local" class="form-control input" name="fechaIngreso" id="fechaIngreso" required />
              <label for="fechaIngreso">Fecha de Compra:</label>
            </div>

            <!-- Botón más delgado y estilizado -->
            <button type="button" id="btnPermitirFechaPasada" class="btn btn-outline-secondary px-2"
              style="height: 58px; width: 42px;" title="Permitir fechas pasadas">
              <i class="fa-solid fa-unlock"></i>
            </button>
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
              <label for="producto"><strong>Buscar Producto:</strong></label>
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
              <input type="number" class="form-control input" name="preciocompra" id="preciocompra" placeholder="Precio"
                required />
              <label for="preciocompra"><strong>Precio</strong></label>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-floating">
              <input type="number" class="form-control input" name="cantidadcompra" id="cantidadcompra"
                placeholder="Cantidad" required />
              <label for="cantidadcompra"><strong>Cantidad</strong></label>
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

  <div class="card mt-2 border">
    <!-- <div class="card border"> -->
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
            <td><input type="text" class="form-control input form-control-sm text-end" id="neto" readonly></td>
          </tr>
          <tr>
            <td colspan="4" class="text-end">DSCT</td>
            <td><input type="text" class="form-control input form-control-sm text-end" id="totalDescuento" readonly>
            </td>
          </tr>
          <tr>
            <td colspan="4" class="text-end">IGV</td>
            <td><input type="text" class="form-control input form-control-sm text-end" id="igv" readonly></td>
          </tr>
          <tr>
            <td colspan="4" class="text-end">Importe</td>
            <td><input type="text" class="form-control input form-control-sm text-end" id="total" readonly></td>
          </tr>
        </tbody>
      </table>
      <div class="mt-4">
        <button id="btnFinalizarCompra" type="button" class="btn btn-success text-end">Aceptar</button>
        <a href="" type="reset" class="btn btn-secondary" id="btnCancelarVenta">
          Cancelar
        </a>
      </div>
    </div>
  </div>
</div>
<!-- Modal de registrar producto (versión compacta con estilos) -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md" style="margin-top: 20px;">
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
            <div class="col-6">
              <div class="form-floating">
                <input type="number" class="form-control" id="precio" name="precio" placeholder="Precio"
                  style="background-color: white; color: black;" />
                <label for="precio">Precio:</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-floating ">
                <input type="number" class="form-control input" step="0.1" id="stockInicial" name="stockInicial"
                  placeholder="stockInicial" min="0" />
                <label for="stockInicial">Stock Actual</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-floating ">
                <input type="number" class="form-control input" step="0.1" id="stockmin" name="stockmin"
                  placeholder="stockmin" min="0" />
                <label for="stockmin">Stock min.</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-floating ">
                <input type="number" step="0.1" class="form-control input" id="stockmax" name="stockmax"
                  placeholder="stockmax" min="0" />
                <label for="stockmax">Stock max.</label>
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
<!-- Modal de registrar nueva empresa / proveedor (estilo igual al primer modal) -->
<div class="modal fade" id="modalNuevoProveedor" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md" style="margin-top: 60px;">
    <div class="modal-content" style="background-color: #fff; color: #000;">
      <div class="modal-header">
        <h5 class="modal-title">Registrar Nueva Empresa / Proveedor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formProveedor">
          <div class="row g-3">
            <div class="col-12">
              <div class="form-floating">
                <input type="text" id="ruc" name="ruc" class="form-control" placeholder="RUC" minlength="11"
                  maxlength="11" required style="background-color: white; color: black;" />
                <label for="ruc"><strong>RUC</strong></label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <input type="text" id="nomcomercial" name="nomcomercial" class="form-control"
                  placeholder="Nombre Comercial" minlength="5" maxlength="100" required
                  style="background-color: white; color: black;" />
                <label for="nomcomercial"><strong>Nombre Comercial</strong></label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <input type="text" id="razonsocial" name="razonsocial" class="form-control" placeholder="Razón Social"
                  minlength="5" maxlength="100" required style="background-color: white; color: black;" />
                <label for="razonsocial"><strong>Razón Social</strong></label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-floating">
                <input type="text" id="telempresa" name="telempresa" class="form-control" placeholder="Teléfono"
                  minlength="9" maxlength="9" style="background-color: white; color: black;" />
                <label for="telempresa"><strong>Teléfono</strong></label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-floating">
                <input type="email" id="correoemp" name="correoemp" class="form-control" placeholder="Correo"
                  minlength="10" maxlength="100" style="background-color: white; color: black;" />
                <label for="correoemp"><strong>Correo</strong></label>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="formProveedor" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<?php
require_once "../../partials/_footer.php";
?>
<script src="<?= SERVERURL ?>views/page/ordenservicios/js/registrar-ordenes.js"></script>
<script src="<?= SERVERURL ?>views/page/compras/js/registrar-compras.js"></script>
<!-- js de carga moneda -->
<script src="<?= SERVERURL ?>views/assets/js/moneda.js"></script>
<script src="<?= SERVERURL ?>views/page/clientes/js/registrar-cliente.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const modalEl = document.getElementById('modalNuevoProveedor');
    const bsModal = new bootstrap.Modal(modalEl);
    const formProv = document.getElementById('formProveedor');
    const selectProv = document.getElementById('proveedor');

    modalEl.addEventListener('hidden.bs.modal', () => formProv.reset());

    formProv.addEventListener('submit', async e => {
      e.preventDefault();
      const data = new URLSearchParams();
      data.append('operation', 'registerEmpresa');
      data.append('ruc', formProv.ruc.value);
      data.append('nomcomercial', formProv.nomcomercial.value);
      data.append('razonsocial', formProv.razonsocial.value);
      data.append('telempresa', formProv.telempresa.value);
      data.append('correoemp', formProv.correoemp.value);

      let text, result;
      try {
        const resp = await fetch('<?= SERVERURL ?>app/controllers/Proveedor.controller.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: data.toString()
        });
        text = await resp.text();
        result = JSON.parse(text);
        console.log("Respuesta parseada:", result);
      } catch (err) {
        console.error("Error al parsear JSON:", text, err);
        showToast("Respuesta del servidor no es JSON válido", "ERROR");
        return;
      }

      if (result.status === true) {
        console.log("Registro OK, cerrando modal");
        const newOption = document.createElement('option');
        newOption.value = result.idproveedor;
        newOption.textContent = result.nomcomercial;
        newOption.selected = true;
        selectProv.appendChild(newOption);

        bsModal.hide();

        showToast(result.message, 'SUCCESS', 1500);
      } else {
        console.log("Status false:", result.message);
        showToast(result.message, 'ERROR', 2000);
      }
    });
  });
</script>
<script>
  // 1) Declárala UNA sola vez, arriba de todo:
  let selectedProduct = {};
  const detalleCompra = [];
  let originalPrecio = 0;
</script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const fechaInput = document.getElementById("fechaIngreso");
    const btnPermitir = document.getElementById('btnPermitirFechaPasada');
    if (!fechaInput) return;

    // Función para rellenar con ceros
    const pad = n => String(n).padStart(2, '0');

    const now = new Date();
    const yyyy = now.getFullYear();
    const MM = pad(now.getMonth() + 1);
    const dd = pad(now.getDate());
    const hh = pad(now.getHours());
    const mm = pad(now.getMinutes());

    // Valor por defecto: ahora mismo
    fechaInput.value = `${yyyy}-${MM}-${dd}T${hh}:${mm}`;

    // Rango: desde hace 2 días hasta hoy
    const twoDaysAgo = new Date(now);
    twoDaysAgo.setDate(now.getDate() - 2);
    const yyyy2 = twoDaysAgo.getFullYear();
    const MM2 = pad(twoDaysAgo.getMonth() + 1);
    const dd2 = pad(twoDaysAgo.getDate());

    fechaInput.min = `${yyyy2}-${MM2}-${dd2}T00:00`;
    fechaInput.max = `${yyyy}-${MM}-${dd}T23:59`;

    btnPermitir.addEventListener("click", () => {
      // Solo quitamos el "min", mantenemos "max" = hoy
      fechaInput.removeAttribute("min");
      btnPermitir.disabled = true;
      btnPermitir.innerHTML = '<i class="fa-solid fa-unlock-keyhole text-success"></i>';
      btnPermitir.title = "Fechas pasadas habilitadas";
    });
  });
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
          document.getElementById("cantidadcompra").value = 1;
          document.getElementById("descuento").value = 0;

          // **** Actualización clave: asignar el id retornado al objeto global selectedProduct ****
          // Se asume que la respuesta JSON ahora incluye la propiedad "idproducto" obtenida en PHP.
          selectedProduct.idproducto = resp.idproducto;
          selectedProduct.subcategoria_producto = `${subcategoriaText} ${descripcion}`;
          selectedProduct.precio = document.getElementById("precio").value;
          // nueva línea para jalarlo al formulario de compra:
          document.getElementById("preciocompra").value = selectedProduct.precio;
          // 1) captura la cantidad ingresada en el modal:
          const modalCantidad = document.getElementById("stockInicial").value;
          // 2) asígnala al campo stock del formulario principal:
          document.getElementById("stock").value = modalCantidad;
          selectedProduct.stock = parseInt(modalCantidad, 10);
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
<!-- <script>
  document.addEventListener('DOMContentLoaded', function () {
    // Variables y elementos
    const proveedorSelect = document.getElementById('proveedor');
    const inputProductElement = document.getElementById("producto");
    const numSerieInput = document.getElementById("numserie");
    const numComInput = document.getElementById("numcom");
    /* const fechaInput = document.getElementById('fecha'); */
    const monedaSelect = document.getElementById('moneda');
    const tipoInputs = document.querySelectorAll('input[name="tipo"]');
    const agregarProductoBtn = document.querySelector("#agregarProducto");
    const tabla = document.querySelector("#tabla-detalle-compra tbody");
    const btnFinalizarCompra = document.getElementById('btnFinalizarCompra');
    // Nuevos elementos de input para los detalles del producto
    const inputStock = document.getElementById("stock");
    const inputPrecio = document.getElementById("preciocompra");
    const inputCantidad = document.getElementById("cantidadcompra");
    const inputDescuento = document.getElementById("descuento");
    const fechaInput = document.getElementById("fechaIngreso");
    
    inputPrecio.addEventListener("blur", () => {
      const val = parseFloat(inputPrecio.value);
      const precioOriginal = parseFloat(selectedProduct.precio);

      if (isNaN(val) || val <= 0) {
        alert("Precio inválido.");
        inputPrecio.value = precioOriginal.toFixed(2);
        return;
      }

      // Validar si el nuevo precio es menor al original
      if (val < precioOriginal) {
        const confirmar = confirm(`Has ingresado un precio menor al original (${precioOriginal.toFixed(2)}). ¿Deseas continuar?`);
        if (!confirmar) {
          inputPrecio.value = precioOriginal.toFixed(2);
        }
      }
    });
    // --- Funciones auxiliares ---
    function calcularTotales() {
      let totalImporte = 0;
      let totalDescuento = 0;
      tabla.querySelectorAll("tr").forEach(fila => {
        // 1) cantidad desde el input de la celda
        const cantidadLinea = parseFloat(fila.querySelector('.cantidad-input').value) || 0;
        // 2) precio y descuento unitario de las celdas
        const precioUnitario = parseFloat(fila.children[2].textContent) || 0;
        const descUnitario = parseFloat(fila.children[4].textContent) || 0;
        // 3) neto y acumulados
        const importeLinea = (precioUnitario - descUnitario) * cantidadLinea;
        totalImporte += importeLinea;
        totalDescuento += descUnitario * cantidadLinea;
      });
      // 4) IGV 18% y neto
      const igv = totalImporte - (totalImporte / 1.18);
      const neto = totalImporte / 1.18;
      // 5) resultado a inputs
      document.getElementById("neto").value = neto.toFixed(2);
      document.getElementById("totalDescuento").value = totalDescuento.toFixed(2);
      document.getElementById("igv").value = igv.toFixed(2);
      document.getElementById("total").value = totalImporte.toFixed(2);
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
      const idp = selectedProduct.idproducto;
      const nomProducto = inputProductElement.value.trim();
      const precioProducto = parseFloat(inputPrecio.value);
      const cantidadProducto = parseFloat(inputCantidad.value);
      if (inputDescuento.value.trim() === "") {
        inputDescuento.value = "0";
      }
      const descuentoProducto = parseFloat(inputDescuento.value) || 0;

      //VALIDACIONES
      // 1) Campos completos
      if (!nomProducto || isNaN(precioProducto) || isNaN(cantidadProducto)) {
        return alert("Por favor, complete todos los campos correctamente.");
      }
      if (isNaN(precioProducto) || precioProducto <= 0) {
        return alert("Ingresa un precio válido mayor que cero.");
      }
      if (cantidadProducto <= 0) {
        alert("La cantidad debe ser mayor que cero.");
        inputCantidad.value = 1;  // reset al mínimo
        return;
      }
      // 2) Descuento ≤ precio unitario
      if (descuentoProducto > precioProducto) {
        alert("El descuento unitario no puede ser mayor que el precio unitario.");
        document.getElementById("descuento").value = "";
        return;
      }
      if (descuentoProducto < 0) {
        alert("El descuento no puede ser negativo.");
        inputDescuento.value = 0;
        return;
      }

      // 3) No duplicar
      if (estaDuplicado(selectedProduct.idproducto)) {
        alert("Este producto ya ha sido agregado.");
        return resetCamposProducto();
      }
      const stockDisponible = selectedProduct.stock || 0;
      if (cantidadProducto > stockDisponible) {
        alert(
          `No puedes pedir ${cantidadProducto} unidades; solo hay ${stockDisponible} en stock.`
        );
        inputCantidad.value = stockDisponible || 1;
        return;
      }

      // 4) Cálculo de importe unitario descontado y total
      const netoUnit = precioProducto - descuentoProducto;
      const importeTotal = netoUnit * cantidadProducto;

      // 5) Crear fila mostrando descuento unitario
      const nuevaFila = document.createElement("tr");
      nuevaFila.dataset.idproducto = selectedProduct.idproducto;
      nuevaFila.innerHTML = `
        <td>${tabla.rows.length + 1}</td>
        <td>${nomProducto}</td>
        <td>${precioProducto.toFixed(2)}</td>
        <td>
          <div class="input-group input-group-sm cantidad-control" style="width: 8rem;">
            <button class="btn btn-outline-secondary btn-decrement" type="button">–</button>
            <input type="number"
                  class="form-control text-center p-0 border-0 bg-transparent cantidad-input"
                  value="${cantidadProducto}"
                  min="1"
                  max="${stockDisponible}">
            <button class="btn btn-outline-secondary btn-increment" type="button">＋</button>
          </div>
        </td>
        <td>${descuentoProducto.toFixed(2)}</td>
        <td class="importe-cell">${importeTotal.toFixed(2)}</td>
        <td><button class="btn btn-danger btn-sm btn-quitar">X</button></td>
      `;

      // Agregar comportamientos a la fila
      const decBtn = nuevaFila.querySelector(".btn-decrement");
      const incBtn = nuevaFila.querySelector(".btn-increment");
      const qtyInput = nuevaFila.querySelector(".cantidad-input");
      const importeCell = nuevaFila.querySelector(".importe-cell");

      function actualizarLinea() {
        let qty = parseInt(qtyInput.value, 10) || 1;
        if (qty < 1) qty = 1;
        qtyInput.value = qty;

        const nuevoImporte = netoUnit * qty;
        importeCell.textContent = nuevoImporte.toFixed(2);

        // Actualiza array detalleCompra
        const idx = detalleCompra.findIndex(d => d.idproducto === selectedProduct.idproducto);
        if (idx >= 0) {
          detalleCompra[idx].cantidad = qty;
          detalleCompra[idx].importe = nuevoImporte.toFixed(2);
        }

        actualizarNumeros();
        calcularTotales();
      }

      decBtn.addEventListener("click", () => { qtyInput.stepDown(); actualizarLinea(); });
      incBtn.addEventListener("click", () => { qtyInput.stepUp(); actualizarLinea(); });
      qtyInput.addEventListener("input", actualizarLinea);

      // Eliminar fila
      nuevaFila.querySelector(".btn-quitar").addEventListener("click", function () {
        nuevaFila.remove();
        const idx = detalleCompra.findIndex(d => d.idproducto === selectedProduct.idproducto);
        if (idx >= 0) detalleCompra.splice(idx, 1);
        actualizarNumeros();
        calcularTotales();
      });

      // Insertar en DOM y array
      tabla.appendChild(nuevaFila);
      detalleCompra.push({
        idproducto: selectedProduct.idproducto,
        producto: nomProducto,
        precio: precioProducto,
        cantidad: cantidadProducto,
        descuento: descuentoProducto,
        importe: importeTotal.toFixed(2)
      });

      resetCamposProducto();
      actualizarNumeros();
      calcularTotales();
    });

    function resetCamposProducto() {
      inputProductElement.value = "";
      inputStock.value = "";
      inputPrecio.value = "";
      inputCantidad.value = 1;
      inputDescuento.value = 0;
    }

    function resetCamposProducto() {
      inputProductElement.value = "";
      inputStock.value = "";
      inputPrecio.value = "";
      inputCantidad.value = 1;
      inputDescuento.value = 0;
    }

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
        if (!items.length) return;
        if (e.key === "ArrowDown") {
          currentFocus++;
          addActive(items);
        } else if (e.key === "ArrowUp") {
          currentFocus--;
          addActive(items);
        } else if (e.key === "Enter") {
          e.preventDefault();
          if (currentFocus > -1) items[currentFocus].click();
        }
      });

      function addActive(items) {
        removeActive(items);
        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = items.length - 1;
        const el = items[currentFocus];
        el.classList.add("autocomplete-active");
        // esto hará que el elemento activo se vea
        el.scrollIntoView({ block: "nearest" });
      }

      function removeActive(items) {
        Array.from(items).forEach(i => i.classList.remove("autocomplete-active"));
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

              inputDescuento.addEventListener("focus", function () {
                if (inputDescuento.value === "0") {
                  inputDescuento.value = "";
                }
              });

              inputDescuento.addEventListener("keydown", function (e) {
                if (inputDescuento.value === "0" && e.key >= "0" && e.key <= "9") {
                  inputDescuento.value = "";
                }
              });

              selectedProduct = {
                idproducto: producto.idproducto,
                subcategoria_producto: producto.subcategoria_producto,
                precio: producto.precio,
                stock: producto.stock
              };
              originalPrecio = selectedProduct.precio;
              inputStock.value = selectedProduct.stock;
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
        agregarProductoBtn.focus(); // agregarProductoBtn.click();  // Si prefieres ejecutar la acción
      }
    });
    // Evento del botón "Guardar" para enviar la compra
    btnFinalizarCompra.addEventListener('click', function (e) {
      e.preventDefault();

      if (!proveedorSelect.value || proveedorSelect.value === 'Selecciona proveedor') {
        showToast('Debes seleccionar primero un proveedor.', 'WARNING', 2000);
        return;
      }
      if (detalleCompra.length === 0) {
        showToast('Agrega al menos un producto', 'WARNING', 2000);
        return;
      }
      Swal.fire({
        title: '¿Deseas guardar la compra?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33'
      }).then(result => {
        if (result.isConfirmed) {
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
                showToast('Compra registrada exitosamente.', 'SUCCESS', 1500);
                setTimeout(() => {
                  window.location.href = 'listar-compras.php';
                }, 1500);
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
      });
    });
  });
</script> -->
</body>

</html>