<?php

const NAMEVIEW = "Productos | Editar";

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

  #img-preview {
    max-width: 120px;
    max-height: 120px;
    margin-top: 10px;
    display: block;
  }
</style>

<div class="container-main">
  <form
    action="<?= SERVERURL ?>app/controllers/producto.controller.php"
    id="formProducto"
    method="POST"
    enctype="multipart/form-data">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="idproducto" id="idproducto" value="">
    <input type="hidden" name="img_old" id="img_old" value="">


    <div class="card border" style="margin-top:50px;">
      <div class="card-body">
        <div class="row">
<div class="col-md-3">
            <div class="form-floating mb-3">
              <input type="text" class="form-control input" id="codigobarra" name="codigobarra" placeholder="Código de barras" autocomplete="off" autofocus />
              <label for="codigobarra">Código de Barras</label>
            </div>
          </div>

          <!-- MARCA -->
          <div class="col-md-3 mb-3">
            <div class="form-floating">
              <select
                class="form-select"
                id="marca"
                name="idmarca"
                style="color: black;"
                required
                disabled>
                <option value="">Seleccione una opcion</option>
              </select>
              <label for="marca">Marca:</label>
            </div>
          </div>

          <!-- CATEGORÍA -->
          <div class="col-md-3 mb-3">
            <div class="form-floating">
              <select
                class="form-select"
                id="categoria"
                name="categoria"
                style="color: black;"
                required
                disabled>
                <option value="">Seleccione una opcion</option>
              </select>
              <label for="categoria">Categoría:</label>
            </div>
          </div>

          <!-- SUBCATEGORÍA -->
          <div class="col-md-3 mb-3">
            <div class="form-floating">
              <select
                class="form-select"
                name="subcategoria"
                id="subcategoria"
                style="color: black;"
                required
                disabled>
                <option value="">Selecciona una opcion</option>
              </select>
              <label for="subcategoria">Subcategoría:</label>
            </div>
          </div>

          <!-- DESCRIPCIÓN -->
          <div class="col-md-5">
            <div class="form-floating mb-3">
              <textarea
                class="form-control input"
                id="descripcion"
                rows="4"
                name="descripcion"
                placeholder="Descripción"></textarea>
              <label for="descripcion">Descripción</label>
            </div>
          </div>

          <!-- PRESENTACIÓN (solo lectura) -->
          <div class="col-md-3">
            <div class="form-floating mb-3">
              <input
                type="text"
                class="form-control input"
                id="presentacion"
                name="presentacion"
                placeholder="Presentación"
                disabled />
              <label for="presentacion">Presentación</label>
            </div>
          </div>

          <!-- CANTIDAD POR PRESENTACIÓN -->
          <div class="col-md-2">
            <div class="form-floating mb-3">
              <input
                type="number"
                class="form-control input"
                step="0.1"
                id="cantidad"
                name="cantidad"
                placeholder="Cantidad"
                min="0"
                required />
              <label for="cantidad">Cantidad</label>
            </div>
          </div>

          <!-- UNIDAD DE MEDIDA (solo lectura) -->
          <div class="col-md-2">
            <div class="form-floating mb-3">
              <input
                type="text"
                class="form-control input"
                id="undmedida"
                name="undmedida"
                placeholder="Medida"
                disabled />
              <label for="undmedida">Und. de Medida</label>
            </div>
          </div>

          <!-- PRECIO -->
          <div class="col-md-2">
            <div class="form-floating mb-3">
              <input
                type="number"
                class="form-control input"
                step="0.01"
                id="precio"
                name="precio"
                placeholder="Precio"
                min="0"
                required />
              <label for="precio">Precio</label>
            </div>
          </div>

          <!-- STOCK ACTUAL (solo lectura) -->
          <div class="col-md-2">
            <div class="form-floating mb-3">
              <input
                type="number"
                class="form-control input"
                step="0.01"
                id="stockActual"
                name="stockActual"
                placeholder="Stock Actual"
                min="0"
                disabled />
              <label for="stockActual">Stock Actual</label>
            </div>
          </div>

          <!-- STOCK MÍNIMO -->
          <div class="col-md-2">
            <div class="form-floating mb-3">
              <input
                type="number"
                class="form-control input"
                step="0.01"
                id="stockmin"
                name="stockmin"
                placeholder="Stock mínimo"
                min="0"
                required />
              <label for="stockmin">Stock min.</label>
            </div>
          </div>

          <!-- STOCK MÁXIMO -->
          <div class="col-md-2">
            <div class="form-floating mb-3">
              <input
                type="number"
                class="form-control input"
                step="0.01"
                id="stockmax"
                name="stockmax"
                placeholder="Stock máximo"
                min="0" />
              <label for="stockmax">Stock max.</label>
            </div>
          </div>

          <!-- IMAGEN (nueva) -->
          <div class="col-md-4">
            <div class="form-floating mb-3">
              <input
                type="file"
                class="btn btn-outline-dark border input-img"
                name="img"
                id="img"
                accept="image/png, image/jpeg" />
            </div>
            <!-- Previsualización de la imagen antigua (SIN type="hidden") -->
            <!--img id="img-preview" src="" alt="Imagen anterior del producto"-->
          </div>
        </div><!-- /row -->
      </div><!-- /card-body -->

      <div class="card-footer">
        <div style="display: flex; justify-content: flex-end; gap: 20px">
          <button
            type="button"
            class="btn btn-secondary"
            href="listar-producto.php">
            Cancelar
          </button>
          <button type="submit" class="btn btn-success" id="btnRegistrarProducto">
            Aceptar
          </button>
        </div>
      </div><!-- /card-footer -->
    </div><!-- /card -->
  </form>
</div><!-- /container-main -->
</div><!-- /container-main -->
</div><!-- /container-main -->

<?php
require_once "../../partials/_footer.php";
?>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const inputBarcode = document.getElementById("codigobarra");
    const descripcion = document.getElementById("descripcion");

    inputBarcode.addEventListener("keydown", function(e) {
      if (e.key === "Enter") {
        e.preventDefault();
        // Al presionar Enter, saltamos el foco al select de Marca:
        descripcion.focus();
      }
    });
  });
</script>



<script>
  document.addEventListener("DOMContentLoaded", function() {
    const marcaSelect = document.getElementById("marca");
    const categoriaSelect = document.getElementById("categoria");
    const subcategoriaSelect = document.getElementById("subcategoria");

    // 1) Cargar todas las marcas
    const promMarcas = fetch("http://localhost/fix360/app/controllers/marca.controller.php?task=getAllMarcaProducto")
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

    // 2) Cargar todas las categorías
    const promCategorias = fetch("http://localhost/fix360/app/controllers/categoria.controller.php?task=getAll")
      .then(response => response.json())
      .then(data => {
        data.forEach(item => {
          const option = document.createElement("option");
          option.value = item.idcategoria;
          option.textContent = item.categoria;
          categoriaSelect.appendChild(option);
        });
      })
      .catch(error => console.error("Error al cargar categorías:", error));

    // 3) Función para recargar subcategorías según la categoría seleccionada
    function cargarSubcategorias() {
      const catId = categoriaSelect.value;
      subcategoriaSelect.innerHTML = '<option value="">Seleccione una opcion</option>';

      if (catId) {
        fetch(`http://localhost/fix360/app/controllers/subcategoria.controller.php?task=getSubcategoriaByCategoria&idcategoria=${encodeURIComponent(catId)}`)
          .then(response => response.json())
          .then(data => {
            data.forEach(item => {
              const option = document.createElement("option");
              option.value = item.idsubcategoria;
              option.textContent = item.subcategoria;
              subcategoriaSelect.appendChild(option);
            });
          })
          .catch(error => console.error("Error al cargar subcategorías:", error));
      }
    }
    categoriaSelect.addEventListener("change", cargarSubcategorias);

    // 4) Cuando marcas y categorías estén listas, leemos idproducto de la URL
    Promise.all([promMarcas, promCategorias]).then(() => {
      const urlParams = new URLSearchParams(window.location.search);
      const idprod = urlParams.get('idproducto');
      if (!idprod) {
        alert("No se especificó el ID de producto.");
        return;
      }

      // Guardamos el ID en el campo oculto
      document.getElementById('idproducto').value = idprod;

      // 5) Llamamos al controlador para traer los datos del producto
      fetch(`http://localhost/fix360/app/controllers/producto.controller.php?task=find&idproducto=${idprod}`)
        .then(response => response.json())
        .then(data => {
          // 5.1) Rellenar Marca y Categoría
          marcaSelect.value = data.idmarca;
          categoriaSelect.value = data.idcategoria;

          // 5.2) Cargar subcategorías para esa categoría
          cargarSubcategorias();

          // 5.3) Tras un breve retardo (200 ms), seleccionar la subcategoría
          setTimeout(() => {
            subcategoriaSelect.value = data.idsubcategoria;
          }, 200);

          // 5.4) Rellenar los demás campos
          document.getElementById('descripcion').value = data.descripcion;
          document.getElementById('presentacion').value = data.presentacion;
          document.getElementById('undmedida').value = data.undmedida;
          document.getElementById('cantidad').value = data.cantidad_por_presentacion;
          document.getElementById('precio').value = data.precio;
          document.getElementById('stockActual').value = data.stock_actual;
          document.getElementById('stockmin').value = data.stockmin;
          document.getElementById('stockmax').value = data.stockmax;
          document.getElementById('codigobarra').value = data.codigobarra;

          // 5.5) Mostrar imagen previa y guardar su ruta en el hidden
          document.getElementById('img-preview').src = data.img;
          document.getElementById('img_old').value = data.img;
        })
        .catch(error => console.error("Error al obtener datos del producto:", error));
    });
  });

  // --- Envío del formulario (AJAX) ---
  document.getElementById("formProducto").addEventListener("submit", function(e) {
    e.preventDefault();

    if (!confirm("¿Está seguro de que desea actualizar este producto?")) {
      return;
    }
    // Validaciones básicas
    const descripcion = document.getElementById("descripcion").value.trim();
    const codigobarra = document.getElementById("codigobarra").value.trim();
    const cantidad = parseFloat(document.getElementById("cantidad").value);
    const precio = parseFloat(document.getElementById("precio").value);
    const stockmin = parseFloat(document.getElementById("stockmin").value);
    const stockmax = document.getElementById("stockmax").value !== "" ?
      parseFloat(document.getElementById("stockmax").value) :
      null;

    if (!descripcion) {
      alert("La descripción es obligatoria.");
      return;
    }
    if (isNaN(cantidad) || cantidad <= 0) {
      alert("La cantidad debe ser mayor que 0.");
      return;
    }
    if (isNaN(precio) || precio < 0) {
      alert("El precio no puede ser negativo.");
      return;
    }
    if (isNaN(stockmin) || stockmin < 0) {
      alert("El stock mínimo no puede ser negativo.");
      return;
    }
    if (stockmax !== null && stockmax < stockmin) {
      alert("El stock máximo debe ser mayor o igual al mínimo.");
      return;
    }

    // Construimos FormData (incluye archivo si se seleccionó)
    const form = document.getElementById("formProducto");
    const formData = new FormData(form);

    // Si no se cargó archivo nuevo, borramos la clave "img" para que el SP no cambie la ruta
    if (!formData.get("img") || formData.get("img").size === 0) {
      formData.delete("img");
    }

    fetch("http://localhost/fix360/app/controllers/producto.controller.php", {
        method: "POST",
        body: formData
      })
      .then(response => response.json())
      .then(resp => {
        if (resp.status === "success") {
          alert("Producto actualizado correctamente.");
          window.location.href = "listar-Producto.php";
        } else {
          alert("Error: " + (resp.message || "No se pudo actualizar."));
        }
      })
      .catch(err => {
        console.error("Error en la solicitud:", err);
        alert("Ocurrió un error al comunicar con el servidor.");
      });
  });
</script>
</body>

</html>