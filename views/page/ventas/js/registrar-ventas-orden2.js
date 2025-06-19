window.alert = function (msg, type = "WARNING", duration = 2000) {
  showToast(msg, type, duration);
};
document.addEventListener("DOMContentLoaded", function () {
  // Variables y elementos
  const hiddenIdCliente = document.getElementById("hiddenIdCliente");
  const inputProp = document.getElementById("propietario");
  const inputProductElement = document.getElementById("producto");
  const inputStock = document.getElementById("stock");
  const inputPrecio = document.getElementById("precio");
  const inputCantidad = document.getElementById("cantidad");
  const inputDescuento = document.getElementById("descuento");
  let selectedProduct = {};
  const numSerieInput = document.getElementById("numserie");
  const numComInput = document.getElementById("numcom");
  const tipoInputs = document.querySelectorAll('input[name="tipo"]');
  const agregarProductoBtn = document.getElementById("agregarProducto");
  const tabla = document.querySelector("#tabla-detalle tbody");
  const detalleVenta = [];
  const vehiculoSelect = document.getElementById("vehiculo");
  const btnFinalizarVenta = document.getElementById("btnFinalizarVenta");
  const detalleServicios = [];
  const tablaServ = document.querySelector("#tabla-detalle-servicios tbody");
  const btnAgregarDetalleServicio = document.getElementById(
    "btnAgregarDetalleServicio"
  );
  const selectServicio = document.getElementById("servicio");
  const selectMecanico = document.getElementById("mecanico");
  let mecanicosCache = null;
  const inputPrecioServicio = document.getElementById("precioServicio");
  const fechaInput = document.getElementById("fechaIngreso");
  const monedaSelect = document.getElementById("moneda");
  const kmInput = document.getElementById("kilometraje");
  const btnToggleService = document.getElementById("btnToggleService");
  const obsField = document.getElementById("observaciones");
  const gruField = document.getElementById("ingresogrua");
  const hiddenIdPropietario = document.getElementById("hiddenIdPropietario");
  const inputClienteVisible = document.getElementById("inputClienteVisible");
  hiddenIdPropietario.addEventListener("change", actualizarEstadoGuardar);

  numSerieInput.value = "";
  numComInput.value = "";

  async function cargarMecanicos() {
    // Si ya tenemos datos, no volvemos a fetch, solo repoblamos:
    if (Array.isArray(mecanicosCache)) {
      repoblarSelect(mecanicosCache);
      return;
    }

    try {
      const resp = await fetch(
        `${FIX360_BASE_URL}app/controllers/mecanico.controller.php?task=getAllMecanico`
      );
      if (!resp.ok) throw new Error(`Status ${resp.status}`);
      const data = await resp.json();
      mecanicosCache = data; // ← cacheamos
      repoblarSelect(mecanicosCache);
    } catch (err) {
      console.error("Error al cargar mecánicos:", err);
      showToast("No se pudieron cargar los mecánicos.", "ERROR", 1500);
    }
  }

  function repoblarSelect(lista) {
    selectMecanico.innerHTML = '<option value="">Eliga un mecánico</option>';
    lista.forEach((item) => {
      const opt = document.createElement("option");
      opt.value = item.idcolaborador;
      opt.textContent = item.nombres;
      selectMecanico.appendChild(opt);
    });
  }

  // Llamada inicial
  cargarMecanicos();

  // 1) Función para habilitar/deshabilitar el botón “Guardar”
  function actualizarEstadoGuardar() {
    const tieneProductos = detalleVenta.length > 0;
    const tieneServicios = detalleServicios.length > 0;
    const propietarioSeleccionado = !!hiddenIdPropietario.value.trim();
    // Habilita si hay al menos un producto o un servicio y hay propietario
    btnFinalizarVenta.disabled = !(
      (tieneProductos || tieneServicios) &&
      propietarioSeleccionado
    );
  }
  actualizarEstadoGuardar();

  // 2) Cargar lista de servicios por subcategoría
  async function cargarServiciosPorSubcategoria(idsubcat) {
    if (!idsubcat) {
      document.getElementById("servicio").innerHTML =
        '<option value="">Eliga un servicio</option>';
      return;
    }
    try {
      const resp = await fetch(
        `${FIX360_BASE_URL}app/controllers/Servicio.Controller.php?task=getServicioBySubcategoria&idsubcategoria=${idsubcat}`
      );
      const data = await resp.json();
      let html = '<option value="">Eliga un servicio</option>';
      data.forEach((item) => {
        html += `<option value="${item.idservicio}">${item.servicio}</option>`;
      });
      document.getElementById("servicio").innerHTML = html;
    } catch (err) {
      console.error("Error al cargar servicios:", err);
      showToast("Error al cargar servicios.", "ERROR", 1500);
    }
  }

  document
    .getElementById("subcategoria")
    .addEventListener("change", function () {
      const idsubcat = this.value;
      cargarServiciosPorSubcategoria(idsubcat);
    });

  // 3) Abrir modal para registrar un servicio nuevo
  document
    .getElementById("btnAgregarServicio")
    .addEventListener("click", function () {
      const selectSub = document.getElementById("subcategoria");
      const idsubcat = selectSub.value;
      const textoSub = selectSub.options[selectSub.selectedIndex]?.text || "";

      if (!idsubcat) {
        showToast(
          "Primero debe seleccionar un Tipo de Servicio (subcategoría).",
          "WARNING",
          1500
        );
        return;
      }
      document.getElementById("modalSubcategoriaId").value = idsubcat;
      document.getElementById("modalSubcategoriaNombre").value = textoSub;
      document.getElementById("modalServicioNombre").value = "";
      new bootstrap.Modal(document.getElementById("ModalServicio")).show();
    });

  // 4) Registrar servicio desde el modal
  document
    .getElementById("btnRegistrarServicioModal")
    .addEventListener("click", async function () {
      const idsubcategoria = document.getElementById(
        "modalSubcategoriaId"
      ).value;
      const servicioNombre = document
        .getElementById("modalServicioNombre")
        .value.trim();

      if (!servicioNombre) {
        showToast("Debe ingresar el nombre del servicio.", "WARNING", 1500);
        return;
      }

      const payload = {
        task: "registerServicio",
        idsubcategoria: idsubcategoria,
        servicio: servicioNombre,
      };

      try {
        const resp = await fetch(
          `${FIX360_BASE_URL}app/controllers/Servicio.Controller.php`,
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload),
          }
        );
        const json = await resp.json();
        if (json.error) {
          showToast("Error: " + json.error, "ERROR", 2000);
          return;
        }

        // Si tuvo éxito:
        const nuevoId = json.idservicio;
        const nuevoNom = json.servicio;

        // Cerrar modal
        const modalEl = document.getElementById("ModalServicio");
        bootstrap.Modal.getInstance(modalEl).hide();

        // Agregar nueva opción al <select id="servicio"> y seleccionarla
        const selectServ = document.getElementById("servicio");
        const opt = document.createElement("option");
        opt.value = nuevoId;
        opt.textContent = nuevoNom;
        opt.selected = true;
        selectServ.appendChild(opt);

        showToast("Servicio registrado correctamente.", "SUCCESS", 1500);
      } catch (err) {
        console.error("Error al registrar servicio:", err);
        showToast("Ocurrió un error al registrar el servicio.", "ERROR", 1500);
      }
    });

  // --- Funcionalidades para fecha predeterminada ---
  document.addEventListener("DOMContentLoaded", () => {
    setFechaDefault();
  });

  function setFechaDefault() {
    const input = document.getElementById("fechaIngreso");
    if (!input) {
      console.warn("No encontré #fechaIngreso");
      return;
    }
    const now = new Date();
    const pad = (n) => String(n).padStart(2, "0");
    const yyyy = now.getFullYear(),
      MM = pad(now.getMonth() + 1),
      dd = pad(now.getDate()),
      hh = pad(now.getHours()),
      mm = pad(now.getMinutes());
    input.value = `${yyyy}-${MM}-${dd}T${hh}:${mm}`;
    // opcional: rango
    const twoDaysAgo = new Date(now);
    twoDaysAgo.setDate(now.getDate() - 2);
    input.min = `${twoDaysAgo.getFullYear()}-${pad(
      twoDaysAgo.getMonth() + 1
    )}-${pad(twoDaysAgo.getDate())}T00:00`;
    input.max = `${yyyy}-${MM}-${dd}T23:59`;
  }

  btnToggleService.addEventListener("click", function (e) {
    e.preventDefault();

    // 1) Validar que haya vehículo seleccionado
    const idVehiculo = vehiculoSelect.value;
    if (!idVehiculo) {
      showToast(
        "Debes seleccionar un vehículo antes de agregar servicios.",
        "WARNING",
        2000
      );
      vehiculoSelect.focus();
      return;
    }

    // 2) Validar kilometraje ingresado y coherente con prevKilometraje
    const kmVal = parseFloat(kmInput.value);
    if (isNaN(kmVal) || kmInput.value.trim() === "") {
      showToast(
        "Debes ingresar el kilometraje antes de agregar servicios.",
        "WARNING",
        2000
      );
      kmInput.focus();
      return;
    }
    if (prevKilometraje !== null && kmVal < prevKilometraje) {
      showToast(
        `El kilometraje no puede ser menor que el último registrado (${prevKilometraje}).`,
        "ERROR",
        2000
      );
      kmInput.value = prevKilometraje;
      kmInput.focus();
      return;
    }

    // 3) Si todo OK, muestra la sección de servicios
    detalleServicios.length = 0;
    tablaServ.innerHTML = "";
    actualizarNumerosServicios();

    document.getElementById("serviceSection").classList.remove("d-none");
    document.getElementById("serviceListCard").classList.remove("d-none");

    this.disabled = true;
    this.classList.remove("btn-success");
    this.classList.add("btn-secondary");

    obsField.disabled = false;
    gruField.disabled = false;
  });

  // --- NUEVO: variable global para almacenar el último kilometraje y función para traerlo ---
  let prevKilometraje = null; // ←–– aquí guardamos el último valor traído
  async function fetchUltimoKilometraje(idvehiculo) {
    console.log("🔍 fetchUltimoKilometraje(", idvehiculo, ")");
    try {
      const url = `${window.FIX360_BASE_URL}app/controllers/vehiculo.controller.php?task=getUltimoKilometraje&idvehiculo=${idvehiculo}`;
      console.log("→ Fetch URL:", url);
      const res = await fetch(url);
      if (!res.ok) throw new Error(res.status);
      const data = await res.json();
      // Si no viene nada, asumimos cero
      const ultimo = parseFloat(data.ultimo_kilometraje) || 0;
      prevKilometraje = ultimo;
      console.log("SP response:", data);
      kmInput.value = ultimo > 0 ? ultimo : "";
    } catch (err) {
      console.error("Error al cargar último kilometraje:", err);
    }
  }

  // --- Funciones auxiliares ---
  function calcularTotales() {
    let totalImporte = 0;
    let totalDescuento = 0;

    // 1) Productos
    tabla.querySelectorAll("tr").forEach((fila) => {
      // ----------- LECTURA DESDE <span class="precio-texto"> -----------
      const precioSpan = fila.querySelector(".precio-texto");
      const precio = precioSpan ? parseFloat(precioSpan.textContent) || 0 : 0;

      // Cantidad (sigue siendo input)
      const cantidad =
        parseFloat(fila.querySelector(".cantidad-input").value) || 0;

      // ----------- LECTURA DESDE <span class="descuento-texto"> -----------
      const descuentoSpan = fila.querySelector(".descuento-texto");
      const descuento = descuentoSpan
        ? parseFloat(descuentoSpan.textContent) || 0
        : 0;

      const importeLinea = (precio - descuento) * cantidad;
      totalImporte += importeLinea;
      totalDescuento += descuento * cantidad;
    });

    // 2) Servicios
    const totalServicios = detalleServicios.reduce(
      (sum, s) => sum + s.precio,
      0
    );
    totalImporte += totalServicios;

    // 3) IGV y neto
    const igv = totalImporte - totalImporte / 1.18;
    const neto = totalImporte / 1.18;

    // 4) Mostrar en el formulario
    document.getElementById("neto").value = neto.toFixed(2);
    document.getElementById("totalDescuento").value = totalDescuento.toFixed(2);
    document.getElementById("igv").value = igv.toFixed(2);
    document.getElementById("total").value = totalImporte.toFixed(2);
  }

  function actualizarNumeros() {
    [...tabla.rows].forEach((fila, i) => (fila.cells[0].textContent = i + 1));
  }

  function estaDuplicado(idproducto = 0) {
    return detalleVenta.some((d) => d.idproducto == idproducto);
  }

  // Cotización → Venta
  const API_BASE = window.FIX360_BASE_URL + "app/controllers/";
  const params = new URLSearchParams(window.location.search);
  const cotId = params.get("id");

  if (cotId) {
    cargarMecanicos();
    // — 1) CARGAR CABECERA —
    fetch(
      `${API_BASE}cotizacion.controller.php?action=getSoloCliente&idcotizacion=${cotId}`
    )
      .then((r) => r.json())
      .then((data) => {
        if (data.error) {
          console.error(data.error);
          return;
        }
        // — Propietario —
        hiddenIdPropietario.value = data.idcliente;
        hiddenIdCliente.value = 0;
        inputProp.value = data.cliente;

        // — Vehículo —
        vehiculoSelect.innerHTML = "";
        if (data.idvehiculo) {
          const optVeh = document.createElement("option");
          optVeh.value = data.idvehiculo;
          optVeh.textContent = data.vehiculo;
          vehiculoSelect.appendChild(optVeh);
          vehiculoSelect.value = data.idvehiculo;
          btnToggleService.disabled = false;
        }

        // — Kilometraje —
        prevKilometraje = parseFloat(data.ultimo_km) || 0;
        kmInput.value = prevKilometraje > 0 ? prevKilometraje : "";

        // Recalcular estado de “Guardar”
        hiddenIdPropietario.dispatchEvent(new Event("change"));
        actualizarEstadoGuardar();
      })
      .catch(console.error);

    // — 2) CARGAR DETALLE DE PRODUCTOS Y SERVICIOS —
    fetch(`${API_BASE}Detcotizacion.controller.php?idcotizacion=${cotId}`)
      .then((r) => r.json())
      .then((items) => {
        let tieneServicios = false;

        items.forEach((item) => {
          const precio = parseFloat(item.precio) || 0;
          const cantidad = parseInt(item.cantidad, 10) || 0;
          const descuento = parseFloat(item.descuento) || 0;
          const importe = ((precio - descuento) * cantidad).toFixed(2);

          if (item.tipo === "producto") {
            // —— PRODUCTO ——
            const tr = document.createElement("tr");
            tr.dataset.idproducto = item.idproducto;
            tr.innerHTML = `
            <td>0</td>
            <td>${item.producto}</td>
            <td><span class="precio-texto">${precio.toFixed(2)}</span></td>
            <td>
              <div class="input-group input-group-sm cantidad-control" style="width:8rem;">
                <button class="btn btn-outline-secondary btn-decrement">−</button>
                <input
                  type="number"
                  class="form-control text-center p-0 border-0 bg-transparent cantidad-input"
                  value="${cantidad}"
                  min="1"
                  max="${item.stockDisponible}"
                >
                <button class="btn btn-outline-secondary btn-increment">＋</button>
              </div>
            </td>
            <td><span class="descuento-texto">${descuento.toFixed(
              2
            )}</span></td>
            <td class="importe-cell">${importe}</td>
            <td><button class="btn btn-danger btn-sm btn-quitar">X</button></td>
          `;
            tabla.appendChild(tr);

            // Listeners de cantidad y eliminación
            const decBtn = tr.querySelector(".btn-decrement");
            const incBtn = tr.querySelector(".btn-increment");
            const qtyInput = tr.querySelector(".cantidad-input");
            const precioSpan = tr.querySelector(".precio-texto");
            const descuentoSpan = tr.querySelector(".descuento-texto");
            const importeCell = tr.querySelector(".importe-cell");
            const quitarBtn = tr.querySelector(".btn-quitar");

            function actualizarLineaProducto() {
              let q = parseInt(qtyInput.value, 10) || 1;
              if (q < 1) q = 1;
              qtyInput.value = q;

              const p = parseFloat(precioSpan.textContent) || 0;
              const d = parseFloat(descuentoSpan.textContent) || 0;
              const imp = ((p - d) * q).toFixed(2);
              importeCell.textContent = imp;

              const idx = detalleVenta.findIndex(
                (dv) => dv.idproducto === item.idproducto
              );
              if (idx >= 0) {
                detalleVenta[idx].cantidad = q;
                detalleVenta[idx].importe = imp;
              }

              actualizarNumeros();
              calcularTotales();
              actualizarEstadoGuardar();
            }

            decBtn.addEventListener("click", () => {
              qtyInput.stepDown();
              actualizarLineaProducto();
            });
            incBtn.addEventListener("click", () => {
              qtyInput.stepUp();
              actualizarLineaProducto();
            });
            qtyInput.addEventListener("input", actualizarLineaProducto);
            quitarBtn.addEventListener("click", () => {
              tr.remove();
              const idx = detalleVenta.findIndex(
                (dv) => dv.idproducto === item.idproducto
              );
              if (idx >= 0) detalleVenta.splice(idx, 1);
              actualizarNumeros();
              calcularTotales();
              actualizarEstadoGuardar();
            });

            detalleVenta.push({
              idproducto: item.idproducto,
              producto: item.producto,
              precio,
              cantidad,
              descuento,
              importe,
            });
          } else if (item.tipo === "servicio") {
            tieneServicios = true;

            // 1) Crea la fila
            const trS = document.createElement("tr");
            trS.dataset.idservicio = item.idservicio;

            // 2) Número de fila
            const tdNum = document.createElement("td");
            tdNum.textContent = tablaServ.rows.length + 1;

            // 3) Servicio
            const tdServ = document.createElement("td");
            tdServ.textContent = item.nombreservicio;

            // 4) Mecánico (clonamos el select que ya tiene todas las opciones)
            const tdMec = document.createElement("td");
            const mecSelect = selectMecanico.cloneNode(true);
            mecSelect.id = ""; // eliminamos el id duplicado
            mecSelect.value = ""; // por defecto ninguno
            mecSelect.addEventListener("change", () => {
              const idx = detalleServicios.findIndex(
                (s) => s.idservicio === item.idservicio
              );
              if (idx >= 0)
                detalleServicios[idx].idmecanico = parseInt(
                  mecSelect.value,
                  10
                );
            });
            tdMec.appendChild(mecSelect);

            // 5) Precio
            const tdPre = document.createElement("td");
            tdPre.textContent = parseFloat(
              item.precio_servicio || item.precio
            ).toFixed(2);

            // 6) Acciones (botón X)
            const tdAcc = document.createElement("td");
            const btnQuitar = document.createElement("button");
            btnQuitar.className = "btn btn-danger btn-sm";
            btnQuitar.textContent = "X";
            btnQuitar.addEventListener("click", () => {
              const idx = detalleServicios.findIndex(
                (s) => s.idservicio === item.idservicio
              );
              if (idx >= 0) detalleServicios.splice(idx, 1);
              trS.remove();
              actualizarNumerosServicios();
              calcularTotales();
              actualizarEstadoGuardar();
            });
            tdAcc.appendChild(btnQuitar);

            // 7) Anexa celdas y fila
            trS.append(tdNum, tdServ, tdMec, tdPre, tdAcc);
            tablaServ.appendChild(trS);

            // 8) Agrega al array detalleServicios
            detalleServicios.push({
              idservicio: item.idservicio,
              servicio: item.nombreservicio,
              idmecanico: null, // se establecerá al cambiar el select
              precio: parseFloat(item.precio_servicio || item.precio),
            });
          }
        });

        // — 3) Mostrar sección de servicios si hay alguno —
        if (tieneServicios) {
          document.getElementById("serviceSection").classList.remove("d-none");
          document.getElementById("serviceListCard").classList.remove("d-none");
          btnToggleService.disabled = true;
          btnToggleService.classList.replace("btn-success", "btn-secondary");
          obsField.disabled = gruField.disabled = false;
        }

        // — 4) Renumerar y recalcular totales/estado —
        actualizarNumeros();
        actualizarNumerosServicios();
        calcularTotales();
        actualizarEstadoGuardar();
      })
      .catch(console.error);
  }
  hiddenIdCliente.addEventListener("change", function () {
    const idcli = parseInt(this.value, 10);
    if (!idcli) {
      inputProp.value = "";
      hiddenIdPropietario.value = "";
      actualizarEstadoGuardar(); // ≤≤≤ Agregado
      return;
    }
    fetch(
      `${API_BASE}cliente.controller.php?action=getDetalles&idcliente=${idcli}`
    )
      .then((r) => r.json())
      .then((clienteData) => {
        inputClienteVisible.value = clienteData.razonSocial;
        hiddenIdPropietario.value = idcli;
        actualizarEstadoGuardar(); // ≤≤≤ Agregado
      })
      .catch(console.error);
  });

  btnAgregarDetalleServicio.addEventListener("click", () => {
    // 1) Lee y valida cada cosa por separado
    const idserv = parseInt(selectServicio.value, 10);
    const idmec = parseInt(selectMecanico.value, 10);
    const precioServ = parseFloat(inputPrecioServicio.value);

    if (!idserv) return alert("Por favor selecciona un servicio válido.");
    if (!idmec) return alert("Por favor selecciona un mecánico válido.");
    if (isNaN(precioServ) || precioServ <= 0)
      return alert("El precio debe ser un número mayor a cero.");
    if (detalleServicios.some((s) => s.idservicio === idserv)) {
      return alert("Ese servicio ya fue agregado.");
    }
    const idVeh = vehiculoSelect.value;
    if (!idVeh) {
      return alert(
        "Debe existir un vehículo seleccionado para agregar servicios."
      );
    }
    // (Opcional) validar kilometraje de nuevo:
    const kmVal = parseFloat(kmInput.value);
    if (isNaN(kmVal) || kmInput.value.trim() === "") {
      return alert(
        "Debe existir un kilometraje válido para agregar servicios."
      );
    }

    // 2) Si todo OK, crear la fila
    const nombreServ = selectServicio.selectedOptions[0].text;
    const nombreMec = selectMecanico.selectedOptions[0].text;

    detalleServicios.push({
      idservicio: idserv,
      idmecanico: idmec,
      precio: precioServ,
    });

    const tr = document.createElement("tr");
    tr.innerHTML = `
    <td>${tablaServ.rows.length + 1}</td>
    <td>${nombreServ}</td>
    <td>${nombreMec}</td>
    <td>${precioServ.toFixed(2)}</td>
    <td><button class="btn btn-danger btn-sm btn-quitar-serv">X</button></td>
  `;
    tr.querySelector(".btn-quitar-serv").addEventListener("click", () => {
      const idx = detalleServicios.findIndex((s) => s.idservicio === idserv);
      detalleServicios.splice(idx, 1);
      tr.remove();
      actualizarNumerosServicios(); // renumera servicios
      calcularTotales(); // recalcula sumas
      actualizarEstadoGuardar();
    });
    tablaServ.appendChild(tr);

    // 3) Limpia el campo de precio para la próxima vez
    inputPrecioServicio.value = "";
    calcularTotales();
    actualizarEstadoGuardar();
  });

  // --- Agregar Producto al Detalle ---
  agregarProductoBtn.addEventListener("click", () => {
    const idp = selectedProduct.idproducto;
    const nombre = inputProductElement.value.trim();
    const precio = parseFloat(inputPrecio.value);
    const cantidad = parseInt(inputCantidad.value, 10);
    if (isNaN(cantidad) || cantidad < 1) {
      alert("La cantidad debe ser un número entero mayor o igual a 1.");
      inputCantidad.value = 1;
      inputCantidad.focus();
      return;
    }
    if (inputDescuento.value.trim() === "") {
      inputDescuento.value = "0";
    }
    const descuento = parseFloat(inputDescuento.value);

    // Validaciones básicas
    if (!idp || nombre !== selectedProduct.subcategoria_producto) {
      alert("Ese producto no existe. Elige uno de la lista.");
      return resetCamposProducto();
    }
    if (!nombre || isNaN(precio) || isNaN(cantidad)) {
      return alert("Completa todos los campos correctamente.");
    }
    if (isNaN(precio) || precio < 1) {
      alert("El precio debe ser un número mayor o igual a 1.");
      inputPrecio.value = selectedProduct.precio.toFixed(2);
      inputPrecio.focus();
      return;
    }
    if (cantidad < 1) {
      alert("La cantidad debe ser mayor que cero.");
      inputCantidad.value = 1;
      return;
    }

    const stockDisponible = selectedProduct.stock || 0;
    if (cantidad > stockDisponible) {
      // 1) Muestro el toast de stock insuficiente
      alert(
        `No puedes pedir ${cantidad} unidades; solo hay ${stockDisponible} en stock.`
      );

      // 2) Limpio TODOS los campos de producto
      resetCamposProducto();

      // 3) Corto la ejecución para no agregar nada
      return;
    }
    if (descuento > precio) {
      alert("El descuento unitario no puede ser mayor que el precio unitario.");
      inputDescuento.value = "";
      return;
    }
    if (descuento < 0) {
      alert("El descuento no puede ser negativo.");
      inputDescuento.value = 0;
      return;
    }
    if (detalleVenta.some((d) => d.idproducto === idp)) {
      alert("Este producto ya ha sido agregado.");
      return resetCamposProducto();
    }

    // Cálculo de importe inicial
    const netoUnit = precio - descuento;
    const importe = netoUnit * cantidad;

    // — Creamos la fila, seteando max="${stockDisponible}" —
    const fila = document.createElement("tr");
    fila.dataset.idproducto = idp;
    fila.innerHTML = `
    <td>${tabla.rows.length + 1}</td>
    <td>${nombre}</td>
    <td>
      <span class="precio-texto">${precio.toFixed(2)}</span>
    </td>
    <td>
      <div class="input-group input-group-sm cantidad-control" style="width: 8rem;">
        <button class="btn btn-outline-secondary btn-decrement" type="button">-</button>
        <input type="number"
               class="form-control text-center p-0 border-0 bg-transparent cantidad-input"
               value="${cantidad}"
               min="1"
               max="${stockDisponible}"
               step="1">
        <button class="btn btn-outline-secondary btn-increment" type="button">+</button>
      </div>
    </td>
    <td>
      <span class="descuento-texto">${descuento.toFixed(2)}</span>
    </td>
    <td class="importe-cell">${importe.toFixed(2)}</td>
    <td><button class="btn btn-danger btn-sm btn-quitar">X</button></td>`;
    tabla.appendChild(fila);

    // Referencias internas de esta fila
    const decBtn = fila.querySelector(".btn-decrement");
    const incBtn = fila.querySelector(".btn-increment");
    const qtyInput = fila.querySelector(".cantidad-input");
    const importeCell = fila.querySelector(".importe-cell");
    const precioSpan = fila.querySelector(".precio-texto");
    const descuentoSpan = fila.querySelector(".descuento-texto");

    function actualizarLinea() {
      // 1) Leer y normalizar cantidad ≥1
      let qty = parseInt(qtyInput.value, 10) || 1;
      if (qty < 1) qty = 1;
      qtyInput.value = qty;

      // 2) Verificar contra el atributo "max" (stock disponible)
      const maxAllowed = parseInt(qtyInput.getAttribute("max"), 10) || Infinity;
      if (qty > maxAllowed) {
        // Si excede, mostramos modal y forzamos qty = maxAllowed
        Swal.fire({
          icon: "warning",
          title: "Stock insuficiente",
          text: `Solo hay ${maxAllowed} unidades disponibles.`,
          confirmButtonText: "Entendido",
          allowOutsideClick: false,
        });
        qty = maxAllowed;
        qtyInput.value = qty;
      }

      // 3) Recalcular importe de la línea
      const precioValor = parseFloat(precioSpan.textContent) || 0;
      const descuentoValor = parseFloat(descuentoSpan.textContent) || 0;
      const netoUnit = precioValor - descuentoValor;
      const nuevoImporte = netoUnit * qty;
      importeCell.textContent = nuevoImporte.toFixed(2);

      // 4) Actualizar el array detalleVenta
      const idx = detalleVenta.findIndex((d) => d.idproducto === idp);
      if (idx >= 0) {
        detalleVenta[idx].cantidad = qty;
        detalleVenta[idx].importe = nuevoImporte.toFixed(2);
      }

      // 5) Renumerar filas y recalcular totales generales
      actualizarNumeros();
      calcularTotales();
    }

    // Listener para “-” (se deja igual)
    decBtn.addEventListener("click", () => {
      qtyInput.stepDown();
      actualizarLinea();
    });

    // Listener para “+” (modificado):
    incBtn.addEventListener("click", () => {
      // 1) Leemos cantidad actual y stock (maxAllowed)
      const currentQty = parseInt(qtyInput.value, 10) || 0;
      const maxAllowed = parseInt(qtyInput.getAttribute("max"), 10) || Infinity;

      if (currentQty >= maxAllowed) {
        // Ya está en el máximo permitido → lanzamos modal
        Swal.fire({
          icon: "warning",
          title: "Stock insuficiente",
          text: `Solo hay ${maxAllowed} unidades disponibles.`,
          confirmButtonText: "Entendido",
          allowOutsideClick: false,
        });
      } else {
        // Estamos por debajo del stock → incrementamos y recalculamos
        qtyInput.stepUp();
        actualizarLinea();
      }
    });

    // Listener para cambios manuales en el input
    qtyInput.addEventListener("input", actualizarLinea);

    // Botón de quitar fila
    fila.querySelector(".btn-quitar").addEventListener("click", () => {
      fila.remove();
      const idx = detalleVenta.findIndex((d) => d.idproducto === idp);
      if (idx >= 0) detalleVenta.splice(idx, 1);
      actualizarNumeros();
      calcularTotales();
      actualizarEstadoGuardar();
    });

    // Agregar el objeto al array detalleVenta
    detalleVenta.push({
      idproducto: idp,
      producto: nombre,
      precio: precio.toFixed(2),
      cantidad: cantidad,
      descuento: descuento.toFixed(2),
      importe: importe.toFixed(2),
    });

    resetCamposProducto();
    actualizarNumeros();
    calcularTotales();
    actualizarEstadoGuardar();
  });

  function resetCamposProducto() {
    inputProductElement.value = "";
    inputStock.value = "";
    inputPrecio.value = "";
    inputCantidad.value = 1;
    inputDescuento.value = 0;
  }

  function actualizarNumeros() {
    const filas = tabla.getElementsByTagName("tr");
    for (let i = 0; i < filas.length; i++) {
      filas[i].children[0].textContent = i + 1;
    }
  }
  function actualizarNumerosServicios() {
    const filas = tablaServ.getElementsByTagName("tr");
    for (let i = 0; i < filas.length; i++) {
      filas[i].children[0].textContent = i + 1;
    }
  }
  /**
   * Crea una versión “debounced” de `func`. Además expone un método `cancel()` para anular el timer pendiente.
   */
  function debounce(func, delay) {
    let timeoutId;
    function wrapped(...args) {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => {
        func.apply(this, args);
      }, delay);
    }
    // Exponemos un método “cancel” para limpiar cualquier timer activo
    wrapped.cancel = function () {
      clearTimeout(timeoutId);
    };
    return wrapped;
  }

  // Función de navegación con el teclado para autocompletar
  function agregaNavegacion(input, itemsDiv) {
    let currentFocus = -1;
    input.addEventListener("keydown", function (e) {
      const items = itemsDiv.getElementsByTagName("div");
      if (!items.length) return;
      if (e.key === "ArrowDown") {
        currentFocus++;
        addActive(items);
      } else if (e.key === "ArrowUp") {
        currentFocus--;
        addActive(items);
      } else if (e.key === "Enter") {
        e.preventDefault();
        if (currentFocus > -1) items[currentFocus].click();
      }
    });

    function addActive(items) {
      removeActive(items);
      if (currentFocus >= items.length) currentFocus = 0;
      if (currentFocus < 0) currentFocus = items.length - 1;
      const el = items[currentFocus];
      el.classList.add("autocomplete-active");
      // esto hará que el elemento activo se vea
      el.scrollIntoView({
        block: "nearest",
      });
    }

    function removeActive(items) {
      Array.from(items).forEach((i) =>
        i.classList.remove("autocomplete-active")
      );
    }
  }

  /**
   * Busca productos y, si encuentra al menos uno, agrega automáticamente el primero a la tabla.
   * Si el término contiene letras o espacios, muestra un dropdown para búsqueda manual.
   * @param {HTMLInputElement} input  El <input> donde se escribe el término de búsqueda.
   */
  function mostrarOpcionesProducto(input) {
    cerrarListas();
    const termino = input.value.trim();
    if (!termino) return;

    // Detectar si es “solo dígitos” (scanner) o no:
    const esSoloDigitos = /^\d+$/.test(termino);

    fetch(
      `http://localhost/Fix360/app/controllers/Venta.controller.php?q=${encodeURIComponent(
        termino
      )}&type=producto`
    )
      .then((response) => response.json())
      .then((data) => {
        if (!Array.isArray(data) || data.length === 0) {
          Swal.fire({
            icon: "warning",
            title: "No se encontraron productos",
            text: "Por favor, verifica el término de búsqueda.",
            confirmButtonText: "Aceptar",
            allowOutsideClick: false,
          });
          return;
        }

        if (esSoloDigitos) {
          // ===== Caso ESCÁNER (código de barras) =====
          const producto = data[0];
          inputProductElement.value = producto.subcategoria_producto;
          inputPrecio.value = parseFloat(producto.precio).toFixed(2);
          inputStock.value = producto.stock;
          inputCantidad.value = 1;
          inputDescuento.value = 0;

          selectedProduct = {
            idproducto: producto.idproducto,
            subcategoria_producto: producto.subcategoria_producto,
            precio: parseFloat(producto.precio),
            stock: producto.stock,
          };

          cerrarListas();
          agregarProductoBtn.click();

          // Devolver foco y limpiar para siguiente escaneo
          inputProductElement.value = "";
          inputProductElement.focus();
        } else {
          // ===== Caso MANUAL (texto con letras o espacios) =====
          const itemsDiv = document.createElement("div");
          itemsDiv.setAttribute("id", "autocomplete-list-producto");
          itemsDiv.setAttribute("class", "autocomplete-items");
          input.parentNode.appendChild(itemsDiv);

          data.forEach(function (producto) {
            const optionDiv = document.createElement("div");
            optionDiv.textContent = producto.subcategoria_producto;
            optionDiv.addEventListener("click", function () {
              inputProductElement.value = producto.subcategoria_producto;
              inputPrecio.value = parseFloat(producto.precio).toFixed(2);
              inputStock.value = producto.stock;
              inputCantidad.value = 1;
              inputDescuento.value = 0;

              selectedProduct = {
                idproducto: producto.idproducto,
                subcategoria_producto: producto.subcategoria_producto,
                precio: parseFloat(producto.precio),
                stock: producto.stock,
              };
              originalPrecio = selectedProduct.precio;
              const inputPrecioVenta = document.getElementById("precio");
              inputPrecioVenta.addEventListener("blur", () => {
                const nuevo = parseFloat(inputPrecioVenta.value);
                if (selectedProduct.idproducto && (!nuevo || nuevo < 1)) {
                  alert("El precio debe ser mayor o igual a 1.");
                  inputPrecioVenta.value = originalPrecio.toFixed(2);
                  inputPrecioVenta.focus();
                  return;
                }
                if (nuevo < originalPrecio) {
                  const ok = window.confirm(
                    `Has ingresado un precio menor al original (${originalPrecio.toFixed(
                      2
                    )}). ¿Deseas continuar?`
                  );
                  if (!ok) {
                    inputPrecioVenta.value = originalPrecio.toFixed(2);
                    document.getElementById("cantidad").focus();
                  }
                }
              });

              cerrarListas();
              // (Aquí podrías hacer inputCantidad.focus(), si quieres)
            });
            itemsDiv.appendChild(optionDiv);
          });

          // Habilitar navegación por teclado en la lista de sugerencias
          agregaNavegacion(input, itemsDiv);
        }
      })
      .catch((err) => {
        console.error("Error al obtener los productos:", err);
        showToast("Error al buscar productos.", "ERROR", 1500);
      });
  }

  // Función para cerrar las listas de autocompletado
  function cerrarListas(elemento) {
    const items = document.getElementsByClassName("autocomplete-items");
    for (let i = 0; i < items.length; i++) {
      if (elemento !== items[i] && elemento !== inputProductElement) {
        items[i].parentNode.removeChild(items[i]);
      }
    }
  }

  // Listeners para el autocompletado de productos usando debounce
  const debouncedMostrarOpcionesProducto = debounce(
    mostrarOpcionesProducto,
    500
  );

  // Cuando el usuario escribe (o hace clic), disparamos el autocompletado “normal” con debounce
  // 1) Listener de ‘input’: sólo para búsquedas manuales con debounce:
  inputProductElement.addEventListener("input", function () {
    const termino = this.value.trim();
    const esSoloDigitos = /^\d+$/.test(termino);

    if (!esSoloDigitos) {
      // Mientras escribes a mano (letras/espacios) usamos el debounce
      debouncedMostrarOpcionesProducto(this);
    } // Si es sólo dígitos, NO hacemos nada aquí; esperamos al ENTER
  });

  // 2) Listener de ‘keyup’: sólo para scanner (ENTER):
  inputProductElement.addEventListener("keyup", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      cerrarListas();
      debouncedMostrarOpcionesProducto.cancel();

      const codigo = this.value.trim();
      if (!codigo) return;

      fetch(
        `${
          window.FIX360_BASE_URL
        }app/controllers/Venta.controller.php?q=${encodeURIComponent(
          codigo
        )}&type=producto`
      )
        .then((r) => r.json())
        .then((data) => {
          if (!Array.isArray(data) || data.length === 0) {
            Swal.fire({
              icon: "warning",
              title: "No se encontraron productos",
              text: "Verifica el código o el nombre.",
              confirmButtonText: "Aceptar",
              allowOutsideClick: false,
            });
            return;
          }

          const producto = data[0];
          const stockDisponible = parseInt(producto.stock, 10) || 0;

          // 1) Busco si ya existe una fila con este producto
          const selectorFila = `tr[data-idproducto="${producto.idproducto}"]`;
          const filaExistente = tabla.querySelector(selectorFila);

          if (filaExistente) {
            // 2) Si ya estaba en el detalle, leo su input de cantidad
            const qtyInput = filaExistente.querySelector(".cantidad-input");
            const currentQty = parseInt(qtyInput.value, 10) || 0;
            const maxAllowed = parseInt(qtyInput.getAttribute("max"), 10) || 0;

            if (currentQty >= maxAllowed) {
              // YA está en stock máximo → muestro alerta
              Swal.fire({
                icon: "warning",
                title: "Stock insuficiente",
                text: `Solo hay ${maxAllowed} unidades disponibles.`,
                confirmButtonText: "Entendido",
                allowOutsideClick: false,
              });
            } else {
              // Subo 1 paso y disparo 'input' para que se ejecute actualizarLinea()
              qtyInput.stepUp();
              qtyInput.dispatchEvent(new Event("input"));
            }
          } else {
            // 3) No existía en la tabla: agrego solo si stockDisponible ≥ 1
            if (stockDisponible < 1) {
              Swal.fire({
                icon: "warning",
                title: "Sin stock",
                text: "No hay unidades disponibles de este producto.",
                confirmButtonText: "Aceptar",
                allowOutsideClick: false,
              });
            } else {
              // Relleno los campos y “simulo” el clic en “Agregar producto”
              inputProductElement.value = producto.subcategoria_producto;
              inputPrecio.value = parseFloat(producto.precio).toFixed(2);
              inputStock.value = stockDisponible;
              inputCantidad.value = 1;
              inputDescuento.value = 0;

              selectedProduct = {
                idproducto: producto.idproducto,
                subcategoria_producto: producto.subcategoria_producto,
                precio: parseFloat(producto.precio),
                stock: stockDisponible,
              };

              agregarProductoBtn.click();
            }
          }
        })
        .catch((err) => {
          console.error("Error al procesar escaneo:", err);
          Swal.fire({
            icon: "error",
            title: "Error al buscar producto",
            text: "Intenta nuevamente.",
            confirmButtonText: "Aceptar",
            allowOutsideClick: false,
          });
        })
        .finally(() => {
          this.value = "";
          this.focus();
        });
    }
  });

  // --- Generación de Serie y Comprobante ---
  /*  function generateNumber(prefix) {
     return `${prefix}${String(Math.floor(Math.random() * 100)).padStart(3, "0")}`;
   }
 
   function generateComprobanteNumber(prefix) {
     return `${prefix}-${String(Math.floor(Math.random() * 1e7)).padStart(7, "0")}`;
   }
 
   function inicializarCampos() {
     const tipo = document.querySelector('input[name="tipo"]:checked').value;
     let prefijoSerie, prefijoComprobante;
     switch (tipo) {
       case "factura":
         prefijoSerie = "F";
         prefijoComprobante = "F";
         break;
       case "boleta":
         prefijoSerie = "B";
         prefijoComprobante = "B";
         break;
       case "orden de trabajo":
         prefijoSerie = "OT";
         prefijoComprobante = "OT";
         break;
       default:
         prefijoSerie = "";
         prefijoComprobante = "";
     }
     numSerieInput.value = generateNumber(prefijoSerie);
     numComInput.value = generateComprobanteNumber(prefijoComprobante);
   }
   tipoInputs.forEach((i) => i.addEventListener("change", inicializarCampos));
   inicializarCampos(); */
  // --- Navegación con Enter entre campos de producto ---
  inputPrecio.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      inputCantidad.focus();
    }
  });
  inputCantidad.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      inputDescuento.focus();
    }
  });
  inputDescuento.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      agregarProductoBtn.focus(); // o : agregarProductoBtn.click();
    }
  });
  // --- Guardar Venta ---
  btnFinalizarVenta.addEventListener("click", function (e) {
    e.preventDefault();

    // — Validación de stock antes de todo —
    for (const tr of tabla.querySelectorAll("tr")) {
      const nombreProd = tr.cells[1].textContent.trim();
      const qtyInput = tr.querySelector(".cantidad-input");
      const qty = parseInt(qtyInput.value, 10) || 0;
      const maxAttr = qtyInput.getAttribute("max");

      const maxStock = maxAttr !== null ? parseInt(maxAttr, 10) : Infinity;

      if (maxStock === 0) {
        return Swal.fire(
          "Error de stock",
          `El producto "${nombreProd}" no tiene unidades disponibles.`,
          "error"
        );
      }
    }

    if (
      tabla.querySelectorAll("tr").length === 0 &&
      detalleServicios.length === 0
    ) {
      return showToast(
        "Agrega al menos un producto o servicio.",
        "WARNING",
        2000
      );
    }

    Swal.fire({
      title: "¿Deseas guardar la venta?",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Aceptar",
      cancelButtonText: "Cancelar",
      confirmButtonColor: "#28a745",
      cancelButtonColor: "#d33",
    }).then((result) => {
      if (!result.isConfirmed) return;

      // Deshabilitar botón y cambiar texto
      btnFinalizarVenta.disabled = true;
      btnFinalizarVenta.textContent = "Guardando...";

      // Reconstruir array de productos leyendo el DOM
      const productosParaEnviar = Array.from(tabla.querySelectorAll("tr")).map(
        (tr) => {
          return {
            idproducto: parseInt(tr.dataset.idproducto, 10),
            precio: parseFloat(tr.querySelector(".precio-texto").textContent),
            descuento: parseFloat(
              tr.querySelector(".descuento-texto").textContent
            ),
            cantidad: parseInt(tr.querySelector(".cantidad-input").value, 10),
          };
        }
      );

      // Armar payload completo
      const tipoSeleccionado = document.querySelector(
        'input[name="tipo"]:checked'
      ).value;
      const data = {
        tipocom: tipoSeleccionado,
        fechahora: fechaInput.value.trim(),
        fechaingreso: null,
        numserie: numSerieInput.value.trim(),
        numcom: numComInput.value.trim(),
        moneda: monedaSelect.value,
        idpropietario: hiddenIdPropietario.value,
        idcliente: hiddenIdCliente.value || null,
        idvehiculo: vehiculoSelect.value ? +vehiculoSelect.value : null,
        kilometraje: parseFloat(kmInput.value) || 0,
        observaciones: obsField.value.trim(),
        ingresogrua: gruField.checked ? 1 : 0,
        productos: productosParaEnviar,
        servicios: detalleServicios,
      };

      // Enviar al backend
      fetch("http://localhost/Fix360/app/controllers/Venta.controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      })
        .then(async (res) => {
          const text = await res.text();
          return JSON.parse(text);
        })
        .then((json) => {
          if (json.status === "success") {
            const msg =
              tipoSeleccionado === "orden de trabajo"
                ? "Guardado con éxito. Orden de Trabajo"
                : `Guardado con éxito. Venta #${json.idventa}`;
            showToast(msg, "SUCCESS", 1500);
            setTimeout(() => (location.href = "listar-ventas.php"), 1500);
          } else {
            Swal.fire(
              "Error",
              json.message || "No se pudo registrar.",
              "error"
            );
          }
        })
        .catch((err) => {
          console.error("Error en fetch:", err);
          Swal.fire("Error", err.message, "error");
        })
        .finally(() => {
          btnFinalizarVenta.disabled = false;
          btnFinalizarVenta.textContent = "Guardar";
        });
    });
  });
  // —– NUEVO: cuando cambie vehículo, traemos último kilometraje —–
  if (vehiculoSelect) {
    vehiculoSelect.addEventListener("change", function () {
      const idVeh = this.value;
      if (idVeh) {
        fetchUltimoKilometraje(idVeh);
      } else {
        // Si no hay vehículo seleccionado, limpiamos el campo de kilometraje
        kmInput.value = "";
        prevKilometraje = null;
      }
    });
  }

  // —– NUEVO: validación extra en el campo de kilometraje—–
  if (kmInput) {
    kmInput.addEventListener("change", () => {
      const nuevo = parseFloat(kmInput.value);
      if (prevKilometraje !== null && nuevo < prevKilometraje) {
        alert(
          `El kilometraje no puede ser menor que el último registrado (${prevKilometraje}).`
        );
        kmInput.value = prevKilometraje;
      }
    });
  }
});
document.addEventListener("click", function (e) {
  const inputFecha = document.getElementById("fechaIngreso");
  const btnPermitir = document.getElementById("btnPermitirFechaPasada");

  const hoy = new Date().toISOString().split("T")[0];
  inputFecha.min = hoy;

  btnPermitir.addEventListener("click", () => {
    inputFecha.removeAttribute("min");
    btnPermitir.disabled = true;
    btnPermitir.innerHTML =
      '<i class="fa-solid fa-unlock-keyhole text-success"></i>';
    btnPermitir.title = "Fechas pasadas habilitadas";
  });
});
