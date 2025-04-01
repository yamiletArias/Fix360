<?php

CONST NAMEVIEW = "Registro de observaciones";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<div class="container-main">
  <div>
    <div>
      <p>Observaciones:</p>
      <textarea style="padding-right: 10px;" rows="10" cols="50" name="" id=""></textarea>
    </div>
    <div>
      <p>Componente:</p>
      <select id="estado" name="estado">
        <option value="activo">Elija un componente</option>
        <option value="activo">Espejos</option>
        <option value="inactivo">Faros Delanteros</option>
        <option value="pendiente">Faros traseros</option>
      </select>
    </div>
    <p>estado:</p>
    <div style="padding-left: 50px ; " class="form-check form-switch">
      <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
    </div style="padding:50px;">
    <p>Foto:</p>
    <input style="padding:20px;" type="file" id="archivo" name="archivo">
  </div>
  <div style="margin-top:40px;">
    <button class="btn btn-danger" onclick="window.location.href='listar-observacion-orden.html'">Cancelar</button>
    <button class="btn btn-success" onclick="window.location.href='listar-observacion-orden.html'">Aceptar</button>
  </div>
</div>


</div>


</div>

<?php 

require_once "../../partials/_footer.php";

?>

</body>

</html>