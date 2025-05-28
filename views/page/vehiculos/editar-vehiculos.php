<?php

const NAMEVIEW = "Vehiculo | Edicion";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<!---hasta aqui sera el header-->
<div class="container-main">
  <form action="" id="FormVehiculo">
    <div class="card border">
      <div class="card-body">
        <div class="row">

          <div class="col-md-4 ">
            <div class="form-floating">
              <select class="form-select input" id="marcav" name="marcav" style="color: black;" required>
                <option value="">Seleccione una opcion</option>
                <!-- Se llenará dinámicamente -->
              </select>
              <label for="marcav"><strong> Marca del vehiculo:</strong></label>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="form-floating">
              <select class="form-select" id="tipov" name="tipov" style="color: black;" required>
                <option value="">Seleccione una opcion</option>
                <!-- Se llenará dinámicamente -->
              </select>
              <label for="tipov"><strong>Tipo de vehiculo:</strong></label>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="form-floating">
              <select class="form-select" id="modelo" name="modelo" style="color: black;" required>
                <option value="">Seleccione una opcion</option>
                <!-- Se llenará dinámicamente -->
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
              <input type="text" class="form-control input" id="fnumserie" minlength="17" maxlength="17"
                placeholder="numerodeserie" />
              <label for="fnumserie">N° de serie</label>
            </div>
          </div>

          <div class="col-md-2 mb-3">
            <div class="form-floating">
              <input type="text" class="form-control input" id="fcolor" placeholder="#e0aef6" minlength="3"
                maxlength="20" />
              <label for="fcolor"><strong>Color</strong></label>
            </div>
          </div>

          <div class="col-md-2 mb-3">
            <div class="form-floating">
              <select class="form-select" id="ftcombustible" style="color: black;">
                <option value="">Seleccione una opcion</option>
                <!-- Se llenará dinámicamente -->
              </select>
              <label for="ftcombustible"><strong> Tipo de combustible:</strong></label>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="form-floating">
              <input type="text" class="form-control input" id="vin" placeholder="vin" minlength="17"
                maxlength="17" />
              <label for="vin">VIN</label>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="form-floating">
              <input type="text" class="form-control input" id="numchasis" placeholder="numchasis" minlength="17"
                maxlength="17" />
              <label for="numchasis">N° Chasis</label>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="form-floating input-group mb-3">
              <input type="text" disabled class="form-control input" id="propietario" placeholder="Propietario" />
              <label for="propietario">Propietario</label>
              <!-- En tu formulario principal -->
              <input type="hidden" id="hiddenIdPropietario" name="idpropietario">
              <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                data-bs-target="#miModal">
                ...
              </button>
            </div>
          </div>

        </div>
      </div>
      <div class="card-footer text-end">
        <a href="javascript:history.back()" class="btn btn-secondary text-end">Cerrar</a>
        <button type="button" class="btn btn-success text-end" id="btnRegistrarVehiculo">Guardar</button>
      </div>
    </div>
  </form>
</div>
</div>
</div>
<!--FIN VENTAS-->

<?php
require_once "../../partials/_footer.php";
?>

<!-- Modal para seleccionar propietario -->
<div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
  <div class="modal-dialog">
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
                <!-- Se actualizarán las opciones según el tipo -->
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
 document.addEventListener("DOMContentLoaded", () => {
    // 1) Precargar marcas, tipos y combustibles
    cargarSelectsIniciales()
      .then(() => {
        // 2) Leer idvehiculo de la URL y traer datos
        const params = new URLSearchParams(window.location.search);
        const idvehiculo = parseInt(params.get("id"), 10);
        if (idvehiculo > 0) cargarDatosVehiculo(idvehiculo);
      });

    // 3) Lógica del modal de propietarios
    inicializarModalPropietario();

    // 4) Botón Guardar
    document.getElementById("btnRegistrarVehiculo")
      ?.addEventListener("click", onGuardarVehiculo);
  });

  // ----------------------------
  // Rellena sólo marca, tipo y combustible
  // ----------------------------
  async function cargarSelectsIniciales() {
    try {
      // Marcas
      const resMarcas = await fetch(`${SERVERURL}app/controllers/Marca.controller.php?task=getAllMarcaVehiculo`);
      const marcas    = await resMarcas.json();
      const selMarca  = document.getElementById("marcav");
      marcas.forEach(item =>
        selMarca.insertAdjacentHTML("beforeend",
          `<option value="${item.idmarca}">${item.nombre}</option>`
        )
      );

      // Tipos
      const resTipos = await fetch(`${SERVERURL}app/controllers/Tipov.controller.php?task=getAllTipoVehiculo`);
      const tipos    = await resTipos.json();
      const selTipo  = document.getElementById("tipov");
      tipos.forEach(item =>
        selTipo.insertAdjacentHTML("beforeend",
          `<option value="${item.idtipov}">${item.tipov}</option>`
        )
      );

      // Combustibles
      const resComb = await fetch(`${SERVERURL}app/controllers/Tcombustible.controller.php?task=getAllTcombustible`);
      const combs   = await resComb.json();
      const selComb = document.getElementById("ftcombustible");
      combs.forEach(item =>
        selComb.insertAdjacentHTML("beforeend",
          `<option value="${item.idtcombustible}">${item.tcombustible}</option>`
        )
      );

      // Asociar recarga de modelos a cambios de marca/tipo
      document.getElementById("marcav").addEventListener("change", recargarModelos);
      document.getElementById("tipov").addEventListener("change", recargarModelos);

    } catch (err) {
      console.error("Error cargando selects iniciales:", err);
    }
  }

  // ----------------------------
  // Recarga modelos según marca + tipo
  // ----------------------------
  async function recargarModelos() {
    const idMarca = document.getElementById("marcav").value;
    const idTipo  = document.getElementById("tipov").value;
    const selMod  = document.getElementById("modelo");
    selMod.innerHTML = '<option value="">Seleccione una opcion</option>';
    if (!idMarca || !idTipo) return;

    try {
      const res = await fetch(
        `${SERVERURL}app/controllers/Modelo.controller.php?task=getModelosByTipoMarca&idtipov=${idTipo}&idmarca=${idMarca}`
      );
      const modelos = await res.json();
      modelos.forEach(item =>
        selMod.insertAdjacentHTML("beforeend",
          `<option value="${item.idmodelo}">${item.modelo}</option>`
        )
      );
    } catch (err) {
      console.error("Error cargando modelos:", err);
    }
  }

  // ----------------------------
  // Carga el vehículo + propietario y marca/tipo/modelo/combustible
  // ----------------------------
  async function cargarDatosVehiculo(idvehiculo) {
    try {
      const res  = await fetch(
        `${SERVERURL}app/controllers/Vehiculo.controller.php?task=getVehiculoConPropietario&idvehiculo=${idvehiculo}`
      );
      const data = await res.json();
      if (!data.idvehiculo) return console.warn("Vehículo no encontrado:", idvehiculo);

      // Text inputs
      document.getElementById("fplaca").value    = data.placa      || "";
      document.getElementById("fanio").value     = data.anio       || "";
      document.getElementById("fnumserie").value = data.numserie   || "";
      document.getElementById("fcolor").value    = data.color      || "";
      document.getElementById("vin").value       = data.vin        || "";
      document.getElementById("numchasis").value = data.numchasis  || "";

      // Propietario
      document.getElementById("hiddenIdPropietario").value = data.idcliente_propietario || "";
      document.getElementById("propietario").value         = data.propietario          || "";

      // Marca, tipo y combustible (estos selects ya están llenos)
      document.getElementById("marcav").value        = data.idmarca;
      document.getElementById("tipov").value         = data.idtipov;
      document.getElementById("ftcombustible").value = data.idtcombustible;

      // Finalmente, recarga modelos y selecciona el correcto
      await recargarModelos();
      document.getElementById("modelo").value = data.idmodelo;

    } catch (err) {
      console.error("Error al cargar datos del vehículo:", err);
    }
  }

  // --------------------------------------------------
  // 4) Lógica del modal para seleccionar propietario
  // --------------------------------------------------
  function inicializarModalPropietario() {
    // 4.1) Cuando el modal se muestre, enfocar cuadro de búsqueda
    const miModal = document.getElementById("miModal");
    if (miModal) {
      miModal.addEventListener("shown.bs.modal", () => {
        document.getElementById("vbuscado").focus();
      });
    }

    // 4.2) Al hacer clic en cada radio “Persona/Empresa” o teclear
    const rpersona = document.getElementById("rbtnpersona");
    const rempresa = document.getElementById("rbtnempresa");
    const vbus = document.getElementById("vbuscado");
    if (rpersona && rempresa && vbus) {
      rpersona.addEventListener("click", () => {
        actualizarOpciones();
        buscarPropietario();
      });
      rempresa.addEventListener("click", () => {
        actualizarOpciones();
        buscarPropietario();
      });
      vbus.addEventListener("keyup", buscarPropietario);
    }

    // 4.3) Al hacer clic en “Confirmar” dentro de la tabla
    const tablaRes = document.querySelector("#tabla-resultado");
    if (tablaRes) {
      tablaRes.addEventListener("click", e => {
        const btn = e.target.closest(".btn-confirmar");
        if (!btn) return;

        const id = btn.dataset.id;                             // idcliente seleccionado
        const nombre = btn.closest("tr").cells[1].textContent;  // nombre (columna 2)

        document.getElementById("hiddenIdPropietario").value = id;
        document.getElementById("propietario").value = nombre;

        // Cargar vehículos (si existiera un select de vehículos en este formulario)
        cargarVehiculos();

        setTimeout(() => {
          bootstrap.Modal.getOrCreateInstance(
            document.getElementById("miModal")
          ).hide();
        }, 100);
      });
    }

    // Inicializar el select de “métodos de búsqueda”
    actualizarOpciones();
  }

  function actualizarOpciones() {
    const select = document.getElementById("selectMetodo");
    const isPersona = document.getElementById("rbtnpersona").checked;
    select.innerHTML = isPersona
      ? `<option value="dni">DNI</option>
         <option value="nombre">Apellidos y nombres</option>`
      : `<option value="ruc">RUC</option>
         <option value="razonsocial">Razón Social</option>`;
  }

  async function buscarPropietario() {
    const tipo = document.getElementById("rbtnpersona").checked
      ? "persona"
      : "empresa";
    const metodo = document.getElementById("selectMetodo").value;
    const valor = document.getElementById("vbuscado").value.trim();

    if (!valor) {
      document.querySelector("#tabla-resultado tbody").innerHTML = "";
      return;
    }

    try {
      const url = `${SERVERURL}app/controllers/Propietario.controller.php`;
      const query = `?tipo=${tipo}&metodo=${metodo}&valor=${encodeURIComponent(valor)}`;
      const res = await fetch(url + query);
      const data = await res.json();

      const tbody = document.querySelector("#tabla-resultado tbody");
      tbody.innerHTML = data
        .map(
          (item, i) => `
          <tr>
            <td>${i + 1}</td>
            <td>${item.nombre}</td>
            <td>${item.documento}</td>
            <td>
              <button class="btn btn-success btn-sm btn-confirmar"
                      data-id="${item.idcliente}"
                      data-bs-dismiss="modal">
                <i class="fa-solid fa-circle-check"></i>
              </button>
            </td>
          </tr>
        `
        )
        .join("");
    } catch (err) {
      console.error("Error al buscar propietario:", err);
    }
  }

  async function cargarVehiculos() {
    const idProp = document.getElementById("hiddenIdPropietario").value;
    const sel = document.getElementById("vehiculo");
    if (!sel) return;
    sel.innerHTML = '<option value="">Seleccione una opción</option>';
    if (!idProp) return;

    try {
      const url = `${SERVERURL}app/controllers/vehiculo.controller.php`;
      const query = `?task=getVehiculoByCliente&idcliente=${idProp}`;
      const res = await fetch(url + query);
      const data = await res.json();

      data.forEach(item => {
        sel.insertAdjacentHTML(
          "beforeend",
          `<option value="${item.idvehiculo}">${item.vehiculo}</option>`
        );
      });

      if (sel.options.length > 1) {
        sel.selectedIndex = 1;
        sel.dispatchEvent(new Event("change"));
      }
    } catch (err) {
      console.error("Error al cargar vehículos:", err);
    }
  }


  // --------------------------------------------------
  // 4) Cuando el usuario hace clic en “Guardar”
  // --------------------------------------------------
  async function onGuardarVehiculo(e) {
    e.preventDefault();

    // Leer idvehiculo de la URL
    const params = new URLSearchParams(window.location.search);
    const idvehiculo = parseInt(params.get("id"), 10);
    if (isNaN(idvehiculo) || idvehiculo <= 0) {
      return alert("ID de vehículo inválido.");
    }

    // Validar que haya propietario seleccionado
    const idProp = parseInt(document.getElementById("hiddenIdPropietario").value, 10);
    if (isNaN(idProp) || idProp <= 0) {
      return alert("Debe seleccionar un propietario.");
    }

    // Armar payload:
    const payload = {
      task: "updateVehiculoConHistorico",
      idvehiculo: idvehiculo,
      idmarca: parseInt(document.getElementById("marcav").value, 10),
      idtipov: parseInt(document.getElementById("tipov").value, 10),
      idmodelo: parseInt(document.getElementById("modelo").value, 10),
      placa: document.getElementById("fplaca").value.trim(),
      anio: document.getElementById("fanio").value.trim(),
      numserie: document.getElementById("fnumserie").value.trim(),
      color: document.getElementById("fcolor").value.trim(),
      idtcombustible: parseInt(document.getElementById("ftcombustible").value, 10),
      vin: document.getElementById("vin").value.trim(),
      numchasis: document.getElementById("numchasis").value.trim(),
      idcliente_nuevo: idProp
    };

    // Enviar a controller
    try {
      const res = await fetch(`${SERVERURL}app/controllers/vehiculo.controller.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });
      const js = await res.json();

      if (js.status === true) {
        showToast("Vehículo actualizado correctamente", "SUCCESS", 1500);
        setTimeout(() => {
          window.location.href = "listar-vehiculos.php";
        }, 1000);
      } else {
        const msg = js.message || "Error al actualizar el vehículo";
        showToast(msg, "ERROR", 2000);
      }
    } catch (err) {
      console.error("Error en fetch al actualizar vehículo:", err);
      showToast("Error de red o servidor", "ERROR", 2000);
    }
  }

</script>
</body>
</html>
