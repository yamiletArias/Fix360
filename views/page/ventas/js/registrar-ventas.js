document.addEventListener('DOMContentLoaded', function () {
    // Variables y elementos
    const inputCliente       = document.getElementById("cliente");
    const inputProductElement= document.getElementById("producto");
    const inputPrecio        = document.getElementById("precio");
    const inputCantidad      = document.getElementById("cantidad");
    const inputDescuento     = document.getElementById("descuento");
    let clienteId            = null;
    let selectedProduct      = {};
    const numSerieInput      = document.getElementById("numserie");
    const numComInput        = document.getElementById("numcom");
    const tipoInputs         = document.querySelectorAll('input[name="tipo"]');
    const agregarProductoBtn = document.getElementById("agregarProducto");
    const tabla              = document.querySelector("#tabla-detalle tbody");
    const detalleVenta       = [];
    const btnFinalizarVenta  = document.getElementById('btnFinalizarVenta');
    const fechaInput         = document.getElementById("fecha");
    const monedaSelect       = document.getElementById('moneda');

    // --- Funciones auxiliares ---

    function calcularTotales() {
        let totalImporte = 0, totalDescuento = 0;
        document.querySelectorAll("#tabla-detalle tbody tr").forEach(fila => {
            totalImporte   += parseFloat(fila.children[5].textContent) || 0;
            totalDescuento += parseFloat(fila.children[4].textContent) || 0;
        });
        const igv  = totalImporte - (totalImporte / 1.18);
        const neto = totalImporte / 1.18;
        document.getElementById("total").value          = totalImporte.toFixed(2);
        document.getElementById("totalDescuento").value = totalDescuento.toFixed(2);
        document.getElementById("igv").value            = igv.toFixed(2);
        document.getElementById("neto").value           = neto.toFixed(2);
    }

    function actualizarNumeros() {
        [...tabla.rows].forEach((fila, i) => fila.cells[0].textContent = i + 1);
    }

    function estaDuplicado(idproducto = 0) {
        return detalleVenta.some(d => d.idproducto == idproducto);
    }

    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

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
            Array.from(items).forEach(item => item.classList.remove("autocomplete-active"));
        }
    }

    function cerrarListas(excepto) {
        document.querySelectorAll(".autocomplete-items").forEach(div => {
            if (div !== excepto) div.remove();
        });
    }

    // --- Autocompletado Clientes ---

    function mostrarOpcionesCliente(input) {
        cerrarListas();
        if (!input.value) return;
        fetch(`http://localhost/Fix360/app/controllers/Venta.controller.php?q=${encodeURIComponent(input.value)}&type=cliente`)
            .then(res => res.json())
            .then(data => {
                const itemsDiv = document.createElement("div");
                itemsDiv.id    = "autocomplete-list-cliente";
                itemsDiv.className = "autocomplete-items";
                input.parentNode.appendChild(itemsDiv);

                if (data.length === 0) {
                    const noRes = document.createElement("div");
                    noRes.textContent = 'No se encontraron clientes';
                    itemsDiv.appendChild(noRes);
                } else {
                    data.forEach(cliente => {
                        const optionDiv = document.createElement("div");
                        optionDiv.textContent = cliente.cliente;
                        optionDiv.addEventListener("click", () => {
                            input.value  = cliente.cliente;
                            clienteId    = cliente.idcliente;
                            cerrarListas(itemsDiv);
                        });
                        itemsDiv.appendChild(optionDiv);
                    });
                    agregaNavegacion(input, itemsDiv);
                }
            })
            .catch(err => console.error('Error al obtener los clientes:', err));
    }
    const debouncedClientes = debounce(mostrarOpcionesCliente, 300);
    inputCliente.addEventListener("input",  () => debouncedClientes(inputCliente));
    inputCliente.addEventListener("click",  () => debouncedClientes(inputCliente));
    document.addEventListener("click", e => cerrarListas(e.target));

    // --- Autocompletado Productos ---

    function mostrarOpcionesProducto(input) {
        cerrarListas();
        if (!input.value) return;
        fetch(`http://localhost/Fix360/app/controllers/Venta.controller.php?q=${encodeURIComponent(input.value)}&type=producto`)
            .then(res => res.json())
            .then(data => {
                const itemsDiv = document.createElement("div");
                itemsDiv.id    = "autocomplete-list-producto";
                itemsDiv.className = "autocomplete-items";
                input.parentNode.appendChild(itemsDiv);

                if (data.length === 0) {
                    const noRes = document.createElement("div");
                    noRes.textContent = 'No se encontraron productos';
                    itemsDiv.appendChild(noRes);
                } else {
                    data.forEach(prod => {
                        const optionDiv = document.createElement("div");
                        optionDiv.textContent = prod.subcategoria_producto;
                        optionDiv.addEventListener("click", () => {
                            inputProductElement.value = prod.subcategoria_producto;
                            inputPrecio.value         = prod.precio;
                            inputCantidad.value       = 1;
                            inputDescuento.value      = 0;
                            selectedProduct = {
                                idproducto: prod.idproducto,
                                subcategoria_producto: prod.subcategoria_producto,
                                precio: prod.precio
                            };
                            cerrarListas(itemsDiv);
                        });
                        itemsDiv.appendChild(optionDiv);
                    });
                    agregaNavegacion(input, itemsDiv);
                }
            })
            .catch(err => console.error('Error al obtener productos:', err));
    }
    const debouncedProductos = debounce(mostrarOpcionesProducto, 300);
    inputProductElement.addEventListener("input", () => debouncedProductos(inputProductElement));
    inputProductElement.addEventListener("click", () => debouncedProductos(inputProductElement));

    // --- Agregar Producto al Detalle ---

    agregarProductoBtn.addEventListener("click", () => {
        const nombre   = inputProductElement.value;
        const precio   = parseFloat(inputPrecio.value);
        const cantidad = parseFloat(inputCantidad.value);
        const descuento= parseFloat(inputDescuento.value);
        if (!nombre || isNaN(precio) || isNaN(cantidad)) {
            return alert("Completa todos los campos correctamente.");
        }
        if (estaDuplicado(selectedProduct.idproducto)) {
            alert("Este producto ya ha sido agregado.");
            inputProductElement.value = "";
            inputPrecio.value = "";
            inputCantidad.value = 1;
            inputDescuento.value = 0;
            return;
        }
        const importe = (precio * cantidad) - descuento;
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
        fila.querySelector("button").addEventListener("click", () => {
            fila.remove();
            actualizarNumeros();
            calcularTotales();
        });
        tabla.appendChild(fila);

        detalleVenta.push({ 
            idproducto: selectedProduct.idproducto,
            producto: nombre,
            precio, cantidad, descuento,
            importe: importe.toFixed(2)
        });

        // Reset campos
        inputProductElement.value = "";
        inputPrecio.value = "";
        inputCantidad.value = 1;
        inputDescuento.value = 0;

        calcularTotales();
    });

    // --- Generación de Serie y Comprobante ---

    function generateNumber(type) {
        return `${type}${String(Math.floor(Math.random()*100)).padStart(3,"0")}`;
    }
    function generateComprobanteNumber(type) {
        return `${type}-${String(Math.floor(Math.random()*1e7)).padStart(7,"0")}`;
    }
    function inicializarCampos() {
        const tipo = document.querySelector('input[name="tipo"]:checked').value;
        if (tipo === "boleta") {
            numSerieInput.value = generateNumber("B");
            numComInput.value   = generateComprobanteNumber("B");
        } else {
            numSerieInput.value = generateNumber("F");
            numComInput.value   = generateComprobanteNumber("F");
        }
    }
    tipoInputs.forEach(i => i.addEventListener("change", inicializarCampos));
    inicializarCampos();

    // --- Fecha por defecto ---

    (function setFechaDefault(){
        const t = new Date();
        fechaInput.value = `${t.getFullYear()}-${String(t.getMonth()+1).padStart(2,"0")}-${String(t.getDate()).padStart(2,"0")}`;
    })();

    // --- Navegación con Enter entre campos de producto ---

    inputProductElement.addEventListener("keydown", e => { if (e.key==="Enter") { e.preventDefault(); inputPrecio.focus(); } });
    inputPrecio.addEventListener("keydown",      e => { if (e.key==="Enter") { e.preventDefault(); inputCantidad.focus(); } });
    inputCantidad.addEventListener("keydown",    e => { if (e.key==="Enter") { e.preventDefault(); inputDescuento.focus(); } });
    inputDescuento.addEventListener("keydown",   e => {
        if (e.key==="Enter") {
            e.preventDefault();
            agregarProductoBtn.focus();
            // o bien: agregarProductoBtn.click();
        }
    });

    // --- Guardar Venta ---

    btnFinalizarVenta.addEventListener("click", function (e) {
        e.preventDefault();
        btnFinalizarVenta.disabled = true;
        btnFinalizarVenta.textContent = "Guardando...";
        numSerieInput.disabled = numComInput.disabled = false;

        if (detalleVenta.length === 0) {
            alert("Agrega al menos un producto.");
            btnFinalizarVenta.disabled = false;
            btnFinalizarVenta.textContent = "Guardar";
            return;
        }

        const data = {
            tipocom: document.querySelector('input[name="tipo"]:checked').value,
            fechahora: fechaInput.value.trim(),
            numserie: numSerieInput.value.trim(),
            numcom: numComInput.value.trim(),
            moneda: monedaSelect.value,
            idcliente: clienteId,
            productos: detalleVenta
        };

        fetch("http://localhost/Fix360/app/controllers/Venta.controller.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        })
        .then(r => r.text())
        .then(text => {
            try {
                const json = JSON.parse(text);
                if (json.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Venta registrada con éxito!',
                        showConfirmButton: false,
                        timer: 1800
                    }).then(() => window.location.href = 'listar-ventas.php');
                } else {
                    throw new Error();
                }
            } catch {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Respuesta inesperada del servidor.'
                });
            }
        })
        .finally(() => {
            btnFinalizarVenta.disabled = false;
            btnFinalizarVenta.textContent = "Guardar";
        });
    });
});
