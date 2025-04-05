<?php

const NAMEVIEW = "Editar datos del vehiculo";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<!---hasta aqui sera el header-->
<div class="container-main">
  <div class="card border" style="margin-top:50px;">
    <div class="card-body">
      <div class="row">

        <div class="col-md-4 mb-3">
          <div class="form-floating">
            <select class="form-select" id="tipov" name="tipov" style="color: black;" required>
              <option value="">Seleccione una opcion</option>
            </select>
            <label for="tipov">Tipo de vehiculo:</label>
          </div>
        </div>

        <div class="col-md-4 ">
          <div class="form-floating">
            <select class="form-select" id="marcav" name="marcav" style="color: black;" required>
              <option value="">Seleccione una opcion</option>

            </select>
            <label for="marcav">Marca del vehiculo:</label>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-floating">
            <select class="form-select" id="modelo" name="modelo" style="color: black;" required>
              <option value="">Seleccione una opcion</option>
            </select>
            <label for="modelo">Modelo del vehiculo:</label>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-floating">
            <input type="text" class="form-control" id="fplaca" placeholder="placadeejemplo" minlength="6" required
              maxlength="6" />
            <label for="fplaca">Placa</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating">
            <input type="text" class="form-control" id="fanio" placeholder="anio" minlength="4" maxlength="4"
              required />
            <label for="fanio">Año</label>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="form-floating">
            <input type="text" class="form-control" id="fnumserie" placeholder="numerodeserie" />
            <label for="fnumserie">N° de serie</label>
          </div>
        </div>

        <div class="col-md-4 mb-3">
          <div class="form-floating">
            <input type="text" class="form-control" id="fcolor" placeholder="#e0aef6" />
            <label for="fcolor">Color</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating">
            <select class="form-select" id="ftcombustible" style="color: black;">
              <option value="Gasolina" selected>Gasolina</option>
              <option value="Diesel">Diesel</option>
              <option value="GNV">GNV</option>
              <option value="GLP">GLP</option>
              <option value="Biodiésel">biodiésel</option>
              <option value="Etanol">Etanol</option>
              <option value="Allinol">Allinol</option>
              <option value="Electricidad">Electricidad</option>
              <option value="Hidrogeno">Hidrogeno</option>
              <option value="Biocombustible">Biocombustible</option>
            </select>
            <label for="ftcombustible">Tipo de combustible:</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating input-group mb-3">
            <input type="text" disabled class="form-control" id="floatingInput" value="1" />
            <label for="floatingInput">Propietario:</label>

            <input type="hidden" id="hiddenIdCliente" />
            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
              data-bs-target="#miModal">
              ...
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="card-footer">
      <div class="text-end">
        <button class="btn btn-secondary" onclick="window.location.href='listar-vehiculos.php'">
          Cancelar
        </button>
        <button class="btn btn-success" id="btnRegistrarVehiculo">
          Aceptar
        </button>
      </div>
    </div>

  </div>
</div>
</div>
</div>
<!--FIN VENTAS-->

<?php
require_once "../../partials/_footer.php";
?>

<div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
  <div class="modal-dialog"> <!-- Modal grande si lo requieres -->
    <div class="modal-content">

      <!-- Encabezado -->
      <div class="modal-header">
        <h2 class="modal-title" id="miModalLabel">Seleccionar Propietario</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Cuerpo -->
      <div class="modal-body">

        <!-- Fila para Tipo de Propietario -->
        <div class="row mb-3">
          <div class="col">
            <label><strong>Tipo de propietario:</strong></label>
            <!-- Contenedor de radio buttons -->
            <div style="display: flex; align-items: center; gap: 10px; margin-left:20px;">
              <div class="form-check form-check-inline" style="margin-right:40px;">
                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnpersona"
                  onclick="actualizarOpciones(); buscarPropietario();" checked>
                <label class="form-check-label" for="rbtnpersona" style="margin-left:5px;">Persona</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnempresa"
                  onclick="actualizarOpciones(); buscarPropietario();">
                <label class="form-check-label" for="rbtnempresa" style="margin-left:5px;">Empresa</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Fila para Método de Búsqueda -->
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <select id="selectMetodo" class="form-select" style="color: black;">
                <!-- Se actualizarán las opciones según el tipo (persona/empresa) -->
              </select>
              <label for="selectMetodo">Método de búsqueda:</label>
            </div>
          </div>
        </div>

        <!-- Fila para Valor Buscado -->
        <div class="row mb-3">
          <div class="col">
            <div class="form-floating">
              <input type="text" class="form-control" id="vbuscado" style="background-color: white;"
                placeholder="Valor buscado" />
              <label for="vbuscado">Valor buscado</label>
            </div>
          </div>
        </div>

        <!-- Tabla de Resultados -->
        <p class="mt-3"><strong>Resultado:</strong></p>
        <div class="table-responsive">
          <table id="tabla-resultado" class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Confirmar</th>
              </tr>
            </thead>
            <tbody>
              <!-- Se llenará dinámicamente -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pie del Modal -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>




</body>

</html>