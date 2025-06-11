document.addEventListener("DOMContentLoaded", function () {
  // Variables y elementos
  const proveedorSelect = document.getElementById("proveedor");
  const inputProductElement = document.getElementById("producto");
  const numSerieInput = document.getElementById("numserie");
  const numComInput = document.getElementById("numcom");
  const monedaSelect = document.getElementById("moneda");
  const tipoInputs = document.querySelectorAll('input[name="tipo"]');
  const agregarProductoBtn = document.querySelector("#agregarProducto");
  const tabla = document.querySelector("#tabla-detalle-compra tbody");
  const btnFinalizarCompra = document.getElementById("btnFinalizarCompra");
  // Nuevos elementos de input para los detalles del producto
  const inputStock = document.getElementById("stock");
  const inputPrecio = document.getElementById("preciocompra");
  const inputCantidad = document.getElementById("cantidadcompra");
  const inputDescuento = document.getElementById("descuento");
  const fechaInput = document.getElementById("fechaIngreso");
  // 1) Declárala UNA sola vez, arriba de todo:
  const detalleCompra = [];
  /*     numSerieInput.value = "";
    numComInput.value = ""; */
  // — Función que decide si habilitar o no el botón “Guardar” —
  function actualizarEstadoBotonGuardar() {
    const proveedorValido   = proveedorSelect.selectedIndex > 0;
    const tieneProductos    = detalleCompra.length > 0;
    const serieValida       = numSerieInput.value.trim() !== "";
    const comprobanteValido = numComInput.value.trim() !== "";
    btnFinalizarCompra.disabled = !(
      proveedorValido &&
      tieneProductos &&
      serieValida &&
      comprobanteValido
    );
  }

  // Listeners de inputs básicos
  proveedorSelect.addEventListener("change", actualizarEstadoBotonGuardar);
  numSerieInput.addEventListener("input",   actualizarEstadoBotonGuardar);
  numComInput.addEventListener("input",     actualizarEstadoBotonGuardar);


  // --- Funciones auxiliares ---
  function calcularTotales() {
    let totalImporte = 0;
    let totalDescuento = 0;

    // 1) Recorremos cada fila del detalle de compra
    tabla.querySelectorAll("tr").forEach((fila) => {
      // – Cantidad desde el input
      const cantidadLinea =
        parseFloat(fila.querySelector(".cantidad-input").value) || 0;

      // – Precio unitario desde input .precio-input
      const precioInputFila = fila.querySelector(".precio-input");
      const precioUnitario = precioInputFila
        ? parseFloat(precioInputFila.value) || 0
        : 0;

      // – Descuento unitario desde input .descuento-input
      const descuentoInputFila = fila.querySelector(".descuento-input");
      const descUnitario = descuentoInputFila
        ? parseFloat(descuentoInputFila.value) || 0
        : 0;

      // – Calculamos importe de esa línea
      const importeLinea = (precioUnitario - descUnitario) * cantidadLinea;
      totalImporte += importeLinea;
      totalDescuento += descUnitario * cantidadLinea;
    });

    // 2) IGV 18% y neto
    const igv = totalImporte - totalImporte / 1.18;
    const neto = totalImporte / 1.18;

    // 3) Mostrar resultados en los inputs correspondientes
    document.getElementById("neto").value = neto.toFixed(2);
    document.getElementById("totalDescuento").value = totalDescuento.toFixed(2);
    document.getElementById("igv").value = igv.toFixed(2);
    document.getElementById("total").value = totalImporte.toFixed(2);
  }
  // Función para evitar duplicados en productos
  function estaDuplicado(idproducto = 0) {
    let duplicado = false;
    let i = 0;
    while (i < detalleCompra.length && !duplicado) {
      if (detalleCompra[i].idproducto == idproducto) {
        duplicado = true;
      }
      i++;
    }
    return duplicado;
  }
  // Manejador del botón "Agregar" para añadir producto al detalle de compra
  agregarProductoBtn.addEventListener("click", function () {
    const idp = selectedProduct.idproducto;
    const nomProducto = inputProductElement.value.trim();
    const precioProducto = parseFloat(inputPrecio.value);
    const cantidadProducto = parseFloat(inputCantidad.value);
    if (inputDescuento.value.trim() === "") {
      inputDescuento.value = "0";
    }
    const descuentoProducto = parseFloat(inputDescuento.value) || 0;

    // — VALIDACIONES —
    if (!nomProducto || isNaN(precioProducto) || isNaN(cantidadProducto)) {
      return alert("Por favor, complete todos los campos correctamente.");
    }
    if (isNaN(precioProducto) || precioProducto <= 0) {
      return alert("Ingresa un precio válido mayor que cero.");
    }
    if (cantidadProducto <= 0) {
      alert("La cantidad debe ser mayor que cero.");
      inputCantidad.value = 1; // reset al mínimo
      return;
    }
    if (descuentoProducto > precioProducto) {
      alert("El descuento unitario no puede ser mayor que el precio unitario.");
      inputDescuento.value = "";
      return;
    }
    if (descuentoProducto < 0) {
      alert("El descuento no puede ser negativo.");
      inputDescuento.value = 0;
      return;
    }
    // Revisar duplicados
    if (estaDuplicado(selectedProduct.idproducto)) {
      alert("Este producto ya ha sido agregado.");
      return resetCamposProducto();
    }

    // — Cálculo de importe total de la línea —
    const netoUnit = precioProducto - descuentoProducto;
    const importeTotal = netoUnit * cantidadProducto;

    // — Creamos la fila con inputs editables para precio y descuento —
    const nuevaFila = document.createElement("tr");
    nuevaFila.dataset.idproducto = selectedProduct.idproducto;
    nuevaFila.innerHTML = `
      <td>${tabla.rows.length + 1}</td>
      <td>${nomProducto}</td>

      <!-- PRECIO como input editable -->
      <td>
        <input type="number"
               class="form-control form-control-sm precio-input"
               value="${precioProducto.toFixed(2)}"
               min="0.01"
               step="0.01"
               style="width:5rem;">
      </td>

      <!-- CANTIDAD editable con botones -->
      <td>
        <div class="input-group input-group-sm cantidad-control" style="width: 8rem;">
          <button class="btn btn-outline-secondary btn-decrement" type="button">–</button>
          <input type="number"
                 class="form-control text-center p-0 border-0 bg-transparent cantidad-input"
                 value="${cantidadProducto}"
                 min="1"
                 step="1">
          <button class="btn btn-outline-secondary btn-increment" type="button">＋</button>
        </div>
      </td>

      <!-- DESCUENTO como input editable -->
      <td>
        <input type="number"
               class="form-control form-control-sm descuento-input"
               value="${descuentoProducto.toFixed(2)}"
               min="0"
               step="0.01"
               style="width:5rem;">
      </td>

      <td class="importe-cell">${importeTotal.toFixed(2)}</td>
      <td><button class="btn btn-danger btn-sm btn-quitar">X</button></td>
    `;
    tabla.appendChild(nuevaFila);

    // — Obtengo referencias en la fila recién creada —
    const decBtn = nuevaFila.querySelector(".btn-decrement");
    const incBtn = nuevaFila.querySelector(".btn-increment");
    const qtyInput = nuevaFila.querySelector(".cantidad-input");
    const precioInputFila = nuevaFila.querySelector(".precio-input");
    const descuentoInputFila = nuevaFila.querySelector(".descuento-input");
    const importeCell = nuevaFila.querySelector(".importe-cell");

    // — Función para actualizar esta línea cuando cambie cantidad, precio o descuento —
    function actualizarLinea() {
      let qty = parseFloat(qtyInput.value) || 1;
      if (qty < 1) qty = 1;
      qtyInput.value = qty;

      // Leemos precio y descuento actuales desde sus inputs
      let precioActual = parseFloat(precioInputFila.value) || 0;
      let descuentoActual = parseFloat(descuentoInputFila.value) || 0;

      // Si descuento > precio, lo ajustamos a precio
      if (descuentoActual > precioActual) {
        descuentoActual = precioActual;
        descuentoInputFila.value = descuentoActual.toFixed(2);
      }
      if (descuentoActual < 0) {
        descuentoActual = 0;
        descuentoInputFila.value = "0.00";
      }

      // Cálculo de nuevo importe de la línea
      const netoUnit = precioActual - descuentoActual;
      const nuevoImporte = netoUnit * qty;
      importeCell.textContent = nuevoImporte.toFixed(2);

      // Actualizo en el array detalleCompra
      const idx = detalleCompra.findIndex(
        (d) => d.idproducto === selectedProduct.idproducto
      );
      if (idx >= 0) {
        detalleCompra[idx].precio = precioActual;
        detalleCompra[idx].descuento = descuentoActual;
        detalleCompra[idx].cantidad = qty;
        detalleCompra[idx].importe = nuevoImporte.toFixed(2);
      }

      actualizarNumeros();
      calcularTotales();
    }

    // Botones de cantidad
    decBtn.addEventListener("click", () => {
      qtyInput.stepDown();
      actualizarLinea();
    });
    incBtn.addEventListener("click", () => {
      qtyInput.stepUp();
      actualizarLinea();
    });
    qtyInput.addEventListener("input", actualizarLinea);

    // Cuando cambie precio
    precioInputFila.addEventListener("input", actualizarLinea);

    // Cuando cambie descuento
    descuentoInputFila.addEventListener("input", actualizarLinea);

    // Botón de eliminar esta fila
    nuevaFila
      .querySelector(".btn-quitar")
      .addEventListener("click", function () {
        nuevaFila.remove();
        const idx = detalleCompra.findIndex(
          (d) => d.idproducto === selectedProduct.idproducto
        );
        if (idx >= 0) detalleCompra.splice(idx, 1);
        actualizarNumeros();
        calcularTotales();
        actualizarEstadoBotonGuardar();
      });

    // — Agrego al array detalleCompra —
    detalleCompra.push({
      idproducto: selectedProduct.idproducto,
      producto: nomProducto,
      precio: precioProducto,
      cantidad: cantidadProducto,
      descuento: descuentoProducto,
      importe: importeTotal.toFixed(2),
    });

    resetCamposProducto();
    actualizarNumeros();
    calcularTotales();
    actualizarEstadoBotonGuardar();
  });

  function resetCamposProducto() {
    inputProductElement.value = "";
    inputStock.value = "";
    inputPrecio.value = "";
    inputCantidad.value = 1;
    inputDescuento.value = 0;
  }

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
      el.scrollIntoView({ block: "nearest" });
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
      `http://localhost/Fix360/app/controllers/Compra.controller.php?q=${searchTerm}&type=producto`
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
            originalPrecio = selectedProduct.precio;
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
  // === Soporte de scanner “sin Enter” ===
  const manejarEscaneo = () => {
    const codigo = inputProductElement.value.trim();
    if (!/^\d{3,}$/.test(codigo)) return; // ajusta {3,} según tu longitud mínima

    cerrarListas();
    debouncedMostrarOpcionesProducto.cancel?.();

    fetch(
      `http://localhost/Fix360/app/controllers/Compra.controller.php?q=${encodeURIComponent(
        codigo
      )}&type=producto`
    )
      .then((r) => r.json())
      .then((data) => {
        if (!Array.isArray(data) || data.length === 0) {
          return Swal.fire({
            icon: "warning",
            title: "Producto no encontrado",
            text: `Código ${codigo}`,
            confirmButtonText: "OK",
            allowOutsideClick: false,
          });
        }
        const producto = data[0];
        const stock = parseInt(producto.stock, 10) || 0;
        const selector = `tr[data-idproducto="${producto.idproducto}"]`;
        const fila = tabla.querySelector(selector);

        if (fila) {
          // ya existe ⇒ sumamos sólo 1
          const qtyInput = fila.querySelector(".cantidad-input");
          qtyInput.stepUp();
          qtyInput.dispatchEvent(new Event("input"));
        } else if (stock > 0) {
          // no existe ⇒ agregamos con cantidad=1
          inputProductElement.value = producto.subcategoria_producto;
          inputPrecio.value = parseFloat(producto.precio).toFixed(2);
          inputStock.value = stock;
          inputCantidad.value = 1;
          inputDescuento.value = 0;

          selectedProduct = {
            idproducto: producto.idproducto,
            subcategoria_producto: producto.subcategoria_producto,
            precio: parseFloat(producto.precio),
            stock: stock,
          };

          agregarProductoBtn.click();
        } else {
          Swal.fire({
            icon: "warning",
            title: "Sin stock",
            text: producto.subcategoria_producto,
            confirmButtonText: "OK",
            allowOutsideClick: false,
          });
        }
      })
      .catch((err) => {
        console.error(err);
        Swal.fire("Error", "No se pudo buscar el producto", "error");
      })
      .finally(() => {
        inputProductElement.value = "";
        inputProductElement.focus();
      });
  };

  // Ahora lo envolvemos en debounce para que sólo corra 150 ms tras el último dígito
  const debouncedScan = debounce(manejarEscaneo, 150);
  inputProductElement.addEventListener("input", debouncedScan);
  // Funciones para generar número de serie y de comprobante
  /* function generateNumber(type) {
        const randomNumber = Math.floor(Math.random() * 100);
        return `${type}${String(randomNumber).padStart(3, "0")}`;
    }
    function generateComprobanteNumber(type) {
        const randomNumber = Math.floor(Math.random() * 10000000);
        return `${type}-${String(randomNumber).padStart(7, "0")}`;
    } */
  /* function inicializarCampos() {
        const tipoSeleccionado = document.querySelector(
            'input[name="tipo"]:checked'
        ).value;
        if (tipoSeleccionado === "factura") {
            numSerieInput.value = generateNumber("F");
            numComInput.value = generateComprobanteNumber("F");
        } else {
            numSerieInput.value = generateNumber("B");
            numComInput.value = generateComprobanteNumber("B");
        }
    } */
  /* inicializarCampos();
    tipoInputs.forEach((input) => {
        input.addEventListener("change", inicializarCampos);
    }); */
  // Establecer fecha actual
  const setFechaDefault = () => {
    const today = new Date();
    const day = String(today.getDate()).padStart(2, "0");
    const month = String(today.getMonth() + 1).padStart(2, "0");
    const year = today.getFullYear();
    fechaInput.value = `${year}-${month}-${day}`;
  };
  setFechaDefault();

  // Carga de proveedores vía AJAX
  fetch(
    "http://localhost/Fix360/app/controllers/Compra.controller.php?type=proveedor"
  )
    .then((response) => response.json())
    .then((data) => {
      proveedorSelect.innerHTML =
        "<option selected>Selecciona proveedor</option>";
      if (data.error) {
        console.error("Error:", data.error);
        return;
      }
      data.forEach((proveedor) => {
        const option = document.createElement("option");
        option.value = proveedor.idproveedor;
        option.textContent = proveedor.nombre_empresa;
        proveedorSelect.appendChild(option);
      });
    })
    .catch((error) => console.error("Error al cargar los proveedores:", error));

  // Navegación con Enter para ir de campo en campo (productos, precio, cantidad y descuento)
  inputProductElement.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      inputPrecio.focus();
    }
  });

  inputPrecio.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      inputCantidad.focus();
    }
  });

  inputCantidad.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      inputDescuento.focus();
    }
  });
  inputDescuento.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      // Opcional: puedes mover el foco al botón de agregar o ejecutar su acción directamente
      agregarProductoBtn.focus(); // agregarProductoBtn.click();  // Si prefieres ejecutar la acción
    }
  });
  // Evento del botón "Guardar" para enviar la compra
  btnFinalizarCompra.addEventListener("click", function (e) {
    e.preventDefault();

    // Validaciones previas:
    if (
      !proveedorSelect.value ||
      proveedorSelect.value === "Selecciona proveedor"
    ) {
      showToast("Debes seleccionar primero un proveedor.", "WARNING", 2000);
      return;
    }
    if (detalleCompra.length === 0) {
      showToast("Agrega al menos un producto", "WARNING", 2000);
      return;
    }

    Swal.fire({
      title: "¿Deseas guardar la compra?",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Aceptar",
      cancelButtonText: "Cancelar",
      confirmButtonColor: "#28a745",
      cancelButtonColor: "#d33",
    }).then((result) => {
      if (!result.isConfirmed) return;

      btnFinalizarCompra.disabled = true;
      btnFinalizarCompra.textContent = "Guardando...";

      const payload = {
        tipocom: document.querySelector('input[name="tipo"]:checked').value,
        fechacompra: fechaInput.value,
        numserie: numSerieInput.value,
        numcom: numComInput.value,
        moneda: monedaSelect.value,
        idproveedor: proveedorSelect.value,
        productos: detalleCompra,
      };

      //console.log("Enviando a Compra.controller:", payload);

      fetch("http://localhost/Fix360/app/controllers/Compra.controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      })
        .then((res) => {
          //console.log("HTTP status:", res.status, res.statusText);
          return res.text();
        })
        .then((textoCrudo) => {
          //console.log("Respuesta cruda del servidor:", textoCrudo);

          // Intentamos parsear si parece JSON:
          let json;
          try {
            json = JSON.parse(textoCrudo);
          } catch (err) {
            throw new Error(
              "El servidor no devolvió un JSON válido. Ver consola para más detalles."
            );
          }
          return json;
        })
        .then((json) => {
          //console.log("JSON parseado:", json);
          if (json.status === "success") {
            showToast("Compra registrada exitosamente.", "SUCCESS", 1500);
            setTimeout(() => {
              window.location.href = "listar-compras.php";
            }, 1500);
          } else {
            Swal.fire(
              "Error",
              "No se pudo registrar la compra: " +
                (json.message || "Desconocido"),
              "error"
            );
          }
        })
        .catch((err) => {
          //console.error("Error en fetch/parseo:", err);
          Swal.fire("Error", err.message || "Fallo de conexión.", "error");
        })
        .finally(() => {
          btnFinalizarCompra.disabled = false;
          btnFinalizarCompra.textContent = "Guardar";
        });
    });
  });
});
document.addEventListener("DOMContentLoaded", () => {
  const fechaInput = document.getElementById("fechaIngreso");
  const btnPermitir = document.getElementById("btnPermitirFechaPasada");
  if (!fechaInput) return;

  // Función para rellenar con ceros
  const pad = (n) => String(n).padStart(2, "0");

  const now = new Date();
  const yyyy = now.getFullYear();
  const MM = pad(now.getMonth() + 1);
  const dd = pad(now.getDate());
  const hh = pad(now.getHours());
  const mm = pad(now.getMinutes());

  // Valor por defecto: ahora mismo
  fechaInput.value = `${yyyy}-${MM}-${dd}T${hh}:${mm}`;

  // Rango: desde hace 2 días hasta hoy
  const twoDaysAgo = new Date(now);
  twoDaysAgo.setDate(now.getDate() - 2);
  const yyyy2 = twoDaysAgo.getFullYear();
  const MM2 = pad(twoDaysAgo.getMonth() + 1);
  const dd2 = pad(twoDaysAgo.getDate());

  fechaInput.min = `${yyyy2}-${MM2}-${dd2}T00:00`;
  fechaInput.max = `${yyyy}-${MM}-${dd}T23:59`;

  btnPermitir.addEventListener("click", () => {
    // Solo quitamos el "min", mantenemos "max" = hoy
    fechaInput.removeAttribute("min");
    btnPermitir.disabled = true;
    btnPermitir.innerHTML =
      '<i class="fa-solid fa-unlock-keyhole text-success"></i>';
    btnPermitir.title = "Fechas pasadas habilitadas";
  });
});
