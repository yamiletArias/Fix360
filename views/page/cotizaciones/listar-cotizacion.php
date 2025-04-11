<?php
const NAMEVIEW = "Lista de cotizaciones";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>


<div class="container-main">
  <div>
    <div class="text-end">
      <button type="button" onclick="window.location.href='registrar-cotizacion.php'" class="btn btn-success text-end">
        Registrar
      </button>
    </div>
    <div class="table-container mt-5">
      <table id="miTabla" class="table table-striped display">
        <thead>
          <tr>
            <th class="text-center">#</th>
            <th>Cliente</th>
            <th>Precio</th>
            <!-- <td>Moneda</td> -->
            <!-- <th>Descuento</th> -->
            <th class="text-center">vigencia</th>
            <th>Opciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center">1</td>
            <td class="text-left">Jesus Valerio</td>
            <td>520.00</td>
            <!-- <td>Soles</td> -->
            <!-- <td>0%</td> -->
            <td>20/03/2025</td>
            <td>
              <button title="Detalle" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#miModal">
                <i class="fa-solid fa-list"></i>
              </button>
              <button title="Editar" onclick="window.location.href='editar-cotizacion.php'"
                class="btn btn-warning btn-sm">
                <i class="fa-solid fa-pen-to-square"></i>
              </button>
              <button title="Generar PDF" onclick="window.location.href='../../../app/reports/reporteprueba.php'"
                class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-file-pdf"></i>
              </button>
              <button title="Eliminar" class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>
          <tr>
            <td class="text-center">2</td>
            <td class="text-left">William Tasayco</td>
            <td>300.00</td>
            <!-- <td>Dolares</td> -->
            <!-- <td>0%</td> -->
            <td>VENCIO</td>
            <td>
              <button title="Detalle" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#miModal">
                <i class="fa-solid fa-list"></i>
              </button>
              <button title="Editar" onclick="window.location.href='editar-cotizacion.php'"
                class="btn btn-warning btn-sm">
                <i class="fa-solid fa-pen-to-square"></i>
              </button>
              <button title="Generar PDF"
                onclick="window.location.href='../../../app/reports/content/reporteprueba.php'"
                class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-file-pdf"></i>
              </button>
              <button title="Eliminar" class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>
          <tr>
            <td class="text-center">3</td>
            <td class="text-left">Estefano Sanchez</td>
            <td>150.00</td>
            <!-- <td>Soles</td> -->
            <!-- <td>20%</td> -->
            <td>VENCIO</td>
            <td>
              <button title="Detalle" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#miModal">
                <i class="fa-solid fa-list"></i>
              </button>
              <button title="Editar" onclick="window.location.href='editar-cotizacion.php'"
                class="btn btn-warning btn-sm">
                <i class="fa-solid fa-pen-to-square"></i>
              </button>
              <button title="Generar PDF"
                onclick="window.location.href='../../../app/reports/content/reporteprueba.php'"
                class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-file-pdf"></i>
              </button>
              <button title="Eliminar" class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                <i class="fa-solid fa-trash"></i>
              </button>
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
  document
    .querySelector("#btnEliminar")
    .addEventListener("click", async function () {
      const id = this.getAttribute("data-id"); // ID del registro a eliminar (opcional)

      if (
        await ask("¿Estás seguro de eliminar este registro?", "Compras")
      ) {
        showToast("Registro eliminado correctamente", "SUCCESS");
        // Aquí podrías agregar la lógica para eliminar el registro
        console.log(`Eliminando registro con ID: 1`);
      } else {
        showToast("Operación cancelada", "WARNING");
      }
    });

  $(document).ready(function () {
    // Inicializar las tablas con DataTables
    var tableDia = $("#miTabla").DataTable();
    var tableSemana = $("#miTablaSemana").DataTable();
    var tableSemana = $("#miTablaMes").DataTable();

    // Función para alternar entre las tablas
    $("#btnDia").on("click", function () {
      // Mostrar la tabla Día y ocultar las otras
      $("#tableDia").show();
      $("#tableSemana").hide();
      $("#tableMes").hide();
    });

    $("#btnSemana").on("click", function () {
      // Mostrar la tabla Semana y ocultar las otras
      $("#tableSemana").show();
      $("#tableDia").hide();
      $("#tableMes").hide();
    });

    $("#btnMes").on("click", function () {
      // Mostrar la tabla Mes y ocultar las otras
      $("#tableMes").show();
      $("#tableDia").hide();
      $("#tableSemana").hide();
    });
  });
</script>

<!-- Modal -->
<div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle de la Cotizacion 001-002</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div>
          <div class="table-container">
            <!-- Tabla Día -->
            <table id="miTabla" class="table table-striped display">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Productos</th>
                  <th>Precio</th>
                  <td>Moneda</td>
                  <th>Descuento</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Filtro de aceite</td>
                  <td>120.00</td>
                  <td>Soles</td>
                  <td>0%</td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>Pastillas de freno</td>
                  <td>150.00</td>
                  <td>Soles</td>
                  <td>0%</td>
                </tr>
                <tr>
                  <td>3</td>
                  <td>Amortiguador delantero</td>
                  <td>250.00</td>
                  <td>Soles</td>
                  <td>20%</td>
                </tr>
              </tbody>
            </table>
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


  <!-- endinject -->
  <!-- Custom js for this page -->
  <!-- End custom js for this page -->
  </body>

  </html>