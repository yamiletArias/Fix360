<?php
// content/data-reporte-caja.php
?>
<style>
    body {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 10pt;
        line-height: 1.3;
        margin: 0;
        padding: 0;
    }

    .title {
        text-align: center;
        font-size: 16pt;
        font-weight: bold;
        margin-bottom: 15px;
        padding-bottom: 5px;
        border-bottom: 1px solid #999;
    }

    table.info-table {
        width: 96%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    table.info-table th {
        background-color: #f0f0f0;
        padding: 6px 8px;
        text-align: left;
        font-weight: bold;
        border: 1px solid #555;
        width: 36.5%;
    }

    table.info-table td {
        padding: 6px 8px;
        border: 1px solid #555;
    }

    .column-wrap .section {
        font-weight: bold;
        background: #f0f0f0;
        padding: 6px 8px;
        border: 1px solid #555;
        margin-bottom: 5px;
        page-break-after: avoid;
    }

    .content-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        border: 1px solid #555;
    }

    .content-table td {
        padding: 5px 8px;
        border-bottom: 1px dotted #ccc;
        word-wrap: break-word;
        word-break: break-all;
    }

    .content-table tr:last-child td {
        border-bottom: none;
    }

    .label-cell {
        text-align: left;
        width: 65%;
    }

    .value-cell {
        text-align: right;
        font-weight: 500;
        width: 35%;
    }

    .total-row td {
        font-weight: bold;
        border-top: 1px solid #555;
        border-bottom: 1px solid #555;
        background-color: #f9f9f9;
    }

    .two-columns {
        width: 101.5%;
        table-layout: fixed;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .two-columns td {
        vertical-align: top;
        padding-right: 10px;
    }

    .saldo-inicial {
        width: 100%;
        margin-top: 20px;
        margin-bottom: 15px;
    }

    .resumen-section {
        clear: both;
        margin-top: 15px;
    }
</style>

<page format="A4-L" backtop="10mm" backbottom="15mm" backleft="10mm" backright="10mm">

    <!-- TITULO -->
    <div class="title">ARQUEO DE CAJA - FIX 360</div>

    <!-- INFO -->
    <table class="info-table">
        <tr>
            <th>Presentado por</th>
            <td><?= htmlspecialchars($usuario ?? 'Invitado', ENT_QUOTES, 'UTF-8') ?></td>
            <th>Fecha</th>
            <td><?= htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <tr>
            <th>Hora inicio</th>
            <td><?= $resumen['hora_inicio'] ?? '08:00' ?></td>
            <th>Hora cierre</th>
            <td><?= $resumen['hora_cierre'] ?? '18:00' ?></td>
        </tr>
    </table>

    <!-- SALDO INICIAL -->
    <div class="saldo-inicial">
        <div class="column-wrap">
            <div class="section">Saldo Inicial</div>
            <table class="content-table">
                <tr>
                    <td class="label-cell">Saldo restante</td>
                    <td class="value-cell">S/ <?= number_format($resumen['total_caja'] ?? 0, 2) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- INGRESOS / EGRESOS DOS COLUMNAS -->
    <table class="two-columns">
        <tr>
            <td style="width: 50%;">
                <div class="column-wrap">
                    <div class="section">Ingresos</div>
                    <table class="content-table">
                        <?php if (!empty($ingresos)): ?>
                            <?php foreach ($ingresos as $item): ?>
                                <tr>
                                    <td class="label-cell"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="value-cell">S/ <?= number_format($item['valor'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="label-cell">Sin ingresos</td>
                                <td class="value-cell">-</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </td>
            <td style="width: 50%;">
                <div class="column-wrap">
                    <div class="section">Egresos</div>
                    <table class="content-table">
                        <?php if (!empty($egresos)): ?>
                            <?php foreach ($egresos as $item): ?>
                                <tr>
                                    <td class="label-cell"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="value-cell">S/ <?= number_format($item['valor'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="label-cell">Sin egresos</td>
                                <td class="value-cell">-</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- RESUMEN -->
    <div class="resumen-section">
        <div class="column-wrap">
            <div class="section">Resumen</div>
            <table class="content-table">
                <?php
                $resumen_items = [
                    'saldo_anterior' => 'Saldo anterior en efectivo',
                    'ingreso_efectivo' => 'Ingreso diario efectivo',
                    'total_efectivo' => 'Total efectivo',
                    'total_egresos' => 'Total egresos',
                    'total_caja' => 'Total caja'
                ];
                $spacerInserted = false;
                foreach ($resumen_items as $key => $label):
                    $is_total = in_array($key, ['total_efectivo', 'total_egresos', 'total_caja']);
                    // Cuando llegue al primer total, meto una fila vacía de separación
                    if ($is_total && !$spacerInserted):
                        $spacerInserted = true;
                        ?>
                        <tr>
                            <td colspan="2" style="border:none; padding:0; height:8px;"></td>
                        </tr>
                        <?php
                    endif;
                    ?>
                    <tr <?= $is_total ? 'class="total-row"' : '' ?>>
                        <td class="label-cell"><?= $label ?></td>
                        <td class="value-cell">
                            S/ <?= number_format($resumen[$key] ?? 0, 2) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <!-- PIE FIJO -->
    <page_footer>
        <div style="
            width:100%;
            text-align:center;
            font-size:10pt;
            color:#666;
            border-top:1px solid #999;
            padding-top:8px;
        ">
            Documento generado el <?= htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8') ?>
            a las <?= $resumen['hora_cierre'] ?? '18:00' ?>
        </div>
    </page_footer>
</page>