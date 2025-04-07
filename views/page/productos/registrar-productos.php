<?php

CONST NAMEVIEW = "Registro de producto";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

        <div class="container-main">
          <div class="card">
            <div class="card-header">
              <label
                ><strong><h3>Registrar Producto</h3></strong></label
              >
            </div>
            <div class="card-body">
              <div class="row">
                <!-- Marca -->
                <div class="col-md-4 mb-3">
                  <div class="form-floating">
                    <select class="form-select" id="marca" name="marca" style="color: black;" required>
                      <option>Marca</option>
                    </select>
                    <label for="marca">Marca:</label>
                  </div>
                </div>

                <!-- Subcategoria -->
                <div class="col-md-4">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="subcategoria" />
                    <label for="subcategoria">Subcategoría:</label>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-floating mb-3">
                    <textarea
                      class="form-control"
                      id="descripcion"
                      rows="4"
                    ></textarea>
                    <label for="descripcion">Descripción:</label>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-floating ">
                    <input type="text" class="form-control" id="presentacion" placeholder="presentacion" />
                    <label for="presentacion">Presentación</label>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="cantidad"  placeholder="cantidad"/>
                    <label for="cantidad">Cantidad</label>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="medida" placeholder="medida" />
                    <label for="medida">Medida</label>
                  </div>
                </div>

                <!-- Precio -->
                <div class="col-md-3">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="precio" placeholder="presio" />
                    <label for="precio">Precio</label>
                  </div>
                </div>

              </div>

              <!-- Imagen -->
              <div
                class="image-upload"
                onclick="document.getElementById('imageInput').click();"
              >
                <img id="previewImage" src="" alt="Imagen" width="80" /><br />
                <input
                  type="file"
                  id="imageInput"
                  style="display: none"
                  accept="image/*"
                  onchange="previewImage(event)"
                />
              </div>
            </div>

            <!-- Card Footer -->
            <div class="card-footer">
              <div style="display: flex; justify-content: flex-end; gap: 20px">
                <button
                  class="btn btn-secondary"
                  onclick="window.location.href='listar-Producto.php'"
                >
                  Cancelar
                </button>
                <button
                  class="btn btn-success"
                  onclick="window.location.href='listar-Producto.php'"
                >
                  Finalizar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php

    require_once "../../partials/_footer.php";
    
   ?>
   
  </body>
</html>
