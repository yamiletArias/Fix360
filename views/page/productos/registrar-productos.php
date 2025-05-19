<?php

const NAMEVIEW = "Productos | Registro";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<style>
  .input-img{
    margin: 0px;
    padding: 20px;
    margin-right:100px;
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
        <div class="col-md-4 mb-3">
          <div class="form-floating">
            <select class="form-select" id="marca" name="idmarca" style="color: black;" required>
              <option>Seleccione una opcion</option>
            </select>
            <label for="marca">Marca:</label>
          </div>
        </div>

        <div class="col-md-4 mb-3">
          <div class="form-floating">
            <select class="form-select" id="categoria" name="categoria" style="color: black;" required>
              <option>Seleccione una opcion</option>
            </select>
            <label for="categoria">Categoria:</label>
          </div>
        </div>

        <!-- Subcategoria -->
        <div class="col-md-4">
          <div class="form-floating">
            <select class="form-select" name="subcategoria" id="subcategoria" style="color: black;" required>
              <option value="">Selecciona una opcion</option>
            </select>
            <label for="subcategoria">Subcategoría:</label>
          </div>
        </div>

        <div class="col-md-5">
          <div class="form-floating mb-3">
            <textarea class="form-control input" id="descripcion" rows="4" name="descripcion" placeholder="descripcion"></textarea>
            <label for="descripcion">Descripción</label>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-floating ">
            <input type="text" class="form-control input" id="presentacion" name="presentacion" placeholder="presentacion" />
            <label for="presentacion">Presentación</label>
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-floating mb-3">
            <input type="number" class="form-control input" step="0.1" id="cantidad" name="cantidad" placeholder="cantidad" min="0" />
            <label for="cantidad">Cantidad</label>
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-floating mb-3">
            <input type="text" class="form-control input" id="undmedida" name="undmedida" placeholder="medida" />
            <label for="undmedida">Und. de Medida</label>
          </div>
        </div>

        
        <div class="col-md-2">
          <div class="form-floating mb-3">
            <input type="number" class="form-control input" step="0.1" id="precio" name="precio" placeholder="presio" min="0" />
            <label for="precio">Precio</label>
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-floating ">
            <input type="number" class="form-control input" step="0.1" id="stockInicial" name="stockInicial" placeholder="stockInicial" min="0" />
            <label for="stockInicial">Stock Actual</label>
          </div>
        </div>

          <div class="col-md-2">
          <div class="form-floating ">
            <input type="number" class="form-control input" step="0.1" id="stockmin" name="stockmin" placeholder="stockmin" min="0" />
            <label for="stockmin">Stock min.</label>
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-floating ">
            <input type="number" step="0.1" class="form-control input" id="stockmax" name="stockmax" placeholder="stockmax" min="0" />
            <label for="stockmax">Stock max.</label>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-floating mb-3">
            <input type="file" class="btn btn-outline-dark border input-img" name="img" id="img" accept="image/png, image/jpeg" placeholder="img">
          </div>
        </div>

    </div>
  </div>

  <div class="card-footer">
    <div style="display: flex; justify-content: flex-end; gap: 20px">
      <button class="btn btn-secondary" onclick="window.location.href='listar-Producto.php'">
        Cancelar
      </button>
      <button type="submit" class="btn btn-success" id="btnRegistrarProducto">
        Aceptar
      </button>
    </div>
  </div>

  </form>
</div>
</div>
</div>
</div>

<?php

require_once "../../partials/_footer.php";

?>
<script>
document.getElementById("btnRegistrarProducto").addEventListener("click", function (e) {
  e.preventDefault();

  const confirmacion = window.confirm("¿Estás seguro de que deseas registrar el producto?");
  if (!confirmacion) {
    return; // Si el usuario cancela, no hace nada
  }

  const form = document.getElementById("formProducto");
  const formData = new FormData(form);

  for (let pair of formData.entries()) {
    console.log(pair[0] + ': ' + pair[1]);
  }

  fetch("http://localhost/fix360/app/controllers/producto.controller.php", {
    method: "POST",
    body: formData
  })
    .then(response => response.json())
    .then(resp => {
      if (resp.rows > 0) {
        showToast('Producto registrado exitosamente.', 'SUCCESS', 1500);
        setTimeout(() => {
          window.location.href = 'listar-producto.php';
        }, 1500);
      } else {
        console.log("Error en el registro");
      }
    })
    .catch(err => {
      console.log("Error en la solicitud", err);
    });
});
</script>


<script>
  document.addEventListener("DOMContentLoaded", function(){
    const marcaSelect = document.getElementById("marca");
    const categoriaSelect = document.getElementById("categoria");
    const subcategoriaSelect = document.getElementById("subcategoria");

    fetch("http://localhost/fix360/app/controllers/marca.controller.php?task=getAllMarcaProducto")
    .then(response => response.json())
    .then(data => {
      data.forEach(item =>{
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
      data.forEach( item => {
        const option = document.createElement("option");
        option.value = item.idcategoria;
        option.textContent = item.categoria;
        categoriaSelect.appendChild(option);
      });
    })
    .catch(error => console.error("Error al cargar categorias:", error));

    function cargarSubcategorias(){
      const categoria = categoriaSelect.value;

      subcategoriaSelect.innerHTML = '<option value="">Seleccione una opcion</option>';

      if(categoria){
        fetch(`http://localhost/fix360/app/controllers/subcategoria.controller.php?task=getSubcategoriaByCategoria&idcategoria=${encodeURIComponent(categoria)}`)
        .then(response => response.json())
        .then(data =>{
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

</body>

</html>