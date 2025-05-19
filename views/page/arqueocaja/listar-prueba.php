<?php

const NAMEVIEW = "Arqueo de caja";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>
<div class="container py-4 mt-1">
    <!-- <div class="d-flex justify-content-between align-items-center mb-1">
        <h2 class="h4">ARQUEO DE CAJA - FIX 360</h2>
        <button class="btn btn-danger btn-sm">
            <i class="fa-solid fa-file-pdf"></i>
        </button>
    </div> -->

    <div class="card mb-1">
        <div class="card-body">
            <div class="row mb-1 align-items-center">
                <label for="presentado-por" class="col-sm-3 col-form-label fw-bold input">Presentado por</label>
                <div class="col-sm-3">
                    <input id="presentado-por" type="text" readonly class="form-control-plaintext input"
                        value="Elena Castilla">
                </div>
                <label for="fecha" class="col-sm-3 col-form-label fw-bold input">Fecha</label>
                <div class="col-sm-3">
                    <input type="date" id="fecha" class="form-control input" value="" onchange="cargarIngresos()">
                </div>
            </div>
            <div class="row mb-1 align-items-center input">
                <!-- Hora inicio -->
                <label class="col-sm-3 col-form-label fw-bold">Hora inicio</label>
                <div class="col-sm-3">
                    <input type="text" readonly class="form-control-plaintext" value="08:00">
                </div>

                <!-- Hora cierre -->
                <label class="col-sm-3 col-form-label fw-bold">Hora cierre</label>
                <div class="col-sm-3">
                    <input type="text" readonly class="form-control-plaintext" value="18:00">
                </div>
            </div>
        </div>
    </div>

    <!-- Saldo inicial -->
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">Saldo Inicial</div>
                <div class="card-body">
                    <div class="mb-3 row align-items-center">
                        <label for="saldo-restante" class="col-sm-6 col-form-label fw-bold">
                            Saldo restante
                        </label>
                        <div class="col-sm-6">
                            <input type="text" readonly id="saldo-restante" class="form-control-plaintext text-end"
                                value="">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingresos -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">Ingresos</div>
                <div class="card-body" id="contenedor-ingresos">
                    <!-- Aquí inyectaremos filas -->
                </div>
            </div>
        </div>

        <!-- Egresos -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">Egresos</div>
                <div class="card-body" id="contenedor-egresos">
                    <!-- Aquí inyectaremos filas -->
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen -->
    <div class="card">
        <div class="card-header bg-light">
            Resumen
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <label class="col-sm-6 form-label">Saldo anterior en efectivo</label>
                <div class="col-sm-6 text-end">
                    <span id="saldo-anterior"></span>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-6 form-label">Ingreso diario efectivo</label>
                <div class="col-sm-6 text-end">
                    <span id="ingreso-diario"></span>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-6 form-label">Total efectivo</label>
                <div class="col-sm-6 text-end">
                    <span id="total-efectivo"></span>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-6 form-label">Total egresos</label>
                <div class="col-sm-6 text-end">
                    <span id="total-egresos"></span>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-6 form-label">Total efectivo caja</label>
                <div class="col-sm-6 text-end fw-bold">
                    <span id="total-efectivo-caja"></span>
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
        document.getElementById('saldo-restante').value =
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
            cont.innerHTML = `<div class="text-center text-muted py-3">No hay egresos para ${fecha}</div>`;
            return;
        }

        egresos.forEach(({ label, valor }) => {
            const row = document.createElement('div');
            row.className = 'row mb-2';
            row.innerHTML = `
        <label class="col-6 form-label">${label}</label>
        <div class="col-6 text-end">S/ ${parseFloat(valor).toFixed(2)}</div>
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
            cont.innerHTML = `<div class="text-center text-muted py-3">No hay ingresos para ${fecha}</div>`;
        } else {
            ingresos.forEach(({ label, valor }) => {
                const row = document.createElement('div');
                row.className = 'row mb-2';
                row.innerHTML = `
          <label class="col-6 form-label">${label}</label>
          <div class="col-6 text-end">S/ ${parseFloat(valor).toFixed(2)}</div>
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
    });
</script>

</body>

</html>