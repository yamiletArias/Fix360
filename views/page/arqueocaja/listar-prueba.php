<?php

const NAMEVIEW = "Arqueo de caja";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<div class="container py-4 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h2 class="h4">ARQUEO DE CAJA - FIX 360</h2>
        <button class="btn btn-danger btn-sm">
            <i class="fa-solid fa-file-pdf"></i>
        </button>
    </div>

    <div class="card mb-1">
        <div class="card-body">
            <div class="row mb-1">
                <label class="col-sm-3 col-form-label fw-bold">Presentado por</label>
                <div class="col-sm-9">
                    <input type="text" readonly class="form-control-plaintext" value="Elena Castilla">
                </div>
            </div>
            <div class="row mb-1">
                <label class="col-sm-3 col-form-label fw-bold">Fecha</label>
                <div class="col-sm-9">
                    <input type="date" id="fecha" class="form-control" value="2025-03-24"
                        onchange="actualizarValores()">
                </div>
            </div>
            <div class="row mb-1">
                <label class="col-sm-3 col-form-label fw-bold">Hora inicio</label>
                <div class="col-sm-9">
                    <input type="text" readonly class="form-control-plaintext" value="08:00">
                </div>
            </div>
            <div class="row">
                <label class="col-sm-3 col-form-label fw-bold">Hora cierre</label>
                <div class="col-sm-9">
                    <input type="text" readonly class="form-control-plaintext" value="18:00">
                </div>
            </div>
        </div>
    </div>

    <?php
    // Datos de ejemplo
    $ingresos = [
        'Efectivo' => 'S/ 50.00',
        'Yape' => 'S/ 30.00',
        'Plin' => 'S/ 40.00',
        'Visa' => 'S/ 25.00',
        'Depósito' => 'S/ 200.00'
    ];

    $egresos = [
        'Combustible' => 'S/ -',
        'Almuerzo' => 'S/ -',
        'Pasajes' => 'S/ 16.00',
        'Compra de insumos' => 'S/ 20.00',
        'Servicios varios' => 'S/ 236.00',
        'Otros Conceptos' => 'S/ -',
        'Gerencia' => 'S/ -'
    ];
    ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    Saldo Inicial
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Saldo restante</label>
                        <input type="text" readonly id="saldo-restante" class="form-control-plaintext"
                            value="S/ 385.00">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    Ingresos
                </div>
                <div class="card-body">
                    <?php foreach ($ingresos as $label => $valor): ?>
                        <div class="row mb-2">
                            <label class="col-6 form-label"><?= $label ?></label>
                            <div class="col-6 text-end">
                                <span><?= $valor ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    Egresos
                </div>
                <div class="card-body">
                    <?php foreach ($egresos as $label => $valor): ?>
                        <div class="row mb-2">
                            <label class="col-6 form-label"><?= $label ?></label>
                            <div class="col-6 text-end">
                                <span><?= $valor ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            Resumen
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <label class="col-sm-6 form-label">Saldo anterior en efectivo</label>
                <div class="col-sm-6 text-end">
                    <span id="saldo-anterior">S/ 385.00</span>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-6 form-label">Ingreso diario efectivo</label>
                <div class="col-sm-6 text-end">
                    <span id="ingreso-diario">S/ 545.00</span>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-6 form-label">Total efectivo</label>
                <div class="col-sm-6 text-end">
                    <span id="total-efectivo">S/ 930.00</span>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-6 form-label">Total egresos</label>
                <div class="col-sm-6 text-end">
                    <span id="total-egresos">S/ 272.00</span>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-6 form-label">Total efectivo caja</label>
                <div class="col-sm-6 text-end fw-bold">
                    <span id="total-efectivo-caja">S/ 658.00</span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-6 form-label">Otros aportes registrados</label>
                <div class="col-sm-6 text-end">
                    <span>-</span><br>
                    <small class="text-muted">Yape, Plin, Bancos</small>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>

<?php
require_once "../../partials/_footer.php";
?>

<script>
    function actualizarValores() {
        var fecha = document.getElementById('fecha').value;
        if (fecha === "2025-03-23") {
            // valores ...
        } else if (fecha === "2025-03-24") {
            // valores ...
        }
    }
</script>

</body>

</html>