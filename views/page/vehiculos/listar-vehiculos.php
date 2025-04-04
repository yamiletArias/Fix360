<?php

CONST NAMEVIEW = "Lista de vehiculos";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
      <div class="container-main">
        <div class="header-group text-end">
          <div>
            <button title="Registrar vehiculo" type="button" onclick="window.location.href='registrar-vehiculos.php'"
              class="btn btn-success ">
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

    
<script>
const tabla = document.querySelector("#mitabla tbody");
let  enlace = null;

function obtenerDatos() {
  
  fetch(`../../../app/controllers/Vehiculo.controller.php?task=getAll`,{
    method: 'GET'
  })
  .then(response =>{return response.json()})
  .then(data =>{
    data.forEach(element =>{
      let i = 0;
      tabla.innerHTML += `
      <tr>
        <td>${i + 1}</td>
        <td>${element.propietario}</td>
        <td>${element.tipov} </td>
        <td>${element.marca} </td>
        <td>${element.placa} </td>
        <td>${element.color} </td>
        <td> 
        <a href = 'editar-vehiculo.php?id=${element.id}' class='btn btn-sm-warning'> <i class= "fa-solid fa-pen-to-square"></i> </a>  
        </td>
      </tr>
  
      `;
    });
  })
  .catch(error =>{console.error(error)});
}
document.addEventListener("DOMContentLoaded", () =>{
  obtenerDatos();
})
</script>

</body>

</html>