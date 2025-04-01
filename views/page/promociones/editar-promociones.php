<?php

const NAMEVIEW = "Editar datos de promocion";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<div class="container-main">
  <div class="card">
    <div class="card-header">
      <label><strong>
          <h3>Editar Promoción</h3>
        </strong></label>
    </div>
    <div class="card-body">
      <div class="row">
        <!-- Nombre de la Promoción -->
        <div class="col-md-6">
          <div class="form-floating input-group mb-3">
            <input type="text" class="form-control" id="descripcion" />
            <label for="descripcion">Nombre de la promoción:</label>
          </div>
        </div>

        <!-- Productos -->
        <div class="col-md-6">
          <div class="form-floating input-group mb-3">
            <input type="text" class="form-control" id="productos" />
            <label for="productos">Productos:</label>
          </div>
        </div>

        <!-- Fecha de Inicio -->
        <div class="col-md-6">
          <div class="form-floating">
            <input type="date" class="form-control" id="fechaInicio" />
            <label for="fechaInicio">Fecha de Inicio:</label>
          </div>
        </div>

        <!-- Fecha de Fin -->
        <div class="col-md-6">
          <div class="form-floating">
            <input type="date" class="form-control" id="fechaFin" />
            <label for="fechaFin">Fecha de Fin:</label>
          </div>
        </div>

        <!-- Cantidad Máxima -->
        <div class="col-md-6">
          <div class="form-floating input-group mb-3">
            <input
              type="number"
              step="1"
              class="form-control"
              id="cantidadMaxima" />
            <label for="cantidadMaxima">Cantidad Máxima:</label>
          </div>
        </div>

        <!-- Cantidad -->
        <div class="col-md-6">
          <div class="form-floating input-group mb-3">
            <input type="number" class="form-control" id="cantidad" />
            <label for="cantidad">Cantidad:</label>
          </div>
        </div>

        <!-- Precio Oferta -->
        <div class="col-md-6">
          <div class="form-floating input-group mb-3">
            <input
              type="number"
              class="form-control"
              id="precioOferta" />
            <label for="precioOferta">Precio Oferta:</label>
          </div>
        </div>

        <!-- Descripción -->
        <div class="col-md-6">
          <div class="form-floating input-group mb-3">
            <input type="text" class="form-control" id="descripcion" />
            <label for="descripcion">Descripción:</label>
          </div>
        </div>

        <div
          class="image-upload"
          onclick="document.getElementById('imageInput').click();">
          <img id="previewImage" src="" alt="Imagen" width="80" /><br />
          <input
            type="file"
            id="imageInput"
            style="display: none"
            accept="image/*"
            onchange="previewImage(event)" />
        </div>
      </div>
    </div>

    <!-- Card Footer -->
    <div class="card-footer">
      <div style="margin-left: 1150px">
        <button
          class="btn btn-secondary"
          onclick="window.location.href='listar-promociones.html'">
          Cancelar
        </button>
        <button
          class="btn btn-success"
          onclick="window.location.href='listar-promociones.html'">
          Aceptar
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