<?php

const NAMEVIEW = "Servicios Brindados";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<style>
  .btn-registrar {
    padding: 0px;
    margin: 0px;
  }

  /* .btn-opciones{
    padding: 7px;
  }
    */
</style>


<div class="container-main">
  <div class="card border">
    <div class="card-header">
      <div class="row">
        <p class="col-md-9"></p>
        <label class="col-md-1 text-end">Cliente:</label>
        <h5 class="col-md-2 text-end">Cardenas Miguel</h5>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-4">
          <ul class="list-group">
            <li class="list-group-item">T. vehiculo: camioneta</li>
            <li class="list-group-item">Marca: Briliance</li>
            <li class="list-group-item">Modelo: MDC</li>
            <li class="list-group-item">Placa: 8S5WCK</li>
            <li class="list-group-item">Color: Rojo</li>
          </ul>
        </div>
        <div class="col-md-8">
        <table class="table table-striped ">
        <thead>
          <tr>
            <th>#</th>
            <th>T. Vehiculo</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Placa</th>
            <th>Color</th>
            <th>mecanico</th>
            <th>Operaciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Camioneta</td>
            <td>Briliance</td>
            <td>MarcaDCarro</td>
            <td>8S5WCK</td>
            <td>Rojo</td>
            <td>Walter Aquije</td>
            <td>
              <a href="#" class="btn btn-sm btn-warning btn-opciones" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a> <!-- Edita DETEQUIPOS -->
              <a href="#" class="btn btn-sm btn-danger btn-opciones" title="Eliminar"><i class="fa-solid fa-trash"></i></a> <!-- Elimina de forma física de DETEQUIPOS -->
            </td>
          </tr>
          <tr>
            <td>1</td>
            <td>Camioneta</td>
            <td>Briliance</td>
            <td>MarcaDCarro</td>
            <td>8S5WCK</td>
            <td>Rojo</td>
            <td>Walter Aquije</td>
            <td>
              <a href="#" class="btn btn-sm btn-warning btn-opciones" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a> <!-- Edita DETEQUIPOS -->
              <a href="#" class="btn btn-sm btn-danger btn-opciones" title="Eliminar"><i class="fa-solid fa-trash"></i></a> <!-- Elimina de forma física de DETEQUIPOS -->
            </td>
          </tr>
          <tr>
            <td>1</td>
            <td>Camioneta</td>
            <td>Briliance</td>
            <td>MarcaDCarro</td>
            <td>8S5WCK</td>
            <td>Rojo</td>
            <td>Walter Aquije</td>
            <td>
              <a href="#" class="btn btn-sm btn-warning btn-opciones" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a> <!-- Edita DETEQUIPOS -->
              <a href="#" class="btn btn-sm btn-danger btn-opciones" title="Eliminar"><i class="fa-solid fa-trash"></i></a> <!-- Elimina de forma física de DETEQUIPOS -->
            </td>
          </tr>
        </tbody>
      </table>
        </div>
      </div>


    </div>
    <div class="card-footer">
      <a href="registrar-ordenes2.php" class="btn btn-sm btn-secondary">Volver</a>
    </div>
  </div>
</div>
</div>

<?php

require_once "../../partials/_footer.php";

?>



<script src="<?= SERVERURL ?>views/page/ordenservicios/js/registrar-ordenes.js"></script>
</body>

</html>