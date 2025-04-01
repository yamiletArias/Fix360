<?php

CONST NAMEVIEW = "Lista de vehiculos";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
      <div class="container-main">
        <div class="header-group">
          <div>
            <button title="Registrar vehiculo" type="button" onclick="window.location.href='Registrar-vehiculos.html'"
              class="btn btn-success">
              Registrar
            </button>
          </div>
        </div>
        <div class="table-container">
          <table id="miTabla" class="table table-striped display">
            <thead>
              <tr>
                <td>#</td>
                <th title="Propietario actual del vehiculo">Propietario</th>
                <th title="Tipo de vehiculo">T. Vehiculo</th>
                <th title="Marca del vehiculo">Marca</th>
                <th title="Placa del vehiculo">Placa</th>
                <th title="Color del vehiculo">Color</th>
                <th>Opciones</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Jhon Francia</td>
                <td>Sedan</td>
                <td>Toyota</td>
                <td>798-FBH</td>
                <td>ROJO</td>
                <td>
                  <button title="Detalle del vehiculo" data-bs-toggle="modal" data-bs-target="#miModal"
                    class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-list"></i>
                  </button>
                  <button title="Editar vehiculo" onclick="window.location.href='editar-vehiculos.html'"
                    class="btn btn-warning btn-sm">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <button title="Historial del vehiculo" onclick="window.location.href='historial-vehiculos.html'"
                    class="btn btn-outline-dark btn-sm" data-id="1">
                    <i class="fa-solid fa-file-alt"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>2</td>
                <td>Jose Campos</td>
                <td>Sport</td>
                <td>Audi</td>
                <td>F4F-789</td>
                <td>GRIS</td>
                <td>
                  <button title="Detalle del vehiculo" data-bs-toggle="modal" data-bs-target="#miModal"
                    class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-list"></i>
                  </button>
                  <button title="Editar vehiculo" onclick="window.location.href='editar-vehiculos.html'"
                    class="btn btn-warning btn-sm">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <button title="Historial del vehiculo" onclick="window.location.href='historial-vehiculos.html'"
                    class="btn btn-outline-dark btn-sm" data-id="1">
                    <i class="fa-solid fa-file-alt"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>3</td>
                <td>Maria Mercedes</td>
                <td>SUV</td>
                <td>Volkswagen</td>
                <td>DEF-456</td>
                <td>AZUL</td>
                <td>
                  <button title="Detalle del vehiculo" data-bs-toggle="modal" data-bs-target="#miModal"
                    class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-list"></i>
                  </button>
                  <button title="Editar vehiculo" onclick="window.location.href='editar-vehiculos.html'"
                    class="btn btn-warning btn-sm">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <button title="Historial del vehiculo" onclick="window.location.href='historial-vehiculos.html'"
                    class="btn btn-outline-dark btn-sm" data-id="1">
                    <i class="fa-solid fa-file-alt"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title" id="miModalLabel">Detalle del vehiculo</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" style="padding: 10px">
            <div class="row">
              <div class="form-group" style="margin: 10px">
                <div class="form-floating input-group">
                  <input type="text" disabled class="form-control" id="floatingInput" />
                  <label for="floatingInput">Modelo:</label>
                </div>
              </div>
              <div class="form-group" style="margin: 10px">
                <div class="form-floating input-group">
                  <input type="text" disabled class="form-control" id="floatingInput" />
                  <label for="floatingInput">Año:</label>
                </div>
              </div>
              <div class="form-group" style="margin: 10px">
                <div class="form-floating input-group">
                  <input type="text" disabled class="form-control" id="floatingInput" />
                  <label for="floatingInput">N° Serie:</label>
                </div>
              </div>
              <div class="form-group" style="margin: 10px">
                <div class="form-floating input-group">
                  <input type="text" disabled class="form-control" id="floatingInput" />
                  <label for="floatingInput">Tipo de combustible:</label>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
    

<?php

require_once "../../partials/_footer.php";

?>

    

</body>

</html>