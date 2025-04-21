<?php
const NAMEVIEW = "Registrar Cotización";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<div class="container-main mt-5">
    <div class="card border">
        <div class="card-header d-flex justify-content-between align-items-center">
            <!-- Título a la izquierda -->
            <div>
                <h3 class="mb-0">Complete los datos</h3>
            </div>
            <!-- Botón a la derecha -->
            <div>
                <a href="listar-cotizacion.php" class="btn btn-sm btn-success">
                    Mostrar Lista
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="" method="POST" autocomplete="off" id="formulario-detalle">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" class="form-control input" name="fecha" id="fecha" required />
                            <label for="fecha">Fecha de Cotización</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" class="form-control input" name="vigenciadias" id="vigenciadias"
                                required />
                            <label for="vigenciadias">Vigencia</label>
                        </div>
                    </div>
                </div>
                <!-- Sección Cliente, Fecha y Moneda -->
                <div class="row g-2 mt-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input name="cliente" id="cliente" type="text" class=" form-control input"
                                placeholder="Producto" required />
                            <label for="cliente">Cliente</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select input" id="moneda" name="moneda" style="color: black;" required>
                                <option value="soles" selected>Soles</option>
                                <!-- Aquí se insertan dinámicamente el resto de monedas -->
                            </select>
                            <label for="moneda">Moneda:</label>
                        </div>
                    </div>
                </div>

                <!-- Sección Producto, Precio, Cantidad y Descuento -->
                <div class="row g-2 mt-3">
                    <div class="col-md-5">
                        <div class="autocomplete">
                            <div class="form-floating">
                                <!-- Campo de búsqueda de Producto -->
                                <input name="producto" id="producto" type="text"
                                    class="autocomplete-input form-control input" placeholder="Buscar Producto"
                                    required>
                                <label for="producto">Buscar Producto:</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control input" name="precio" id="precio" required />
                            <label for="precio">Precio</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control input" name="cantidad" id="cantidad" required />
                            <label for="cantidad">Cantidad</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <div class="form-floating">
                                <input type="number" class="form-control input" name="descuento" id="descuento"
                                    required />
                                <label for="descuento">Descuento</label>
                            </div>
                            <button type="button" class="btn btn-success" id="agregarProducto">Agregar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="container-main-2 mt-4">
        <div class="card border">
            <div class="card-body p-3">
                <table class="table table-striped table-sm mb-0" id="tabla-detalle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Dsct</th>
                            <th>Importe</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="card-footer text-end">
                <table class="tabla table-sm">
                    <colgroup>
                        <col style="width: 10%;">
                        <col style="width: 60%;">
                        <col style="width: 10%;">
                        <col style="width: 10%;">
                        <col style="width: 10%;">
                        <col style="width: 5%;">
                    </colgroup>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-end">Importe</td>
                            <td>
                                <input type="text" class="form-control input form-control-sm text-end" id="total"
                                    readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Dsct</td>
                            <td>
                                <input type="text" class="form-control input form-control-sm text-end"
                                    id="totalDescuento" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Igv</td>
                            <td>
                                <input type="text" class="form-control input form-control-sm text-end" id="igv"
                                    readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Neto</td>
                            <td>
                                <input type="text" class="form-control input form-control-sm text-end" id="neto"
                                    readonly>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-4">
                    <a href="" class="btn input btn-success" id="btnFinalizarCotizacion">
                        Guardar
                    </a>
                    <a href="" class="btn input btn-secondary" id="btnCancelarCotizacion">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- fin de cotizacion -->
</div>
</div>
</body>
</html>

<!-- 
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputCliente = document.getElementById("cliente");
        const inputProductElement = document.getElementById("producto");
        const inputPrecio = document.getElementById("precio");
        const inputCantidad = document.getElementById("cantidad");
        const inputDescuento = document.getElementById("descuento");

        let clienteId = null;
        let selectedProduct = {};
        const agregarProductoBtn = document.getElementById("agregarProducto");
        const tabla = document.querySelector("#tabla-detalle tbody");
        const detalleCotizacion = [];
        const fechaInput = document.getElementById("fecha");
        const vigenciaDiasInput = document.getElementById("vigenciadias");
        const monedaSelect = document.getElementById('moneda');

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
            fetch(`http://localhost/Fix360/app/controllers/Cotizacion.controller.php?q=${searchTerm}&type=cliente`)
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
            fetch(`http://localhost/Fix360/app/controllers/Cotizacion.controller.php?q=${searchTerm}&type=producto`)
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

        // Agregar producto al detalle de venta
        agregarProductoBtn.addEventListener("click", function () {
            const productoNombre = inputProductElement.value;
            const productoPrecio = parseFloat(inputPrecio.value);
            const productoCantidad = parseFloat(inputCantidad.value);
            const productoDescuento = parseFloat(inputDescuento.value);
            if (!productoNombre || isNaN(productoPrecio) || isNaN(productoCantidad)) {
                alert("Por favor, complete todos los campos correctamente.");
                return;
            }
            if (estaDuplicado(selectedProduct.idproducto)) {
                alert("Este producto ya ha sido agregado.");
                inputProductElement.value = "";
                inputPrecio.value = "";
                inputCantidad.value = 1;
                inputDescuento.value = 0;
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
            detalleCotizacion.push(detalle);
            inputProductElement.value = "";
            inputPrecio.value = "";
            inputCantidad.value = 1;
            inputDescuento.value = 0;
            calcularTotales();
        });

        function actualizarNumeros() {
            const filas = tabla.getElementsByTagName("tr");
            for (let i = 0; i < filas.length; i++) {
                filas[i].children[0].textContent = i + 1;
            }
        }

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

        // Establecer fecha actual por defecto en ambos campos
        const setFechaDefault = () => {
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            const formattedDate = `${year}-${month}-${day}`;

            fechaInput.value = formattedDate;
            vigenciaDiasInput.value = formattedDate;
        };
        setFechaDefault();

        // Variable para almacenar días de vigencia calculados
        let diasVigencia = 0;
        // Evento para calcular días de vigencia al cambiar la fecha
        vigenciaDiasInput.addEventListener("change", function () {
            const fechaCotizacion = new Date(fechaInput.value);
            const fechaVigencia = new Date(vigenciaDiasInput.value);

            // Calcula diferencia en milisegundos y convierte a días
            const diffTime = fechaVigencia - fechaCotizacion;
            diasVigencia = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diasVigencia < 0) {
                alert("La vigencia no puede ser menor a la fecha de cotización.");
                vigenciaDiasInput.value = fechaInput.value; // Restablece la fecha
                diasVigencia = 0;
            } else {
                console.log(`Días de vigencia: ${diasVigencia}`);
            }
        });

        // Verifica si el producto ya está en el detalle para evitar duplicados
        function estaDuplicado(idproducto = 0) {
            let estado = false;
            let i = 0;
            while (i < detalleCotizacion.length && !estado) {
                if (detalleCotizacion[i].idproducto == idproducto) {
                    estado = true;
                }
                i++;
            }
            return estado;
        }

        //boton Guardar
        btnFinalizarCotizacion.addEventListener("click", function (e) {
            e.preventDefault();
            btnFinalizarCotizacion.disabled = true;
            btnFinalizarCotizacion.textContent = "Guardando...";

            // Validaciones
            if (!clienteId) {
                alert("Por favor, selecciona un cliente.");
                btnFinalizarCotizacion.disabled = false;
                btnFinalizarCotizacion.textContent = "Guardar";
                return;
            }
            if (detalleCotizacion.length === 0) {
                alert("Por favor, agrega al menos un producto.");
                btnFinalizarCotizacion.disabled = false;
                btnFinalizarCotizacion.textContent = "Guardar";
                return;
            }

            // Armar objeto de datos a enviar
            const data = {
                fechahora: fechaInput.value.trim(),
                vigenciadias: diasVigencia,
                moneda: monedaSelect.value,
                idcliente: clienteId,
                productos: detalleCotizacion
            };

            // Envío de datos al servidor
            fetch("http://localhost/Fix360/app/controllers/Cotizacion.controller.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            })
                .then(response => response.text())
                .then(text => {
                    // Procesamiento de la respuesta
                    console.log("Respuesta del servidor:", text);
                    try {
                        const json = JSON.parse(text);
                        if (json && json.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Cotizacion registrada con éxito!',
                                showConfirmButton: false,
                                timer: 1800
                            }).then(() => {
                                window.location.href = 'listar-cotizacion.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al registrar la Cotizacion',
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
                    btnFinalizarCotizacion.disabled = false;
                    btnFinalizarCotizacion.textContent = "Guardar";
                });
        });
    });
</script> -->
<script src="<?= SERVERURL?>views/page/cotizaciones/js/registrar-cotizacion.js"></script>
<!-- js de carga moneda -->
<script src="<?= SERVERURL ?>views/assets/js/tipomoneda.js"></script>
<?php

require_once "../../partials/_footer.php";

?>