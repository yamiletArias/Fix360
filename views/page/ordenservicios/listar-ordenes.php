<?php

const NAMEVIEW = "Ordenes de Servicio";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>



<div class="container-main">
  <div class="card border">
  <div class="card-header">
  <div class="row align-items-center">
    <div class="col-md-3 mb-2 mb-md-0">
      <!-- Botones de filtro: Día, Semana, Mes -->
      <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-primary text-white">Semana</button>
        <button type="button" class="btn btn-primary text-white">Mes</button>
        <button type="button" class="btn btn-danger text-white">
          <i class="fa-solid fa-file-pdf"></i>
        </button>
      </div>
    </div>
    <div class="col-md-6"></div>
    <div class="col-md-3 text-md-end">
      <!-- Input para la fecha y botón -->
      <div class="input-group">
        <input type="date" class="form-control" aria-label="Fecha" aria-describedby="button-addon2" id="Fecha">
        <a href="registrar-ordenes2.php" class="btn btn-success text-center" type="button" id="button-addon2" >Registrar</a>
      </div>
    </div>
  </div>
</div>

    <div class="card-body">
      <div class="table-container">
        <table id="miTabla" class="table table-striped display">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th>Propietario</th>
              <th>Cliente</th>
              <th>Fch. Ingreso</th>
              <th>Fch. Salida</th>
              <th>Placa</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Carlos Gonzales</td>
              <td>Jose Hernandez</td>
              <td>21/02/2025 10:00</td>
              <td>21/07/2025 10:00</td>
              <td>G4D1CS</td>
              <td>
                <button class="btn btn-sm btn-danger">
                  <i class="fa-solid fa-trash"></i>
                </button>
                <button class="btn btn-sm btn-info">
                  <i class="fa-solid fa-clipboard-list"></i>
                </button>
                <button class="btn btn-sm btn-primary">
                  <i class="fa-solid fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-dark">
                  <i class="fa-solid fa-calendar-days"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td>1</td>
              <td>Carlos Gonzales</td>
              <td>Jose Hernandez</td>
              <td>21/02/2025 10:00</td>
              <td>21/07/2025 10:00</td>
              <td>G4D1CS</td>
              <td>
                <button class="btn btn-sm btn-danger">
                  <i class="fa-solid fa-trash"></i>
                </button>
                <button class="btn btn-sm btn-info">
                  <i class="fa-solid fa-clipboard-list"></i>
                </button>
                <button class="btn btn-sm btn-primary">
                  <i class="fa-solid fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-dark">
                  <i class="fa-solid fa-calendar-days"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td>1</td>
              <td>Carlos Gonzales</td>
              <td>Jose Hernandez</td>
              <td>21/02/2025 10:00</td>
              <td>21/07/2025 10:00</td>
              <td>G4D1CS</td>
              <td>
                <button class="btn btn-sm btn-danger">
                  <i class="fa-solid fa-trash"></i>
                </button>
                <button class="btn btn-sm btn-info">
                  <i class="fa-solid fa-clipboard-list"></i>
                </button>
                <button class="btn btn-sm btn-primary">
                  <i class="fa-solid fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-dark">
                  <i class="fa-solid fa-calendar-days"></i>
                </button>
              </td>
            </tr>




          </tbody>

        </table>
      </div>
    </div>


  </div>
</div>
</div>


</div>


<?php

require_once "../../partials/_footer.php";

?>

<script>
   window.addEventListener('DOMContentLoaded', () => {
    const hoy = new Date().toISOString().split('T')[0]; // Formato YYYY-MM-DD
    document.getElementById('Fecha').value = hoy;
  });
</script>

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