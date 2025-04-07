<?php

const NAMEVIEW = "Lista de clientes";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<div class="container-main">
  <div class="mb-3" style="margin-left: 20px;">
    <label class="form-label"><strong>Tipo de Cliente:</strong></label>
    <div class="row">
      <div class="form-group col-md-10">

        <div class="form-check form-check-inline">
          <input class="form-check-input text-start" type="radio" name="tipoCliente" id="persona" checked onchange="mostrarTabla('persona')">
          <label class="form-check-label text-start" for="persona">Persona</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input text-start" type="radio" name="tipoCliente" id="empresa" onchange="mostrarTabla('empresa')">
          <label class="form-check-label text-start" for="empresa">Empresa</label>
        </div>
      </div>
      <div class="text-end col-md-2">
        <button onclick="window.location.href='registrar-cliente.php'" class=" btn btn-success text-end">Registrar</button>
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
  function cargarTablaPersona() { // Inicio de cargarTablaPersona()
    // Si ya está inicializado, se destruye la instancia anterior
    if ($.fn.DataTable.isDataTable("#tablaPersona")) {
      $("#tablaPersona").DataTable().destroy();
    } // Cierra if

    // Inicializa DataTable con configuración mínima y renderizado de valores vacíos
    $("#tablaPersona").DataTable({ // Inicio de configuración DataTable para persona
      ajax: {
        url: "http://localhost/fix360/app/controllers/Cliente.controller.php?tipo=persona",
        dataSrc: ""
      }, // Cierra ajax
      columns: [{
          data: null,
          render: (data, type, row, meta) => meta.row + 1 // Número de fila  
        }, // Cierra columna 1
        {
          data: "nombres",
          render: (data, type, row) => {
            // Si nombres o apellidos están vacíos, se muestra "No proporcionado"
            let nombre = data ? data : "No proporcionado";
            let apellido = row.apellidos ? row.apellidos : "No proporcionado";
            return nombre + " " + apellido;
          }
        }, // Cierra columna 2
        {
          data: "tipodoc",
        }, // Cierra columna 3
        {
          data: "numdoc",
        }, // Cierra columna 4
        {
          data: "telprincipal",
          defaultContent: "No proporcionado" // Si telprincipal es vacío  
        }, // Cierra columna 5
        {
          data: null,
          render: (data, type, row) => {
            // Botón de detalle, puedes personalizarlo si lo deseas  
            return `
                    <a class="btn btn-warning btn-sm" title="Ver detalles" href="editar-cliente.php?id=${row.idcliente}">
                    <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <button class="btn btn-info btn-sm" title="Ver detalles" onclick="verDetallePersona(${row.idcliente})">
                      <i class='fa-solid fa-clipboard-list'></i>
                    </button>
                    
                    `      
                    ;
          }
        } // Cierra columna 6
      ], // Cierra columns
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
      } // Cierra language
    }); // Cierra DataTable inicialización
  } // Cierra cargarTablaPersona()

  function verDetallePersona(modelo, anio, serie, combustible) {
    document.querySelector("#modeloInput").value = modelo || 'No proporcionado';
    document.querySelector("#anioInput").value = anio || 'No proporcionado';
    document.querySelector("#serieInput").value = serie || 'No proporcionado';
    document.querySelector("#combustibleInput").value = combustible || 'No proporcionado';

    // Mostrar el modal de Bootstrap
    let modal = new bootstrap.Modal(document.getElementById("miModalPersona"));
    modal.show();
  }

  function verDetalleEmpresa(modelo, anio, serie, combustible) {
    document.querySelector("#modeloInput").value = modelo || 'No proporcionado';
    document.querySelector("#anioInput").value = anio || 'No proporcionado';
    document.querySelector("#serieInput").value = serie || 'No proporcionado';
    document.querySelector("#combustibleInput").value = combustible || 'No proporcionado';

    // Mostrar el modal de Bootstrap
    let modal = new bootstrap.Modal(document.getElementById("miModalEmpresa"));
    modal.show();
  }

  // Función para inicializar el DataTable para clientes Empresa de forma similar
  function cargarTablaEmpresa() { // Inicio de cargarTablaEmpresa()
    if ($.fn.DataTable.isDataTable("#tablaEmpresa")) {
      $("#tablaEmpresa").DataTable().destroy();
    } // Cierra if

    $("#tablaEmpresa").DataTable({ // Inicio de configuración DataTable para empresa
      ajax: {
        url: "http://localhost/fix360/app/controllers/Cliente.controller.php?tipo=empresa",
        dataSrc: ""
      }, // Cierra ajax
      columns: [{
          data: null,
          render: (data, type, row, meta) => meta.row + 1
        }, // Cierra columna 1
        {
          data: "nomcomercial",
        }, // Cierra columna 2
        {
          data: "ruc",
        }, // Cierra columna 3
        {
          data: "telefono",
          defaultContent: "No proporcionado"
        }, // Cierra columna 4
        {
          data: null,
          render: (data, type, row) => {
            return `
            <a class="btn btn-warning btn-sm" title="Ver detalles" href="editar-cliente.php?id=${row.idcliente}">
                    <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <button class="btn btn-info btn-sm" title="Ver detalles" onclick="verDetalleEmpresa(${row.idcliente})">
                      <i class='fa-solid fa-clipboard-list'></i>
                    </button>`;
          }
        } // Cierra columna 5
      ], // Cierra columns
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
      } // Cierra language
    }); // Cierra DataTable inicialización
  } // Cierra cargarTablaEmpresa()


  // Función para mostrar/ocultar la tabla correcta según el tipo seleccionado
  function mostrarTabla(tipo) { // Inicio de mostrarTabla()
    if (tipo === "persona") {
      document.getElementById("tablaEmpresaContainer").style.display = "none";
      document.getElementById("tablaPersonaContainer").style.display = "block";
      cargarTablaPersona();
    } else if (tipo === "empresa") {
      document.getElementById("tablaPersonaContainer").style.display = "none";
      document.getElementById("tablaEmpresaContainer").style.display = "block";
      cargarTablaEmpresa();
    }
  } // Cierra mostrarTabla()


  // Inicializar la vista al cargar la página: se muestra la tabla de personas por defecto
  document.addEventListener("DOMContentLoaded", function() { // Inicio de DOMContentLoaded para inicialización
    mostrarTabla("persona");
  }); // Cierra DOMContentLoaded


  // Función para ver detalles (ejemplo de uso: podrías abrir un modal para mostrar más información)
  function verDetalle(idcliente) { // Inicio de verDetalle()
    console.log("Ver detalles del cliente con ID:", idcliente);
    // Aquí podrías hacer una solicitud AJAX para obtener todos los datos y mostrarlos en un modal  
  } // Cierra verDetalle()
</script>


<div class="modal fade" id="miModalPersona" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
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


<div class="modal fade" id="miModalEmpresa" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
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