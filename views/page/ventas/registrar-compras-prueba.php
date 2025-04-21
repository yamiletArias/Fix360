<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Variables y elementos
    const inputCliente = document.getElementById("cliente");
    const inputProductElement = document.getElementById("producto");
    let clienteId = null;
    let selectedProduct = {};
    const numSerieInput = document.getElementById("numserie");
    const numComInput = document.getElementById("numcom");
    const tipoInputs = document.querySelectorAll('input[name="tipo"]');
    const agregarProductoBtn = document.getElementById("agregarProducto");
    const tabla = document.querySelector("#tabla-detalle tbody");
    const detalleVenta = [];
    const btnFinalizarVenta = document.getElementById('btnFinalizarVenta');
    const fechaInput = document.getElementById("fecha");
    const monedaSelect = document.getElementById('moneda');

    // ------------------------------
    // Funciones de utilidad
    // ------------------------------

    // Confirmación con SweetAlert2
    async function ask(mensaje, titulo = "Confirmación") {
        const result = await Swal.fire({
            title: titulo,
            text: mensaje,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sí, registrar",
            cancelButtonText: "Cancelar",
        });
        return result.isConfirmed;
    }

    // Mostrar toast (puedes adaptarlo a tu librería)
    function showToast(msg, icon, duration = 2000) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon.toLowerCase(),
            title: msg,
            showConfirmButton: false,
            timer: duration
        });
    }

    // Devuelve el array de detalle para enviar
    function obtenerDetalleProductos() {
        return detalleVenta;
    }

    // Calcular importes, descuento, IGV y neto
    function calcularTotales() {
        let totalImporte = 0, totalDescuento = 0;
        tabla.querySelectorAll("tr").forEach(fila => {
            totalImporte += parseFloat(fila.children[5].textContent) || 0;
            totalDescuento += parseFloat(fila.children[4].textContent) || 0;
        });
        const igv = totalImporte - (totalImporte / 1.18);
        const neto = totalImporte / 1.18;
        document.getElementById("total").value = totalImporte.toFixed(2);
        document.getElementById("totalDescuento").value = totalDescuento.toFixed(2);
        document.getElementById("igv").value = igv.toFixed(2);
        document.getElementById("neto").value = neto.toFixed(2);
    }

    // Evita duplicados en el detalle
    function estaDuplicado(idproducto = 0) {
        return detalleVenta.some(d => d.idproducto === idproducto);
    }

    // Autocompletado de clientes
    function mostrarOpcionesCliente(input) {
        cerrarListas();
        if (!input.value) return;
        fetch(`http://localhost/Fix360/app/controllers/Venta.controller.php?q=${input.value}&type=cliente`)
            .then(r => r.json())
            .then(data => {
                const itemsDiv = document.createElement("div");
                itemsDiv.id = "autocomplete-list";
                itemsDiv.className = "autocomplete-items";
                input.parentNode.appendChild(itemsDiv);
                if (data.length === 0) {
                    const noResults = document.createElement("div");
                    noResults.textContent = 'No se encontraron clientes';
                    itemsDiv.appendChild(noResults);
                } else {
                    data.forEach(cliente => {
                        const option = document.createElement("div");
                        option.textContent = cliente.cliente;
                        option.addEventListener("click", () => {
                            input.value = cliente.cliente;
                            clienteId = cliente.idcliente;
                            cerrarListas();
                        });
                        itemsDiv.appendChild(option);
                    });
                }
            })
            .catch(console.error);
    }

    // Autocompletado de productos
    function mostrarOpcionesProducto(input) {
        cerrarListas();
        if (!input.value) return;
        fetch(`http://localhost/Fix360/app/controllers/Venta.controller.php?q=${input.value}&type=producto`)
            .then(r => r.json())
            .then(data => {
                const itemsDiv = document.createElement("div");
                itemsDiv.id = "autocomplete-list-producto";
                itemsDiv.className = "autocomplete-items";
                input.parentNode.appendChild(itemsDiv);
                if (data.length === 0) {
                    const noResults = document.createElement("div");
                    noResults.textContent = 'No se encontraron productos';
                    itemsDiv.appendChild(noResults);
                } else {
                    data.forEach(producto => {
                        const option = document.createElement("div");
                        option.textContent = producto.subcategoria_producto;
                        option.addEventListener("click", () => {
                            input.value = producto.subcategoria_producto;
                            document.getElementById('precio').value = producto.precio;
                            document.getElementById('cantidad').value = 1;
                            document.getElementById('descuento').value = 0;
                            selectedProduct = {
                                idproducto: producto.idproducto,
                                precio: producto.precio
                            };
                            cerrarListas();
                        });
                        itemsDiv.appendChild(option);
                    });
                }
            })
            .catch(console.error);
    }

    // Cierra cualquier lista de autocompletado
    function cerrarListas() {
        document.querySelectorAll(".autocomplete-items").forEach(el => el.remove());
    }

    // Generar número de serie
    function generateNumber(type) {
        const rnd = Math.floor(Math.random() * 100);
        return `${type}${String(rnd).padStart(3, "0")}`;
    }

    // Generar número de comprobante
    function generateComprobanteNumber(type) {
        const rnd = Math.floor(Math.random() * 10000000);
        return `${type}-${String(rnd).padStart(7, "0")}`;
    }

    // Inicializar serie y comprobante según tipo
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

    // Fecha actual por defecto
    function setFechaDefault() {
        const today = new Date();
        fechaInput.value = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
    }

    // ------------------------------
    // Lógica de eventos
    // ------------------------------

    // Autocompletados
    inputCliente.addEventListener("input", () => mostrarOpcionesCliente(inputCliente));
    inputCliente.addEventListener("click", () => mostrarOpcionesCliente(inputCliente));
    inputProductElement.addEventListener("input", () => mostrarOpcionesProducto(inputProductElement));
    inputProductElement.addEventListener("click", () => mostrarOpcionesProducto(inputProductElement));
    document.addEventListener("click", e => cerrarListas());

    // Cambio de tipo para regenerar serie y comprobante
    tipoInputs.forEach(input => input.addEventListener("change", inicializarCampos));

    // Botón "Agregar" producto
    agregarProductoBtn.addEventListener("click", function () {
        const nombre = inputProductElement.value.trim();
        const precio = parseFloat(document.getElementById('precio').value);
        const cantidad = parseFloat(document.getElementById('cantidad').value);
        const descuento = parseFloat(document.getElementById('descuento').value);
        if (!nombre || isNaN(precio) || isNaN(cantidad) || cantidad <= 0) {
            return alert("Por favor completa correctamente el producto, precio y cantidad.");
        }
        if (estaDuplicado(selectedProduct.idproducto)) {
            return alert("Este producto ya ha sido agregado.");
        }
        const importe = precio * cantidad - descuento;
        const fila = document.createElement("tr");
        fila.innerHTML = `
            <td>${tabla.rows.length + 1}</td>
            <td>${nombre}</td>
            <td>${precio.toFixed(2)}</td>
            <td>${cantidad}</td>
            <td>${descuento.toFixed(2)}</td>
            <td>${importe.toFixed(2)}</td>
            <td><button class="btn btn-danger btn-sm">X</button></td>
        `;
        // Eliminar fila y actualizar array y totales
        fila.querySelector("button").addEventListener("click", () => {
            const idx = Array.from(tabla.children).indexOf(fila);
            detalleVenta.splice(idx, 1);
            fila.remove();
            actualizarNumeros();
            calcularTotales();
        });
        tabla.appendChild(fila);
        detalleVenta.push({
            idproducto: selectedProduct.idproducto,
            producto: nombre,
            precio, cantidad, descuento, importe: importe.toFixed(2)
        });
        inputProductElement.value = "";
        document.getElementById('precio').value = "";
        document.getElementById('cantidad').value = 1;
        document.getElementById('descuento').value = 0;
        calcularTotales();
    });

    // Renumerar filas tras eliminación
    function actualizarNumeros() {
        tabla.querySelectorAll("tr").forEach((fila, i) => {
            fila.children[0].textContent = i + 1;
        });
    }

    // Un único listener para "Guardar" con confirmación
    if (btnFinalizarVenta) {
        btnFinalizarVenta.addEventListener("click", async function (e) {
            e.preventDefault();
            numSerieInput.disabled = false;
            numComInput.disabled = false;

            if (detalleVenta.length === 0) {
                return showToast("Por favor, agrega al menos un producto.", "WARNING", 3000);
            }

            const datosVenta = {
                tipocom: document.querySelector('input[name="tipo"]:checked').value,
                fechahora: fechaInput.value.trim(),
                numserie: numSerieInput.value.trim(),
                numcom: numComInput.value.trim(),
                moneda: monedaSelect.value,
                idcliente: clienteId,
                productos: obtenerDetalleProductos()
            };

            // Llama a registrarVenta, que contiene el ask()
            await registrarVenta(datosVenta);
        });
    }

    // Inicializaciones al cargar la página
    inicializarCampos();
    setFechaDefault();

    // Función principal para registrar en el servidor
    async function registrarVenta(datosVenta) {
        const confirmado = await ask("¿Estás segura de registrar esta venta?", "Registro de Venta");
        if (!confirmado) {
            return showToast("Registro cancelado.", "WARNING", 3000);
        }
        try {
            const resp = await fetch("http://localhost/fix360/app/controllers/Venta.controller.php", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosVenta)
            });
            const json = await resp.json();
            if (json.rows > 0) {
                
                setTimeout(() => window.location.href = 'listar-ventas.php', 1500);
            } else {
                showToast("Hubo un error. Intenta nuevamente.", "ERROR", 3000);
            }
        } catch (err) {
            console.error(err);
            showToast("Error de conexión. Intenta nuevamente.", "ERROR", 3000);
        }
    }

    // Función debounce
    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Función para navegación con el teclado en la lista de autocompletado
    function agregaNavegacion(input, itemsDiv) {
        let currentFocus = -1;
        input.addEventListener("keydown", function (e) {
            const items = itemsDiv.getElementsByTagName("div");
            if (e.key === "ArrowDown") {
                currentFocus++;
                addActive(items);
            } else if (e.key === "ArrowUp") {
                currentFocus--;
                addActive(items);
            } else if (e.key === "Enter") {
                e.preventDefault();
                if (currentFocus > -1 && items[currentFocus]) {
                    items[currentFocus].click();
                }
            }
        });

        function addActive(items) {
            if (!items) return false;
            removeActive(items);
            if (currentFocus >= items.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = items.length - 1;
            items[currentFocus].classList.add("autocomplete-active");
        }

        function removeActive(items) {
            for (let i = 0; i < items.length; i++) {
                items[i].classList.remove("autocomplete-active");
            }
        }
    }

    // Función de autocompletado para clientes
    function mostrarOpcionesCliente(input) {
        cerrarListas();
        if (!input.value) return;
        const searchTerm = input.value;
        fetch(`http://localhost/Fix360/app/controllers/Venta.controller.php?q=${searchTerm}&type=cliente`)
            .then(response => response.json())
            .then(data => {
                const itemsDiv = document.createElement("div");
                itemsDiv.setAttribute("id", "autocomplete-list");
                itemsDiv.setAttribute("class", "autocomplete-items");
                input.parentNode.appendChild(itemsDiv);
                if (data.length === 0) {
                    const noResultsDiv = document.createElement("div");
                    noResultsDiv.textContent = 'No se encontraron clientes';
                    itemsDiv.appendChild(noResultsDiv);
                    return;
                }
                data.forEach(function (cliente) {
                    const optionDiv = document.createElement("div");
                    optionDiv.textContent = cliente.cliente;
                    optionDiv.addEventListener("click", function () {
                        input.value = cliente.cliente;
                        clienteId = cliente.idcliente;
                        cerrarListas();
                    });
                    itemsDiv.appendChild(optionDiv);
                });
                // Habilitar navegación con el teclado en la lista de clientes
                agregaNavegacion(input, itemsDiv);
            })
            .catch(err => console.error('Error al obtener los clientes: ', err));
    }

    // Función de autocompletado para productos
    function mostrarOpcionesProducto(input) {
        cerrarListas();
        if (!input.value) return;
        const searchTerm = input.value;
        fetch(`http://localhost/Fix360/app/controllers/Venta.controller.php?q=${searchTerm}&type=producto`)
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
                        inputCantidad.value = 1;
                        inputDescuento.value = 0;
                        selectedProduct = {
                            idproducto: producto.idproducto,
                            subcategoria_producto: producto.subcategoria_producto,
                            precio: producto.precio
                        };
                        cerrarListas();
                        // Al seleccionar con el autocomplete, mueve el foco al campo "precio"
                        inputPrecio.focus();
                    });
                    itemsDiv.appendChild(optionDiv);
                });
                // Habilitar navegación con el teclado en la lista de productos
                agregaNavegacion(input, itemsDiv);
            })
            .catch(err => console.error('Error al obtener los productos: ', err));
    }

    // Función para cerrar las listas de autocompletado
    function cerrarListas(elemento) {
        const items = document.getElementsByClassName("autocomplete-items");
        while (items.length > 0) {
            items[0].parentNode.removeChild(items[0]);
        }
    }

    // Crear versiones debounced de las funciones de autocompletado
    const debouncedMostrarOpcionesCliente = debounce(mostrarOpcionesCliente, 500);
    const debouncedMostrarOpcionesProducto = debounce(mostrarOpcionesProducto, 500);

    // Listeners usando debounce para clientes y productos
    inputCliente.addEventListener("input", function () {
        debouncedMostrarOpcionesCliente(this);
    });
    inputCliente.addEventListener("click", function () {
        debouncedMostrarOpcionesCliente(this);
    });
    inputProductElement.addEventListener("input", function () {
        debouncedMostrarOpcionesProducto(this);
    });
    inputProductElement.addEventListener("click", function () {
        debouncedMostrarOpcionesProducto(this);
    });

    document.addEventListener("click", function (e) {
        cerrarListas(e.target);
    });

    // Agregar eventos de navegación por Enter en los campos: precio, cantidad y descuento
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

    // Puedes decidir qué hacer al presionar Enter en descuento:
    // Por ejemplo: mover el foco al botón "Agregar" o llamar a la función de agregar producto.
    inputDescuento.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            agregarProductoBtn.focus();
            // o directamente desencadenar la acción, por ejemplo:
            // agregarProductoBtn.click();
        }
    });
});

</script>