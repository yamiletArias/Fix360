<?php

const NAMEVIEW = "Lista de Vehiculos";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";


// 1) Leer el id de cliente que llega por GET
$idcliente = isset($_GET['idcliente']) ? intval($_GET['idcliente']) : 0;

// 2) Obtener el nombre del cliente para el título
$nombreCliente = "";
if ($idcliente) {
  $info = file_get_contents(
    "http://localhost/fix360/app/controllers/Cliente.controller.php?task=getClienteById&idcliente=$idcliente"
  );
  $cli = json_decode($info, true);

  // Extraer correctamente "propietario" desde el objeto JSON
  if (isset($cli['status']) && $cli['status'] === true && isset($cli['propietario'])) {
    $nombreCliente = htmlspecialchars($cli['propietario']);
  } else {
    $nombreCliente = "Cliente #$idcliente";
  }
} else {
  $nombreCliente = "Cliente no especificado";
}

?>

<div class="container-main">
  <div class="row">
    <div class="col-md-10">
      <div class="text-right">
        <h3>Vehículos a nombre de: <strong><?= htmlspecialchars($nombreCliente) ?></strong></h3>
      </div>
    </div>

  </div>

  <div class="table-responsive">
    <table id="tablaVehiculos" class="table table-striped display">
      <thead>
        <tr>
          <th>#</th>
          <th>T. Vehiculo</th>
          <th>Marca</th>
          <th>Placa</th>
          <th>Color</th>
          <th>Opciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
  <div class="text-right">
    <a href="javascript:history.back()" class="btn btn-secondary">Volver</a>
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
          <div class="form-group" style="margin: 10px">
            <div class="form-floating input-group">
              <input type="text" disabled class="form-control input" id="modificadoInput" />
              <label for="modificadoInput">Ultima vez modificado:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px">
            <div class="form-floating input-group">
              <input type="text" disabled class="form-control input" id="vinInput" />
              <label for="vinInput">VIN:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px">
            <div class="form-floating input-group">
              <input type="text" disabled class="form-control input" id="numchasisInput" />
              <label for="numchasisInput">N° Chasis:</label>
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
  document.addEventListener("DOMContentLoaded", function() {
    $('#tablaVehiculos').DataTable({
      ajax: {
        url: 'http://localhost/fix360/app/controllers/vehiculo.controller.php',
        data: {
          task: 'getVehiculoByCliente',
          idcliente: <?= $idcliente ?>
        },
        dataSrc: ''
      },
      columns: [{
          data: null,
          render: (d, t, r, m) => m.row + 1
        },
        {
          data: 'tipov',
          defaultContent: 'N/A'
        },
        {
          data: 'nombre',
          defaultContent: 'N/A'
        },
        {
          data: 'placa',
          defaultContent: 'N/A'
        },
        {
          data: 'color',
          defaultContent: 'N/A'
        },
        { // Columna 7: Opciones (botones: editar, ver detalle, y otro para ver más)
          data: null,
          render: function(data, type, row) { // Inicio de render de opciones
            return `
              <a href="editar-vehiculos.php?id=${row.idvehiculo}" class="btn btn-sm btn-warning" title="Editar">
                <i class="fa-solid fa-pen-to-square"></i>
              </a>
              <button class="btn btn-sm btn-info" title="Detalle" onclick="verDetalle('${row.modelo}', '${row.anio}', '${row.numserie}', '${row.tcombustible}','${row.modificado}','${row.vin}','${row.numchasis}')">
                <i class="fa-solid fa-clipboard-list"></i>
              </button>
                            <a href="historial-vehiculos-prueba.php?id=${row.idvehiculo}" class="btn btn-sm btn-outline-primary" title="Ver más">
                <i class="fa-solid fa-list"></i>
              </a>
            `;
          } // Cierra render de opciones
        }

      ],
      language: {
        lengthMenu: "Mostrar _MENU_ registros por página",
        zeroRecords: "No se encontraron vehículos",
        info: "Página _PAGE_ de _PAGES_",
        infoEmpty: "No hay vehículos asignados",
        search: "Buscar:",
        loadingRecords: "Cargando..."
      }
    });
  });

  function cleanField(value) {
    // si viene null, undefined, cadena 'null' o cadena vacía
    if (value == null || value === 'null' || value === '') {
      return 'No proporcionado';
    }
    return value;
  }

  function verDetalle(modelo, anio, serie, combustible, modificado, vin, numchasis) {
    document.querySelector("#modeloInput").value = cleanField(modelo);
    document.querySelector("#anioInput").value = cleanField(anio);
    document.querySelector("#serieInput").value = cleanField(serie);
    document.querySelector("#combustibleInput").value = cleanField(combustible);
    document.querySelector("#modificadoInput").value = cleanField(modificado);
    document.querySelector("#vinInput").value = cleanField(vin);
    document.querySelector("#numchasisInput").value = cleanField(numchasis);

    new bootstrap.Modal(document.getElementById("miModal")).show();
  }
</script>