<?php
const NAMEVIEW = "Ventas | Registro";
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
                <a href="listar-ventas.php" class="btn btn-sm btn-success">
                    Mostrar Lista
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="" method="POST" autocomplete="off" id="formulario-detalle">
                <div class="row g-2">
                    <div class="col-md-5">
                        <label>
                            <input class="form-check-input text-start" type="radio" name="tipo" value="factura"
                                onclick="inicializarCampos()">
                            Factura
                        </label>
                        <label style="padding-left: 10px;">
                            <input class="form-check-input text-start" type="radio" name="tipo" value="boleta"
                                onclick="inicializarCampos()" checked>
                            Boleta
                        </label>
                    </div>
                    <!-- N° serie y N° comprobante -->
                    <div class="col-md-7 d-flex align-items-center justify-content-end">
                        <label for="numserie" class="mb-0">N° serie:</label>
                        <input type="text" class="form-control input text-center form-control-sm w-25 ms-2"
                            name="numserie" id="numserie" required disabled />
                        <label for="numcom" class="mb-0 ms-2">N° comprobante:</label>
                        <input type="text" name="numcomprobante" id="numcom"
                            class="form-control text-center input form-control-sm w-25 ms-2" required disabled />
                    </div>
                </div>
                <!-- Sección Cliente, Fecha y Moneda -->
                <div class="row g-2 mt-3">
                    <div class="col-md-4">
                        <div class="form-floating input-group mb-3">
                            <input type="text" disabled class="form-control input" id="propietario"
                                placeholder="Propietario" />
                            <label for="propietario"><strong>Propietario</strong></label>
                            <input type="hidden" id="hiddenIdPropietario" name="idpropietario" />
                            <!-- <input type="hidden" id="hiddenIdCliente" /> -->
                            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                                data-bs-target="#miModal">
                                ...
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 ">
                        <div class="form-floating input-group mb-3">
                            <input type="text" disabled class="form-control input" id="cliente" placeholder="Cliente">
                            <input type="hidden" id="hiddenIdCliente" name="idcliente">
                            <label for="cliente">Cliente</label>
                            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                                data-bs-target="#ModalCliente">…</button>
                        </div>
                    </div>

                    <div class="col-md-4 ">
                        <div class="form-floating">
                            <input type="text" class="form-control input" id="observaciones" placeholder="observaciones"
                                maxlength="255">
                            <label for="observaciones">Observaciones</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select" id="vehiculo" name="vehiculo" style="color:black;">
                                <option selected>Sin vehiculo</option>
                            </select>
                            <label for="vehiculo"><strong>Eliga un vehículo</strong></label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" step="0.1" class="form-control input" id="kilometraje"
                                placeholder="201">
                            <label for="kilometraje"><strong>Kilometraje</strong></label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="date" class="form-control input" name="fechaIngreso" id="fechaIngreso"
                                required />
                            <label for="fechaIngreso">Fecha de venta:</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <select class="form-select input" id="moneda" name="moneda" style="color: black;" required>
                                <!-- “Soles” siempre estático y seleccionado -->
                                <option value="Soles" selected>Soles</option>
                                <!-- Aquí sólo meteremos el resto -->
                            </select>
                            <label for="moneda">Moneda:</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <div class="form-check mt-3 ps-4">
                                <input class="form-check-input" type="checkbox" id="ingresogrua" name="ingresogrua">
                                <label class="form-check-label" for="ingresogrua">
                                    Ingreso grúa
                                </label>
                            </div>
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
                    <div class="col-md-1">
                        <div class="form-floating">
                            <input type="number" class="form-control input" name="stock" id="stock" placeholder="Stock"
                                required readonly />
                            <label for="stock">Stock</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control input" name="precio" id="precio"
                                placeholder="Precio" required />
                            <label for="precio"><strong>Precio</strong></label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control input" name="cantidad" id="cantidad"
                                placeholder="Cantidad" required />
                            <label for="cantidad"><strong>Cantidad</strong></label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <div class="form-floating">
                                <input type="number" class="form-control input" name="descuento" id="descuento"
                                    placeholder="DSCT" required />
                                <label for="descuento">DSCT</label>
                            </div>
                            <button type="button" class="btn btn-sm btn-success" id="agregarProducto">Agregar</button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select" id="subcategoria" name="subcategoria" style="color: black;"
                                required>
                                <option selected>Eliga un tipo de servicio</option>

                            </select>
                            <label for="subcategoria">Tipo de Servicio:</label>
                        </div>
                    </div>
                    <div class="col-md-3 ">
                        <div class="input-group ">
                            <div class="form-floating">
                                <select class="form-select" id="servicio" name="servicio" style="color:black;">
                                    <option selected>Eliga un servicio</option>
                                </select>
                                <label for="servicio">Servicio:</label>
                            </div>
                            <button class="btn btn-sm btn-success" type="button" id="btnAgregarDetalle"
                                data-bs-toggle="modal" data-bs-target="#ModalServicio">
                                <i class="fa-solid fa-circle-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3 ">
                        <div class="form-floating">
                            <select class="form-select" id="mecanico" name="mecanico" style="color:black;">
                                <option selected>Eliga un mecánico</option>
                            </select>
                            <label for="mecanico">Mecánico:</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <div class="form-floating">
                                <input type="number" class="form-control input" step="0.1" placeholder="Precio Servicio"
                                    aria-label="Precio Servicio" min="0.01" id="precioServicio" />
                                <label for="precioServicio">Precio Servicio</label>
                            </div>
                            <button class="btn btn-sm btn-success" type="button"
                                id="btnAgregarServicio">Agregar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Sección de Detalles de la Venta -->
    <div class="card mt-2 border">
        <!-- <div class="card border"> -->
        <div class="card-body">
            <table class="table table-striped table-sm" id="tabla-detalle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Dsct $</th>
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
        <div class="card-body">
            <table class="table table-striped table-sm" id="tabla-detalle-servicios">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Servicio</th>
                        <th>Mecanico</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Datos asíncronos -->
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
                <!-- <a href="" type="button" class="btn input btn-success" id="btnFinalizarVenta">
            Aceptar
          </a> -->
                <button id="btnFinalizarVenta" type="button" class="btn btn-success text-end">Aceptar</button>
                <a href="" type="reset" class="btn btn-secondary" id="btnCancelarVenta">
                    Cancelar
                </a>
            </div>
        </div>
    </div>
</div>
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
<div class="modal fade" id="ModalCliente" tabindex="-1" aria-labelledby="ModalClienteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h2 class="modal-title" id="ModalClienteLabel">Seleccionar Cliente</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <!-- Forzamos tipo “persona” -->
                <input type="hidden" id="tipoBusquedaCliente" value="persona">

                <!-- Método de búsqueda -->
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <select id="selectMetodoCliente" class="form-select"
                                style="background-color: white;color:black;">
                                <option value="dni">DNI</option>
                                <option value="nombre">Nombre</option>
                            </select>
                            <label for="selectMetodoCliente">Método de búsqueda</label>
                        </div>
                    </div>
                </div>

                <!-- Valor buscado -->
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control input" id="vbuscadoCliente"
                                style="background-color: white;" placeholder="Valor buscado" autofocus>
                            <label for="vbuscadoCliente">Valor buscado</label>
                        </div>
                    </div>
                </div>
                <!-- Resultados -->
                <p class="mt-3"><strong>Resultado:</strong></p>
                <div class="table-responsive">
                    <table id="tabla-resultado-cliente" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Confirmar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llena dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

<?php
require_once "../../partials/_footer.php";
?>
<!-- Formulario Venta -->
<script src="<?= SERVERURL ?>views/page/ordenservicios/js/registrar-ordenes.js"></script>
<!-- js de carga moneda -->
<script src="<?= SERVERURL ?>views/assets/js/moneda.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Variables y elementos
        /* const inputCliente = document.getElementById("cliente"); */
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
        const btnFinalizarVenta = document.getElementById('btnFinalizarVenta');
        const detalleServicios = [];
        const tablaServ = document.querySelector("#tabla-detalle-servicios tbody");
        const btnAgregarServicio = document.getElementById("btnAgregarServicio");
        const selectServicio = document.getElementById("servicio");
        const selectMecanico = document.getElementById("mecanico");
        const inputPrecioServicio = document.getElementById("precioServicio");
        const hiddenIdCliente = document.getElementById("hiddenIdCliente");
        /* const inputPrecioServicio = document.getElementById("precioServicio"); */
        function initDateField(id) {
            const el = document.getElementById(id);
            if (!el) return; // si no existe, no hace nada
            const today = new Date();
            const twoAgo = new Date();
            twoAgo.setDate(today.getDate() - 2);
            const fmt = d => d.toISOString().split('T')[0];
            el.value = fmt(today);
            el.min = fmt(twoAgo);
            el.max = fmt(today);
        }

        initDateField('fechaIngreso');
        const fechaInput = document.getElementById("fechaIngreso");
        const monedaSelect = document.getElementById('moneda');

        // --- Funciones auxiliares ---
        function calcularTotales() {
            let totalImporte = 0;
            let totalDescuento = 0;

            // Recorre cada fila de detalle
            tabla.querySelectorAll("tr").forEach(fila => {
                const cantidad = parseFloat(fila.querySelector('.cantidad-input').value) || 0;
                const precio = parseFloat(fila.children[2].textContent) || 0;
                const descuento = parseFloat(fila.children[4].textContent) || 0;

                // Importe neto de la línea
                const importeLinea = (precio - descuento) * cantidad;

                totalImporte += importeLinea;
                totalDescuento += descuento * cantidad;
            });

            // Cálculo de IGV (18%) y neto
            const igv = totalImporte - (totalImporte / 1.18);
            const neto = totalImporte / 1.18;

            // Asignar a los inputs del footer
            document.getElementById("neto").value = neto.toFixed(2);
            document.getElementById("totalDescuento").value = totalDescuento.toFixed(2);
            document.getElementById("igv").value = igv.toFixed(2);
            document.getElementById("total").value = totalImporte.toFixed(2);
        }

        function actualizarNumeros() {
            [...tabla.rows].forEach((fila, i) => fila.cells[0].textContent = i + 1);
        }

        function estaDuplicado(idproducto = 0) {
            return detalleVenta.some(d => d.idproducto == idproducto);
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
            if (detalleServicios.some(s => s.idservicio === idserv)) {
                return alert("Ese servicio ya fue agregado.");
            }

            // 2) Si todo OK, crear la fila
            const nombreServ = selectServicio.selectedOptions[0].text;
            const nombreMec = selectMecanico.selectedOptions[0].text;

            detalleServicios.push({ idservicio: idserv, idmecanico: idmec, precio: precioServ });

            const tr = document.createElement("tr");
            tr.innerHTML = `
    <td>${tablaServ.rows.length + 1}</td>
    <td>${nombreServ}</td>
    <td>${nombreMec}</td>
    <td>${precioServ.toFixed(2)}</td>
    <td><button class="btn btn-danger btn-sm btn-quitar-serv">X</button></td>
  `;
            tr.querySelector(".btn-quitar-serv").addEventListener("click", () => {
                const idx = detalleServicios.findIndex(s => s.idservicio === idserv);
                detalleServicios.splice(idx, 1);
                tr.remove();
                [...tablaServ.rows].forEach((r, i) => r.cells[0].textContent = i + 1);
            });
            tablaServ.appendChild(tr);

            // 3) Limpia el campo de precio para la próxima vez
            inputPrecioServicio.value = "";
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
                alert(`No puedes pedir ${cantidad} unidades; solo hay ${stockDisponible} en stock.`);
                document.getElementById("cantidad").value = stockDisponible;
                return;
            }

            // Validación de descuento unitario
            if (descuento > precio) {
                alert("El descuento unitario no puede ser mayor que el precio unitario.");
                document.getElementById("descuento").value = "";
                return;
            }

            if (detalleVenta.some(d => d.idproducto === idp)) {
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
                const idx = detalleVenta.findIndex(d => d.idproducto === idElim);
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
                const idx = detalleVenta.findIndex(d => d.idproducto === idp);
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
                const idx = detalleVenta.findIndex(d => d.idproducto === idp);
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
                importe: importe.toFixed(2)
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
                    block: "nearest"
                });
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
        tipoInputs.forEach(i => i.addEventListener("change", inicializarCampos));
        inicializarCampos();
        // --- Navegación con Enter entre campos de producto ---
        inputProductElement.addEventListener("keydown", e => {
            if (e.key === "Enter") {
                e.preventDefault();
                inputPrecio.focus();
            }
        });
        inputPrecio.addEventListener("keydown", e => {
            if (e.key === "Enter") {
                e.preventDefault();
                inputCantidad.focus();
            }
        });
        inputCantidad.addEventListener("keydown", e => {
            if (e.key === "Enter") {
                e.preventDefault();
                inputDescuento.focus();
            }
        });
        inputDescuento.addEventListener("keydown", e => {
            if (e.key === "Enter") {
                e.preventDefault();
                agregarProductoBtn.focus(); // o : agregarProductoBtn.click();
            }
        });
        // --- Guardar Venta ---
        btnFinalizarVenta.addEventListener('click', function (e) {
            e.preventDefault();

            // 0) Capturo hiddenIdCliente (¡muy importante!)
            const hiddenIdCliente = document.getElementById("hiddenIdCliente");

            if (detalleVenta.length === 0 && detalleServicios.length === 0) {
                return showToast("Agrega al menos un producto o servicio.", "WARNING", 2000);
            }

            Swal.fire({
                title: '¿Deseas guardar la venta?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33'
            }).then(result => {
                if (!result.isConfirmed) return;

                // Deshabilito el botón
                btnFinalizarVenta.disabled = true;
                btnFinalizarVenta.textContent = 'Guardando...';

                // 1) Construyo el objeto de datos
                const data = {
                    tipocom: document.querySelector('input[name="tipo"]:checked').value,
                    fechahora: fechaInput.value.trim(),   // ya no usas fechaVenta
                    fechaingreso: null,
                    numserie: numSerieInput.value.trim(),
                    numcom: numComInput.value.trim(),
                    moneda: monedaSelect.value,
                    idpropietario: +document.getElementById("hiddenIdPropietario").value,
                    idcliente: +hiddenIdCliente.value,
                    idvehiculo: vehiculoSelect.value ? +vehiculoSelect.value : null,
                    kilometraje: parseFloat(document.getElementById("kilometraje").value) || 0,
                    observaciones: document.getElementById("observaciones").value.trim(),
                    ingresogrua: document.getElementById("ingresogrua").checked ? 1 : 0,
                    productos: detalleVenta,
                    servicios: detalleServicios
                };

                console.log("Payload a enviar:", data);

                // 2) Disparo el fetch
                fetch("http://localhost/Fix360/app/controllers/Venta.controller.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(data)
                })
                    .then(async res => {
                        const text = await res.text();           // leo texto bruto
                        console.log("Respuesta HTTP:", res.status, text);
                        try {
                            return JSON.parse(text);               // intento parsear JSON
                        } catch {
                            throw new Error("Respuesta no es JSON válido");
                        }
                    })
                    .then(json => {
                        if (json.status === "success") {
                            showToast(
                                'Guardado con éxito. Venta #' + json.idventa +
                                (json.idorden ? ', Orden #' + json.idorden : ''),
                                'SUCCESS', 1500
                            );
                            setTimeout(() => location.href = 'listar-ventas.php', 1500);
                        } else {
                            Swal.fire('Error', json.message || 'No se pudo registrar.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error("Error en fetch:", err);
                        Swal.fire('Error', err.message, 'error');
                    })
                    .finally(() => {
                        btnFinalizarVenta.disabled = false;
                        btnFinalizarVenta.textContent = "Guardar";
                    });
            });
        });
    });
</script>

</body>

</html>
<!-- <script>
  document.addEventListener('DOMContentLoaded', () => {
    // — variables del modal de Propietario —
    const selectMetodo = document.getElementById("selectMetodo");
    const vbuscado = document.getElementById("vbuscado");
    const tablaRes = document.getElementById("tabla-resultado").getElementsByTagName("tbody")[0];
    const hiddenIdCli = document.getElementById("hiddenIdCliente");
    const vehiculoSelect = document.getElementById("vehiculo");
    const inputProp = document.getElementById("propietario");
    let propietarioTimer;

    // --- NUEVO: cargarVehiculos y listener ---
    function cargarVehiculos() {
      const id = hiddenIdCliente.value;
      vehiculoSelect.innerHTML = '<option value="">Sin vehículo</option>';
      if (!id) return;
      fetch(`http://localhost/fix360/app/controllers/vehiculo.controller.php?task=getVehiculoByCliente&idcliente=${encodeURIComponent(id)}`)
        .then(res => res.json())
        .then(data => {
          data.forEach(item => {
            const opt = document.createElement("option");
            opt.value = item.idvehiculo;
            opt.textContent = item.vehiculo;
            vehiculoSelect.appendChild(opt);
          });
        })
        .catch(err => console.error("Error al cargar vehículos:", err));
    }
    hiddenIdCliente.addEventListener("change", cargarVehiculos);
    // — FIN cargarVehiculos —

    // 1) Actualiza las opciones de búsqueda según Persona / Empresa
    window.actualizarOpciones = function () {
      const esEmpresa = document.getElementById("rbtnempresa").checked;
      // redefinimos los métodos disponibles
      selectMetodo.innerHTML = esEmpresa ?
        '<option value="ruc">RUC</option><option value="razonsocial">Razón Social</option>' :
        '<option value="dni">DNI</option><option value="nombre">Apellidos y Nombres</option>';
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