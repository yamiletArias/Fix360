  <?php

  const NAMEVIEW = "Productos | Registro";

  require_once "../../../app/helpers/helper.php";
  require_once "../../../app/config/app.php";
  require_once "../../partials/header.php";

  ?>

  <style>
    .input-img {
      margin: 0px;
      padding: 20px;
      margin-right: 100px;
      height: 55px;
      width: 100%;

    }
  </style>
  <div class="container-main">
    <form action="<?= SERVERURL ?>app/controllers/producto.controller.php" id="formProducto" method="POST" enctype="multipart/form-data">
      <div class="card border" style="margin-top:50px;">
        <div class="card-body">
          <div class="row">
            <!-- Marca -->

            <div class="col-md-3">
              <div class="form-floating mb-3">
                <input type="text" class="form-control input" id="codigobarra" name="codigobarra" placeholder="Código de barras" autocomplete="off" autofocus />
                <label for="codigobarra">Código de Barras</label>
              </div>
            </div>

            <div class="col-md-3 mb-3">
              <div class="form-floating input-group">
                <select class="form-select" id="marca" name="idmarca" style="color: black;" required>
                  <option>Seleccione una opcion</option>
                </select>
                <label for="marca">Marca:</label>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalMarca">
                  <i class="fa-solid fa-plus"></i>
                </button>
              </div>
            </div>

            <div class="col-md-3 mb-3">
              <div class="form-floating input-group">
                <select class="form-select" id="categoria" name="categoria" style="color: black;" required>
                  <option>Seleccione una opcion</option>
                </select>
                <label for="categoria">Categoria:</label>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalCategoria">
                  <i class="fa-solid fa-plus"></i>
                </button>
              </div>
            </div>

            <!-- Subcategoria -->
            <div class="col-md-3 mb-3">
              <div class="form-floating input-group">
                <select class="form-select" name="subcategoria" id="subcategoria" style="color: black;" required>
                  <option value="">Selecciona una opcion</option>
                </select>
                <label for="subcategoria">Subcategoría:</label>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalSubcategoria">
                  <i class="fa-solid fa-plus"></i>
                </button>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-floating mb-3">
                <textarea class="form-control input" id="descripcion" rows="4" name="descripcion" autocomplete="off" placeholder="descripcion"></textarea>
                <label for="descripcion">Descripción</label>
              </div>
            </div>

            <div class="col-md-3 mb-3">
              <div class="form-floating ">
                <input type="text" class="form-control input" id="presentacion" name="presentacion" autocomplete="off" placeholder="presentacion" />
                <label for="presentacion">Presentación</label>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-floating mb-3">
                <input type="number" class="form-control input" step="0.1" id="cantidad" name="cantidad"  autocomplete="off" placeholder="cantidad" min="0" />
                <label for="cantidad">Cantidad</label>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-floating mb-3">
                <input type="text" class="form-control input" id="undmedida" name="undmedida" placeholder="medida" autocomplete="off" />
                <label for="undmedida">Und. de Medida</label>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-floating ">
                <input type="number" class="form-control input" step="0.1" id="stockInicial" name="stockInicial" placeholder="stockInicial" min="0" autocomplete="off" value="0" />
                <label for="stockInicial">Stock Actual</label>
              </div>
            </div>


            <div class="col-md-2">
              <div class="form-floating">
                <input type="number" class="form-control input" step="0.1" id="precioc" name="precioc" placeholder="presioc" min="0" autocomplete="off" />
                <label for="precioc">Precio de Compra</label>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-floating">
                  <input type="number" class="form-control input" step="0.1" id="preciov" name="preciov" placeholder="presio" min="0" autocomplete="off" />
                <label for="preciov">Precio de Venta</label>
              </div>
            </div>

            

            <div class="col-md-2 ">
              <div class="form-floating ">
                <input type="number" class="form-control input" step="0.1" id="stockmin" name="stockmin" placeholder="stockmin" min="0" autocomplete="off" />
                <label for="stockmin">Stock min.</label>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-floating ">
                <input type="number" step="0.1" class="form-control input" id="stockmax" name="stockmax" placeholder="stockmax" min="0" autocomplete="off" />
                <label for="stockmax">Stock max.</label>
              </div>
            </div>

            <div class="col-md-4 ">
              <div class="form-floating mb-3">
                <input type="file" class="btn btn-outline-dark border input-img" name="img" id="img" accept="image/png, image/jpeg" placeholder="img">
              </div>
            </div>

          </div>
        </div>

        <div class="card-footer">
          <div style="display: flex; justify-content: flex-end; gap: 20px">
            <a class="btn btn-secondary" href="listar-producto.php">
              Cancelar
            </a>
            <button type="submit" class="btn btn-success" id="btnRegistrarProducto">
              Guardar
            </button>
          </div>
        </div>

    </form>
  </div>
  </div>
  </div>
  </div>

  <!-- Modal para agregar Marca -->
  <div class="modal fade" id="modalMarca" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form id="formAddMarca" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nueva Marca</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="form-floating">
            <input type="text" id="inputMarca" class="form-control input" placeholder="marca"  style="background-color: white;" autocomplete="off" required>
            <label for="inputMarca" class="form-label"><strong>Marca</strong></label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal para agregar Categoria -->
  <div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form id="formAddCategoria" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nueva Categoría</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="form-floating">
            <input type="text" id="inputCategoria" class="form-control input" placeholder="categoria"  style="background-color: white;" autocomplete="off" required>
            <label for="inputCategoria" class="form-label"><strong>Categoria</strong></label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal para agregar Subcategoría -->
  <div class="modal fade" id="modalSubcategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form id="formAddSubcategoria" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nueva Subcategoría</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="form-floating">

            <input type="text" id="inputSubcategoria" class="form-control input" placeholder="subcategoria"  style="background-color: white;" autocomplete="off"  required>
            <label for="inputSubcategoria" class="form-label"><strong>Subcategoria</strong></label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>


  <?php

  require_once "../../partials/_footer.php";

  ?>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const inputBarcode = document.getElementById("codigobarra");
      const marcaSelect = document.getElementById("marca");

      inputBarcode.addEventListener("keydown", function(e) {
        if (e.key === "Enter") {
          e.preventDefault();
          // Al presionar Enter, saltamos el foco al select de Marca:
          marcaSelect.focus();
        }
      });
    });
  </script>

  <script>
  

  document.addEventListener("DOMContentLoaded", () => {
    // Modal Marca
    const modalMarca = document.getElementById("modalMarca");
    const inputMarca = document.getElementById("inputMarca");
    if (modalMarca && inputMarca) {
      modalMarca.addEventListener("shown.bs.modal", () => {
        inputMarca.focus();
      });
    }

    // Modal Categoría
    const modalCategoria = document.getElementById("modalCategoria");
    const inputCategoria = document.getElementById("inputCategoria");
    if (modalCategoria && inputCategoria) {
      modalCategoria.addEventListener("shown.bs.modal", () => {
        inputCategoria.focus();
      });
    }

    // Modal Subcategoría
    const modalSubcategoria = document.getElementById("modalSubcategoria");
    const inputSubcategoria = document.getElementById("inputSubcategoria");
    if (modalSubcategoria && inputSubcategoria) {
      modalSubcategoria.addEventListener("shown.bs.modal", () => {
        inputSubcategoria.focus();
      });
    }
  });
</script>



<script>
  document.getElementById("btnRegistrarProducto").addEventListener("click", async function(e) {
    e.preventDefault();

    // 1) Validaciones previas: asegurarnos que los campos obligatorios no estén vacíos
    const marcaSelect         = document.getElementById("marca");
    const categoriaSelect     = document.getElementById("categoria");
    const subcategoriaSelect  = document.getElementById("subcategoria");
    const descripcionInput    = document.getElementById("descripcion");
    const presentacionInput   = document.getElementById("presentacion");
    const cantidadInput       = document.getElementById("cantidad");
    const undmedidaInput      = document.getElementById("undmedida");
    const preciocInput        = document.getElementById("precioc");
    const preciovInput        = document.getElementById("preciov");
    const stockInput          = document.getElementById("stockInicial");

    if (!marcaSelect.value) {
      showToast('Debe seleccionar una marca', 'ERROR', 1500);
      return;
    }
    if (!categoriaSelect.value) {
      showToast('Debe seleccionar una categoría', 'ERROR', 1500);
      return;
    }
    if (!subcategoriaSelect.value) {
      showToast('Debe seleccionar una subcategoría', 'ERROR', 1500);
      return;
    }
    if (!descripcionInput.value.trim()) {
      showToast('La descripción no puede estar vacía', 'ERROR', 1500);
      return;
    }
    if (!presentacionInput.value.trim()) {
      showToast('La presentacion no puede estar vacía', 'ERROR', 1500);
      return;
    }
    if (!cantidadInput.value.trim()) {
      showToast('La cantidad no puede estar vacía', 'ERROR', 1500);
      return;
    }
    if (!undmedidaInput.value.trim()) {
      showToast('La unidad de medida no puede estar vacía', 'ERROR', 1500);
      return;
    }
    if (!preciocInput.value || Number(preciocInput.value) < 0) {
      showToast('Ingrese un precio de compra válido', 'ERROR', 1500);
      return;
    }
    
    if (!preciovInput.value || Number(preciovInput.value) < 0) {
      showToast('Ingrese un precio de venta válido', 'ERROR', 1500);
      return;
    }
    if (Number(preciovInput.value) <= Number(preciocInput.value)) {
  showToast('El precio de venta debe ser mayor que el de compra', 'ERROR', 2000);
  return;
}
    if (!stockInput.value || Number(stockInput.value) < 0) {
      showToast('Ingrese un stock inicial válido', 'ERROR', 1500);
      return;
    }

    // 2) Preguntar con SweetAlert2 si desea continuar
    const confirmado = await ask(
      "¿Está seguro de registrar este producto?",
      "Productos"
    );
    if (!confirmado) {
      return; // El usuario canceló
    }

    // 3) Preparar el envío por AJAX
    const form = document.getElementById("formProducto");
    const formData = new FormData(form);

    try {
      const resp = await fetch("<?= SERVERURL ?>app/controllers/producto.controller.php", {
        method: "POST",
        body: formData
      });
      const result = await resp.json();

      if (result.rows > 0) {
        // 4a) Éxito: mostrar toast y redirigir
        showToast('Producto registrado exitosamente.', 'SUCCESS', 1500);
        setTimeout(() => {
          window.location.href = 'listar-producto.php';
        }, 1500);
      } else {
        // 4b) Error lógico (por ejemplo, el código de barras ya existe)
        showToast(result.message || 'Error al registrar el producto.', 'ERROR', 2000);
      }
    } catch (err) {
      // 4c) Error de red o servidor
      console.error("Error en la solicitud:", err);
      showToast('Error de servidor. Intenta nuevamente.', 'ERROR', 2000);
    }
  });
</script>
  


  <script>
    document.addEventListener("DOMContentLoaded", function() {
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
    document.addEventListener("DOMContentLoaded", () => {
      const marcaSelect = document.getElementById("marca");
      const categoriaSelect = document.getElementById("categoria");
      const subcategoriaSelect = document.getElementById("subcategoria");

      // 2.1: Alta Marca
      document.getElementById("formAddMarca").addEventListener("submit", async e => {
        e.preventDefault();
        const nombre = document.getElementById("inputMarca").value.trim();
        if (!nombre) return;

        const resp = await fetch("<?= SERVERURL ?>app/controllers/marca.controller.php?task=registerMarcaProducto", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            nombre
          })
        });
        const data = await resp.json();
        if (data.success) {
          // 1) Añadir opción y seleccionarla
          const opt = new Option(nombre, data.idmarca, true, true);
          marcaSelect.add(opt);
          // 2) Cerrar modal
          bootstrap.Modal.getInstance(document.getElementById("modalMarca")).hide();
          showToast("Marca registrada.", "SUCCESS", 1500);
          document.getElementById("inputMarca").value = "";
        } else {
          showToast("Error al registrar marca.", "ERROR", 1500);
        }
      });

      // 2.2: Alta Categoría
      document.getElementById("formAddCategoria").addEventListener("submit", async e => {
        e.preventDefault();
        const categoria = document.getElementById("inputCategoria").value.trim();
        if (!categoria) return;

        const resp = await fetch("<?= SERVERURL ?>app/controllers/categoria.controller.php?task=add", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            categoria
          })
        });
        const data = await resp.json();
        if (data.success) {
          const opt = new Option(categoria, data.idcategoria, true, true);
          categoriaSelect.add(opt);
          bootstrap.Modal.getInstance(document.getElementById("modalCategoria")).hide();
          showToast("Categoría registrada.", "SUCCESS", 1500);
          document.getElementById("inputCategoria").value = "";
          // Refrescar subcategorías limpias
          subcategoriaSelect.innerHTML = '<option value="">Seleccione una opcion</option>';
        } else {
          showToast("Error al registrar categoría.", "ERROR", 1500);
        }
      });

      // 2.3: Alta Subcategoría
      document.getElementById("formAddSubcategoria").addEventListener("submit", async e => {
        e.preventDefault();
        const subcat = document.getElementById("inputSubcategoria").value.trim();
        const idcat = categoriaSelect.value;
        if (!subcat || !idcat) return showToast("Selecciona categoría primero.", "WARNING", 1500);

        const resp = await fetch("<?= SERVERURL ?>app/controllers/subcategoria.controller.php?task=add", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            idcategoria: idcat,
            subcategoria: subcat
          })
        });
        const data = await resp.json();
        if (data.success) {
          const opt = new Option(subcat, data.idsubcategoria, true, true);
          subcategoriaSelect.add(opt);
          bootstrap.Modal.getInstance(document.getElementById("modalSubcategoria")).hide();
          showToast("Subcategoría registrada.", "SUCCESS", 1500);
          document.getElementById("inputSubcategoria").value = "";
        } else {
          showToast("Error al registrar subcategoría.", "ERROR", 1500);
        }
      });
    });
  </script>


  </body>

  </html>