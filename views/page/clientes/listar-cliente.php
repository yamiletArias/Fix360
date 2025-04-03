<?php

const NAMEVIEW = "Lista de clientes";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<div class="container-main">
  <div class="mb-3" style="margin-left: 20px;">
    <label class="form-label"><strong>Tipo de Cliente:</strong></label>
    <div class="form-group">
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="tipoCliente" id="persona" checked onchange="mostrarTabla('persona')">
        <label class="form-check-label" for="persona">Persona</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="tipoCliente" id="empresa" onchange="mostrarTabla('empresa')">
        <label class="form-check-label" for="empresa">Empresa</label>
      </div>
      <div style="margin-left:1020px;">
        <button onclick="window.location.href='registrar-cliente.php'" class="btn btn-success">Registrar</button>
      </div>
    </div>
  </div>

  <!-- Contenedor para clientes persona -->
  <div id="tablaPersonaContainer" style="display: none;">
    <div class="table-container">
      <table id="tablaPersona" class="table table-striped display">
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
        <tbody>
          <!-- Se llenará vía DataTable -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- Contenedor para clientes empresa -->
  <div id="tablaEmpresaContainer" style="display: none;">
    <div class="table-container">
      <table id="tablaEmpresa" class="table table-striped display">
        <thead>
          <tr>
            <th>#</th>
            <th>Nombre Comercial</th>
            <th>RUC</th>
            <th>Teléfono</th>
            <th>Opciones</th>
          </tr>
        </thead>
        <tbody>
          <!-- Se llenará vía DataTable -->
        </tbody>
      </table>
    </div>
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
  // Función para inicializar el DataTable para clientes Persona
function cargarTablaPersona() {
  if ($.fn.DataTable.isDataTable("#tablaPersona")) {
    $("#tablaPersona").DataTable().destroy();
  }
  $("#tablaPersona").DataTable({
    ajax: {
      url: "http://localhost/fix360/app/controllers/Cliente.controller.php?tipo=persona",
      dataSrc: ""
    },
    columns: [
      { data: null, render: (data, type, row, meta) => meta.row + 1 },
      { data: "nombres", render: (data, type, row) => data + " " + row.apellidos },
      { data: "tipodoc" },
      { data: "numdoc" },
      { data: "telprincipal" },
      { data: null, render: (data, type, row) => {
          return `<button class="btn btn-primary btn-sm" title="Ver detalles" onclick="verDetalle(${row.idcliente})">
                    <i class="fa-solid fa-list"></i>
                  </button>`;
        }
      }
    ],
    language: {
      "lengthMenu": "Mostrar _MENU_ registros por página",
      "zeroRecords": "No se encontraron resultados",
      "info": "Mostrando página _PAGE_ de _PAGES_",
      "infoEmpty": "No hay registros disponibles",
      "infoFiltered": "(filtrado de _MAX_ registros totales)",
      "search": "Buscar:",
      "loadingRecords": "Cargando...",
      "processing": "Procesando...",
      "emptyTable": "No hay datos disponibles en la tabla"
    }
  });
}

// Función para inicializar el DataTable para clientes Empresa
function cargarTablaEmpresa() {
  if ($.fn.DataTable.isDataTable("#tablaEmpresa")) {
    $("#tablaEmpresa").DataTable().destroy();
  }
  $("#tablaEmpresa").DataTable({
    ajax: {
      url: "http://localhost/fix360/app/controllers/Cliente.controller.php?tipo=empresa",
      dataSrc: ""
    },
    columns: [
      { data: null, render: (data, type, row, meta) => meta.row + 1 },
      { data: "nomcomercial" },
      { data: "ruc" },
      { data: "telefono" },
      { data: null, render: (data, type, row) => {
          return `<button class="btn btn-primary btn-sm" title="Ver detalles" onclick="verDetalle(${row.idcliente})">
                    <i class="fa-solid fa-list"></i>
                  </button>`;
        }
      }
    ],
    language: {
      "lengthMenu": "Mostrar _MENU_ registros por página",
      "zeroRecords": "No se encontraron resultados",
      "info": "Mostrando página _PAGE_ de _PAGES_",
      "infoEmpty": "No hay registros disponibles",
      "infoFiltered": "(filtrado de _MAX_ registros totales)",
      "search": "Buscar:",
      "loadingRecords": "Cargando...",
      "processing": "Procesando...",
      "emptyTable": "No hay datos disponibles en la tabla"
    }
  });
}

// Función para mostrar/ocultar la tabla correcta según el tipo seleccionado
function mostrarTabla(tipo) {
  if (tipo === "persona") {
    // Ocultar la tabla de empresa y mostrar la de persona
    document.getElementById("tablaEmpresaContainer").style.display = "none";
    document.getElementById("tablaPersonaContainer").style.display = "block";
    cargarTablaPersona();
  } else if (tipo === "empresa") {
    // Ocultar la tabla de persona y mostrar la de empresa
    document.getElementById("tablaPersonaContainer").style.display = "none";
    document.getElementById("tablaEmpresaContainer").style.display = "block";
    cargarTablaEmpresa();
  }
}

// Inicializar la vista al cargar la página: por defecto mostramos la tabla de personas
document.addEventListener("DOMContentLoaded", function () {
  mostrarTabla("persona");
});

function verDetalle(idcliente) {
  console.log("Ver detalles del cliente con ID:", idcliente);
  // Aquí podrías hacer una solicitud AJAX para obtener todos los datos y mostrarlos en un modal
}

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