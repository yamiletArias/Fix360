<?php

const NAMEVIEW = "Registro de Ventas";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>

  <style>
    .container-ventas {
      background: transparent;
      padding: 30px;
      border-radius: 8px;
      box-shadow: none;
      width: 2900px;
      /* Aumenta el tamaño */
      min-height: 700px;
      /* Aumenta la altura */
      margin-left: 50px;
      /* Lo mueve más a la derecha */
      margin-top: 50px;
    }

    .form-group {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      margin-top: 25px;
      gap: 15px;
    }

    .form-group label {
      margin-right: 10px;
    }

    input,
    select,
    button {
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    input[type="text"],
    select {
      flex: 1;
    }

    input[type="date"] {
      width: 160px;
    }

    .small-input {
      width: 130px;
    }

    .medium-input {
      width: 200px;
    }

    .table-container {
      margin-top: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    table,
    th,
    td {
      /* border: 1px solid #ccc; */
      text-align: center;
      padding: 10px;
    }

    .btn-container {
      display: flex;
      justify-content: flex-end;
      margin-top: 40px;
    }

    .btn-finalizar {
      background: green;
      color: white;
      padding: 12px;
      border: none;
      cursor: pointer;
      font-size: 16px;
    }

    .header-group {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .right-group {
      display: flex;
      gap: 10px;
    }

    tfoot th {
      border: none;
      /* Quita los bordes de todos los <th> en el footer */
      background: transparent;
      /* Fondo transparente */
    }

    tfoot th.total-label,
    tfoot th.total-value {
      border-top: 2px solid black;
      /* Agrega borde solo en las celdas de total */
      border-bottom: 2px solid black;
      font-weight: bold;

      /* Fondo blanco para destacar */
    }

    #miTabla td {
      padding: 2px !important;
      /* Reduce el espacio interno solo en los td */
      font-size: 15px;
      /* Reduce el tamaño de fuente solo en los td */
      line-height: 1 !important;
      /* Reduce la altura de las filas */
      white-space: nowrap;
      /* Evita saltos de línea en las celdas */
    }

    #miTabla td:first-child,
    #miTabla th:first-child {
      text-align: center;
    }

    #miTablaSemana td {
      padding: 2px !important;
      /* Reduce el espacio interno solo en los td */
      font-size: 15px;
      /* Reduce el tamaño de fuente solo en los td */
      line-height: 1 !important;
      /* Reduce la altura de las filas */
      white-space: nowrap;
      /* Evita saltos de línea en las celdas */
    }

    #miTablaSemana td:first-child,
    #miTablaSemana th:first-child {
      text-align: center;
    }

    #miTablaMes td {
      padding: 2px !important;
      /* Reduce el espacio interno solo en los td */
      font-size: 15px;
      /* Reduce el tamaño de fuente solo en los td */
      line-height: 1 !important;
      /* Reduce la altura de las filas */
      white-space: nowrap;
      /* Evita saltos de línea en las celdas */
    }

    #miTablaMes td:first-child,
    #miTablaMes th:first-child {
      text-align: center;
    }
  </style>
</head>

<body>
  <!-- VENTAS -->
  <div class="container-ventas">
    <div class="header-group">
      <div class="form-group">
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
        </div>
        <button type="button" class="btn-outline-danger fa-solid fa-file-pdf"></button>
      </div>
      <div>
        <button type="button" onclick="window.location.href='registrar-ventas.html'" class="btn btn-success">
          Registrar
        </button>
      </div>
    </div>

    <div id="tableDia" class="table-container">
      <!-- Tabla Día -->
      <table id="miTabla" class="table table-striped display">
        <thead>
          <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>T. Comprobante</th>
            <th>N° Comprobante</th>
            <th>Fecha Hora</th>
            <!-- <th>Importe</th> -->
            <th>Opciones</th>
          </tr>
        </thead>
        <tbody>
          <!-- contenido dinamico -->
        </tbody>
      </table>
    </div>
  </div>
  </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="miModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalle de la Venta 001-0022</h5>
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
</body>

</html>
<script>
  document.addEventListener("DOMContentLoaded", function () {

    function obtenerVentas() {
      fetch(`/Fix360/app/controllers/Venta.controller.php`, {
        method: 'GET'
      })
        .then(response => response.json())
        .then(data => {
          const tabla = document.querySelector("#miTabla tbody");
          tabla.innerHTML = '';

          if (data.length === 0) {
            tabla.innerHTML = `<tr><td colspan="6">No hay ventas registradas.</td></tr>`;
          } else {
            data.forEach(element => {
              tabla.innerHTML += `
              <tr data-id="${element.id}">
                <td>${element.id}</td>
                <td>${element.cliente}</td>
                <td>${element.tipocom}</td>
                <td>${element.numcom}</td>
                <td>${element.fechahora}</td>
                <td>
                  <button title="Editar" onclick="window.location.href='editar-ventas.html'" class="btn btn-warning btn-sm">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <button title="Eliminar" class="btn btn-danger btn-sm btnEliminar" data-id="${element.id}">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                  <button title="Detalle" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#miModal">
                    <i class="fa-solid fa-circle-info"></i>
                  </button>
                </td>
              </tr>
            `;
            });
          }

          if (!$.fn.dataTable.isDataTable('#miTabla')) {
            $('#miTabla').DataTable();
          }
        })
        .catch(error => {
          console.error("Error al obtener los datos:", error);
        });
    }

    // Delegar el evento de clic a la tabla para los botones "Eliminar"
    document.querySelector("#miTabla tbody").addEventListener("click", async function (event) {
      if (event.target && event.target.matches("button.btnEliminar")) {
        const id = event.target.getAttribute("data-id"); // Obtener el ID del registro a eliminar

        // Usar la función ask para confirmar la eliminación
        if (await ask("¿Estás seguro de eliminar este registro?", "Venta")) {
          showToast("Registro eliminado correctamente", "SUCCESS");
          console.log(`Eliminando registro con ID: ${id}`);
        } else {
          showToast("Operación cancelada", "WARNING");
        }
      }
    });

    // Inicializar las tablas con DataTables y manejar las vistas de diferentes tablas
    $(document).ready(function () {
      var tableDia = $("#miTabla").DataTable();
      var tableSemana = $("#miTablaSemana").DataTable();
      var tableMes = $("#miTablaMes").DataTable();

      // Función para alternar entre las tablas
      $("#btnDia").on("click", function () {
        $("#tableDia").show();
        $("#tableSemana").hide();
        $("#tableMes").hide();
      });

      $("#btnSemana").on("click", function () {
        $("#tableSemana").show();
        $("#tableDia").hide();
        $("#tableMes").hide();
      });

      $("#btnMes").on("click", function () {
        $("#tableMes").show();
        $("#tableDia").hide();
        $("#tableSemana").hide();
      });
    });

    // Llamar la función para obtener las ventas cuando la página se cargue
    obtenerVentas();
  });
</script>

<?php

require_once "../../partials/_footer.php";

?>