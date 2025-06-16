<?php
const NAMEVIEW = "Cotizacion | Registro";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>

<div class="container-main mt-5">
    <div class="card border">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <!-- Título a la izquierda -->
            </div>
            <div>
                <a href="listar-cotizacion.php" class="btn btn-sm btn-success">Mostrar Lista</a>
                <button id="btnToggleService" class="btn btn-sm btn-success">Agregar servicio</button>
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
                            <input type="date" class="form-control input" name="vigenciadias" id="vigenciadias" required />
                            <label for="vigenciadias">Vigencia</label>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-3">
                    <div class="col-md-6">
                        <div class="form-floating input-group mb-3">
                            <input type="text" disabled class="form-control input" id="propietario" placeholder="Propietario" />
                            <label for="propietario"><strong>Propietario</strong></label>
                            <input type="hidden" id="hiddenIdCliente" />
                            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#miModal">...</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select input" id="moneda" name="moneda" style="color: black;" required>
                                <!-- Opciones cargadas por moneda.js -->
                            </select>
                            <label for="moneda">Moneda:</label>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-3">
                    <div class="col-md-5">
                        <div class="autocomplete">
                            <div class="form-floating">
                                <input name="producto" id="producto" type="text" class="autocomplete-input form-control input" placeholder="Buscar Producto" required>
                                <label for="producto"><strong>Buscar Producto:</strong></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control input" name="precio" id="precio" required step="0.01" min="0" />
                            <label for="precio">Precio</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control input" name="cantidad" id="cantidad" required step="1" min="1" />
                            <label for="cantidad">Cantidad</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <div class="form-floating">
                                <input type="number" class="form-control input" name="descuento" id="descuento" required step="0.01" min="0" />
                                <label for="descuento">Descuento</label>
                            </div>
                            <button type="button" class="btn btn-success" id="agregarProducto">Agregar</button>
                        </div>
                    </div>
                </div>

                <div id="serviceSection" class="row g-2 mt-3 d-none">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select class="form-select" id="subcategoria" name="subcategoria" style="color: black;" required>
                                <option value="">Eliga un tipo de servicio</option>
                                <!-- Opciones cargadas por JS -->
                            </select>
                            <label for="subcategoria">Tipo de Servicio:</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="form-floating">
                                <select class="form-select" id="servicio" name="servicio" style="color: black;" required>
                                    <option value="">Eliga un servicio</option>
                                    <!-- Opciones cargadas por JS -->
                                </select>
                                <label for="servicio">Servicio:</label>
                            </div>
                            <button class="btn btn-sm btn-success" type="button" id="btnAgregarServicio" data-bs-toggle="modal" data-bs-target="#ModalServicio">
                                <i class="fa-solid fa-circle-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 mt-3">
                        <div class="input-group">
                            <div class="form-floating">
                                <input type="number" class="form-control input" step="0.01" min="0.01" id="precioServicio" />
                                <label for="precioServicio">Precio Servicio</label>
                            </div>
                            <button class="btn btn-sm btn-success" type="button" id="btnAgregarDetalleServicio">Agregar</button>
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

    <div id="serviceListCard" class="card mt-2 border d-none">
        <div class="card-body">
            <table class="table table-striped table-sm" id="tabla-detalle-servicios">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Servicio</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí se agregarán los detalles de servicios -->
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
                        <td><input type="text" class="form-control input form-control-sm text-end" id="neto" readonly></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end">DSCT</td>
                        <td><input type="text" class="form-control input form-control-sm text-end" id="totalDescuento" readonly></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end">IGV</td>
                        <td><input type="text" class="form-control input form-control-sm text-end" id="igv" readonly></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end">Importe</td>
                        <td><input type="text" class="form-control input form-control-sm text-end" id="total" readonly></td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-4">
                <button id="btnFinalizarCotizacion" type="button" class="btn btn-success text-end">Aceptar</button>
                <a href="" type="reset" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- Modal para registrar nuevo servicio -->
<div class="modal fade" id="ModalServicio" tabindex="-1" aria-labelledby="ModalServicioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalServicioLabel">Registrar Nuevo Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <input type="hidden" id="modalSubcategoriaId">
                            <input type="text" class="form-control" id="modalSubcategoriaNombre" disabled>
                            <label for="modalSubcategoriaNombre">Subcategoría</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="modalServicioNombre" required>
                            <label for="modalServicioNombre">Nombre del Servicio</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnGuardarServicio">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para seleccionar propietario -->
<div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="miModalLabel">Seleccionar Propietario</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col">
                        <label><strong>Tipo de propietario:</strong></label>
                        <div style="display: flex; align-items: center; gap: 10px; margin-left:20px;">
                            <div class="form-check form-check-inline" style="margin-right:40px;">
                                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnpersona" onclick="actualizarOpciones(); buscarPropietario();" checked>
                                <label class="form-check-label" for="rbtnpersona" style="margin-left:5px;">Persona</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipoBusqueda" id="rbtnempresa" onclick="actualizarOpciones(); buscarPropietario();">
                                <label class="form-check-label" for="rbtnempresa" style="margin-left:5px;">Empresa</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <select id="selectMetodo" class="form-select" style="color: black;">
                                <!-- Opciones dinámicas -->
                            </select>
                            <label for="selectMetodo">Método de búsqueda:</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control input" id="vbuscado" placeholder="Valor buscado" style="background-color: white;" autofocus />
                            <label for="vbuscado">Valor buscado</label>
                        </div>
                    </div>
                </div>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script src="<?= SERVERURL ?>views/assets/js/moneda.js"></script>
<script src="<?= SERVERURL ?>views/page/cotizaciones/js/registrar-cotizacion.js"></script>
</body>
</html>