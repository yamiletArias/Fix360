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

    function calcularTotales() {
        let totalImporte = 0;
        let totalDescuento = 0;

        document.querySelectorAll("#tabla-detalle tbody tr").forEach(fila => {
            const subtotal = parseFloat(fila.querySelector("td:nth-child(6)").textContent) || 0;
            const descuento = parseFloat(fila.querySelector("td:nth-child(5)").textContent) || 0;
            totalImporte += subtotal;
            totalDescuento += descuento;
        });

        // Calcular IGV y Neto
        const igv = totalImporte - (totalImporte / 1.18);
        const neto = totalImporte / 1.18;
        document.getElementById("total").value = totalImporte.toFixed(2);
        document.getElementById("totalDescuento").value = totalDescuento.toFixed(2);
        document.getElementById("igv").value = igv.toFixed(2);
        document.getElementById("neto").value = neto.toFixed(2);
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
            })
            .catch(err => console.error('Error al obtener los clientes: ', err));
    }
    inputCliente.addEventListener("input", function () {
        mostrarOpcionesCliente(this);
    });
    inputCliente.addEventListener("click", function () {
        mostrarOpcionesCliente(this);
    });

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
                        document.getElementById('precio').value = producto.precio;
                        document.getElementById('cantidad').value = 1;
                        document.getElementById('descuento').value = 0;
                        selectedProduct = {
                            idproducto: producto.idproducto,
                            subcategoria_producto: producto.subcategoria_producto,
                            precio: producto.precio
                        };
                        cerrarListas();
                    });
                    itemsDiv.appendChild(optionDiv);
                });
            })
            .catch(err => console.error('Error al obtener los productos: ', err));
    }
    inputProductElement.addEventListener("input", function () {
        mostrarOpcionesProducto(this);
    });
    inputProductElement.addEventListener("click", function () {
        mostrarOpcionesProducto(this);
    });
    document.addEventListener("click", function (e) {
        cerrarListas(e.target);
    });
    function cerrarListas(elemento) {
        const items = document.getElementsByClassName("autocomplete-items");
        while (items.length > 0) {
            items[0].parentNode.removeChild(items[0]);
        }
    }

    // Verifica si el producto ya está en el detalle para evitar duplicados
    function estaDuplicado(idproducto = 0) {
        let estado = false;
        let i = 0;
        while (i < detalleVenta.length && !estado) {
            if (detalleVenta[i].idproducto == idproducto) {
                estado = true;
            }
            i++;
        }
        return estado;
    }

    // Agregar producto al detalle de venta
    agregarProductoBtn.addEventListener("click", function () {
        const productoNombre = inputProductElement.value;
        const productoPrecio = parseFloat(document.getElementById('precio').value);
        const productoCantidad = parseFloat(document.getElementById('cantidad').value);
        const productoDescuento = parseFloat(document.getElementById('descuento').value);
        if (!productoNombre || isNaN(productoPrecio) || isNaN(productoCantidad)) {
            alert("Por favor, complete todos los campos correctamente.");
            return;
        }
        if (estaDuplicado(selectedProduct.idproducto)) {
            alert("Este producto ya ha sido agregado.");
            inputProductElement.value = "";
            document.getElementById('precio').value = "";
            document.getElementById('cantidad').value = 1;
            document.getElementById('descuento').value = 0;
            return;
        }
        const importe = (productoPrecio * productoCantidad) - productoDescuento;
        const nuevaFila = document.createElement("tr");
        nuevaFila.innerHTML = `
        <td>${tabla.rows.length + 1}</td>
        <td>${productoNombre}</td>
        <td>${productoPrecio.toFixed(2)}</td>
        <td>${productoCantidad}</td>
        <td>${productoDescuento.toFixed(2)}</td>
        <td>${importe.toFixed(2)}</td>
        <td><button class="btn btn-danger btn-sm">X</button></td>
      `;
        // Al eliminar una fila, además de actualizar números, se deben recalcular los totales
        nuevaFila.querySelector("button").addEventListener("click", function () {
            nuevaFila.remove();
            actualizarNumeros();
            calcularTotales();
        });
        tabla.appendChild(nuevaFila);

        // Agregar al array de detalles
        const detalle = {
            idproducto: selectedProduct.idproducto,
            producto: productoNombre,
            precio: productoPrecio,
            cantidad: productoCantidad,
            descuento: productoDescuento,
            importe: importe.toFixed(2)
        };
        detalleVenta.push(detalle);

        // Limpiar campos de producto
        inputProductElement.value = "";
        document.getElementById('precio').value = "";
        document.getElementById('cantidad').value = 1;
        document.getElementById('descuento').value = 0;

        // ¡Recalcular totales tras agregar!
        calcularTotales();
    });
    function actualizarNumeros() {
        const filas = tabla.getElementsByTagName("tr");
        for (let i = 0; i < filas.length; i++) {
            filas[i].children[0].textContent = i + 1;
        }
    }

    // Funciones para generar números de serie y comprobante
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
        if (tipoSeleccionado === "boleta") {
            numSerieInput.value = generateNumber("B");
            numComInput.value = generateComprobanteNumber("B");
        } else {
            numSerieInput.value = generateNumber("F");
            numComInput.value = generateComprobanteNumber("F");
        }
    }
    inicializarCampos();
    tipoInputs.forEach((input) => {
        input.addEventListener("change", inicializarCampos);
    });

    // Establecer fecha actual por defecto
    const setFechaDefault = () => {
        const today = new Date();
        const day = String(today.getDate()).padStart(2, '0');
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = today.getFullYear();
        fechaInput.value = `${year}-${month}-${day}`;
    };
    setFechaDefault();

    // Script del botón "Guardar"
    btnFinalizarVenta.addEventListener("click", function (e) {
        e.preventDefault();
        btnFinalizarVenta.disabled = true;
        btnFinalizarVenta.textContent = "Guardando...";

        // Habilitar los inputs para que se envíen los valores
        numSerieInput.disabled = false;
        numComInput.disabled = false;

        // Validación de cliente comentada para hacerlo opcional
        // if (!clienteId) {
        //   alert("Por favor, selecciona un cliente.");
        //   btnFinalizarVenta.disabled = false;
        //   btnFinalizarVenta.textContent = "Guardar";
        //   return;
        // }

        if (detalleVenta.length === 0) {
            alert("Por favor, agrega al menos un producto.");
            btnFinalizarVenta.disabled = false;
            btnFinalizarVenta.textContent = "Guardar";
            return;
        }

        // Armar el objeto de datos a enviar
        const data = {
            tipocom: document.querySelector('input[name="tipo"]:checked').value,
            fechahora: fechaInput.value.trim(),
            numserie: numSerieInput.value.trim(),
            numcom: numComInput.value.trim(),
            moneda: monedaSelect.value,
            idcliente: clienteId,  // Si no se selecciona, este valor será null o 0
            productos: detalleVenta
        };

        // Enviar datos al servidor usando fetch...
        fetch("http://localhost/Fix360/app/controllers/Venta.controller.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        })
            .then(response => response.text())
            .then(text => {
                console.log("Respuesta del servidor:", text);
                try {
                    const json = JSON.parse(text);
                    if (json && json.status === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Venta registrada con éxito!',
                            showConfirmButton: false,
                            timer: 1800
                        }).then(() => {
                            window.location.href = 'listar-ventas.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al registrar la venta',
                            text: 'Inténtalo nuevamente.',
                        });
                    }
                } catch (e) {
                    console.error("No se pudo parsear JSON:", e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Respuesta inesperada',
                        text: 'El servidor no devolvió una respuesta válida.',
                    });
                }
            })
            .finally(() => {
                btnFinalizarVenta.disabled = false;
                btnFinalizarVenta.textContent = "Guardar";
            });
    });
});