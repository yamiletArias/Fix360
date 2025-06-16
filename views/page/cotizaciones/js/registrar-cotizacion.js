// views/page/cotizaciones/js/registrar-cotizacion.js
document.addEventListener("DOMContentLoaded", () => {
  // — Referencias DOM —
  const inputProduct = document.getElementById("producto");
  const inputPrecio = document.getElementById("precio");
  const inputCantidad = document.getElementById("cantidad");
  const inputDescuento = document.getElementById("descuento");
  const btnAgregarProd = document.getElementById("agregarProducto");
  const tablaProductos = document.querySelector("#tabla-detalle tbody");

  const fechaInput = document.getElementById("fecha");
  const vigenciaInput = document.getElementById("vigenciadias");
  const monedaSelect = document.getElementById("moneda");
  const btnGuardarCot = document.getElementById("btnFinalizarCotizacion");

  const btnToggleService = document.getElementById("btnToggleService");
  const serviceSection = document.getElementById("serviceSection");
  const serviceListCard = document.getElementById("serviceListCard");
  const tablaServicios = document.querySelector("#tabla-detalle-servicios tbody");
  const btnAgregarServ = document.getElementById("btnAgregarDetalleServicio");
  const selectSubcategoria = document.getElementById("subcategoria");
  const selectServicio = document.getElementById("servicio");
  const inputPrecioServ = document.getElementById("precioServicio");
  const btnGuardarServicio = document.getElementById("btnGuardarServicio");
  const btnAgregarServicio = document.getElementById("btnAgregarServicio");

  const detalleCotizacion = [];
  const detalleServicios = [];

  // Estado para autocompletar
  let selectedProduct = {};
  let diasVigencia = 0;

  // — Toggle sección de servicios (deshabilita botón tras primer clic) —
  btnToggleService.addEventListener("click", e => {
    e.preventDefault();
    serviceSection.classList.remove("d-none");
    serviceListCard.classList.remove("d-none");
    btnToggleService.disabled = true;
    btnToggleService.classList.remove("btn-success");
    btnToggleService.classList.add("btn-secondary");
    cargarSubcategorias();
  });

  // — Cargar subcategorías al mostrar sección de servicios —
  function cargarSubcategorias() {
    fetch(`${window.FIX360_BASE_URL}app/controllers/subcategoria.Controller.php?task=getServicioSubcategoria`)
      .then(r => r.json())
      .then(data => {
        selectSubcategoria.innerHTML = '<option value="">Eliga un tipo de servicio</option>' + data.map(s => `<option value="${s.idsubcategoria}">${s.subcategoria}</option>`).join("");
      })
      .catch(err => console.error("Error al cargar subcategorías:", err));
  }

  // — Autocompletado de productos —
  function debounce(fn, delay) {
    let timeout;
    return (...args) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => fn(...args), delay);
    };
  }
  function cerrarListas() {
    document.querySelectorAll(".autocomplete-items").forEach(el => el.remove());
  }
  function mostrarOpcionesProducto() {
    cerrarListas();
    if (!inputProduct.value) return;

    fetch(
      `${window.FIX360_BASE_URL}app/controllers/Cotizacion.controller.php` +
      `?q=${encodeURIComponent(inputProduct.value)}` +
      `&type=producto`
    )
      .then(r => r.json())
      .then(json => {
        // 1) Normalizar el array de productos:
        //    tu controlador debería devolver { status, data: [...] }
        //    o bien el array directamente. Así cubrimos ambos casos.
        const productos = Array.isArray(json)
          ? json
          : Array.isArray(json.data)
            ? json.data
            : [];

        // 2) Si no hay nada, mostramos un warning y salimos
        if (productos.length === 0) {
          Swal.fire({
            icon: 'warning',
            title: 'No se encontraron productos',
            text: 'Verifica el término de búsqueda.',
            confirmButtonText: 'Aceptar',
            allowOutsideClick: false
          });
          return;
        }

        // 3) Creamos el contenedor de sugerencias
        const cont = document.createElement("div");
        cont.className = "autocomplete-items";
        inputProduct.parentNode.append(cont);

        // 4) Agregamos una div por cada producto
        productos.forEach(prod => {
          const opt = document.createElement("div");
          opt.textContent = prod.subcategoria_producto;
          opt.addEventListener("click", () => {
            inputProduct.value = prod.subcategoria_producto;
            inputPrecio.value = prod.preciov;
            inputCantidad.value = 1;
            inputDescuento.value = 0;
            selectedProduct = { idproducto: prod.idproducto, precio: prod.precio };
            cerrarListas();
          });
          cont.append(opt);
        });
      })
      .catch(err => {
        console.error("Error al obtener los productos:", err);
        showToast("Error al buscar productos.", "ERROR", 1500);
      });
  }
  const debMostrar = debounce(mostrarOpcionesProducto, 300);
  inputProduct.addEventListener("input", () => debMostrar());
  inputProduct.addEventListener("click", () => debMostrar());
  document.addEventListener("click", e => {
    if (e.target !== inputProduct) cerrarListas();
  });

  // — Navegación con Enter —
  inputProduct.addEventListener("keydown", e => e.key === "Enter" && (e.preventDefault(), inputPrecio.focus()));
  inputPrecio.addEventListener("keydown", e => e.key === "Enter" && (e.preventDefault(), inputCantidad.focus()));
  inputCantidad.addEventListener("keydown", e => e.key === "Enter" && (e.preventDefault(), inputDescuento.focus()));
  inputDescuento.addEventListener("keydown", e => e.key === "Enter" && (e.preventDefault(), btnAgregarProd.focus()));

  // — Agregar producto —
  btnAgregarProd.addEventListener("click", () => {
    const nombre = inputProduct.value.trim();
    const precio = parseFloat(inputPrecio.value);
    const cant = parseFloat(inputCantidad.value);
    const desc = parseFloat(inputDescuento.value) || 0;
    if (!nombre || isNaN(precio) || isNaN(cant) || precio <= 0 || cant <= 0) {
      return alert("Completa todos los campos correctamente");
    }
    if (detalleCotizacion.some(d => d.idproducto === selectedProduct.idproducto)) {
      return alert("Ya agregaste este producto");
    }
    const importe = ((precio * cant) - desc).toFixed(2);
    const det = { idproducto: selectedProduct.idproducto, producto: nombre, precio, cantidad: cant, descuento: desc, importe };
    detalleCotizacion.push(det);

    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${tablaProductos.rows.length + 1}</td>
      <td>${det.producto}</td>
      <td>${det.precio.toFixed(2)}</td>
      <td>${det.cantidad}</td>
      <td>${det.descuento.toFixed(2)}</td>
      <td>${det.importe}</td>
      <td><button class="btn btn-danger btn-sm btn-quitar">X</button></td>
    `;
    tr.querySelector(".btn-quitar").addEventListener("click", () => {
      const idx = detalleCotizacion.findIndex(d => d.idproducto === det.idproducto);
      detalleCotizacion.splice(idx, 1);
      tr.remove();
      renumerar(tablaProductos);
      calcularTotales();
    });
    tablaProductos.append(tr);
    renumerar(tablaProductos);
    calcularTotales();
    resetCamposProducto();
  });

  function resetCamposProducto() {
    inputProduct.value = "";
    inputPrecio.value = "";
    inputCantidad.value = 1;
    inputDescuento.value = 0;
  }

  // — Renumerar filas —
  function renumerar(tbody) {
    Array.from(tbody.rows).forEach((r, i) => r.cells[0].textContent = i + 1);
  }

  // — Calcular totales (productos + servicios) —
  function calcularTotales() {
    let totalImp = 0, totalDesc = 0;
    detalleCotizacion.forEach(d => {
      totalImp += parseFloat(d.importe);
      totalDesc += d.descuento * d.cantidad;
    });
    detalleServicios.forEach(s => {
      totalImp += parseFloat(s.importe);
    });
    const igv = totalImp - (totalImp / 1.18);
    const neto = totalImp / 1.18;
    document.getElementById("total").value = totalImp.toFixed(2);
    document.getElementById("totalDescuento").value = totalDesc.toFixed(2);
    document.getElementById("igv").value = igv.toFixed(2);
    document.getElementById("neto").value = neto.toFixed(2);
  }

  // — Fecha por defecto —
  (() => {
    const hoy = new Date(), pad = n => n.toString().padStart(2, "0");
    const f = `${hoy.getFullYear()}-${pad(hoy.getMonth() + 1)}-${pad(hoy.getDate())}`;
    fechaInput.value = f;
    vigenciaInput.value = f;
  })();

  vigenciaInput.addEventListener("change", () => {
    const f1 = new Date(fechaInput.value), f2 = new Date(vigenciaInput.value);
    diasVigencia = Math.ceil((f2 - f1) / (1000 * 60 * 60 * 24));
    if (diasVigencia < 0) {
      alert("Vigencia no puede ser anterior a la fecha");
      vigenciaInput.value = fechaInput.value;
      diasVigencia = 0;
    }
  });

  // — Cargar servicios por subcategoría —
  selectSubcategoria.addEventListener("change", async function () {
    if (!this.value) {
      selectServicio.innerHTML = '<option value="">Eliga un servicio</option>';
      return;
    }
    try {
      selectServicio.innerHTML = '<option>Cargando...</option>';
      const res = await fetch(`${window.FIX360_BASE_URL}app/controllers/Servicio.Controller.php?task=getServicioBySubcategoria&idsubcategoria=${this.value}`);
      const datos = await res.json();
      selectServicio.innerHTML = '<option value="">Eliga un servicio</option>' + datos.map(s => `<option value="${s.idservicio}">${s.servicio}</option>`).join("");
    } catch (err) {
      console.error(err);
      selectServicio.innerHTML = '<option value="">Error al cargar</option>';
    }
  });
  const modalServicioEl = document.getElementById("ModalServicio");
  const modalServicio = new bootstrap.Modal(modalServicioEl, {
    backdrop: true,
    keyboard: false
  });
  // — Modal para registrar nuevo servicio —
  btnAgregarServicio.addEventListener("click", () => {
    const idc = selectSubcategoria.value;
    if (!idc) {
      return Swal.fire("Atención", "Selecciona primero una subcategoría", "warning");
    }
    // completamos los campos del modal
    document.getElementById("modalSubcategoriaId").value = idc;
    document.getElementById("modalSubcategoriaNombre").value = selectSubcategoria.selectedOptions[0].text;
    document.getElementById("modalServicioNombre").value = "";
    // ahora sí mostramos el modal
    modalServicio.show();
  });

  btnGuardarServicio.addEventListener("click", async () => {
    const idsubcategoria = document.getElementById("modalSubcategoriaId").value;
    const nombreServicio = document.getElementById("modalServicioNombre").value.trim();

    if (!nombreServicio) {
      return Swal.fire("Error", "Ingresa el nombre del servicio", "warning");
    }

    try {
      // Enviamos por POST JSON al controlador
      const res = await fetch(
        `${window.FIX360_BASE_URL}app/controllers/Servicio.Controller.php`,
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            task: "registerServicio",
            idsubcategoria,
            servicio: nombreServicio
          })
        }
      );
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const json = await res.json();

      // El controlador POST devuelve { success: true, idservicio, servicio }
      if (json.success) {
        Swal.fire("¡Listo!", "Servicio registrado con éxito", "success");

        // ① Crear el <option> y añadirlo al <select>
        const nuevaOpcion = document.createElement("option");
        nuevaOpcion.value = json.idservicio;
        nuevaOpcion.text = json.servicio;
        selectServicio.appendChild(nuevaOpcion);

        // ② Opcional: marcarlo como seleccionado
        selectServicio.value = json.idservicio;

        // ③ Cerrar el modal limpio
        bootstrap.Modal.getInstance(document.getElementById("ModalServicio")).hide();
      } else {
        Swal.fire("Error", json.error || "No se pudo registrar el servicio", "error");
      }
    } catch (err) {
      console.error(err);
      Swal.fire("Error", "Fallo de conexión", "error");
    }
  });

  // — Agregar detalle de servicio —
  btnAgregarServ.addEventListener("click", () => {
    const idserv = parseInt(selectServicio.value, 10);
    const precio = parseFloat(inputPrecioServ.value);
    if (!idserv || isNaN(precio) || precio <= 0) {
      return alert("Completa todos los campos de servicio");
    }
    if (detalleServicios.some(s => s.idservicio === idserv)) {
      return alert("Ya agregaste este servicio");
    }
    const nombreS = selectServicio.selectedOptions[0].text;
    const importe = precio.toFixed(2);
    detalleServicios.push({ idservicio: idserv, servicio: nombreS, precio, importe });

    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${tablaServicios.rows.length + 1}</td>
      <td>${nombreS}</td>
      <td>${precio.toFixed(2)}</td>
      <td><button class="btn btn-danger btn-sm btn-quitar">X</button></td>
    `;
    tr.querySelector(".btn-quitar").addEventListener("click", () => {
      const idx = detalleServicios.findIndex(s => s.idservicio === idserv);
      detalleServicios.splice(idx, 1);
      tr.remove();
      renumerar(tablaServicios);
      calcularTotales();
    });
    tablaServicios.append(tr);
    inputPrecioServ.value = "";
    renumerar(tablaServicios);
    calcularTotales();
  });

  // — Guardar cotización —
  btnGuardarCot.addEventListener("click", e => {
    e.preventDefault();

    const clienteId = document.getElementById("hiddenIdCliente").value;
    if (!clienteId) {
      return alert("Selecciona un cliente");
    }
    if (detalleCotizacion.length === 0 && detalleServicios.length === 0) {
      return alert("Agrega al menos un producto o servicio");
    }

    btnGuardarCot.disabled = true;
    btnGuardarCot.textContent = "Guardando...";

    // 1) Unificar productos y servicios en un solo array "items"
    const items = [
      ...detalleCotizacion.map(d => ({
        idproducto: d.idproducto,
        idservicio: null,
        cantidad: d.cantidad,
        precio: d.precio,
        descuento: d.descuento
      })),
      ...detalleServicios.map(s => ({
        idproducto: null,
        idservicio: s.idservicio,
        cantidad: 1,
        precio: s.precio,
        descuento: 0
      }))
    ];

    // 2) Armar el payload que el backend espera
    const payload = {
      fechahora: fechaInput.value,
      vigenciadias: diasVigencia,
      moneda: monedaSelect.value,
      idcliente: clienteId,
      items: items
    };

    // 3) Log para depurar (ya con payload definido)
    console.log("Items a enviar:", items);
    console.log("Payload completo:", payload);

    // 4) Enviar la petición
    fetch(`${window.FIX360_BASE_URL}app/controllers/Cotizacion.controller.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    })
      .then(res => res.json())
      .then(json => {
        if (json.status === "success") {
          Swal.fire({
            icon: "success",
            title: "¡Registrado!",
            timer: 1500,
            showConfirmButton: false
          }).then(() => {
            window.location.href = "listar-cotizacion.php";
          });
        } else {
          Swal.fire("Error", json.message || "No se pudo guardar", "error");
        }
      })
      .catch(err => {
        console.error(err);
        Swal.fire("Error", "Fallo de conexión", "error");
      })
      .finally(() => {
        btnGuardarCot.disabled = false;
        btnGuardarCot.textContent = "Aceptar";
      });
  });
  // — Modal propietario —
  window.actualizarOpciones = function () {
    const esEmp = document.getElementById("rbtnempresa").checked;
    document.getElementById("selectMetodo").innerHTML = esEmp
      ? '<option value="ruc">RUC</option><option value="razonsocial">Razón Social</option>'
      : '<option value="dni">DNI</option><option value="nombre">Nombres</option>';
  };
  window.buscarPropietario = function () {
    const tipo = document.querySelector('input[name="tipoBusqueda"]:checked').id === "rbtnempresa" ? "empresa" : "persona";
    const met = document.getElementById("selectMetodo").value;
    const val = document.getElementById("vbuscado").value.trim();
    if (!val) return document.querySelector("#tabla-resultado tbody").innerHTML = "";
    fetch(`${window.FIX360_BASE_URL}app/controllers/propietario.controller.php?task=buscarPropietario&tipo=${tipo}&metodo=${met}&valor=${encodeURIComponent(val)}`)
      .then(r => r.json())
      .then(data => {
        const tb = document.querySelector("#tabla-resultado tbody");
        tb.innerHTML = "";
        data.forEach((it, i) => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${i + 1}</td>
            <td>${it.nombre}</td>
            <td>${it.documento}</td>
            <td><button class="btn btn-success btn-sm" data-id="${it.idcliente}"><i class="fa-solid fa-circle-check"></i></button></td>
          `;
          tb.append(tr);
        });
      });
  };
  document.getElementById("tabla-resultado").addEventListener("click", e => {
    const btn = e.target.closest(".btn-success");
    if (!btn) return;
    const id = btn.dataset.id;
    const nombre = btn.closest("tr").cells[1].textContent;
    document.getElementById("hiddenIdCliente").value = id;
    document.getElementById("propietario").value = nombre;
    document.querySelector("#miModal .btn-close").click();
  });
  document.getElementById("vbuscado").addEventListener("input", debounce(window.buscarPropietario, 300));
  document.getElementById("selectMetodo").addEventListener("change", debounce(window.buscarPropietario, 300));
  actualizarOpciones();
});