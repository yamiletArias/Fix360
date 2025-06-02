<?php
// app/reports/content/data-reporte-egresos.php

// Variables disponibles:
//   $datos         → arreglo con todos los egresos
//   $usuarioNombre → nombre completo del colaborador que genera el PDF

function fmtFecha($cadena) {
    // Si ya está en "DD/MM/YYYY", devuélvelo
    if (strpos($cadena, '/') !== false) {
        return $cadena;
    }
    // Si viniera en ISO, lo convertimos; pero según la API ya viene "02/06/2025"
    $d = new DateTime($cadena);
    return $d->format('d/m/Y');
}
?>

<page backtop="20mm" backbottom="15mm" backleft="5mm" backright="5mm">
  <page_header>
    <h1 style="font-size:16pt; margin:0">Reporte de Egresos</h1>
    <div style="font-size:10pt; margin-top:4px;">
      <?= "Generado por: " . htmlspecialchars($usuarioNombre, ENT_QUOTES, 'UTF-8') . " | " . date('d/m/Y H:i') ?>
    </div>
    <hr>
  </page_header>

  <page_footer>
    <hr>
    <div style="width:100%; text-align:right; font-size:9pt;">
      Página [[page_cu]]/[[page_nb]]
    </div>
  </page_footer>

  <table
    border="1"
    cellpadding="3"
    cellspacing="0"
    width="100%"
    style="width:100%; font-size:11pt; border-collapse:collapse;"
  >
    <colgroup>
      <col style="width:5%;"   />  <!-- # -->
      <col style="width:15%;"  />  <!-- Fecha -->
      <col style="width:20%;"  />  <!-- Registrador -->
      <col style="width:20%;"  />  <!-- Receptor -->
      <col style="width:25%;"  />  <!-- Concepto -->
      <col style="width:10%;"  />  <!-- Monto -->
      <col style="width:10%;"  />  <!-- N° Comprobante -->
    </colgroup>

    <thead>
      <tr>
        <th style="padding:2px; border:1px solid #333; text-align:center;">#</th>
        <th style="padding:2px; border:1px solid #333; text-align:center;">Fecha</th>
        <th style="padding:2px; border:1px solid #333; text-align:center;">Registrador</th>
        <th style="padding:2px; border:1px solid #333; text-align:center;">Receptor</th>
        <th style="padding:2px; border:1px solid #333; text-align:center;">Concepto</th>
        <th style="padding:2px; border:1px solid #333; text-align:center;">Monto</th>
        <th style="padding:2px; border:1px solid #333; text-align:center;">N° Com.</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($datos as $i => $fila): ?>
        <tr <?= $i % 2 ? 'style="background-color:#f9f9f9;"' : '' ?>>
          <td style="padding:3px; border:1px solid #333; text-align:center;"><?= $i + 1 ?></td>
          <td style="padding:3px; border:1px solid #333; text-align:center;"><?= fmtFecha($fila['fecha']) ?></td>
          <td style="padding:3px; border:1px solid #333;">
            <?= htmlspecialchars($fila['registrador'], ENT_QUOTES, 'UTF-8') ?>
          </td>
          <td style="padding:3px; border:1px solid #333;">
            <?= htmlspecialchars($fila['receptor'], ENT_QUOTES, 'UTF-8') ?>
          </td>
          <td style="padding:3px; border:1px solid #333; text-align:center;">
            <?= htmlspecialchars($fila['concepto'], ENT_QUOTES, 'UTF-8') ?>
          </td>
          <td style="padding:3px; border:1px solid #333; text-align:right;">
            <?= number_format((float)$fila['monto'], 2, ',', '.') ?>
          </td>
          <td style="padding:3px; border:1px solid #333; text-align:center;">
            <?= ($fila['numcomprobante'] && trim($fila['numcomprobante']) !== '')
                ? htmlspecialchars($fila['numcomprobante'], ENT_QUOTES, 'UTF-8')
                : '—'
            ?>
          </td>
        </tr>
      <?php endforeach; ?>

      <?php if (empty($datos)): ?>
        <tr>
          <td colspan="7" style="text-align:center; padding:6px; border:1px solid #333;">
            No hay egresos para este periodo
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</page>
