<?php

const NAMEVIEW = "Arqueo de caja";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<div class="container py-4 mt-4">
    <!--     <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">
            <i class="fa-solid fa-cash-register text-primary me-2"></i>
            ARQUEO DE CAJA - FIX 360
        </h2>
    </div> -->

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="fa-solid fa-info-circle me-2"></i>Información general
            </div>
            <div>
                <button class="btn btn-sm btn-outline-light me-2" onclick="changeDate(-1)">
                    <i class="fa-solid fa-chevron-left"></i> Día anterior
                </button>
                <button class="btn btn-sm btn-outline-light me-2" onclick="changeDate(1)">
                    Día siguiente <i class="fa-solid fa-chevron-right"></i>
                </button>
                <button class="btn btn-danger btn-sm" id="print-btn">
                    <i class="fa-solid fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3 align-items-center">
                <label for="presentado-por" class="col-sm-3 col-form-label fw-bold input">
                    <i class="fa-solid fa-user me-2"></i>Presentado por
                </label>
                <div class="col-sm-3">
                    <input id="presentado-por" type="text" readonly class="form-control-plaintext input border-bottom"
                        value="<?=
                            htmlspecialchars(
                                $usuario['nombreCompleto']    // o $usuario['namuser'], según tu SP
                                ?? 'Invitado',
                                ENT_QUOTES,
                                'UTF-8'
                            )
                            ?>">
                </div>
                <label for="fecha" class="col-sm-3 col-form-label fw-bold input">
                    <i class="fa-solid fa-calendar me-2"></i>Fecha
                </label>
                <div class="col-sm-3">
                    <input type="date" id="fecha" class="form-control input" value="" onchange="cargarIngresos()">
                </div>
            </div>
            <div class="row mb-1 align-items-center input">
                <!-- Hora inicio -->
                <label class="col-sm-3 col-form-label fw-bold">
                    <i class="fa-solid fa-clock me-2"></i>Hora inicio
                </label>
                <div class="col-sm-3">
                    <input type="text" readonly class="form-control-plaintext border-bottom" value="08:00">
                </div>

                <!-- Hora cierre -->
                <label class="col-sm-3 col-form-label fw-bold">
                    <i class="fa-solid fa-clock me-2"></i>Hora cierre
                </label>
                <div class="col-sm-3">
                    <input type="text" readonly class="form-control-plaintext border-bottom" value="18:00">
                </div>
            </div>
        </div>
    </div>

    <!-- Saldo inicial, Ingresos, Egresos en la misma fila -->
    <div class="row">
        <!-- Saldo inicial -->
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <i class="fa-solid fa-wallet me-2"></i>Saldo Inicial
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <h5 class="mb-3">Saldo restante</h5>
                        <h3 class="display-6 fw-bold text-success" id="saldo-restante">S/ 0.00</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingresos -->
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <i class="fa-solid fa-arrow-down me-2"></i>Ingresos
                </div>
                <div class="card-body" id="contenedor-ingresos">
                    <div class="text-center text-muted py-5">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando ingresos...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Egresos -->
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm h-100">
                <div class="card-header bg-danger text-white">
                    <i class="fa-solid fa-arrow-up me-2"></i>Egresos
                </div>
                <div class="card-body" id="contenedor-egresos">
                    <div class="text-center text-muted py-5">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando egresos...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-dark text-white">
            <i class="fa-solid fa-chart-pie me-2"></i>Resumen
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <td class="fs-6">Saldo anterior en efectivo</td>
                            <td class="text-end fw-bold fs-6" id="saldo-anterior">S/ 0.00</td>
                        </tr>
                        <tr>
                            <td class="fs-6">Ingreso diario efectivo</td>
                            <td class="text-end fw-bold text-success fs-6" id="ingreso-diario">S/ 0.00</td>
                        </tr>
                        <tr>
                            <td class="fs-6">Total efectivo</td>
                            <td class="text-end fw-bold fs-6" id="total-efectivo">S/ 0.00</td>
                        </tr>
                        <tr>
                            <td class="fs-6">Total egresos</td>
                            <td class="text-end fw-bold text-danger fs-6" id="total-egresos">S/ 0.00</td>
                        </tr>
                        <tr class="table-active">
                            <td class="fw-bold fs-6">Total efectivo caja</td>
                            <td class="text-end fw-bold fs-6" id="total-efectivo-caja">S/ 0.00</td>
                        </tr>
                        <tr>
                            <td class="fs-6">Otros aportes registrados</td>
                            <td class="text-end fs-6">
                                <span>-</span><br>
                                <small class="text-muted">Yape, Plin, Bancos</small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- <div class="card-footer bg-light text-center">
            <button class="btn btn-primary">
                <i class="fa-solid fa-check me-2"></i>Confirmar arqueo
            </button>
        </div> -->
    </div>
</div>
</div>
</div>
</div>

<?php
require_once "../../partials/_footer.php";
?>

<script>
    function changeDate(days) {
        const input = document.getElementById('fecha');
        if (!input.value) return;
        const d = new Date(input.value);
        d.setDate(d.getDate() + days);
        input.value = d.toISOString().slice(0, 10);
        cargarIngresos();
    }

    async function cargarResumen() {
        const fecha = document.getElementById('fecha').value;
        if (!fecha) return;

        const url = `<?= SERVERURL ?>app/controllers/Arqueo.controller.php?accion=resumen&fecha=${fecha}`;
        const res = await fetch(url);
        if (!res.ok) return console.error('Error al traer resumen:', res.status);
        const data = await res.json();

        // Saldo inicial
        document.getElementById('saldo-restante').textContent =
            `S/ ${data.total_caja.toFixed(2)}`;
        // Resumen
        document.getElementById('saldo-anterior').textContent = `S/ ${data.saldo_anterior.toFixed(2)}`;
        document.getElementById('ingreso-diario').textContent = `S/ ${data.ingreso_efectivo.toFixed(2)}`;
        document.getElementById('total-efectivo').textContent = `S/ ${data.total_efectivo.toFixed(2)}`;
        document.getElementById('total-egresos').textContent = `S/ ${data.total_egresos.toFixed(2)}`;
        document.getElementById('total-efectivo-caja').textContent = `S/ ${data.total_caja.toFixed(2)}`;
    }

    async function cargarEgresos() {
        const fecha = document.getElementById('fecha').value;
        if (!fecha) return;

        const url = `<?= SERVERURL ?>app/controllers/Arqueo.controller.php?accion=egresos&fecha=${fecha}`;
        const res = await fetch(url);
        const text = await res.text();
        let egresos;
        try {
            egresos = JSON.parse(text);
        } catch {
            return document.getElementById('contenedor-egresos').innerHTML = `
                <div class="alert alert-danger">
                    Respuesta no JSON:<br>${text.replace(/</g, '&lt;')}
                </div>`;
        }

        const cont = document.getElementById('contenedor-egresos');
        cont.innerHTML = '';
        if (!Array.isArray(egresos) || egresos.length === 0) {
            cont.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="fa-solid fa-info-circle fa-2x mb-3"></i>
                    <p>No hay egresos para ${fecha}</p>
                </div>`;
            return;
        }

        egresos.forEach(({ label, valor }) => {
            const row = document.createElement('div');
            row.className = 'row mb-2 border-bottom pb-2';
            row.innerHTML = `
                <label class="col-7 form-label">${label}</label>
                <div class="col-5 text-end text-danger fw-bold">S/ ${parseFloat(valor).toFixed(2)}</div>
            `;
            cont.appendChild(row);
        });
    }

    async function cargarIngresos() {
        const fecha = document.getElementById('fecha').value;
        if (!fecha) return;

        const url = `<?= SERVERURL ?>app/controllers/Arqueo.controller.php?accion=ingresos&fecha=${fecha}`;
        const res = await fetch(url);
        const text = await res.text();
        let ingresos;
        try {
            ingresos = JSON.parse(text);
        } catch {
            return document.getElementById('contenedor-ingresos').innerHTML = `
                <div class="alert alert-danger">
                    Respuesta no JSON:<br>${text.replace(/</g, '&lt;')}
                </div>`;
        }

        const cont = document.getElementById('contenedor-ingresos');
        cont.innerHTML = '';
        if (!Array.isArray(ingresos) || ingresos.length === 0) {
            cont.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="fa-solid fa-info-circle fa-2x mb-3"></i>
                    <p>No hay ingresos para ${fecha}</p>
                </div>`;
        } else {
            ingresos.forEach(({ label, valor }) => {
                const row = document.createElement('div');
                row.className = 'row mb-2 border-bottom pb-2';
                row.innerHTML = `
                    <label class="col-7 form-label">${label}</label>
                    <div class="col-5 text-end text-success fw-bold">S/ ${parseFloat(valor).toFixed(2)}</div>
                `;
                cont.appendChild(row);
            });
        }

        // Primero egresos, luego resumen
        await cargarEgresos();
        await cargarResumen();
    }


    document.addEventListener('DOMContentLoaded', () => {
        const hoy = new Date().toISOString().slice(0, 10);
        document.getElementById('fecha').value = hoy;
        cargarIngresos();

        // Imprimir PDF
        /* document.getElementById('print-btn').addEventListener('click', () => {
            window.print();
        }); */
        document.getElementById('print-btn').addEventListener('click', () => {
            const fecha = document.getElementById('fecha').value;
            if (!fecha) {
                alert('Selecciona primero una fecha');
                return;
            }
            // Si quieres pasar el nombre de usuario, toma la variable PHP 
            // (asegúrate de imprimirla en JS)
            const usuario = <?= json_encode($usuario['nombreCompleto'] ?? 'Invitado') ?>;

            const url = `<?= SERVERURL ?>app/reports/reportearqueo.php`
                + `?fecha=${encodeURIComponent(fecha)}`
                + `&usuario=${encodeURIComponent(usuario)}`;
            window.open(url, '_blank');
        });
    });
</script>
</body>

</html>