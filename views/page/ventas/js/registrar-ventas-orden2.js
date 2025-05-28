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
      // Lee precio desde input
      const precioInput = fila.querySelector(".precio-input");
      const precio = precioInput
        ? parseFloat(precioInput.value) || 0
        : 0;

      // Lee cantidad desde input
      const cantidad = parseFloat(
        fila.querySelector(".cantidad-input").value
      ) || 0;

      // Lee descuento desde input
      const descuentoInput = fila.querySelector(".descuento-input");
      const descuento = descuentoInput
        ? parseFloat(descuentoInput.value) || 0
        : 0;

      const importeLinea = (precio - descuento) * cantidad;
      totalImporte += importeLinea;
      totalDescuento += descuento * cantidad;
    });

    // 2) Servicios (igual que antes)
    const totalServicios = detalleServicios.reduce((sum, s) => sum + s.precio, 0);
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

  //cotizacion
  const API_BASE = window.FIX360_BASE_URL + "app/controllers/";
  const params = new URLSearchParams(window.location.search);
  const cotId = params.get("id");
  if (cotId) {
    // — 1) CARGAR CABECERA —
    fetch(
      `${API_BASE}cotizacion.controller.php?action=getSoloCliente&idcotizacion=${cotId}`
    )
      .then((r) => r.json())
      .then((data) => {
        // 1) Cliente (para vehículos)
        hiddenIdCliente.value = data.idcliente;
        // 2) Propietario (para FK)
        hiddenIdPropietario.value = data.idcliente;
        // 3) Nombre del propietario en el input
        inputProp.value = data.cliente;
        // 4) Disparamos carga de vehículos sobre hiddenIdCliente
        hiddenIdCliente.dispatchEvent(new Event("change"));
      })
      .catch(console.error);

    // — 2) CARGAR DETALLE DE PRODUCTOS —
    fetch(`${API_BASE}Detcotizacion.controller.php?idcotizacion=${cotId}`)
      .then((r) => r.json())
      .then((items) => {
        items.forEach((item) => {
          const precio = parseFloat(item.precio);
          const cantidad = parseFloat(item.cantidad);
          const descuento = parseFloat(item.descuento);
          const importe = (precio - descuento) * cantidad;

          // CREAR FILA EN LA TABLA igual que si hubieras pulsado “Agregar”
          const tr = document.createElement("tr");
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
          tabla.appendChild(tr);
          detalleVenta.push({
            idproducto: item.idproducto,
            producto: item.producto,
            precio,
            cantidad,
            descuento,
            importe: importe.toFixed(2),
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
    if (isNaN(cantidad) || cantidad < 1) {
      alert("La cantidad debe ser un número mayor o igual a 1.");
      inputCantidad.value = 1;
      inputCantidad.focus();
      return;
    }
    if (inputDescuento.value.trim() === "") {
      inputDescuento.value = "0";
    }
    /* inputPrecio.addEventListener("blur", () => {
      const val = parseFloat(inputPrecio.value);
      if (isNaN(val) || val <= 0) {
        alert("Precio inválido.");
        // Asegúrate de que selectedProduct.precio sea un número
        inputPrecio.value = parseFloat(selectedProduct.precio).toFixed(2);
      }
    }); */
    const descuento = parseFloat(inputDescuento.value);

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
    if (cantidad <= 0) {
      alert("La cantidad debe ser mayor que cero.");
      inputCantidad.value = 1; // reset al mínimo
      return;
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
    if (descuento < 0) {
      alert("El descuento no puede ser negativo.");
      inputDescuento.value = 0;
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
  <td>
    <input type="number"
          class="form-control form-control-sm precio-input"
          value="${parseInt(precio)}"
          min="1"
          step="1"
          style="width:5rem;">
  </td>
  <td>
    <div class="input-group input-group-sm cantidad-control" style="width: 8rem;">
      <button class="btn btn-outline-secondary btn-decrement" type="button">-</button>
        <input type="number"
          class="form-control text-center p-0 border-0 bg-transparent cantidad-input"
          value="${parseInt(cantidad)}"
          min="1"
          step="1"
          max="${stockDisponible}">
      <button class="btn btn-outline-secondary btn-increment" type="button">+</button>
    </div>
  </td>
  <td>
    <input type="number"
          class="form-control form-control-sm descuento-input"
          value="${parseInt(descuento)}"
          min="0"
          step="1"
          style="width:5rem;">
    </td>
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
    tabla.addEventListener("input", (e) => {
      // Precio y cantidad deben mostrarse como enteros
      if (e.target.classList.contains("precio-input") ||
        e.target.classList.contains("cantidad-input")) {
        let valor = parseInt(e.target.value) || 0;
        e.target.value = valor;
      }
    });

    const decBtn = fila.querySelector(".btn-decrement");
    const incBtn = fila.querySelector(".btn-increment");
    const priceInput = fila.querySelector(".precio-input");
    const qtyInput = fila.querySelector(".cantidad-input");
    const importeCell = fila.querySelector(".importe-cell");
    const descInput = fila.querySelector(".descuento-input");
    const discountInput = fila.querySelector(".descuento-input");

    function actualizarLinea() {
      let qty = parseFloat(qtyInput.value) || 1;
      if (qty < 1) qty = 1;
      if (qty > stockDisponible) qty = stockDisponible;
      qtyInput.value = qty.toFixed(2);

      let precioNuevo = parseFloat(priceInput.value) || 0;
      if (precioNuevo < 0.01) precioNuevo = 0.01;
      priceInput.value = precioNuevo.toFixed(2);

      let descuentoNuevo = parseFloat(discountInput.value) || 0;
      if (descuentoNuevo < 0) descuentoNuevo = 0;
      if (descuentoNuevo > precioNuevo) descuentoNuevo = precioNuevo;
      discountInput.value = descuentoNuevo.toFixed(2);

      const netoUnit = precioNuevo - descuentoNuevo;
      const nuevoImporte = netoUnit * qty;
      importeCell.textContent = nuevoImporte.toFixed(2);

      const idx = detalleVenta.findIndex(d => d.idproducto === idp);
      if (idx >= 0) {
        detalleVenta[idx].cantidad = qty;
        detalleVenta[idx].precio = precioNuevo;
        detalleVenta[idx].descuento = descuentoNuevo;
        detalleVenta[idx].importe = nuevoImporte.toFixed(2);
      }
      calcularTotales();
    }
    priceInput.addEventListener("input", actualizarLinea);
    qtyInput.addEventListener("input", actualizarLinea);
    discountInput.addEventListener("input", actualizarLinea);
    descInput.addEventListener("input", actualizarLinea);
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
   *
   * @param {HTMLInputElement} input  El <input> donde se escribe el término de búsqueda.
   */
  function mostrarOpcionesProducto(input) {
    cerrarListas();
    const termino = input.value.trim();
    if (!termino) return;

    // Detectar si es “solo dígitos” (scanner) o no:
    const esSoloDigitos = /^\d+$/.test(termino);

    fetch(
      `http://localhost/Fix360/app/controllers/Venta.controller.php?q=${encodeURIComponent(termino)}&type=producto`
    )
      .then((response) => response.json())
      .then((data) => {
        if (!Array.isArray(data) || data.length === 0) {
          showToast("No se encontraron productos.", "WARNING", 1500);
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
                    `Has ingresado un precio menor al original (${originalPrecio.toFixed(2)}). ¿Deseas continuar?`
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
  // Creamos una versión debounced de mostrarOpcionesProducto:
  const debouncedMostrarOpcionesProducto = debounce(mostrarOpcionesProducto, 500);

  // Cuando el usuario escribe (o hace clic), disparamos el autocompletado “normal” con debounce
  // 1) Listener de ‘input’: sólo para búsquedas manuales con debounce:
  inputProductElement.addEventListener("input", function () {
    const termino = this.value.trim();
    const esSoloDigitos = /^\d+$/.test(termino);

    if (!esSoloDigitos) {
      // Mientras escribes a mano (letras/espacios) usamos el debounce
      debouncedMostrarOpcionesProducto(this);
    }
    // Si es sólo dígitos, NO hacemos nada aquí; esperamos al ENTER
  });

  // 2) Listener de ‘keyup’: sólo para scanner (ENTER):
  inputProductElement.addEventListener("keyup", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      // 1) limpias la lista y cancelas el debounce
      cerrarListas();
      debouncedMostrarOpcionesProducto.cancel();

      const codigo = this.value.trim();
      if (!codigo) return;

      // 2) aquí solo lógica de scanner
      fetch(`${window.FIX360_BASE_URL}app/controllers/Venta.controller.php?q=${encodeURIComponent(codigo)}&type=producto`)
        .then(r => r.json())
        .then(data => {
          if (Array.isArray(data) && data.length) {
            const producto = data[0];
            // rellenas los campos
            inputProductElement.value = producto.subcategoria_producto;
            inputPrecio.value = parseFloat(producto.precio).toFixed(2);
            inputStock.value = producto.stock;
            inputCantidad.value = 1;
            inputDescuento.value = 0;
            selectedProduct = {
              idproducto: producto.idproducto,
              subcategoria_producto: producto.subcategoria_producto,
              precio: parseFloat(producto.precio),
              stock: producto.stock
            };
            // y disparas tu “agregar”
            agregarProductoBtn.click();
          } else {
            showToast("No se encontraron productos.", "WARNING", 1500);
          }
        })
        .finally(() => {
          // borra y fócate **después** de agregar
          this.value = "";
          this.focus();
        });
    }
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
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("btn-increment")) {
    const input = e.target.parentElement.querySelector(".cantidad-input");
    input.value = parseInt(input.value || "0") + 1;
    input.dispatchEvent(new Event("input"));
  }

  if (e.target.classList.contains("btn-decrement")) {
    const input = e.target.parentElement.querySelector(".cantidad-input");
    const newVal = parseInt(input.value || "1") - 1;
    input.value = newVal > 0 ? newVal : 1;
    input.dispatchEvent(new Event("input"));
  }
  const inputFecha = document.getElementById("fechaIngreso");
  const btnPermitir = document.getElementById("btnPermitirFechaPasada");

  const hoy = new Date().toISOString().split("T")[0];
  inputFecha.min = hoy;

  btnPermitir.addEventListener("click", () => {
    inputFecha.removeAttribute("min");
    btnPermitir.disabled = true;
    btnPermitir.innerHTML = '<i class="fa-solid fa-unlock-keyhole text-success"></i>';
    btnPermitir.title = "Fechas pasadas habilitadas";
  });
});
