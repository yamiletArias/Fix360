<?php

const NAMEVIEW = "Registro de Órdenes de servicio";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<div class="container-main">
    <div class="card border">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-floating">
                        <select class="form-select" id="subcategoria" name="subcategoria" style="color: black;"
                            required>
                            <option selected>Eliga un tipo de servicio</option>

                        </select>
                        <label for="subcategoria">Tipo de Servicio:</label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-floating">
                        <select class="form-select" id="servicio" name="servicio" style="color:black;">
                            <option selected>Eliga un servicio</option>
                        </select>
                        <label for="servicio">Servicio:</label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-floating">
                        <select class="form-select" id="mecanico" name="mecanico" style="color:black;">
                            <option selected>Eliga un mecánico</option>
                        </select>
                        <label for="mecanico">Mecánico:</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating input-group mb-3">
                        <input type="text" disabled class="form-control input" id="propietario"
                            placeholder="Propietario" />
                        <label for="propietario">Propietario</label>
                        <input type="hidden" id="hiddenIdCliente" />
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                            data-bs-target="#miModal">
                            ...
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating input-group mb-3">
                        <input type="text" disabled class="form-control" id="cliente" placeholder="Cliente">
                        <label for="cliente">Cliente</label>
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                            data-bs-target="#miModal">...</button>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="vehiculo" name="vehiculo" style="color:black;">
                            <option selected>Eliga un vehículo</option>
                        </select>
                        <label for="vehiculo">Vehículo:</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="number" step="0.1" class="form-control input" id="kilometraje" placeholder="201">
                        <label for="kilometraje">Kilometraje</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="date" class="form-control input" id="fechaIngreso">
                        <label for="fechaIngreso">Fecha de ingreso:</label>
                    </div>
                </div>

            </div>
        </div>
        <div class="card-footer">
            <div class="text-end">
                <button class="btn btn-secondary" onclick="window.location.href='listar-ordenes.php'">Cancelar</button>
                <button class="btn btn-success" onclick="window.location.href='listar-ordenes.php'">Aceptar</button>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<?php

require_once "../../partials/_footer.php";

?>

<div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
    <div class="modal-dialog"> <!-- Modal grande si lo requieres -->
        <div class="modal-content">

            <!-- Encabezado -->
            <div class="modal-header">
                <h2 class="modal-title" id="miModalLabel">Seleccionar Propietario</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">

                <!-- Fila para Tipo de Propietario -->
                <div class="row mb-3">
                    <div class="col">
                        <label><strong>Tipo de propietario:</strong></label>
                        <!-- Contenedor de radio buttons -->
                        <div style="display: flex; align-items: center; gap: 10px; margin-left:20px;">
                            <div class="form-check form-check-inline" style="margin-right:40px;">
                                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnpersona"
                                    onclick="actualizarOpciones(); buscarPropietario();" checked>
                                <label class="form-check-label" for="rbtnpersona"
                                    style="margin-left:5px;">Persona</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnempresa"
                                    onclick="actualizarOpciones(); buscarPropietario();">
                                <label class="form-check-label" for="rbtnempresa"
                                    style="margin-left:5px;">Empresa</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fila para Método de Búsqueda -->
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <select id="selectMetodo" class="form-select" style="color: black;">
                                <!-- Se actualizarán las opciones según el tipo (persona/empresa) -->
                            </select>
                            <label for="selectMetodo">Método de búsqueda:</label>
                        </div>
                    </div>
                </div>

                <!-- Fila para Valor Buscado -->
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="vbuscado" style="background-color: white;"
                                placeholder="Valor buscado" />
                            <label for="vbuscado">Valor buscado</label>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Resultados -->
                <p class="mt-3"><strong>Resultado:</strong></p>
                <div class="table-responsive">
                    <table id="tabla-resultado" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Confirmar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pie del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="ModalCliente" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
    <div class="modal-dialog"> <!-- Modal grande si lo requieres -->
        <div class="modal-content">

            <!-- Encabezado -->
            <div class="modal-header">
                <h2 class="modal-title" id="miModalLabel">Seleccionar Propietario</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">

                <!-- Fila para Tipo de Propietario -->
                <div class="row mb-3">
                    <div class="col">
                        <label><strong>Tipo de propietario:</strong></label>
                        <!-- Contenedor de radio buttons -->
                        <div style="display: flex; align-items: center; gap: 10px; margin-left:20px;">
                            <div class="form-check form-check-inline" style="margin-right:40px;">
                                <input  class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnpersona" onclick="actualizarOpciones(); buscarPropietario();" checked>
                                <label class="form-check-label" for="rbtnpersona" style="margin-left:5px;">Persona</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnempresa" onclick="actualizarOpciones(); buscarPropietario();">
                                <label class="form-check-label" for="rbtnempresa" style="margin-left:5px;">Empresa</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fila para Método de Búsqueda -->
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <select id="selectMetodo" class="form-select" style="color: black;">
                                <!-- Se actualizarán las opciones según el tipo (persona/empresa) -->
                            </select>
                            <label for="selectMetodo">Método de búsqueda:</label>
                        </div>
                    </div>
                </div>

                <!-- Fila para Valor Buscado -->
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="vbuscado" style="background-color: white;" placeholder="Valor buscado" />
                            <label for="vbuscado">Valor buscado</label>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Resultados -->
                <p class="mt-3"><strong>Resultado:</strong></p>
                <div class="table-responsive">
                    <table id="tabla-resultado" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Confirmar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pie del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

<!--
<script>
  // Obtiene la fecha actual
  const today = new Date();

  // Función para formatear la fecha en formato 'YYYY-MM-DD'
  function formatDate(date) {
    const yyyy = date.getFullYear();
    const mm = date.getMonth() + 1; // Los meses inician en 0
    const dd = date.getDate();
    return `${yyyy}-${mm < 10 ? '0' + mm : mm}-${dd < 10 ? '0' + dd : dd}`;
  }

  // Calcula la fecha actual formateada
  const currentDate = formatDate(today);

  // Calcula la fecha de 2 días atrás
  const twoDaysAgo = new Date();
  twoDaysAgo.setDate(today.getDate() - 2);
  const minDate = formatDate(twoDaysAgo);

  // Asigna los atributos 'min' y 'max' al input
  const dateInput = document.getElementById('fechaIngreso');
  dateInput.setAttribute('min', minDate);
  dateInput.setAttribute('max', currentDate);
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tiposervicioSelect  = document.getElementById("subcategoria");
        const servicioSelect      = document.getElementById("servicio");
        const mecanicoSelect      = document.getElementById("mecanico");
        const vehiculoSelect      = document.getElementById("vehiculo");
        const propietarioInput    = document.getElementById("hiddenIdCliente");

        fetch("http://localhost/fix360/app/controllers/subcategoria.controller.php?task=getServicioSubcategoria")
            .then(response => response.json())
            .then(data => {
                data.forEach(item => {
                    const option = document.createElement("option");
                    option.value = item.idsubcategoria;
                    option.textContent = item.subcategoria;
                    tiposervicioSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar los tipo de servicio:", error));

        fetch("http://localhost/fix360/app/controllers/mecanico.controller.php?task=getAllMecanico")
            .then(response => response.json())
            .then(data => {
                data.forEach(item => {
                    const option = document.createElement("option");
                    option.value = item.idcolaborador;
                    option.textContent = item.nombres;
                    mecanicoSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error al cargar mecanico:", error));

        function cargarServicio() {
            const tiposervicio = tiposervicioSelect.value;

            servicioSelect.innerHTML = '<option value="">Seleccione una opcion</option>';

            if (tiposervicio) {
                fetch(`http://localhost/fix360/app/controllers/servicio.controller.php?task=getServicioBySubcategoria&idsubcategoria=${encodeURIComponent(tiposervicio)}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            const option = document.createElement("option");
                            option.value = item.idservicio;
                            option.textContent = item.servicio;
                            servicioSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error("Error al cargar los servicios:", error));
            }
        }
        tiposervicioSelect.addEventListener("change", cargarServicio);

        function cargarVehiculos() {
            const propietario = propietarioInput.value;

            vehiculoSelect.innerHTML = '<option value="">Seleccione una opcion</option>';

            if(propietario){
                fetch(`http://localhost/fix360/app/controllers/vehiculo.controller.php?task=getVehiculoByCliente&idcliente=${encodeURIComponent(propietario)}`)
                .then(response => response.json())
                .then(data =>{
                    data.forEach(item => {
                        const option = document.createElement("option");
                        option.value = item.idvehiculo;
                        option.textContent = item.placa;
                        vehiculoSelect.appendChild(option);
                    });
                })
                .catch(error => console.error("Error al cargar los vehiculos: ", error));
            }
        }
        propietarioInput.addEventListener("change", cargarVehiculos);
    });
</script>
-->

<script src="<?= SERVERURL?>views/page/ordenservicios/js/registrar-ordenes.js"></script>
</body>

</html>