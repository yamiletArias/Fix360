<?php
const NAMEVIEW = "Egresos | Registro";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
?>

<div class="container-main mt-5">
  <form id="form-egreso" autocomplete="off">
    <div class="card border mb-3">
      <div class="card-body">
        <div class="row g-3">
          <!-- Colaborador (destinatario) -->
          <div class="col-md-4">
            <div class="form-floating">
              <select class="form-select input" id="idcolaborador" name="idcolaborador" style="color:black;" autofocus required>
                <option value="">Seleccione un colaborador</option>
              </select>
              <label for="idcolaborador">Receptor (Colaborador)</label>
            </div>
          </div>

          <!-- Forma de Pago -->
          <div class="col-md-4">
            <div class="form-floating">
              <select class="form-select input" id="idformapago" name="idformapago" style="color:black;" required>
                <option value="">Seleccione forma de entrega</option>
              </select>
              <label for="idformapago">Forma de entrega</label>
            </div>
          </div>

          <div class="col-md-4 d-flex">
            <div class="form-floating flex-grow-1">
              <input type="datetime-local" class="form-control input" name="fecharegistro" id="fechaIngreso" required />
              <label for="fechaIngreso">Fecha de venta:</label>
            </div>
            <!-- Botón más delgado y estilizado -->
            <button type="button" id="btnPermitirFechaPasada" class="btn btn-outline-warning px-2"
              style="height: 58px; width: 40px;" title="Permitir fechas pasadas">
              <i class="fa-solid fa-lock"></i>
            </button>
          </div>

          <!-- Concepto -->
          <div class="col-md-4">
            <div class="form-floating">
              <select class="form-select input" id="concepto" name="concepto" style="color:black;" required>
                <option value="">Seleccione un concepto</option>
                <option value="combustible">Combustible</option>
                <option value="almuerzo">Almuerzo</option>
                <option value="pasajes">Pasajes</option>
                <option value="compra de insumos">Compra de insumos</option>
                <option value="servicios varios">Servicios varios</option>
                <option value="otros conceptos">Otros conceptos</option>
              </select>
              <label for="concepto">Concepto</label>
            </div>
          </div>

          <!-- Monto y Comprobante -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="number" class="form-control input" id="monto" name="monto" step="0.01" min="0.01" placeholder="0.00" autocomplete="off" required>
              <label for="monto">Monto</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text"
                class="form-control input"
                id="numcomprobante"
                name="numcomprobante"
                maxlength="20"
                placeholder="Opcional">
              <label for="numcomprobante">Número de Comprobante</label>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer text-end">
        <button type="button" id="btnCancelar" href="javascript:history.back()" class="btn btn-secondary">Cancelar</button>
        <button type="submit" id="btnGuardar" class="btn btn-success">Guardar</button>
      </div>
    </div>
  </form>
</div>
</div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script>
  const API = SERVERURL + 'app/controllers/Egreso.controller.php';

  // Al cargar página, poblar selectores
  async function loadSelects() {
    // 1) Colaboradores activos y vigentes
    const resCol = await fetch(`${SERVERURL}app/controllers/Colaborador.controller.php?action=list`);
    const jsonCol = await resCol.json();
    if (jsonCol.status === 'success') {
      let htmlCol = '<option value="" style="color:black;">Seleccione un colaborador</option>';
      jsonCol.data.forEach(c => {
        const nombreCompleto = `${c.apellidos} ${c.nombres}`;
        htmlCol += `<option value="${c.idcolaborador}">${nombreCompleto}</option>`;
      });
      document.getElementById('idcolaborador').innerHTML = htmlCol;
    } else {
      console.error('Error cargando colaboradores:', jsonCol.message);
    }

    // 2) Formas de pago
    const resFP = await fetch(`${SERVERURL}app/controllers/Formapagos.controller.php`);
    const jsonFP = await resFP.json();
    if (jsonFP.status === 'success') {
      let htmlFP = '<option value="">Seleccione forma de entrega</option>';
      jsonFP.data.forEach(f => {
        htmlFP += `<option value="${f.idformapago}">${f.formapago}</option>`;
      });
      document.getElementById('idformapago').innerHTML = htmlFP;
    } else {
      console.error('Error cargando formas de pago:', jsonFP.message);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    loadSelects();

    const fechaIngresoInput = document.getElementById('fechaIngreso');
    const btnCandado       = document.getElementById('btnPermitirFechaPasada');
    const btnCancelar      = document.getElementById('btnCancelar');
    const formEgreso       = document.getElementById('form-egreso');

    // 1) Inicializar con la fecha y hora actual, y deshabilitado
    const ahora = new Date();
    const pad  = n => String(n).padStart(2, '0');
    const yyyy = ahora.getFullYear();
    const mm   = pad(ahora.getMonth() + 1);
    const dd   = pad(ahora.getDate());
    const hh   = pad(ahora.getHours());
    const min  = pad(ahora.getMinutes());
    fechaIngresoInput.value = `${yyyy}-${mm}-${dd}T${hh}:${min}`;
    fechaIngresoInput.disabled = true;

    btnCandado.addEventListener('click', () => {
      fechaIngresoInput.disabled = false;
      btnCandado.innerHTML = '<i class="fa-solid fa-lock-open"></i>';
      btnCandado.classList.remove('btn-outline-warning');
      btnCandado.classList.add('btn-outline-success');
      btnCandado.title = 'Fecha desbloqueada';
      btnCandado.disabled = true;
    });

    btnCancelar.addEventListener('click', () => {
      window.location.href = 'listar-egresos.php';
    });

    formEgreso.addEventListener('submit', async e => {
      e.preventDefault();

      // Validaciones previas
      const idcolaborador  = parseInt(formEgreso.idcolaborador.value, 10);
      const idformapago    = parseInt(formEgreso.idformapago.value, 10);
      const concepto       = formEgreso.concepto.value.trim();
      const monto          = parseFloat(formEgreso.monto.value);
      const numcomprobante = formEgreso.numcomprobante.value.trim();
      if (!idcolaborador)    { showToast('Debe seleccionar un colaborador','ERROR',1500); return; }
      if (!idformapago)      { showToast('Debe seleccionar una forma de entrega','ERROR',1500); return; }
      if (!concepto)         { showToast('Debe seleccionar un concepto','ERROR',1500); return; }
      if (isNaN(monto) || monto <= 0) { showToast('El monto debe ser mayor que 0','ERROR',1500); return; }
      if (!fechaIngresoInput.value)   { showToast('La fecha es obligatoria','ERROR',1500); return; }

      // Formateo de la fecha para MySQL DATETIME
      const rawFecha      = fechaIngresoInput.value;            // "2025-06-12T14:30"
      const fecharegistro = rawFecha.replace('T', ' ') + ':00'; // "2025-06-12 14:30:00"

      // Confirmación
      const confirmado = await ask("¿Estás seguro de que deseas registrar este egreso?","Egresos");
      if (!confirmado) return;

      // Payload
      const payload = {
        action: 'register',
        idcolaborador,
        idformapago,
        concepto,
        monto,
        numcomprobante,
        fecharegistro
      };
      console.log('Payload egreso →', payload);

      try {
        // 1) Llamada al servidor
        const fetchResponse = await fetch(API, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        console.log('Fetch response object →', fetchResponse);

        // 2) Texto crudo de la respuesta
        const text = await fetchResponse.text();
        console.log('Response text →', text);

        // 3) Intentar parsear a JSON
        let json;
        try {
          json = JSON.parse(text);
        } catch (parseErr) {
          console.error('Error parseando JSON:', parseErr);
          showToast('Respuesta inválida del servidor','ERROR',2000);
          return;
        }
        console.log('Response JSON →', json);

        // 4) Lógica según status
        if (json.status === 'success') {
          showToast('Egreso registrado correctamente','SUCCESS',1500);
          setTimeout(() => window.location.href = 'listar-egresos.php', 1500);
        } else {
          showToast('Error: ' + (json.message || 'No se pudo registrar.'),'ERROR',2000);
        }

      } catch (err) {
        console.error('Error en el fetch:', err);
        showToast('Error de servidor. Intenta nuevamente.','ERROR',2000);
      }
    });
  });
</script>

