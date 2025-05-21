<?php

const NAMEVIEW = "Movimientos del Dia";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
      <div class="container-main">

    <h2>  <?= "{$saludo}, " . htmlspecialchars($usuario['nombreCompleto']); ?></h2> 
        <div>
          <h2><strong>Ventas del dia:</strong></h2>
          <div class="table-container">
            <table style="margin-bottom: 30px" id="miTabla" class="table table-striped display">
              <thead>
                <tr>
                  <th>#</th>
                  <th>T. Comprobante</th>
                  <th>N° Comprobante</th>
                  <th>Fecha</th>
                  <th>Total</th>
                  <th>Opciones</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>B</td>
                  <td>002-0034</td>
                  <td>10/03/2025</td>
                  <td>300.00</td>
                  <td>
                    <button title="Editar" onclick="window.location.href='editar-ventas.html'"
                      class="btn btn-warning btn-sm">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button title="Eliminar" class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                    <button title="Detalle" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                      data-bs-target="#miModal">
                      <i class="fa-solid fa-circle-info"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>F</td>
                  <td>001-0028</td>
                  <td>10/03/2025</td>
                  <td>10.00</td>
                  <td>
                    <button title="Editar" onclick="window.location.href='editar-ventas.html'"
                      class="btn btn-warning btn-sm">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button title="Eliminar" class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                    <button title="Detalle" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                      data-bs-target="#miModal">
                      <i class="fa-solid fa-circle-info"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>3</td>
                  <td>B</td>
                  <td>002-0036</td>
                  <td>10/03/2025</td>
                  <td>230.00</td>
                  <td>
                    <button title="Editar" onclick="window.location.href='editar-ventas.html'"
                      class="btn btn-warning btn-sm">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button title="Eliminar" class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                    <button title="Detalle" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                      data-bs-target="#miModal">
                      <i class="fa-solid fa-circle-info"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>4</td>
                  <td>B</td>
                  <td>002-0037</td>
                  <td>10/03/2025</td>
                  <td>361.20</td>
                  <td>
                    <button title="Editar" onclick="window.location.href='editar-ventas.html'"
                      class="btn btn-warning btn-sm">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button title="Eliminar" class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                    <button title="Detalle" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                      data-bs-target="#miModal">
                      <i class="fa-solid fa-circle-info"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>5</td>
                  <td>F</td>
                  <td>002-0030</td>
                  <td>10/03/2025</td>
                  <td>350.50</td>
                  <td>
                    <button title="Editar" onclick="window.location.href='editar-ventas.html'"
                      class="btn btn-warning btn-sm">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button title="Eliminar" class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                    <button title="Detalle" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                      data-bs-target="#miModal">
                      <i class="fa-solid fa-circle-info"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="4" class="text-end fw-bold">Total</td>
                  <td>1,251.70</td>
                  <td colspan="2"></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <div>
          <h2><strong>Compras del dia:</strong></h2>
          <div class="table-container">
            <table id="miTabla" class="table table-striped display">
              <thead>
                <tr>
                  <th>#</th>
                  <th>T. Comprobante</th>
                  <th>N° Comprobante</th>
                  <th class="text-left">Proveedor</th>
                  <th>Fecha Compra</th>
                  <th>Precio</th>
                  <th>Moneda</th>
                  <th>Opciones</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th>1</th>
                  <th>Boleta</th>
                  <th>0001-032</th>
                  <th class="text-left">Repuestos S.A.C</th>
                  <th>10/03/2025</th>
                  <th>350.00</th>
                  <th>SOLES</th>
                  <td>
                    <button onclick="window.location.href='actualizar-compras.html'" class="btn btn-warning btn-sm">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <th>2</th>
                  <th>Factura</th>
                  <th>0001-033</th>
                  <th class="text-left">Aceros Arequipa</th>
                  <th>11/03/2025</th>
                  <th>800.50</th>
                  <th>DOLARES</th>
                  <td>
                    <button onclick="window.location.href='actualizar-compras.html'" class="btn btn-warning btn-sm">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <th>3</th>
                  <th>Boleta</th>
                  <th>0001-034</th>
                  <th class="text-left">Comercializadora XYZ</th>
                  <th>12/03/2025</th>
                  <th>550.00</th>
                  <th>SOLES</th>
                  <td>
                    <button onclick="window.location.href='actualizar-compras.html'" class="btn btn-warning btn-sm">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <th>4</th>
                  <th>Factura</th>
                  <th>0001-035</th>
                  <th class="text-left">Equipos Industriales S.A.</th>
                  <th>13/03/2025</th>
                  <th>600.00</th>
                  <th>DOLARES</th>
                  <td>
                    <button onclick="window.location.href='actualizar-compras.html'" class="btn btn-warning btn-sm">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" id="btnEliminar" data-id="data-123">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="5" class="text-end fw-bold">Total</td>
                  <td>1,251.70</td>
                  <td colspan="2"></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <!-- Arqueo de Caja -->
        <br>
        <div class="">
          <h2><strong>Arqueo de Caja</strong></h2>
          <p class="text-left"><strong>Fecha:</strong> 10/03/2025</p> <!-- Fecha después del título -->
          <div class="table-container">
            <table id="arqueoCaja" class="table table-striped display">
              <thead>
                <tr>
                  <td colspan="4" class="text-center"><strong>Resumen Arqueo de Caja</strong></td>
                </tr>
                <tr>
                  <th>Método de Pago</th>
                  <th>Monto</th>
                  <th>Comentarios</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-left"><strong>Efectivo</strong></td>
                  <td class="text-right">S/. 750.00</td>
                  <td class="text-left">Monto en efectivo</td>
                </tr>
                <tr>
                  <td class="text-left"><strong>Yape</strong></td>
                  <td class="text-right">S/. 324.50</td>
                  <td class="text-left">Pago por Yape</td>
                </tr>
                <tr>
                  <td class="text-left"><strong>Transferencia</strong></td>
                  <td class="text-right">S/. 400.00</td>
                  <td class="text-left">Pago por transferencia bancaria</td>
                </tr>
                <tr>
                  <td colspan="4" class="text-left"><strong>Salida de caja</strong></td>
                </tr>
                <tr>
                  <td class="text-left"><strong>Compras</strong></td>
                  <td class="text-right">- S/. 110.00</td>
                  <td class="text-left">Respuesto de (Bujías) </td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="1" class="text-end fw-bold">Total Arqueo</td>
                  <td class="text-right"><strong>1,364.5</strong></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
  </div>

  <?php
require_once "../../partials/_footer.php";
?>
</body>

</html>