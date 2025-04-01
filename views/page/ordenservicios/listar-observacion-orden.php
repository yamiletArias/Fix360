<?php

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<div class="container-main ">
  <div class="form-group">
    <label>Observaciones:</label>
    <textarea style="margin-right: 500px; padding-right: 100px;" disabled rows="10" cols="50" name=""
      id="">Observaciones del auto</textarea>
    <button style="margin-top: 130px; margin-left: 350px;"
      onclick="window.location.href='registrar-observacion-ordenes.html'"
      class=" btn btn-success">Registrar</button>
  </div>
  <div style="display: flex; gap: 20px; flex-wrap: wrap;">
    <div class="card" style="width: 18rem; position: relative;">
      <div class="form-floating">
        <label style="display: flex;">
          <strong>Espejos delanteros:</strong>
        </label>
        <img
          src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSXzvt_A5UQ3xFzsAKD-ufZy0jSFQjUcEmb5w&s"
          class="card-img-top" alt="...">

        <!-- Switch en la esquina inferior derecha de la imagen -->
        <div class="switch-container form-switch" style="position: absolute; bottom: 10px; right: 10px;">
          <label for="swtestado" class="form-check-label"
            style="color: white; font-weight: bold;margin-right:50px;">Estado:</label>
          <input class="form-check-input" type="checkbox" role="switch" id="swtestado" disabled
            style="transform: scale(1.2);">
        </div>
      </div>
      <div class="card-footer">
        <h4>Opciones:</h4>
        <button class="btn btn-warning btn-sm" onclick="window.location.href='editar-observacion-ordenes.html'">
          <i class="fa-solid fa-pen-to-square"></i>
        </button>
        <button id="btnEliminar" class="btn btn-danger btn-sm">
          <i class="fa-solid fa-trash"></i>
        </button>
      </div>
    </div>

    <div class="card" style="width: 18rem; position: relative;">
      <div class="form-floating">
        <label style="display: flex; color:white">
          <strong>Motor:</strong>
        </label>
        <img
          src="https://cdn.autobild.es/sites/navi.axelspringer.es/public/media/image/2017/09/mejores-motores-cuatro-cilindros_6.jpg?tf=3840x"
          class="card-img-top" alt="...">

        <!-- Switch en la esquina inferior derecha de la imagen -->
        <div class="switch-container form-switch" style="position: absolute; bottom: 10px; right: 10px;">
          <label for="swtestado" class="form-check-label"
            style="color: white; font-weight: bold;margin-right:50px;">Estado:</label>
          <input class="form-check-input" type="checkbox" role="switch" id="swtestado"
            style="transform: scale(1.2);" checked disabled>
        </div>
      </div>
      <div class="card-footer">
        <h4>Opciones:</h4>
        <button class="btn btn-warning btn-sm" onclick="window.location.href='editar-observacion-ordenes.html'">
          <i class="fa-solid fa-pen-to-square"></i>
        </button>
        <button id="btnEliminar" class="btn btn-danger btn-sm">
          <i class="fa-solid fa-trash"></i>
        </button>
      </div>
    </div>
    <div class="card" style="width: 18rem; position: relative;">
      <div class="form-floating">
        <label style="display: flex; color: white;">
          <strong>Luces delanteras:</strong>
        </label>
        <img
          src="https://www.championautoparts.com/content/dam/marketing/emea/champion/news/align-headligths-header-thumb.jpg"
          class="card-img-top" alt="...">

        <!-- Switch en la esquina inferior derecha de la imagen -->
        <div class="switch-container form-switch" style="position: absolute; bottom: 10px; right: 10px;">
          <label for="swtestado" class="form-check-label"
            style="color: white; font-weight: bold;margin-right:50px;">Estado:</label>
          <input class="form-check-input" type="checkbox" role="switch" id="swtestado" disabled
            style="transform: scale(1.2);">
        </div>
      </div>
      <div class="card-footer">
        <h4>Opciones:</h4>
        <button class="btn btn-warning btn-sm" onclick="window.location.href='editar-observacion-ordenes.html'">
          <i class="fa-solid fa-pen-to-square"></i>
        </button>
        <button id="btnEliminar" class="btn btn-danger btn-sm">
          <i class="fa-solid fa-trash"></i>
        </button>
      </div>
    </div>
    <div class="card" style="width: 18rem; position: relative;">
      <div class="form-floating">
        <label style="display: flex;">
          <strong>Parte trasera:</strong>
        </label>
        <img
          src="https://f.fcdn.app/imgs/c320e0/www.cymaco.com.uy/cym/9d51/original/wysiwyg/1/1280x0/70916208-2715878311764014-869032.jpg"
          class="card-img-top" alt="...">

        <!-- Switch en la esquina inferior derecha de la imagen -->
        <div class="switch-container form-switch" style="position: absolute; bottom: 10px; right: 10px;">
          <label for="swtestado" class="form-check-label"
            style="color: white; font-weight: bold;margin-right:50px;">Estado:</label>
          <input class="form-check-input" type="checkbox" role="switch" id="swtestado"
            style="transform: scale(1.2);" checked disabled>
        </div>
      </div>
      <div class="card-footer">
        <h4>Opciones:</h4>
        <button class="btn btn-warning btn-sm" onclick="window.location.href='editar-observacion-ordenes.html'">
          <i class="fa-solid fa-pen-to-square"></i>
        </button>
        <button id="btnEliminar" class="btn btn-danger btn-sm">
          <i class="fa-solid fa-trash"></i>
        </button>
      </div>
    </div>

  </div>

  <div>
    <button onclick="window.location.href='listar-ordenes.html'" style="margin-top: 20px; "
      class=" btn btn-secondary">Volver</button>
  </div>
</div>


</div>
</div>

<?php

require_once "../../partials/_footer.php";

?>

<script>
  document
    .querySelector("#btnEliminar")
    .addEventListener("click", async () => {
      if (
        await ask("¿Estás seguro de eliminar este registro?", "Usuarios")
      ) {
        showToast("Registro eliminado", "SUCCESS", 2000);
      } else {
        showToast("Eliminación cancelada", "WARNING", 2000);
      }
    });
</script>





</body>

</html>