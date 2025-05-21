<?php

const NAMEVIEW = "Lista de clientes";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
<div class="container-main">
  <div class="mb-3">
    <label class="form-label"><strong>Tipo de Cliente:</strong></label>
    <div class="row">
      <div class="form-group col-md-10">

        <div class="form-check form-check-inline" style="margin-left: 20px;">
          <input class="form-check-input text-start" type="radio" name="tipoCliente" id="persona" checked
            onchange="mostrarTabla('persona')">
          <label class="form-check-label text-start" for="persona" style="margin: 0px;">Persona</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input text-start" type="radio" name="tipoCliente" id="empresa"
            onchange="mostrarTabla('empresa')">
          <label class="form-check-label text-start" for="empresa" style="margin: 0px;">Empresa</label>
        </div>
      </div>
      <div class="text-end col-md-2">
        <button onclick="window.location.href='registrar-cliente.php'"
          class=" btn btn-success text-end">Registrar</button>
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
              <input type="text" disabled class="form-control input" id="RUCPersonaInput">
              <label for="RUCPersonaInput">RUC:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px;">
            <div class="form-floating input-group ">
              <input type="text" disabled class="form-control input" id="CorreoPersonaInput">
              <label for="CorreoPersonaInput">Correo:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px;">
            <div class="form-floating input-group ">
              <input type="text" disabled class="form-control input" id="TelAlternativoPersonaInput">
              <label for="TelAlternativoPersonaInput">Telefono alternativo:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px;">
            <div class="form-floating input-group">
              <input type="text" disabled class="form-control input" id="DireccionPersonaInput">
              <label for="DireccionPersonaInput">Direccion:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px;">
            <div class="form-floating input-group">
              <input type="text" disabled class="form-control input" id="ModificacionPersonaInput">
              <label for="ModificacionPersonaInput">Ultima modificacion:</label>
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
              <input type="text" disabled class="form-control input" id="RazonSocialEmpresaInput">
              <label for="RazonSocialEmpresaInput">Razon Social:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px;">
            <div class="form-floating input-group ">
              <input type="text" disabled class="form-control input" id="CorreoEmpresaInput">
              <label for="CorreoEmpresaInput">Correo:</label>
            </div>
          </div>
          <div class="form-group" style="margin: 10px;">
            <div class="form-floating input-group">
              <input type="text" disabled class="form-control input" id="ModificacionEmpresaInput">
              <label for="ModificacionEmpresaInput">Ultima modificacion:</label>
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

<div class="modal fade" id="ModalAsignarVehiculo" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- Modal grande si lo requieres -->
    <div class="modal-content">
      <!-- Encabezado -->
      <div class="modal-header">
        <h2 class="modal-title" id="miModalLabel">Asignar Vehiculo</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Cuerpo -->
      <div class="modal-body" style="margin-top: 0px;">
        <form action="" id="FormVehiculo">
          <div class="card border">
            <div class="card-body">
              <div class="row">

                <div class="col-md-4 ">
                  <div class="form-floating">
                    <select class="form-select input" id="marcav" name="marcav" style="color: black;" required>
                      <option value="">Seleccione una opcion</option>

                    </select>
                    <label for="marcav"><strong> Marca del vehiculo:</strong></label>
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <div class="form-floating">
                    <select class="form-select" id="tipov" name="tipov" style="color: black;" required>
                      <option value="">Seleccione una opcion</option>
                    </select>
                    <label for="tipov"><strong>Tipo de vehiculo:</strong></label>
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <div class="form-floating">
                    <select class="form-select" id="modelo" name="modelo" style="color: black;" required>
                      <option value="">Seleccione una opcion</option>
                    </select>
                    <label for="modelo"><strong> Modelo del vehiculo:</strong></label>
                  </div>
                </div>

                <div class="col-md-2 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control input" id="fplaca" placeholder="placadeejemplo" minlength="6"
                      required maxlength="6" />
                    <label for="fplaca"><strong>Placa</strong></label>
                  </div>
                </div>

                <div class="col-md-2 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control input" id="fanio" placeholder="anio" minlength="4"
                      maxlength="4" required />
                    <label for="fanio"><strong> Año</strong></label>
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control input" id="fnumserie" minlength="17" maxlength="17" placeholder="numerodeserie" />
                    <label for="fnumserie">N° de serie</label>
                  </div>
                </div>

                <div class="col-md-2 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control input" id="fcolor" placeholder="#e0aef6" minlength="3" maxlength="20" />
                    <label for="fcolor"><strong>Color</strong></label>
                  </div>
                </div>

                <div class="col-md-2 mb-3">
                  <div class="form-floating">
                    <select class="form-select" id="ftcombustible" style="color: black;">
                    </select>
                    <label for="ftcombustible"><strong> Tipo de combustible:</strong></label>
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control input" id="vin" placeholder="vin" minlength="17" maxlength="17" />
                    <label for="vin">VIN</label>
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control input" id="numchasis" placeholder="numchasis" minlength="17" maxlength="17" />
                    <label for="numchasis">N° Chasis</label>
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <div class="form-floating input-group mb-3">
                    <input type="text" disabled class="form-control input" id="floatingInput" placeholder="propietario"
                      value="" />
                    <label for="floatingInput"><strong>Propietario</strong></label>
                    <input type="hidden" id="hiddenIdCliente" />
                  </div>
                </div>

              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Pie del Modal -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btnRegistrarVehiculo">Guardar</button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="ModalVehiculos" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="miModalLabel">Vehículos a Nombre de <span id="nombreCliente"></span></h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="padding: 10px;">
        <!-- Aquí vendrá la tabla con los vehículos -->
        <div class="table-responsive">
          <table id="tabla-vehiculos" class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Tipo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Placa</th>
                <th>Color</th>
              </tr>
            </thead>
            <tbody>
              <!-- Se llenará dinámicamente -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


</div>
</div>

<!-- plugins:js -->
<?php
require_once "../../partials/_footer.php";
?>


<script>
  function cleanField(v) {
    return (v == null || v === '' || v === 'null') ?
      'No proporcionado' :
      v;
  }
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
        },
        {
          data: "telprincipal",
          render: data => cleanField(data)
        },
        {
          data: null,
          render: (data, type, row) => {
            const nombreCliente = `${row.nombres || ''} ${row.apellidos || ''}`.trim();

            return `
                    <a class="btn btn-warning btn-sm" title="Editar datos" href="editar-cliente.php?idpersona=${row.idpersona}">
                    <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <button class="btn btn-info btn-sm" title="Ver detalles" onclick="verDetallePersona('${row.numruc}', '${row.direccion}', '${row.correo}', '${row.telalternativo}','${row.modificado}')">
                      <i class='fa-solid fa-clipboard-list'></i>
                    </button>
                    <button
                      class="btn btn-sm btn-outline-dark"
                      data-bs-toggle="modal"
                      data-bs-target="#ModalAsignarVehiculo"
                      data-idcliente="${row.idcliente}"
                      data-nombrecliente="${nombreCliente}"
                      title="Asignar vehículo"
                    >
                    <i class="fa-solid fa-car"></i>
                    </button>
                     <a class="btn btn-sm btn-confirmar btn-outline-dark" title="Vehiculos a nombre de este cliente"
                      href="../vehiculos/vehiculo-cliente.php?idcliente=${row.idcliente}">
                     <i class="fa-solid fa-id-card"></i>
                    </a>
                    `;
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
      } // Cierra language
    }); // Cierra DataTable inicialización
  } // Cierra cargarTablaPersona()

  // Función auxiliar para verificar valores nulos o vacíos
  function noProporcionado(valor) {
    return (!valor || String(valor).toLowerCase() === "null") ? "No proporcionado" : valor;
  }

  function cleanField(value) {
    // si viene null, undefined, cadena 'null' o cadena vacía
    if (value == null || value === 'null' || value === '') {
      return 'No proporcionado';
    }
    return value;
  }

  function verDetallePersona(numruc, direccion, correo, telalternativo, modificado) {
    document.querySelector("#RUCPersonaInput").value = cleanField(numruc);
    document.querySelector("#DireccionPersonaInput").value = cleanField(direccion);
    document.querySelector("#CorreoPersonaInput").value = cleanField(correo);
    document.querySelector("#TelAlternativoPersonaInput").value = cleanField(telalternativo)
    document.querySelector("#ModificacionPersonaInput").value = cleanField(modificado);

    // Mostrar el modal de Bootstrap
    let modal = new bootstrap.Modal(document.getElementById("miModalPersona"));
    modal.show();
  }

  function verDetalleEmpresa(razonsocial, correo, modificado) {
    document.querySelector("#RazonSocialEmpresaInput").value = cleanField(razonsocial);
    document.querySelector("#CorreoEmpresaInput").value = cleanField(correo);
    document.querySelector("#ModificacionEmpresaInput").value = cleanField(modificado);

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
            <a class="btn btn-warning btn-sm" title="Ver detalles" href="editar-cliente.php?idempresa=${row.idempresa}">
                    <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <button class="btn btn-info btn-sm" title="Ver detalles" onclick="verDetalleEmpresa('${row.razonsocial}','${row.correo}','${row.modificado}')">
                      <i class='fa-solid fa-clipboard-list'></i>
                    </button>
                    <button
        class="btn btn-sm btn-outline-dark"
        data-bs-toggle="modal"
        data-bs-target="#ModalAsignarVehiculo"
        data-idcliente="${row.idcliente}"
        data-nombrecliente="${row.nomcomercial}"
        title="Asignar vehículo"
      >
        <i class="fa-solid fa-car"></i>
      </button>

      <a class="btn btn-sm btn-confirmar btn-outline-dark" title="Vehiculos a nombre de este cliente"
                      href="../vehiculos/vehiculo-cliente.php?idcliente=${row.idcliente}">
                     <i class="fa-solid fa-id-card"></i>
                    </a>
                    `;
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

<!--script src="<?= SERVERURL ?>views/page/clientes/js/asignar-vehiculo.js"></!--script-->

<script
  src="<?= rtrim(SERVERURL, '/') ?>/views/page/clientes/js/asignar-vehiculo.js?v=<?= time() ?>"
  defer
></script>
</body>

</html>