<?php

const NAMEVIEW = "Lista de Vehiculos";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";


// 1) Leer el id de cliente que llega por GET
$idcliente = isset($_GET['idcliente']) ? intval($_GET['idcliente']) : 0;
// 2) (Opcional) Obtener el nombre del cliente para el título
//    Puedes llamar a tu modelo o hacer una consulta directa aquí.
$nombreCliente = "";
if ($idcliente) {
  // Ejemplo rápido con tu controlador Cliente.controller.php:
  $info = file_get_contents("http://localhost/fix360/app/controllers/Cliente.controller.php?task=getClienteById&idcliente=$idcliente");
  $cli  = json_decode($info, true);
  $nombreCliente = $cli[0]['propietario'] ?? "Cliente #$idcliente";
}

?>

<div class="container-main">
  <div class="row">
    <div class="col-md-10">
      <div class="text-right">
        <h3>Vehículos a nombre de: <strong><?= htmlspecialchars($nombreCliente) ?></strong></h3>
      </div>
    </div>
    <div class="col-md-2">
      <div class="text-end">
        <button title="Registrar vehiculo" type="button" onclick="window.location.href='registrar-vehiculos.php'"
          class="btn btn-success ">
          Registrar
        </button>
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
    <a href="../clientes/listar-cliente.php" class="btn btn-secondary">Volver</a>
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
              <a href="editar-vehiculo.php?id=${row.idvehiculo}" class="btn btn-sm btn-outline-primary" title="Ver más">
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

  function verDetalle(modelo, anio, serie, combustible, modificado, vin, numchasis) {
    document.querySelector("#modeloInput").value = modelo || 'No proporcionado';
    document.querySelector("#anioInput").value = anio || 'No proporcionado';
    document.querySelector("#serieInput").value = serie || 'No proporcionado';
    document.querySelector("#combustibleInput").value = combustible || 'No proporcionado';
    document.querySelector("#modificadoInput").value = modificado || 'No proporcionado';
    document.querySelector("#vinInput").value = vin || 'No proporcionado';
    document.querySelector("#numchasisInput").value = numchasis || 'No proporcionado';
    let modal = new bootstrap.Modal(document.getElementById("miModal"));
    modal.show();
  }
</script>