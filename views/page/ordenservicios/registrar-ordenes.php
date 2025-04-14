<?php

const NAMEVIEW = "Registro de órdenes de servicio";

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
                        <input type="text" disabled class="form-control input" id="floatingInput" placeholder="Propietario" />
                        <label for="floatingInput">Propietario</label>
                        <input type="hidden" id="hiddenIdCliente" />
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                            data-bs-target="#miModal">
                            ...
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating input-group mb-3">
                        <input type="text" disabled class="form-control" id="floatingInput" placeholder="Cliente">
                        <label for="floatingInput">Cliente</label>
                        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
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
                                <label class="form-check-label" for="rbtnpersona" style="margin-left:5px;">Persona</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnempresa"
                                    onclick="actualizarOpciones(); buscarPropietario();">
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

<script>
    function actualizarOpciones() {
        const select = document.getElementById("selectMetodo");
        const personaSeleccionada = document.getElementById("rbtnpersona").checked;
        // Limpiar opciones actuales
        select.innerHTML = "";
        if (personaSeleccionada) {
            select.innerHTML += `<option value="dni">DNI</option>`;
            select.innerHTML += `<option value="nombre">Nombre</option>`;
        } else {
            select.innerHTML += `<option value="ruc">RUC</option>`;
            select.innerHTML += `<option value="razonsocial">Razón Social</option>`;
        }
    }

    // Función para buscar propietarios en el modal y llenar la tabla de resultados
    function buscarPropietario() {
        const tipo = document.getElementById("rbtnpersona").checked ? "persona" : "empresa";
        const metodo = document.getElementById("selectMetodo").value;
        const valor = document.getElementById("vbuscado").value.trim();

        // Si no se ingresa valor, limpia la tabla
        if (valor === "") {
            document.querySelector("#tabla-resultado tbody").innerHTML = "";
            return;
        }

        // Construir la URL de la consulta (ajusta la ruta según tu estructura)
        const url = `http://localhost/fix360/app/controllers/Propietario.controller.php?tipo=${encodeURIComponent(tipo)}&metodo=${encodeURIComponent(metodo)}&valor=${encodeURIComponent(valor)}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector("#tabla-resultado tbody");
                tbody.innerHTML = "";
                // Crear filas para cada resultado
                data.forEach((item, index) => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
          <td>${index + 1}</td>
          <td>${item.nombre}</td>
          <td>${item.documento}</td>
          <td>
            <button type="button" class="btn btn-success btn-sm btn-confirmar" data-id="${item.idcliente}" data-bs-dismiss="modal">
  <i class="fa-solid fa-circle-check"></i>
</button>
          </td>
        `;
                    tbody.appendChild(tr);
                });
            })
            .catch(error => console.error("Error en búsqueda:", error));
    }

    // Cuando se hace clic en el botón "Confirmar" del modal
    document.querySelector("#tabla-resultado").addEventListener("click", function(e) {
        if (e.target.closest(".btn-confirmar")) {
            const btn = e.target.closest(".btn-confirmar");
            const idcliente = btn.getAttribute("data-id");

            // Obtener el nombre desde la fila (segunda columna)
            const fila = btn.closest("tr");
            const nombre = fila.cells[1].textContent;

            // Guardar el id y el nombre en los inputs correspondientes
            document.getElementById("hiddenIdCliente").value = idcliente;
            document.getElementById("floatingInput").value = nombre;

            // Cerrar el modal después de un pequeño delay
            setTimeout(() => {
                const modalEl = document.getElementById("miModal");
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.hide();
            }, 100);
        }
    });

    // Escuchar el input de búsqueda para disparar la consulta (puedes agregar debounce para evitar llamadas excesivas)
    document.getElementById("vbuscado").addEventListener("keyup", buscarPropietario);

    // Actualizar opciones del select y disparar búsqueda al cambiar los radio buttons
    document.getElementById("rbtnpersona").addEventListener("click", function() {
        actualizarOpciones();
        buscarPropietario();
    });
    document.getElementById("rbtnempresa").addEventListener("click", function() {
        actualizarOpciones();
        buscarPropietario();
    });

    // Inicializar las opciones del select al cargar el modal
    document.addEventListener("DOMContentLoaded", actualizarOpciones);
    const fechaInput = document.getElementById('fechaIngreso');
    const setFechaDefault = () => {
      const today = new Date();
      const day = String(today.getDate()).padStart(2, '0');
      const month = String(today.getMonth() + 1).padStart(2, '0');
      const year = today.getFullYear();
      fechaInput.value = `${year}-${month}-${day}`;
    };
    setFechaDefault();

    // Ejecutar la función al cargar la página para establecer las opciones iniciales
    actualizarOpciones();
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tiposervicioSelect = document.getElementById("subcategoria");
        const servicioSelect = document.getElementById("servicio");
        const mecanicoSelect = document.getElementById("mecanico");
        const vehiculoSelect = document.getElementById("vehiculo");

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

    });
</script>
</body>

</html>