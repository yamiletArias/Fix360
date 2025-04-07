<?php

const NAMEVIEW = "Registro de producto";

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
  <form action="">

  
  <div class="card border" style="margin-top:50px;">
    <div class="card-body">
      <div class="row">
        <!-- Marca -->
        <div class="col-md-3 mb-3">
          <div class="form-floating">
            <select class="form-select" id="marca" name="marca" style="color: black;" required>
              <option>Marca</option>
            </select>
            <label for="marca">Marca:</label>
          </div>
        </div>

        <div class="col-md-3 mb-3">
          <div class="form-floating">
            <select class="form-select" id="categoria" name="categoria" style="color: black;" required>
              <option>Categoria</option>
            </select>
            <label for="marca">Categoria:</label>
          </div>
        </div>

        <!-- Subcategoria -->
        <div class="col-md-3">
          <div class="form-floating">
            <input type="text" class="form-select" id="subcategoria" style="color: black;" required />
            <label for="subcategoria">Subcategoría:</label>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-floating mb-3">
            <textarea class="form-control" id="descripcion" rows="4" placeholder="descripcion"></textarea>
            <label for="descripcion">Descripción</label>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-floating ">
            <input type="text" class="form-control" id="presentacion" placeholder="presentacion" />
            <label for="presentacion">Presentación</label>
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="cantidad" placeholder="cantidad" />
            <label for="cantidad">Cantidad</label>
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="medida" placeholder="medida" />
            <label for="medida">Medida</label>
          </div>
        </div>

        
        <div class="col-md-2">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="precio" placeholder="presio" />
            <label for="precio">Precio</label>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-floating mb-3">
            <input type="file" class="btn btn-outline-dark border input-img" accept="image/png, image/jpeg" id="img" placeholder="img">
          </div>
        </div>

    </div>
  </div>

  <div class="card-footer">
    <div style="display: flex; justify-content: flex-end; gap: 20px">
      <button class="btn btn-secondary" onclick="window.location.href='listar-Producto.php'">
        Cancelar
      </button>
      <button class="btn btn-success" onclick="window.location.href='listar-Producto.php'">
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

</body>

</html>