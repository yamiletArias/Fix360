document.addEventListener("DOMContentLoaded", function () {
  // Variables y elementos
  /* const inputCliente = document.getElementById("cliente"); */
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
  const btnAgregarServicio = document.getElementById("btnAgregarServicio");
  const selectServicio = document.getElementById("servicio");
  const selectMecanico = document.getElementById("mecanico");
  const inputPrecioServicio = document.getElementById("precioServicio");
  const fechaInput = document.getElementById("fechaIngreso");
  const monedaSelect = document.getElementById("moneda");

  // --- Funciones auxiliares ---
  function calcularTotales() {
    let totalImporte = 0;
    let totalDescuento = 0;

    // 1) Productos
    tabla.querySelectorAll("tr").forEach((fila) => {
      const cantidad =
        parseFloat(fila.querySelector(".cantidad-input").value) || 0;
      const precio = parseFloat(fila.children[2].textContent) || 0;
      const descuento = parseFloat(fila.children[4].textContent) || 0;
      const importeLinea = (precio - descuento) * cantidad;
      totalImporte += importeLinea;
      totalDescuento += descuento * cantidad;
    });

    // 2) Servicios
    const totalServicios = detalleServicios.reduce(
      (sum, s) => sum + s.precio,
      0
    );
    totalImporte += totalServicios; // (en los servicios no hay descuento aparte, así que no tocamos totalDescuento)

    // 3) IGV (18%) y neto
    const igv = totalImporte - totalImporte / 1.18;
    const neto = totalImporte / 1.18;

    // 4) Pinto en el footer
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

  //cotizacion
  const API_BASE = window.FIX360_BASE_URL + 'app/controllers/';
  const params = new URLSearchParams(window.location.search);
  const cotId = params.get('id');
  if (cotId) {
    // — 1) CARGAR CABECERA —
    fetch(`${API_BASE}cotizacion.controller.php?action=getSoloCliente&idcotizacion=${cotId}`)
      .then(r => r.json())
      .then(data => {
        // 1) Cliente (para vehículos)
        hiddenIdCliente.value = data.idcliente;
        // 2) Propietario (para FK)
        hiddenIdPropietario.value = data.idcliente;
        // 3) Nombre del propietario en el input
        inputProp.value = data.cliente;
        // 4) Disparamos carga de vehículos sobre hiddenIdCliente
        hiddenIdCliente.dispatchEvent(new Event('change'));
      })
      .catch(console.error);

    // — 2) CARGAR DETALLE DE PRODUCTOS —
    fetch(`${API_BASE}Detcotizacion.controller.php?idcotizacion=${cotId}`)
      .then(r => r.json())
      .then(items => {
        items.forEach(item => {
          const precio = parseFloat(item.precio);
          const cantidad = parseFloat(item.cantidad);
          const descuento = parseFloat(item.descuento);
          const importe = (precio - descuento) * cantidad;

          // CREAR FILA EN LA TABLA igual que si hubieras pulsado “Agregar”
          const tr = document.createElement('tr');
          tr.dataset.idproducto = item.idproducto;
          tr.innerHTML = `
            <td>0</td>
            <td>${item.producto}</td>
            <td>${precio.toFixed(2)}</td>
            <td>
              <div class="input-group input-group-sm cantidad-control" style="width:8rem;">
                <button class="btn btn-outline-secondary btn-decrement" type="button">–</button>
                <input type="number"
                      class="form-control text-center p-0 border-0 bg-transparent cantidad-input"
                      value="${cantidad}"
                      min="1"
                      max="${item.stock}">
                <button class="btn btn-outline-secondary btn-increment" type="button">＋</button>
              </div>
            </td>
            <td>${descuento.toFixed(2)}</td>
            <td class="importe-cell">${importe.toFixed(2)}</td>
            <td><button class="btn btn-danger btn-sm btn-quitar">X</button></td>`;

          // Adjunta aquí tus listeners de incrementar, decrementar y quitar,
          // idénticos a los de tu handler de “Agregar Producto”

          tabla.appendChild(tr);
          detalleVenta.push({
            idproducto: item.idproducto,
            producto: item.producto,
            precio,
            cantidad,
            descuento,
            importe: importe.toFixed(2)
          });
        });

        // Finalmente, renumera y recalcula totales:
        actualizarNumeros();
        calcularTotales();
      })
      .catch(console.error);
  }

  btnAgregarServicio.addEventListener("click", () => {
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
    });
    tablaServ.appendChild(tr);

    // 3) Limpia el campo de precio para la próxima vez
    inputPrecioServicio.value = "";
    calcularTotales();
  });

  // --- Agregar Producto al Detalle ---
  agregarProductoBtn.addEventListener("click", () => {
    const idp = selectedProduct.idproducto;
    const nombre = inputProductElement.value.trim();
    /* const nombre = inputProductElement.value; */
    const precio = parseFloat(inputPrecio.value);
    const cantidad = parseFloat(inputCantidad.value);
    if (inputDescuento.value.trim() === "") {
      inputDescuento.value = "0";
    }
    const descuento = parseFloat(inputDescuento.value);

    if (!idp || nombre !== selectedProduct.subcategoria_producto) {
      alert("Ese producto no existe. Elige uno de la lista.");
      return resetCamposProducto();
    }
    if (!nombre || isNaN(precio) || isNaN(cantidad)) {
      return alert("Completa todos los campos correctamente.");
    }

    const stockDisponible = selectedProduct.stock || 0;
    /* const stockDisponible = parseFloat(inputStock.value) || 0; */
    if (cantidad > stockDisponible) {
      alert(
        `No puedes pedir ${cantidad} unidades; solo hay ${stockDisponible} en stock.`
      );
      document.getElementById("cantidad").value = stockDisponible;
      return;
    }

    // Validación de descuento unitario
    if (descuento > precio) {
      alert("El descuento unitario no puede ser mayor que el precio unitario.");
      document.getElementById("descuento").value = "";
      return;
    }

    if (detalleVenta.some((d) => d.idproducto === idp)) {
      alert("Este producto ya ha sido agregado.");
      return resetCamposProducto();
    }

    /* const importe = (precio * cantidad) - descuento; */
    const netoUnit = precio - descuento;
    const importe = netoUnit * cantidad;

    const fila = document.createElement("tr");
    // le pongo data-idproducto a la fila
    fila.dataset.idproducto = idp;
    fila.innerHTML = `
        <td>${tabla.rows.length + 1}</td>
        <td>${nombre}</td>
        <td>${precio.toFixed(2)}</td>
        <td>
          <div class="input-group input-group-sm cantidad-control" style="width: 8rem;">
            <button class="btn btn-outline-secondary btn-decrement" type="button">-</button>
            <input type="number"
                  class="form-control text-center p-0 border-0 bg-transparent cantidad-input"
                  value="${cantidad}"
                  min="1"
                  max="${stockDisponible}">
            <button class="btn btn-outline-secondary btn-increment" type="button">+</button>
          </div>
        </td>
        <td>${descuento.toFixed(2)}</td>
        <td class="importe-cell">${importe.toFixed(2)}</td>

        <td><button class="btn btn-danger btn-sm btn-quitar">X</button></td>
      `;
    // al crear el botón de quitar
    fila.querySelector(".btn-quitar").addEventListener("click", () => {
      // 1) quito la fila del DOM
      fila.remove();
      // 2) quito del array usando el idproducto guardado en la fila
      const idElim = parseInt(fila.dataset.idproducto, 10);
      const idx = detalleVenta.findIndex((d) => d.idproducto === idElim);
      if (idx >= 0) detalleVenta.splice(idx, 1);
      // 3) renumero y recalculo
      actualizarNumeros();
      calcularTotales();
    });

    tabla.appendChild(fila);

    const decBtn = fila.querySelector(".btn-decrement");
    const incBtn = fila.querySelector(".btn-increment");
    const qtyInput = fila.querySelector(".cantidad-input");
    const importeCell = fila.querySelector(".importe-cell");

    function actualizarLinea() {
      let qty = parseInt(qtyInput.value, 10) || 1;
      //  capea entre 1 y stockDisponible
      if (qty < 1) qty = 1;
      if (qty > stockDisponible) qty = stockDisponible;
      qtyInput.value = qty;

      // recalcula importe neto para esta línea
      const netoUnit = precio - descuento;
      const nuevoImporte = netoUnit * qty;
      importeCell.textContent = nuevoImporte.toFixed(2);

      // actualiza el array detalleVenta
      const idx = detalleVenta.findIndex((d) => d.idproducto === idp);
      if (idx >= 0) {
        detalleVenta[idx].cantidad = qty;
        detalleVenta[idx].importe = nuevoImporte.toFixed(2);
      }

      // renumera y recalcula totales generales
      calcularTotales();
    }

    // incr/decr
    decBtn.addEventListener("click", () => {
      qtyInput.stepDown();
      actualizarLinea();
    });
    incBtn.addEventListener("click", () => {
      qtyInput.stepUp();
      actualizarLinea();
    });
    // y si alguien escribe un número directamente
    qtyInput.addEventListener("input", actualizarLinea);
    // 3) resto de tu listener de quitar…
    fila.querySelector(".btn-quitar").addEventListener("click", () => {
      fila.remove();
      const idx = detalleVenta.findIndex((d) => d.idproducto === idp);
      if (idx >= 0) detalleVenta.splice(idx, 1);
      actualizarNumeros();
      calcularTotales();
    });

    detalleVenta.push({
      idproducto: idp,
      producto: nombre,
      precio,
      cantidad,
      descuento,
      importe: importe.toFixed(2),
    });
    resetCamposProducto();
    calcularTotales();
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

  // Función de debounce para evitar demasiadas llamadas en tiempo real
  function debounce(func, delay) {
    let timeout;
    return function (...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), delay);
    };
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

  // Función para mostrar opciones de productos (autocompletado)
  function mostrarOpcionesProducto(input) {
    cerrarListas();
    if (!input.value) return;
    const searchTerm = input.value;
    fetch(
      `http://localhost/Fix360/app/controllers/Venta.controller.php?q=${searchTerm}&type=producto`
    )
      .then((response) => response.json())
      .then((data) => {
        const itemsDiv = document.createElement("div");
        itemsDiv.setAttribute("id", "autocomplete-list-producto");
        itemsDiv.setAttribute("class", "autocomplete-items");
        input.parentNode.appendChild(itemsDiv);
        if (data.length === 0) {
          const noResultsDiv = document.createElement("div");
          noResultsDiv.textContent = "No se encontraron productos";
          itemsDiv.appendChild(noResultsDiv);
          return;
        }
        data.forEach(function (producto) {
          const optionDiv = document.createElement("div");
          optionDiv.textContent = producto.subcategoria_producto;
          optionDiv.addEventListener("click", function () {
            input.value = producto.subcategoria_producto;
            inputPrecio.value = producto.precio;
            inputStock.value = producto.stock;
            inputCantidad.value = 1;
            inputDescuento.value = 0;

            inputDescuento.addEventListener("focus", function () {
              if (inputDescuento.value === "0") {
                inputDescuento.value = "";
              }
            });

            inputDescuento.addEventListener("keydown", function (e) {
              if (
                inputDescuento.value === "0" &&
                e.key >= "0" &&
                e.key <= "9"
              ) {
                inputDescuento.value = "";
              }
            });

            selectedProduct = {
              idproducto: producto.idproducto,
              subcategoria_producto: producto.subcategoria_producto,
              precio: producto.precio,
              stock: producto.stock,
            };
            inputStock.value = selectedProduct.stock;
            cerrarListas();
          });
          itemsDiv.appendChild(optionDiv);
        });
        // Habilitar navegación por teclado en la lista de productos
        agregaNavegacion(input, itemsDiv);
      })
      .catch((err) => console.error("Error al obtener los productos: ", err));
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
  inputProductElement.addEventListener("input", function () {
    debouncedMostrarOpcionesProducto(this);
  });
  inputProductElement.addEventListener("click", function () {
    debouncedMostrarOpcionesProducto(this);
  });
  document.addEventListener("click", function (e) {
    cerrarListas(e.target);
  });

  // --- Generación de Serie y Comprobante ---
  function generateNumber(type) {
    return `${type}${String(Math.floor(Math.random() * 100)).padStart(3, "0")}`;
  }

  function generateComprobanteNumber(type) {
    return `${type}-${String(Math.floor(Math.random() * 1e7)).padStart(7, "0")}`;
  }

  function inicializarCampos() {
    const tipo = document.querySelector('input[name="tipo"]:checked').value;
    if (tipo === "boleta") {
      numSerieInput.value = generateNumber("B");
      numComInput.value = generateComprobanteNumber("B");
    } else {
      numSerieInput.value = generateNumber("F");
      numComInput.value = generateComprobanteNumber("F");
    }
  }
  tipoInputs.forEach((i) => i.addEventListener("change", inicializarCampos));
  inicializarCampos();
  // --- Navegación con Enter entre campos de producto ---
  inputProductElement.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      inputPrecio.focus();
    }
  });
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

    // 0) Capturo hiddenIdCliente (¡muy importante!)
    const hiddenIdCliente = document.getElementById("hiddenIdCliente");

    if (detalleVenta.length === 0 && detalleServicios.length === 0) {
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

      // Deshabilito el botón
      btnFinalizarVenta.disabled = true;
      btnFinalizarVenta.textContent = "Guardando...";

      // 1) Construyo el objeto de datos
      const data = {
        tipocom: document.querySelector('input[name="tipo"]:checked').value,
        fechahora: fechaInput.value.trim(),
        fechaingreso: null,
        numserie: numSerieInput.value.trim(),
        numcom: numComInput.value.trim(),
        moneda: monedaSelect.value,
        idpropietario: +document.getElementById("hiddenIdPropietario").value,
        idcliente: +hiddenIdCliente.value,
        idvehiculo: vehiculoSelect.value ? +vehiculoSelect.value : null,
        kilometraje:
          parseFloat(document.getElementById("kilometraje").value) || 0,
        observaciones: document.getElementById("observaciones").value.trim(),
        ingresogrua: document.getElementById("ingresogrua").checked ? 1 : 0,
        productos: detalleVenta,
        servicios: detalleServicios,
      };

      console.log("Payload a enviar:", data);

      // 2) Disparo el fetch
      fetch("http://localhost/Fix360/app/controllers/Venta.controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      })
        .then(async (res) => {
          const text = await res.text(); // leo texto bruto
          console.log("Respuesta HTTP:", res.status, text);
          try {
            return JSON.parse(text); // intento parsear JSON
          } catch {
            throw new Error("Respuesta no es JSON válido");
          }
        })
        .then((json) => {
          if (json.status === "success") {
            showToast(
              "Guardado con éxito. Venta #" +
              json.idventa +
              (json.idorden ? ", Orden #" + json.idorden : ""),
              "SUCCESS",
              1500
            );
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
});
/* document.addEventListener('DOMContentLoaded', () => {
  const cotId = new URLSearchParams(window.location.search).get('id');
  if (!cotId) return;

  const hiddenIdCliente = document.getElementById("hiddenIdCliente");
  const inputProp       = document.getElementById("propietario");
  if (!hiddenIdCli || !inputProp) return;

  fetch(`<?= SERVERURL ?>app/controllers/cotizacion.controller.php?action=getSoloCliente&idcotizacion=${cotId}`)
    .then(res => res.json())
    .then(data => {
      hiddenIdCliente.value = data.idcliente;
      inputProp.value = data.cliente;
      hiddenIdCliente.dispatchEvent(new Event('change'));
    })
    .catch(console.error);
}); */