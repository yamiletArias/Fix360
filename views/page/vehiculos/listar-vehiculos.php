<?php

const NAMEVIEW = "Lista de vehiculos";

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
              <input type="text" disabled class="form-control" id="modeloInput" />
              <label for="modeloInput">Modelo:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px">
            <div class="form-floating input-group">
              <input type="text" disabled class="form-control" id="anioInput" />
              <label for="anioInput">Año:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px">
            <div class="form-floating input-group">
              <input type="text" disabled class="form-control" id="serieInput" />
              <label for="serieInput">N° Serie:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px">
            <div class="form-floating input-group">
              <input type="text" disabled class="form-control" id="combustibleInput" />
              <label for="combustibleInput">Tipo de combustible:</label>
            </div>
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


<?php

require_once "../../partials/_footer.php";

?>

<script>
  // Selección de la tabla
  const tabla = document.querySelector("#miTabla tbody");

  // Inicialización del contador
  let i = 1;

  // Función para obtener los datos
  function obtenerDatos() {
    fetch(`<?= SERVERURL ?>app/controllers/vehiculo.controller.php?task=getAll`, {
        method: 'GET'
      })
      .then(response => response.json())
      .then(data => {
        // Borrar contenido previo
        tabla.innerHTML = '';

        // Iterar sobre los datos y agregar las filas a la tabla
        data.forEach(element => {
          tabla.innerHTML += `
            <tr>
              <td>${i++}</td>
              <td>${element.propietario || 'No disponible'}</td>
              <td>${element.tipov || 'No disponible'}</td>
              <td>${element.nombre || 'No disponible'}</td>
              <td>${element.placa || 'No disponible'}</td>
              <td>${element.color || 'No disponible'}</td>
              <td> 
                <a href='editar-vehiculos.php?id=${element.idvehiculo}' class='btn btn-sm btn-warning'>
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>  
                <button class='btn btn-sm btn-info' onclick="verDetalle('${element.modelo}', '${element.anio}', '${element.numserie}', '${element.tipocombustible}')">
                  <i class='fa-solid fa-clipboard-list'></i>
                </button>
                <a href='editar-vehiculo.php?id=${element.idvehiculo}' class='btn btn-sm btn-outline-primary'>
                  <i class="fa-solid fa-list"></i>
                </a>
              </td>
            </tr>
          `;
        });
      })
      .catch(error => {
        console.error('Error al obtener los datos:', error);
      });
  }

  // Esperar que el DOM se cargue
  document.addEventListener("DOMContentLoaded", () => {
    obtenerDatos(); // Llamar la función para obtener datos
  });

  // Función para ver los detalles del vehículo en el modal
  function verDetalle(modelo, anio, serie, combustible) {
    document.querySelector("#modeloInput").value = modelo || 'No proporcionado';
    document.querySelector("#anioInput").value = anio || 'No proporcionado';
    document.querySelector("#serieInput").value = serie || 'No proporcionado';
    document.querySelector("#combustibleInput").value = combustible || 'No proporcionado';

    // Mostrar el modal de Bootstrap
    let modal = new bootstrap.Modal(document.getElementById("miModal"));
    modal.show();
  }
</script>

</body>

</html>