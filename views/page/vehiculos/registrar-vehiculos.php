<?php

const NAMEVIEW = "Registro de vehiculos";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<!---hasta aqui sera el header-->
<div class="container-main">
  <form action="" id="FormVehiculo">
    <div class="card border" style="margin-top:50px;">
      <div class="card-body">
        <div class="row">

          <div class="col-md-4 mb-3">
            <div class="form-floating">
              <select class="form-select" id="tipov" name="tipov" style="color: black;" required>
                <option value="">Seleccione una opcion</option>
              </select>
              <label for="tipov">Tipo de vehiculo:</label>
            </div>
          </div>

          <div class="col-md-4 ">
            <div class="form-floating">
              <select class="form-select input" id="marcav" name="marcav" style="color: black;" required>
                <option value="">Seleccione una opcion</option>

              </select>
              <label for="marcav">Marca del vehiculo:</label>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-floating">
              <select class="form-select" id="modelo" name="modelo" style="color: black;" required>
                <option value="">Seleccione una opcion</option>
              </select>
              <label for="modelo">Modelo del vehiculo:</label>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" class="form-control input" id="fplaca" placeholder="placadeejemplo" minlength="6" required
                maxlength="6" />
              <label for="fplaca">Placa</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" class="form-control input" id="fanio" placeholder="anio" minlength="4" maxlength="4"
                required />
              <label for="fanio">Año</label>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="form-floating">
              <input type="text" class="form-control input" id="fnumserie" placeholder="numerodeserie" />
              <label for="fnumserie">N° de serie</label>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="form-floating">
              <input type="text" class="form-control input" id="fcolor" placeholder="#e0aef6" />
              <label for="fcolor">Color</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <select class="form-select" id="ftcombustible" style="color: black;">
                <option value="Gasolina" selected>Gasolina</option>
                <option value="Diesel">Diesel</option>
                <option value="GNV">GNV</option>
                <option value="GLP">GLP</option>
                <option value="Biodiésel">biodiésel</option>
                <option value="Etanol">Etanol</option>
                <option value="Allinol">Allinol</option>
                <option value="Electricidad">Electricidad</option>
                <option value="Hidrogeno">Hidrogeno</option>
                <option value="Biocombustible">Biocombustible</option>
              </select>
              <label for="ftcombustible">Tipo de combustible:</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating input-group mb-3">
              <input type="text" disabled class="form-control input" id="floatingInput" placeholder="propietario" />
              <label for="floatingInput">Propietario</label>

              <input type="hidden" id="hiddenIdCliente" />
              <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                data-bs-target="#miModal">
                ...
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="card-footer">
        <div class="text-end">
          <button class="btn btn-secondary" onclick="window.location.href='listar-vehiculos.php'">
            Cancelar
          </button>
          <button type="submit" class="btn btn-success" id="btnRegistrarVehiculo">
            Aceptar
          </button>
        </div>
      </div>
  </form>
</div>
</div>
</div>
</div>
<!--FIN VENTAS-->

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
  document.addEventListener("DOMContentLoaded", function() {
  // Cacheo de selectores
  const fplacaInput = document.getElementById('fplaca');
  // ... (otros selectores ya existentes)

  // Consulta a la API de placas al salir del campo (blur)
  if (fplacaInput) {
    fplacaInput.addEventListener('blur', async function() {
      const placa = fplacaInput.value.trim().toUpperCase(); // Convertir a mayúsculas, si es necesario
      // Valida, por ejemplo, que la placa tenga 6 caracteres (ajusta según tu caso)
      if (placa.length === 6) {
        try {
          // Llamada al endpoint intermedio que creaste (consultaPlaca.php)
          const response = await fetch(`http://localhost/fix360/app/controllers/consultaPlaca.php?placa=${encodeURIComponent(placa)}`);
          const data = await response.json();
          // Supongamos que la API retorna datos como "marca", "modelo", "anio", etc.
          if (data && data.marca) {
            // Por ejemplo, asigna el valor a un select o input correspondiente
            const marcavSelect = document.getElementById("marcav");
            if (marcavSelect) {
              marcavSelect.value = data.marca; // Asigna la marca
            }
            // También podrías completar otros campos como modelo o año:
            const modeloSelect = document.getElementById("modelo");
            if (modeloSelect && data.modelo) {
              modeloSelect.value = data.modelo;
            }
            // Si la API indica que la placa ya existe, podrías mostrar un showToast:
            if (data.duplicado) {
              showToast('La placa ya está registrada.', 'WARNING', 3000);
            }
          } else {
            // Si la API no retorna datos válidos, puedes limpiar o notificar
            showToast('No se encontró información para esa placa.', 'INFO', 3000);
          }
        } catch (error) {
          console.error("Error al consultar la API de placas:", error);
          showToast('Error al consultar información de la placa.', 'ERROR', 3000);
        }
      }
    });
  }

  // ... (resto de tu código, por ejemplo, listeners para DNI, RUC, registro del vehículo, etc.)
});

</script>
<script>
  // Función para actualizar las opciones del select según el tipo de propietario (persona/empresa)
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

  // --- Registro de Vehículo y Propietario ---
  // Escuchar el botón "Aceptar" del formulario de vehículo
  document.getElementById("btnRegistrarVehiculo").addEventListener("click", function(e) {
    e.preventDefault();

    // Recopilar los datos del formulario de vehículo
    const data = {
      idmodelo: document.getElementById("modelo").value,
      placa: document.getElementById("fplaca").value.trim(),
      anio: document.getElementById("fanio").value.trim(),
      numserie: document.getElementById("fnumserie").value.trim(),
      color: document.getElementById("fcolor").value.trim(),
      tipocombustible: document.getElementById("ftcombustible").value,
      // Enviar directamente el idcliente obtenido del modal
      idcliente: document.getElementById("hiddenIdCliente").value
    };

    // Validar que se tenga seleccionado un propietario
    if (!data.idmodelo || !data.placa || !data.anio || !data.numserie || !data.idcliente) {
      alert("Por favor, completa todos los campos obligatorios y selecciona un propietario.");
      return;
    }

    // Enviar la información al controlador (ajusta la URL según tu estructura)
    fetch("http://localhost/fix360/app/controllers/Vehiculo.controller.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(resp => {
        if (resp.rows > 0) {
          alert("Registro exitoso.");
          // Aquí puedes limpiar el formulario o redirigir
        } else {
          console.log("Error en el registro.");
          err => {
            console.error("Error en la solicitud:", err);
          }

        }
      })
      .catch(err => {
        console.error("Error en la solicitud:", err);
      });

  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const tipovSelect = document.getElementById("tipov");
    const marcavSelect = document.getElementById("marcav");
    const modeloSelect = document.getElementById("modelo");

    // Cargar select de tipo de vehículo
    fetch("http://localhost/fix360/app/controllers/Tipov.controller.php")
      .then(response => response.json())
      .then(data => {
        // Asumiendo que cada registro trae campos 'idtipov' y 'tipov'
        data.forEach(item => {
          const option = document.createElement("option");
          option.value = item.idtipov; // Ajusta según el nombre del campo real
          option.textContent = item.tipov; // Ajusta según el nombre del campo real
          tipovSelect.appendChild(option);
        });
      })
      .catch(error => console.error("Error al cargar tipos de vehículo:", error));

    // Cargar select de marcas
    fetch("http://localhost/fix360/app/controllers/Marca.controller.php?task=getAllMarcaVehiculo")
      .then(response => response.json())
      .then(data => {
        // Asumiendo que cada registro trae campos 'idmarca' y 'marca'
        data.forEach(item => {
          const option = document.createElement("option");
          option.value = item.idmarca; // Ajusta según el nombre del campo real
          option.textContent = item.nombre; // Ajusta según el nombre del campo real
          marcavSelect.appendChild(option);
        });
      })
      .catch(error => console.error("Error al cargar marcas:", error));

    // Función para cargar modelos basado en tipo y marca seleccionados
    function cargarModelos() {
      const idtipov = tipovSelect.value;
      const idmarca = marcavSelect.value;

      // Limpiar el select de modelos y agregar opción por defecto
      modeloSelect.innerHTML = '<option value="">Seleccione una opcion</option>';

      // Solo llamar al controller si ambos selects tienen un valor
      if (idtipov && idmarca) {
        fetch(`http://localhost/fix360/app/controllers/Modelo.controller.php?idtipov=${encodeURIComponent(idtipov)}&idmarca=${encodeURIComponent(idmarca)}`)
          .then(response => response.json())
          .then(data => {
            // Asumiendo que cada registro trae campos 'idmodelo' y 'modelo'
            data.forEach(item => {
              const option = document.createElement("option");
              option.value = item.idmodelo;
              option.textContent = item.modelo;
              modeloSelect.appendChild(option);
            });
          })
          .catch(error => console.error("Error al cargar modelos:", error));
      }
    }

    // Agregar event listeners para que cuando cambie el tipo o la marca se actualice el select de modelos
    tipovSelect.addEventListener("change", cargarModelos);
    marcavSelect.addEventListener("change", cargarModelos);
  });
</script>



</body>

</html>