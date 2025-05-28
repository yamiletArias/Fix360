document.addEventListener('DOMContentLoaded', function () {
    // Variables y elementos
    const proveedorSelect = document.getElementById('proveedor');
    const inputProductElement = document.getElementById("producto");
    const numSerieInput = document.getElementById("numserie");
    const numComInput = document.getElementById("numcom");
    const monedaSelect = document.getElementById('moneda');
    const tipoInputs = document.querySelectorAll('input[name="tipo"]');
    const agregarProductoBtn = document.querySelector("#agregarProducto");
    const tabla = document.querySelector("#tabla-detalle-compra tbody");
    const btnFinalizarCompra = document.getElementById('btnFinalizarCompra');
    // Nuevos elementos de input para los detalles del producto
    const inputStock = document.getElementById("stock");
    const inputPrecio = document.getElementById("preciocompra");
    const inputCantidad = document.getElementById("cantidadcompra");
    const inputDescuento = document.getElementById("descuento");
    const fechaInput = document.getElementById("fechaIngreso");
    inputPrecio.addEventListener("blur", () => {
        const val = parseFloat(inputPrecio.value);
        const precioOriginal = parseFloat(selectedProduct.precio);

        if (isNaN(val) || val <= 0) {
            alert("Precio inválido.");
            inputPrecio.value = precioOriginal.toFixed(2);
            return;
        }

        // Validar si el nuevo precio es menor al original
        if (val < precioOriginal) {
            const confirmar = confirm(`Has ingresado un precio menor al original (${precioOriginal.toFixed(2)}). ¿Deseas continuar?`);
            if (!confirmar) {
                inputPrecio.value = precioOriginal.toFixed(2);
            }
        }
    });
    // --- Funciones auxiliares ---
    function calcularTotales() {
        let totalImporte = 0;
        let totalDescuento = 0;
        tabla.querySelectorAll("tr").forEach(fila => {
            // 1) cantidad desde el input de la celda
            const cantidadLinea = parseFloat(fila.querySelector('.cantidad-input').value) || 0;
            // 2) precio y descuento unitario de las celdas
            const precioUnitario = parseFloat(fila.children[2].textContent) || 0;
            const descUnitario = parseFloat(fila.children[4].textContent) || 0;
            // 3) neto y acumulados
            const importeLinea = (precioUnitario - descUnitario) * cantidadLinea;
            totalImporte += importeLinea;
            totalDescuento += descUnitario * cantidadLinea;
        });
        // 4) IGV 18% y neto
        const igv = totalImporte - (totalImporte / 1.18);
        const neto = totalImporte / 1.18;
        // 5) resultado a inputs
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

        //VALIDACIONES
        // 1) Campos completos
        if (!nomProducto || isNaN(precioProducto) || isNaN(cantidadProducto)) {
            return alert("Por favor, complete todos los campos correctamente.");
        }
        if (isNaN(precioProducto) || precioProducto <= 0) {
            return alert("Ingresa un precio válido mayor que cero.");
        }
        if (cantidadProducto <= 0) {
            alert("La cantidad debe ser mayor que cero.");
            inputCantidad.value = 1;  // reset al mínimo
            return;
        }
        // 2) Descuento ≤ precio unitario
        if (descuentoProducto > precioProducto) {
            alert("El descuento unitario no puede ser mayor que el precio unitario.");
            document.getElementById("descuento").value = "";
            return;
        }
        if (descuentoProducto < 0) {
            alert("El descuento no puede ser negativo.");
            inputDescuento.value = 0;
            return;
        }

        // 3) No duplicar
        if (estaDuplicado(selectedProduct.idproducto)) {
            alert("Este producto ya ha sido agregado.");
            return resetCamposProducto();
        }
        const stockDisponible = selectedProduct.stock || 0;
        if (cantidadProducto > stockDisponible) {
            alert(
                `No puedes pedir ${cantidadProducto} unidades; solo hay ${stockDisponible} en stock.`
            );
            inputCantidad.value = stockDisponible || 1;
            return;
        }

        // 4) Cálculo de importe unitario descontado y total
        const netoUnit = precioProducto - descuentoProducto;
        const importeTotal = netoUnit * cantidadProducto;

        // 5) Crear fila mostrando descuento unitario
        const nuevaFila = document.createElement("tr");
        nuevaFila.dataset.idproducto = selectedProduct.idproducto;
        nuevaFila.innerHTML = `
        <td>${tabla.rows.length + 1}</td>
        <td>${nomProducto}</td>
        <td>${precioProducto.toFixed(2)}</td>
        <td>
          <div class="input-group input-group-sm cantidad-control" style="width: 8rem;">
            <button class="btn btn-outline-secondary btn-decrement" type="button">–</button>
            <input type="number"
                  class="form-control text-center p-0 border-0 bg-transparent cantidad-input"
                  value="${cantidadProducto}"
                  min="1"
                  max="${stockDisponible}">
            <button class="btn btn-outline-secondary btn-increment" type="button">＋</button>
          </div>
        </td>
        <td>${descuentoProducto.toFixed(2)}</td>
        <td class="importe-cell">${importeTotal.toFixed(2)}</td>
        <td><button class="btn btn-danger btn-sm btn-quitar">X</button></td>
      `;

        // Agregar comportamientos a la fila
        const decBtn = nuevaFila.querySelector(".btn-decrement");
        const incBtn = nuevaFila.querySelector(".btn-increment");
        const qtyInput = nuevaFila.querySelector(".cantidad-input");
        const importeCell = nuevaFila.querySelector(".importe-cell");

        function actualizarLinea() {
            let qty = parseInt(qtyInput.value, 10) || 1;
            if (qty < 1) qty = 1;
            qtyInput.value = qty;

            const nuevoImporte = netoUnit * qty;
            importeCell.textContent = nuevoImporte.toFixed(2);

            // Actualiza array detalleCompra
            const idx = detalleCompra.findIndex(d => d.idproducto === selectedProduct.idproducto);
            if (idx >= 0) {
                detalleCompra[idx].cantidad = qty;
                detalleCompra[idx].importe = nuevoImporte.toFixed(2);
            }

            actualizarNumeros();
            calcularTotales();
        }

        decBtn.addEventListener("click", () => { qtyInput.stepDown(); actualizarLinea(); });
        incBtn.addEventListener("click", () => { qtyInput.stepUp(); actualizarLinea(); });
        qtyInput.addEventListener("input", actualizarLinea);

        // Eliminar fila
        nuevaFila.querySelector(".btn-quitar").addEventListener("click", function () {
            nuevaFila.remove();
            const idx = detalleCompra.findIndex(d => d.idproducto === selectedProduct.idproducto);
            if (idx >= 0) detalleCompra.splice(idx, 1);
            actualizarNumeros();
            calcularTotales();
        });

        // Insertar en DOM y array
        tabla.appendChild(nuevaFila);
        detalleCompra.push({
            idproducto: selectedProduct.idproducto,
            producto: nomProducto,
            precio: precioProducto,
            cantidad: cantidadProducto,
            descuento: descuentoProducto,
            importe: importeTotal.toFixed(2)
        });

        resetCamposProducto();
        actualizarNumeros();
        calcularTotales();
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
            Array.from(items).forEach(i => i.classList.remove("autocomplete-active"));
        }
    }

    // Función para mostrar opciones de productos (autocompletado)
    function mostrarOpcionesProducto(input) {
        cerrarListas();
        if (!input.value) return;
        const searchTerm = input.value;
        fetch(`http://localhost/Fix360/app/controllers/Compra.controller.php?q=${searchTerm}&type=producto`)
            .then(response => response.json())
            .then(data => {
                const itemsDiv = document.createElement("div");
                itemsDiv.setAttribute("id", "autocomplete-list-producto");
                itemsDiv.setAttribute("class", "autocomplete-items");
                input.parentNode.appendChild(itemsDiv);
                if (data.length === 0) {
                    const noResultsDiv = document.createElement("div");
                    noResultsDiv.textContent = 'No se encontraron productos';
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
                            if (inputDescuento.value === "0" && e.key >= "0" && e.key <= "9") {
                                inputDescuento.value = "";
                            }
                        });

                        selectedProduct = {
                            idproducto: producto.idproducto,
                            subcategoria_producto: producto.subcategoria_producto,
                            precio: producto.precio,
                            stock: producto.stock
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
            .catch(err => console.error('Error al obtener los productos: ', err));
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
    const debouncedMostrarOpcionesProducto = debounce(mostrarOpcionesProducto, 500);
    inputProductElement.addEventListener("input", function () {
        debouncedMostrarOpcionesProducto(this);
    });
    inputProductElement.addEventListener("click", function () {
        debouncedMostrarOpcionesProducto(this);
    });
    document.addEventListener("click", function (e) {
        cerrarListas(e.target);
    });

    // Funciones para generar número de serie y de comprobante
    function generateNumber(type) {
        const randomNumber = Math.floor(Math.random() * 100);
        return `${type}${String(randomNumber).padStart(3, "0")}`;
    }
    function generateComprobanteNumber(type) {
        const randomNumber = Math.floor(Math.random() * 10000000);
        return `${type}-${String(randomNumber).padStart(7, "0")}`;
    }
    function inicializarCampos() {
        const tipoSeleccionado = document.querySelector('input[name="tipo"]:checked').value;
        if (tipoSeleccionado === "factura") {
            numSerieInput.value = generateNumber("F");
            numComInput.value = generateComprobanteNumber("F");
        } else {
            numSerieInput.value = generateNumber("B");
            numComInput.value = generateComprobanteNumber("B");
        }
    }
    inicializarCampos();
    tipoInputs.forEach((input) => {
        input.addEventListener("change", inicializarCampos);
    });
    // Establecer fecha actual
    const setFechaDefault = () => {
        const today = new Date();
        const day = String(today.getDate()).padStart(2, '0');
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = today.getFullYear();
        fechaInput.value = `${year}-${month}-${day}`;
    };
    setFechaDefault();

    // Carga de proveedores vía AJAX
    fetch('http://localhost/Fix360/app/controllers/Compra.controller.php?type=proveedor')
        .then(response => response.json())
        .then(data => {
            proveedorSelect.innerHTML = '<option selected>Selecciona proveedor</option>';
            if (data.error) {
                console.error('Error:', data.error);
                return;
            }
            data.forEach(proveedor => {
                const option = document.createElement('option');
                option.value = proveedor.idproveedor;
                option.textContent = proveedor.nombre_empresa;
                proveedorSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error al cargar los proveedores:', error));

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
    btnFinalizarCompra.addEventListener('click', function (e) {
        e.preventDefault();

        if (!proveedorSelect.value || proveedorSelect.value === 'Selecciona proveedor') {
            showToast('Debes seleccionar primero un proveedor.', 'WARNING', 2000);
            return;
        }
        if (detalleCompra.length === 0) {
            showToast('Agrega al menos un producto', 'WARNING', 2000);
            return;
        }
        Swal.fire({
            title: '¿Deseas guardar la compra?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33'
        }).then(result => {
            if (result.isConfirmed) {
                btnFinalizarCompra.disabled = true;
                btnFinalizarCompra.textContent = 'Guardando...';

                fetch('http://localhost/Fix360/app/controllers/Compra.controller.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        tipocom: document.querySelector('input[name="tipo"]:checked').value,
                        fechacompra: fechaInput.value,
                        numserie: numSerieInput.value,
                        numcom: numComInput.value,
                        moneda: monedaSelect.value,
                        idproveedor: proveedorSelect.value,
                        productos: detalleCompra
                    })
                })
                    .then(res => res.json())
                    .then(json => {
                        if (json.status === 'success') {
                            showToast('Compra registrada exitosamente.', 'SUCCESS', 1500);
                            setTimeout(() => {
                                window.location.href = 'listar-compras.php';
                            }, 1500);
                        } else {
                            Swal.fire('Error', 'No se pudo registrar la compra.', 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Fallo de conexión.', 'error'))
                    .finally(() => {
                        btnFinalizarCompra.disabled = false;
                        btnFinalizarCompra.textContent = 'Guardar';
                    });
            }
        });
    });
});