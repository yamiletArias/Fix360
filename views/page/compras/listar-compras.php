<?php
const NAMEVIEW = "Lista de compras";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<head>
  
</head>
<div class="container mt-5">
  <div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
      <div class="btn-group" role="group" aria-label="Basic example">
        <button id="btnDia" type="button" class="btn btn-primary">
          Día
        </button>
        <button id="btnSemana" type="button" class="btn btn-primary">
          Semana
        </button>
        <button id="btnMes" type="button" class="btn btn-primary">
          Mes
        </button>
        <button type="button" class="btn btn-outline-danger">
          <i class="fa-solid fa-file-pdf"></i>
        </button>
      </div>
      <div class="row mt-3">
        <div class="col-12">
          <div class="col text-end"><a href="registrar-compras.php" class="btn btn-success" disabled>Registrar Compra</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="tableDia" class="table-container">
    <!--Tabla Dia-->
    <table id="miTabla" class="table table-striped display">
      <thead>
        <tr>
          <th >#</th>
          <th>T. Comprobante</th>
          <th>N° Comprobante</th>
          <th class="text-left">Proveedor</th>
          <th>Fecha Compra</th>
          <th>Precio</th>
          <!-- <th>Moneda</th> -->
          <th>Opciones</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>Factura</td>
          <td>0001-022</td>
          <td class="text-left">Repuestos S.A.C</td>
          <td>14/03/2025</td>
          <td>520.00</td>
          <!-- <th>Soles</th> -->
          <td>
            <button title="Editar" onclick="window.location.href='actualizar-compras.html'"
              class="btn btn-warning btn-sm">
              <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <button title="Eliminar" class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
              <i class="fa-solid fa-trash"></i>
            </button>
            <button title="Detalle" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
              data-bs-target="#miModal">
              <i class="fa-solid fa-circle-info"></i>
            </button>
          </td>
        </tr>
        
      </tbody>
    </table>
  </div>

</div>
</div>
</div>
<!--FIN VENTAS-->



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
        <h5 class="modal-title">Detalle de la Compra 0001-022</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Cliente: Jose Hernandez</p>
        <div class="table-container">
          <!-- Tabla Día -->
          <table id="miTabla" class="table table-striped display">
            <thead>
              <tr>
                <th>#</th>
                <th>Productos</th>
                <th>Precio</th>
                <th>Moneda</th>
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
                <td>0%</td>
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

<!--FIN VENTAS-->

<!-- endinject -->
<!-- Custom js for this page -->
<!-- End custom js for this page -->
</body>

</html>

<?php
require_once "../../partials/_footer.php";
?>