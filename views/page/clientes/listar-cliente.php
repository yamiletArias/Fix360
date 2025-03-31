<?php
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
      <div class="container-main">
        <div class="mb-3">
          <label class="form-label"><strong>Tipo de Cliente:</strong></label>
          <div class="form-group">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="tipoCliente" id="persona" checked
              onchange="actualizarTabla()">
              <label class="form-check-label" for="persona">Persona</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="tipoCliente" id="empresa" onchange="actualizarTabla()">
              <label class="form-check-label" for="empresa">Empresa</label>
            </div>
              <button style="margin-left: 1050px;" onclick="window.location.href='registrar-cliente.html'"
              class="btn btn-success">Registrar</button>
          </div>
        </div>
        <div class="table-container">
          <table id="miTabla" class="table table-striped display">
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Tipo Documento</th>
                <th>Nro. Documento</th>
                <th>Teléfono</th>
                <th>Opciones</th>

              </tr>
            </thead>
            <tbody id="tablaClientes">
              <!-- Aquí se insertarán los datos dinámicamente -->
            </tbody>
          </table>
        </div>


      </div>


    </div>
  </div>
  <!--FIN VENTAS-->

  <!-- plugins:js -->
  <?php
  require_once "../../partials/_footer.php";
  ?>

  <script>
    // Datos estáticos
    const personas = [
      { id: 1, nombre: "Jose Hernandez", tipodoc: "DNI", nrodoc: "24658791", telefono: "987654321" },
      { id: 2, nombre: "Josue Pilpe", tipodoc: "CDE", nrodoc: "785248321631", telefono: "912345678" }
    ];

    const empresas = [
      { id: 1, nombre: "Tech Solutions", tipodoc: "RUC", nrodoc: "20547896321", telefono: "987654320" },
      { id: 2, nombre: "Innova Corp", tipodoc: "RUC", nrodoc: "20987654321", telefono: "956789012" }
    ];

    // Función para que se actualize la tabla
    function actualizarTabla() {
      const tabla = document.getElementById("tablaClientes");
      tabla.innerHTML = ""; 

      const tipoSeleccionado = document.querySelector('input[name="tipoCliente"]:checked').id;
      const datos = tipoSeleccionado === "persona" ? personas : empresas;

      datos.forEach((item, index) => {
        const fila = `
              <tr>
                  <td>${index + 1}</td>
                  <td>${item.nombre}</td>
                  <td>${item.tipodoc}</td>
                  <td>${item.nrodoc}</td>
                  <td>${item.telefono}</td>
                  <td>
                    <button title="Detalle del Cliente" data-bs-toggle="modal" data-bs-target="#miModal"
                    class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-list"></i>
                  </button>
                    <button
                      onclick="window.location.href='editar-cliente.html'"
                      class="btn btn-warning btn-sm">
                      <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                      
                      
                    </td>
              </tr>
          `;
        tabla.innerHTML += fila;
      });
    }

    // Llenar la tabla al cargar la página
    actualizarTabla();
  </script>

  <div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title" id="miModalLabel">Detalle del cliente</h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="padding: 10px;">
          <div class="row">
            <div class="form-group" style="margin: 10px;">
              <div class="form-floating input-group ">
                <input type="text" disabled class="form-control" id="floatingInput">
                <label for="floatingInput">Correo:</label>
              </div>
            </div>
            <div class="form-group" style="margin: 10px;">
              <div class="form-floating input-group ">
                <input type="text" disabled class="form-control" id="floatingInput">
                <label for="floatingInput">Telefono alternativo:</label>
              </div>
            </div>
            <div class="form-group" style="margin: 10px;">
              <div class="form-floating input-group">
                <input type="text" disabled class="form-control" id="floatingInput">
                <label for="floatingInput">Direccion:</label>
              </div>
            </div>
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