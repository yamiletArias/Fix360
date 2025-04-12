<?php

const NAMEVIEW = "Lista de vehiculos";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<div class="container-main">
  <div class="header-group">
    <div class="text-end">
      <button title="Registrar vehiculo" type="button" onclick="window.location.href='registrar-vehiculos.php'"
        class="btn btn-success ">
        Registrar
      </button>
    </div>
  </div>
  <div class="table-container" id="tablaVehiculosContainer">
    <table id="tablaVehiculos" class="table table-striped display">
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
              <input type="text" disabled class="form-control input" id="modeloInput" />
              <label for="modeloInput">Modelo:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px">
            <div class="form-floating input-group">
              <input type="text" disabled class="form-control input" id="anioInput" />
              <label for="anioInput">Año:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px">
            <div class="form-floating input-group">
              <input type="text" disabled class="form-control input" id="serieInput" />
              <label for="serieInput">N° Serie:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px">
            <div class="form-floating input-group">
              <input type="text" disabled class="form-control input" id="combustibleInput" />
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

<!--script>
  // Selección de la tabla
  const tabla = document.querySelector("#tablaVehiculos tbody");

  // Inicialización del contador
  let i = 1;

  // Función para obtener los datos
  function obtenerDatos() {

    if($.fn.DataTable.isDataTable("#tablaVehiculos")){
      $("#tablaVehiculos").DataTable().destroy();
    } 
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
                <a href='editar-vehiculos.php?id=${element.idvehiculo}' class='btn btn-sm btn-outline-primary'>
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

  document.addEventListener("DOMContentLoaded", function() {
    obtenerDatos(); // Llamar la función para obtener datos
  });
</script-->

<script>
  function cargarTablaVehiculos() { // Inicio de cargarTablaVehiculos()
    if($.fn.DataTable.isDataTable("#tablaVehiculos")){
      $("#tablaVehiculos").DataTable().destroy();
    } // Cierra if

    $("#tablaVehiculos").DataTable({ // Inicio de configuración DataTable para vehículos
      ajax: {
        url: "<?= SERVERURL ?>app/controllers/vehiculo.controller.php?task=getAll", // URL que retorna JSON con los vehículos
        dataSrc: ""
      }, // Cierra ajax
      columns: [
        { // Columna 1: Número de fila
          data: null,
          render: (data, type, row, meta) => meta.row + 1
        }, // Cierra columna 1
        { // Columna 2: Propietario
          data: "propietario",
          defaultContent: "No disponible"
        }, // Cierra columna 2
        { // Columna 3: Tipo de vehículo (tipov)
          data: "tipov",
          defaultContent: "No disponible"
        }, // Cierra columna 3
        { // Columna 4: Marca (nombre)
          data: "nombre",
          defaultContent: "No disponible"
        }, // Cierra columna 4
        { // Columna 5: Placa
          data: "placa",
          defaultContent: "No disponible"
        }, // Cierra columna 5
        { // Columna 6: Color
          data: "color",
          defaultContent: "No disponible"
        }, // Cierra columna 6
        { // Columna 7: Opciones (botones: editar, ver detalle, y otro para ver más)
          data: null,
          render: function(data, type, row) { // Inicio de render de opciones
            return `
              <a href="editar-vehiculos.php?id=${row.idvehiculo}" class="btn btn-sm btn-warning" title="Editar">
                <i class="fa-solid fa-pen-to-square"></i>
              </a>
              <button class="btn btn-sm btn-info" title="Detalle" onclick="verDetalle('${row.modelo}', '${row.anio}', '${row.numserie}', '${row.tipocombustible}')">
                <i class="fa-solid fa-clipboard-list"></i>
              </button>
              <a href="editar-vehiculo.php?id=${row.idvehiculo}" class="btn btn-sm btn-outline-primary" title="Ver más">
                <i class="fa-solid fa-list"></i>
              </a>
            `;
          } // Cierra render de opciones
        } // Cierra columna 7
      ], // Cierra columns
      language: { // Inicio de configuración de idioma
        "lengthMenu": "Mostrar _MENU_ registros por página",
        "zeroRecords": "No se encontraron resultados",
        "info": "Mostrando página _PAGE_ de _PAGES_",
        "infoEmpty": "No hay registros disponibles",
        "infoFiltered": "(filtrado de _MAX_ registros totales)",
        "search": "Buscar:",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "emptyTable": "No hay datos disponibles en la tabla"
      } // Cierra language
    }); // Cierra DataTable inicialización
  } // Cierra cargarTablaVehiculos()

  document.addEventListener("DOMContentLoaded", function(){
    cargarTablaVehiculos();
  });

  // Función para ver los detalles del vehículo en el modal
  function verDetalle(modelo, anio, serie, combustible) { // Inicio de verDetalle()
    document.querySelector("#modeloInput").value = modelo || 'No proporcionado';
    document.querySelector("#anioInput").value = anio || 'No proporcionado';
    document.querySelector("#serieInput").value = serie || 'No proporcionado';
    document.querySelector("#combustibleInput").value = combustible || 'No proporcionado';
    let modal = new bootstrap.Modal(document.getElementById("miModal"));
    modal.show();
  } // Cierra verDetalle()
</script>

</body>

</html>