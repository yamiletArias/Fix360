<?php

CONST NAMEVIEW = "Ordenes de Servicio";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<div class="container-main">
  <div class="header-group">
    <div class="form-group">
      <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-primary">Dia</button>
        <button type="button" class="btn btn-primary">Semana</button>
        <button type="button" class="btn btn-primary">Mes</button>
        <button type="button" class="btn btn-outline-danger btn-sm">
          <i class="fa-solid fa-file-pdf"></i>
        </button>

      </div>

    </div>

    <div>
      <button type="button" onclick="window.location.href='registrar-ordenes.html'" class="btn btn-success ">Registrar</button>
    </div>

  </div>
  <div class="table-container">
    <table id="miTabla" class="table table-striped display">
      <thead>
        <tr>
          <th class="text-center">#</th>
          <th>Mecanico</th>
          <th>Admin.</th>
          <th>Cliente</th>
          <th>Vehiculo</th>
          <th>Kilometraje</th>
          <th>Ingreso Grua</th>
          <th>Fch. Ingreso</th>
          <th>Fch. Salida</th>
          <th>Opciones</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="text-center">1</td>
          <td class="text-left">Juan torres</td>
          <td>Elena</td>
          <td class="text-left">Estefano Gutierrez</td>
          <td>H6H-980</td>
          <td>1400</td>
          <td>Si</td>
          <td>10/02/2025</td>
          <td>09/01/2025</td>
          <td>
            <button class="btn btn-warning btn-sm" onclick="window.location.href='editar-ordenes.html'">
              <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <Button class=" btn btn-outline-dark btn-sm" onclick="window.location.href='listar-observacion-orden.html'">
              <i class="fa-solid fa-clipboard-list"></i>
            </Button>
          </td>
        </tr>
        <tr>
          <td class="text-center">2</td>
          <td class="text-left">Jose Perez</td>
          <td>Elena</td>
          <td class="text-left">Pedro Tasayco</td>
          <td>JUN-987</td>
          <td>2460</td>
          <td>No</td>
          <td>07/01/2025</td>
          <td><button id="btnConfirmarSalida" data-id="10/10/2024" class="btn btn-success btn-sm">
              <i class="fa-solid fa-circle-check"></i>
            </button>
          </td>
          <td>
            <button class="btn btn-warning btn-sm" onclick="window.location.href='editar-ordenes.html'">
              <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <Button class=" btn btn-outline-dark btn-sm" onclick="window.location.href='listar-observacion-orden.html'">
              <i class="fa-solid fa-clipboard-list"></i>
            </Button>
          </td>
        </tr>
        <tr>
          <td class="text-center">3</td>
          <td class="text-left">Rosario Navarrete</td>
          <td>Elena</td>
          <td class="text-left">Antony Mendoza</td>
          <td>LO7-12X</td>
          <td>1895</td>
          <td>Si</td>
          <td>08/10/2024</td>
          <td>10/11/2024</td>
          <td>
            <button class="btn btn-warning btn-sm" onclick="window.location.href='editar-ordenes.html'">
              <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <Button class=" btn btn-outline-dark btn-sm" onclick="window.location.href='listar-observacion-orden.html'">
              <i class="fa-solid fa-clipboard-list"></i>
            </Button>
          </td>
        </tr>

      </tbody>

    </table>
  </div>


</div>

</div>


</div>


<?php

  require_once "../../partials/_footer.php";
  
 ?>

<script>
  document.querySelector("#btnEliminar").addEventListener("click", async function() {
    const id = this.getAttribute("data-id"); // ID del registro a eliminar (opcional)

    if (await ask("¿Estás seguro de eliminar este registro?", "Ventas")) {
      showToast("Registro eliminado correctamente", "SUCCESS");
      // Aquí podrías agregar la lógica para eliminar el registro
      console.log(`Eliminando registro con ID: 1`);
    } else {
      showToast("Operación cancelada", "WARNING");
    }
  });
</script>

<script>
  document.querySelector("#btnConfirmarSalida").addEventListener("click", async function() {
    const id = this.getAttribute("data-id"); // ID del registro a eliminar (opcional)

    if (await ask("¿Estás seguro de asignarle Fecha de Salida?", "Orden de servicio")) {
      showToast("Registro actualizado correctamente", "SUCCESS");
      // Aquí podrías agregar la lógica para eliminar el registro
      console.log(`Registrada salida de vehiculo con Placa : DCD-428`);
    } else {
      showToast("Operación cancelada", "WARNING");
    }
  });
</script>

<!-- Modal -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle de la Venta 002-0034</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Cliente: Jose Hernandez</p>
        <div class="table-container">

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


</body>

</html>