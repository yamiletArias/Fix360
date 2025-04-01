<?php

CONST NAMEVIEW = "Editor de vehiculo";


require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
      <div class="container-main">
        <div class="card">
          <div class="card-header">
            <label><strong>
                <h3>Editar Vehiculo</h3>
              </strong></label>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <select class="form-select-lg">
                    <option selected>tipo de vehiculo:</option>
                    <option value="direccion">SUV</option>
                    <option value="mecanica">Camioneta</option>
                    <option value="lubricacion">Sedan</option>
                    <option value="otros">Pick up</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <select class="form-select-lg">
                    <option selected>Marca de vehiculo:</option>
                    <option value="direccion">Toyota</option>
                    <option value="mecanica">Honda</option>
                    <option value="lubricacion">Hyundai</option>
                    <option value="otros">Volkswagen</option>
                    <option value="otros">Audi</option>
                    <option value="otros">Porsche</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <select class="form-select-lg">
                    <option selected>Modelo del auto:</option>
                    <option value="mecanico1">4Runner</option>
                    <option value="mecanico2">Agya</option>
                    <option value="mecanico3">Avanza</option>
                    <option value="mecanico3">Corolla</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-floating"> 
                    <input type="text" class="form-control" id="floatingInput" />
                    <label for="floatingInput">Placa:</label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-floating">
                  <input type="text"  class="form-control" id="floatingInput" />
                  <label for="floatingInput">Año:</label>
                </div>
              </div>
              <div class="col-md-4 mb-3">
                <div class="form-floating">
                  <input type="text"  class="form-control" id="floatingInput" />
                  <label for="floatingInput">N° de serie:</label>
                </div>
              </div>

              <div class="col-md-4 mb-3">
                <div class="form-floating">
                  <input type="text"  class="form-control" id="floatingInput" />
                  <label for="floatingInput">Color:</label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <select class="form-select-lg">
                    <option selected>Tipo de combustible:</option>
                    <option value="mecanico1">Gasolina</option>
                    <option value="mecanico2">Diesel</option>
                    <option value="mecanico3">Gas natural</option>
                    <option value="mecanico3">Biocombustible</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-floating input-group mb-3">
                  <input type="text" disabled class="form-control" id="floatingInput" />
                  <label for="floatingInput">Propietario:</label>
                  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#miModal">
                    ...
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div style="margin-left: 1150px">
              <button class="btn btn-danger" onclick="window.location.href='listar-vehiculos.html'">
                Cancelar
              </button>
              <button class="btn btn-success" onclick="window.location.href='listar-vehiculos.html'">
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

  <div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="miModalLabel">
                    Seleccionar Propietario
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 10px;">
                <div class="row">
                    <!-- Radio Buttons -->
                        <div class="col-md-4">
                         <div class="form-group" style="padding: 0px;margin: 0px;">
                             <label style="margin-right: 50px;"><strong>Tipo de cliente:</strong></label>
                             <div class="form-check form-check-inline">
                                 <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnpersona"
                                 onclick="actualizarOpciones()" checked />
                                 <label class="form-check-label" for="rbtnpersona">Persona</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnempresa"
                                    onclick="actualizarOpciones()" />
                                    <label class="form-check-label" for="rbtnempresa">Empresa</label>
                                </div> 
                            </div>
                        </div>
                    <div class="col-md-4">
                        <!-- Select de Métodos de Búsqueda -->
                        <div class="form-group ">
                            <label><strong> Metodo de busqueda:</strong></label>
                            <select id="selectMetodo" class="form-select" aria-label="Default select example"></select>
                        </div>
                    </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><strong>Valor buscado:</strong></label>
                                <input type="text" class="form-control" placeholder="Valor buscado" aria-label="Username"
                                aria-describedby="basic-addon1" />
                            </div>
                        </div>
                    
                    <p>Resultado:</p>
                    <div class="table-container">
                        <table id="tabla-resultado" class="table table-striped display">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>DNI</th>
                                    <th>Confirmar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Jose Hernandez</td>
                                    <td>24658791</td>
                                    <td>
                                        <button type="button" class="btn btn-success">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Josue Pilpe</td>
                                    <td>78524631</td>
                                    <td>
                                        <button type="button" class="btn btn-success">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
<script>
  function actualizarOpciones() {
      const select = document.getElementById("selectMetodo");
      const personaSeleccionada =
          document.getElementById("rbtnpersona").checked;

      // Limpiar opciones actuales
      select.innerHTML = "";

      // Opciones para Persona
      if (personaSeleccionada) {
          select.innerHTML += `<option value="dni">DNI</option>`;
          select.innerHTML += `<option value="nombre">Nombre</option>`;
      }
      // Opciones para Empresa
      else {
          select.innerHTML += `<option value="ruc">RUC</option>`;
          select.innerHTML += `<option value="razonsocial">Razón Social</option>`;
      }
  }

  // Ejecutar la función al cargar la página para establecer las opciones iniciales
  actualizarOpciones();
</script>
  <!-- endinject -->
  <!-- Custom js for this page -->
  <!-- End custom js for this page -->
</body>

</html>