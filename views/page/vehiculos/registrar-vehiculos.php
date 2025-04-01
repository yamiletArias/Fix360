<?php
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
      <!---hasta aqui sera el header-->
      <div class="container-main">
        <div class="card border">
          <div class="card-header">
            <label><strong>
                <h3>Nuevo Registro</h3>
              </strong></label>
          </div>
          <div class="card-body">
            <div class="row">

              <div class="col-md-4 mb-3">
                <div class="form-floating">
                  <select class="form-select" id="tipov" name="tipov" style="color: black;">
                    <option value="">Seleccione una opcion</option>
                  </select>
                  <label for="tipov">Tipo de vehiculo:</label>
                </div>
              </div>

              <div class="col-md-4 ">
                <div class="form-floating">
                  <select class="form-select" id="marcav" name="marcav" style="color: black;">
                    <option value="">Seleccione una opcion</option>
                    
                  </select>
                  <label for="marcav">Marca del vehiculo:</label>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-floating">
                  <select class="form-select" id="modelo" name="modelo" style="color: black;">
                    <option value="">Seleccione una opcion</option>
                  </select>
                  <label for="modelo">Modelo del vehiculo:</label>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-floating"> 
                    <input type="text" class="form-control" id="fplaca" placeholder="placadeejemplo" minlength="6" maxlength="6" />
                    <label for="fplaca">Placa</label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-floating">
                  <input type="text"  class="form-control" id="fanio" placeholder="anio" minlength="4" maxlength="4" />
                  <label for="fanio">Año</label>
                </div>
              </div>
              <div class="col-md-4 mb-3">
                <div class="form-floating">
                  <input type="text"  class="form-control" id="fnumserie"  placeholder="numerodeserie"/>
                  <label for="fnumserie">N° de serie</label>
                </div>
              </div>

              <div class="col-md-4 mb-3">
                <div class="form-floating">
                  <input type="text"  class="form-control" id="fcolor"  placeholder="#e0aef6"/>
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
                  <input type="text" disabled class="form-control" id="floatingInput" />
                  <label for="floatingInput">Propietario:</label>
                  <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#miModal">
                    ...
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="text-end">
              <button class="btn btn-secondary" onclick="window.location.href='listar-vehiculos.html'">
                Cancelar
              </button>
              <button class="btn btn-success"  type="submit">
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
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="miModalLabel">Seleccionar Propietario</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" >
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="d-block"><strong>Tipo de cliente:</strong></label>
                                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnpersona"
                                    onclick="actualizarOpciones()" checked />
                                <label class="form-check-label" for="rbtnpersona">Persona</label>
                                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnempresa"
                                    onclick="actualizarOpciones()" />
                                <label class="form-check-label" for="rbtnempresa">Empresa</label>
                            </div>
                    </div>

                    <!-- Método de Búsqueda -->
                    <div class="col-md-4">
                        <div class="form-floating">
                          <select id="selectMetodo" class="form-select" style="color: black;"></select>
                          <label for="selectmetodo">Método de búsqueda:</label>
                        </div>
                    </div>

                    <!-- Valor Buscado -->
                    <div class="col-md-4">
                      <div class="form-floating">
                       <input type="text"  class="form-control" id="vbuscado" style="background-color: white;" placeholder="vbuscado" />
                        <label for="vbuscado">Valor buscado</label>
                      </div>
                    </div>
                </div>

                <p class="mt-3"><strong>Resultado:</strong></p>
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
                                    <button type="button" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Josue Pilpe</td>
                                <td>78524631</td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Estilos Adicionales -->
<style>
    /* Asegurar que el select no parezca deshabilitado */
    #selectMetodo {
        background-color: white !important;
        cursor: pointer;
    }
</style>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const tipovSelect = document.getElementById("tipov");
    const marcavSelect = document.getElementById("marcav");
    const modeloSelect = document.getElementById("modelo");
  
    // Cargar select de tipo de vehículo
    fetch("http://localhost/fix360/app/controllers/Tipov.controller.php")
      .then(response => response.json())
      .then(data => {
        // Asumiendo que cada registro trae campos 'idtipov' y 'tipov'
        data.forEach(item => {
          const option = document.createElement("option");
          option.value = item.idtipov; // Ajusta según el nombre del campo real
          option.textContent = item.tipov; // Ajusta según el nombre del campo real
          tipovSelect.appendChild(option);
        });
      })
      .catch(error => console.error("Error al cargar tipos de vehículo:", error));
  
    // Cargar select de marcas
    fetch("http://localhost/fix360/app/controllers/Marca.controller.php")
      .then(response => response.json())
      .then(data => {
        // Asumiendo que cada registro trae campos 'idmarca' y 'marca'
        data.forEach(item => {
          const option = document.createElement("option");
          option.value = item.idmarca; // Ajusta según el nombre del campo real
          option.textContent = item.nombre; // Ajusta según el nombre del campo real
          marcavSelect.appendChild(option);
        });
      })
      .catch(error => console.error("Error al cargar marcas:", error));
  
    // Función para cargar modelos basado en tipo y marca seleccionados
    function cargarModelos() {
      const idtipov = tipovSelect.value;
      const idmarca = marcavSelect.value;
  
      // Limpiar el select de modelos y agregar opción por defecto
      modeloSelect.innerHTML = '<option value="">Seleccione una opcion</option>';
  
      // Solo llamar al controller si ambos selects tienen un valor
      if (idtipov && idmarca) {
        fetch(`http://localhost/fix360/app/controllers/Modelo.controller.php?idtipov=${encodeURIComponent(idtipov)}&idmarca=${encodeURIComponent(idmarca)}`)
          .then(response => response.json())
          .then(data => {
            // Asumiendo que cada registro trae campos 'idmodelo' y 'modelo'
            data.forEach(item => {
              const option = document.createElement("option");
              option.value = item.idmodelo;
              option.textContent = item.modelo;
              modeloSelect.appendChild(option);
            });
          })
          .catch(error => console.error("Error al cargar modelos:", error));
      }
    }
  
    // Agregar event listeners para que cuando cambie el tipo o la marca se actualice el select de modelos
    tipovSelect.addEventListener("change", cargarModelos);
    marcavSelect.addEventListener("change", cargarModelos);
  });
  </script>
  

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