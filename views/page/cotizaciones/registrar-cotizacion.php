<?php
const NAMEVIEW = "Cotizacion | Registro";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<div class="container-main mt-5">
    <div class="card border">
        <div class="card-header d-flex justify-content-between align-items-center">
            <!-- Título a la izquierda -->
            <div>
                <!-- <h3 class="mb-0">Complete los datos</h3> -->
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
                        <div class="form-floating input-group mb-3">
                            <input type="text" disabled class="form-control input" id="propietario"
                                placeholder="Propietario" />
                            <label for="propietario"><strong>Propietario</strong></label>
                            <input type="hidden" id="hiddenIdCliente" />
                            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                                data-bs-target="#miModal">
                                ...
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select input" id="moneda" name="moneda" style="color: black;" required>
                                <!-- Aquí sólo meteremos el resto -->
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
                                <label for="producto"><strong>Buscar Producto:</strong></label>
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

    <div class="card mt-2 border">
        <div class="card-body">
            <table class="table table-striped table-sm" id="tabla-detalle">
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
                    <!-- Aquí se agregarán los detalles de los productos -->
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-2 border">
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
                        <td colspan="4" class="text-end">NETO</td>
                        <td>
                            <input type="text" class="form-control input form-control-sm text-end" id="neto" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end">DSCT</td>
                        <td>
                            <input type="text" class="form-control input form-control-sm text-end" id="totalDescuento"
                                readonly>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end">IGV</td>
                        <td>
                            <input type="text" class="form-control input form-control-sm text-end" id="igv" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end">Importe</td>
                        <td>
                            <input type="text" class="form-control input form-control-sm text-end" id="total" readonly>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-4">
                <button id="btnFinalizarCotizacion" type="button" class="btn btn-success text-end">Aceptar</button>
                <a href="" type="reset" class="btn btn-secondary" id="btnFinalizarCotizacion">
                    Cancelar
                </a>
            </div>
        </div>
    </div>

</div>
<!-- fin de cotizacion -->
</div>
</div>
<div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="miModalLabel">Seleccionar Propietario</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Fila para Tipo de Propietario -->
                <div class="row mb-3">
                    <div class="col">
                        <label><strong>Tipo de propietario:</strong></label>
                        <!-- Contenedor de radio buttons -->
                        <div style="display: flex; align-items: center; gap: 10px; margin-left:20px;">
                            <div class="form-check form-check-inline" style="margin-right:40px;">
                                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnpersona"
                                    onclick="actualizarOpciones(); buscarPropietario();" checked>
                                <label class="form-check-label" for="rbtnpersona"
                                    style="margin-left:5px;">Persona</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnempresa"
                                    onclick="actualizarOpciones(); buscarPropietario();">
                                <label class="form-check-label" for="rbtnempresa"
                                    style="margin-left:5px;">Empresa</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fila para Método de Búsqueda -->
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <select id="selectMetodo" class="form-select" style="color: black;">
                                <!-- Se actualizarán las opciones según el tipo (persona/empresa) -->
                            </select>
                            <label for="selectMetodo">Método de búsqueda:</label>
                        </div>
                    </div>
                </div>
                <!-- Fila para Valor Buscado -->
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control input" id="vbuscado" style="background-color: white;"
                                placeholder="Valor buscado" style="accent-color:white;" autofocus />
                            <label for="vbuscado">Valor buscado</label>
                        </div>
                    </div>
                </div>
                <!-- Tabla de Resultados -->
                <p class="mt-3"><strong>Resultado:</strong></p>
                <div class="table-responsive">
                    <table id="tabla-resultado" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Confirmar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pie del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?php
require_once "../../partials/_footer.php";
?>

<script src="<?= SERVERURL ?>views/page/cotizaciones/js/registrar-cotizacion.js"></script>
<!-- js de carga moneda -->
<script src="<?= SERVERURL ?>views/assets/js/moneda.js"></script>
<!-- <script>
    document.addEventListener('DOMContentLoaded', () => {

        // — variables del modal de Propietario —
        const selectMetodo = document.getElementById("selectMetodo");
        const vbuscado = document.getElementById("vbuscado");
        const tablaRes = document.getElementById("tabla-resultado").getElementsByTagName("tbody")[0];
        const hiddenIdCli = document.getElementById("hiddenIdCliente");
        const inputProp = document.getElementById("propietario");

        let propietarioTimer;

        // 1) Actualiza las opciones de búsqueda según Persona / Empresa
        window.actualizarOpciones = function () {
            const esEmpresa = document.getElementById("rbtnempresa").checked;
            // redefinimos los métodos disponibles
            selectMetodo.innerHTML = esEmpresa
                ? '<option value="ruc">RUC</option><option value="razonsocial">Razón Social</option>'
                : '<option value="dni">DNI</option><option value="nombre">Apellidos y Nombres</option>';
        };

        // 2) Función que invoca al controlador y pinta resultados
        window.buscarPropietario = function () {
            const tipo = document.querySelector('input[name="tipoBusqueda"]:checked').id === 'rbtnempresa' ? 'empresa' : 'persona';
            const metodo = selectMetodo.value;
            const valor = vbuscado.value.trim();
            if (!valor) {
                tablaRes.innerHTML = '';
                return;
            }
            fetch(`http://localhost/fix360/app/controllers/propietario.controller.php?task=buscarPropietario&tipo=${tipo}&metodo=${metodo}&valor=${encodeURIComponent(valor)}`)
                .then(r => r.json())
                .then(data => {
                    tablaRes.innerHTML = '';
                    data.forEach((item, i) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
            <td>${i + 1}</td>
            <td>${item.nombre}</td>
            <td>${item.documento}</td>
            <td>
              <button class="btn btn-success btn-sm" data-id="${item.idcliente}">
                <i class="fa-solid fa-circle-check"></i>
              </button>
            </td>`;
                        tablaRes.appendChild(tr);
                    });
                })
                .catch(console.error);
        };

        // 3) Dispara búsqueda con debounce al tipear o cambiar método
        vbuscado.addEventListener('input', () => {
            clearTimeout(propietarioTimer);
            propietarioTimer = setTimeout(buscarPropietario, 300);
        });
        selectMetodo.addEventListener('change', () => {
            clearTimeout(propietarioTimer);
            propietarioTimer = setTimeout(buscarPropietario, 300);
        });

        // 4) Cuando el usuario hace click en “✔” asignamos ID y nombre, y cerramos modal
        document.querySelector("#tabla-resultado").addEventListener("click", function (e) {
            const btn = e.target.closest(".btn-success");
            if (!btn) return;
            const id = btn.getAttribute("data-id");
            const nombre = btn.closest("tr").cells[1].textContent;
            hiddenIdCli.value = id;
            inputProp.value = nombre;
            // disparar evento change para que cargue vehículos, si aplica
            hiddenIdCli.dispatchEvent(new Event("change"));
            // cerrar modal
            document.querySelector("#miModal .btn-close").click();
        });

        // Inicializamos las opciones al abrir el modal
        actualizarOpciones();

    });
</script> -->

<!-- <script>
    document.addEventListener('DOMContentLoaded', function () {

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
        const btnFinalizarCotizacion = document.getElementById("btnFinalizarCotizacion");

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
            fetch(`http://localhost/Fix360/app/controllers/Cotizacion.controller.php?q=${searchTerm}&type=producto`)
                .then(response => response.json())
                .then(obj => {
                    const productos = obj.data || [];
                    const itemsDiv = document.createElement("div");
                    itemsDiv.setAttribute("id", "autocomplete-list-producto");
                    itemsDiv.setAttribute("class", "autocomplete-items");
                    input.parentNode.appendChild(itemsDiv);
                    if (productos.length === 0) {
                        const noResultsDiv = document.createElement("div");
                        noResultsDiv.textContent = 'No se encontraron productos';
                        itemsDiv.appendChild(noResultsDiv);
                        return;
                    }
                    productos.forEach(function (producto) {
                        const optionDiv = document.createElement("div");
                        optionDiv.textContent = producto.subcategoria_producto;
                        optionDiv.addEventListener("click", function () {
                            input.value = producto.subcategoria_producto;
                            inputPrecio.value = producto.precio;
                            /* inputStock.value = producto.stock; */
                            inputCantidad.value = 1;
                            inputDescuento.value = 0;
                            selectedProduct = {
                                idproducto: producto.idproducto,
                                subcategoria_producto: producto.subcategoria_producto,
                                precio: producto.precio
                            };
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
            while (items.length > 0) {
                items[0].parentNode.removeChild(items[0]);
            }
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

        // --- Navegación con Enter entre campos de producto ---
        inputProductElement.addEventListener("keydown", e => { if (e.key === "Enter") { e.preventDefault(); inputPrecio.focus(); } });
        inputPrecio.addEventListener("keydown", e => { if (e.key === "Enter") { e.preventDefault(); inputCantidad.focus(); } });
        inputCantidad.addEventListener("keydown", e => { if (e.key === "Enter") { e.preventDefault(); inputDescuento.focus(); } });
        inputDescuento.addEventListener("keydown", e => {
            if (e.key === "Enter") {
                e.preventDefault();
                agregarProductoBtn.focus();
                // o : agregarProductoBtn.click();
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

        // — Botón Guardar Cotización —
        btnFinalizarCotizacion.addEventListener("click", function (e) {
            e.preventDefault();

            // Deshabilitamos el botón y cambiamos texto de inmediato
            btnFinalizarCotizacion.disabled = true;
            btnFinalizarCotizacion.textContent = "Guardando...";

            // Validaciones básicas
            if (!hiddenIdCliente.value) {
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

            // Diálogo de confirmación
            Swal.fire({
                title: '¿Deseas guardar la cotización?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33'
            }).then(result => {
                if (!result.isConfirmed) {
                    // Si canceló, restauramos el estado del botón
                    btnFinalizarCotizacion.disabled = false;
                    btnFinalizarCotizacion.textContent = "Guardar";
                    return;
                }

                // Preparamos el objeto a enviar
                const data = {
                    fechahora: fechaInput.value.trim(),
                    vigenciadias: diasVigencia,
                    moneda: monedaSelect.value,
                    idcliente: hiddenIdCliente.value,
                    productos: detalleCotizacion
                };

                // Envío al servidor
                fetch("http://localhost/Fix360/app/controllers/Cotizacion.controller.php", {
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
                                    title: '¡Cotización registrada con éxito!',
                                    showConfirmButton: false,
                                    timer: 1800
                                }).then(() => {
                                    window.location.href = 'listar-cotizacion.php';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error al registrar la cotización',
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
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fallo de conexión',
                            text: 'No se pudo contactar al servidor.',
                        });
                    })
                    .finally(() => {
                        btnFinalizarCotizacion.disabled = false;
                        btnFinalizarCotizacion.textContent = "Guardar";
                    });
            });
        });

    });
</script> -->
</body>

</html>