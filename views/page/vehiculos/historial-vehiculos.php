<?php
CONST NAMEVIEW = "Historial del vehiculo";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
      <div class="container-main">
        <div class="table-container">
          <table id="miTabla" class="table table-striped display">
            <thead>
              <tr>
                <th>#</th>
                <th>Mecanico</th>
                <th>Admin</th>
                <th>Cliente</th>
                <th>Propietario</th>
                <th>Kilometraje</th>
                <th>Ingreso Grua</th>
                <th>Fch. Ingreso</th>
                <th>Fch. Salida</th>
                <th>Observaciones</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Juan Torres</td>
                <td>Elena</td>
                <td>Estefano</td>
                <td>Jhon Francia</td>
                <td>5400</td>
                <td>
                  <button class="btn btn-success btn-sm">
                    <i class="fa-solid fa-check"></i>
                  </button>
                </td>
                <td>10/02/2025</td>
                <td>11/02/2025</td>
                <td>
                  <button onclick="window.location.href='observacion-vehiculos.html'" id="btnObservaciones"
                    data-id="data-123" class="btn btn-outline-dark btn-sm">
                    <i class="fa-solid fa-clipboard-list"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>2</td>
                <td>Jose Perez</td>
                <td>Elena</td>
                <td>Pedro Tasayco</td>
                <td>Taxi Driver S.A.C</td>
                <td>2460</td>
                <td>
                  <button class="btn btn-danger btn-sm">
                    <i class="fa-solid fa-xmark"></i>
                  </button>
                </td>
                <td>07/01/2025</td>
                <td>09/01/2025</td>
                <td>
                  <button onclick="window.location.href='observacion-vehiculos.html'" id="btnObservaciones"
                    data-id="data-123" class="btn btn-outline-dark btn-sm">
                    <i class="fa-solid fa-clipboard-list"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>3</td>
                <td>Rosario Navarrete</td>
                <td>Roberto</td>
                <td>Antony Mendoza</td>
                <td>Jose Hernandez</td>
                <td>1895</td>
                <td>
                  <button class="btn btn-success btn-sm" >
                    <i class="fa-solid fa-check"></i>
                  </button>
                </td>
                <td>08/10/2024</td>
                <td>08/11/2024</td>
                <td>
                  <button onclick="window.location.href='observacion-vehiculos.html'" id="btnObservaciones"
                    data-id="data-123" class="btn btn-outline-dark btn-sm">
                    <i class="fa-solid fa-clipboard-list"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Botón Finalizar alineado a la derecha -->
        <div>
          <button onclick="window.location.href='listar-vehiculos.html'" class="btn btn-secondary">
            Volver
          </button>
        </div>
      </div>
    </div>
  </div>
  
<?php

  require_once "../../partials/_footer.php";

?>

  <script>
    document
      .querySelector("#btnEliminar")
      .addEventListener("click", async function () {
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

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>


</body>

</html>