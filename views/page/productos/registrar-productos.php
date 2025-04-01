<?php

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
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <select class="form-control" id="marca">
                      <option>Marca</option>
                    </select>
                    <label for="marca">Marca:</label>
                  </div>
                </div>

                <!-- Subcategoria -->
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="subcategoria" />
                    <label for="subcategoria">Subcategoría:</label>
                  </div>
                </div>

                <!-- Precio -->
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="precio" />
                    <label for="precio">Precio:</label>
                  </div>
                </div>

                <!-- Cantidad -->
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="cantidad" />
                    <label for="cantidad">Cantidad:</label>
                  </div>
                </div>

                <!-- Medida -->
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="medida" />
                    <label for="medida">Medida:</label>
                  </div>
                </div>

                <!-- Presentación -->
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="presentacion" />
                    <label for="presentacion">Presentación:</label>
                  </div>
                </div>

                <!-- Descripción -->
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <textarea
                      class="form-control"
                      id="descripcion"
                      rows="4"
                    ></textarea>
                    <label for="descripcion">Descripción:</label>
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
                  onclick="window.location.href='listar-Producto.html'"
                >
                  Cancelar
                </button>
                <button
                  class="btn btn-success"
                  onclick="window.location.href='listar-Producto.html'"
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
