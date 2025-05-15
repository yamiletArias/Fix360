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
          <div class="col-md-6">
            <div class="form-floating">
              <select class="form-select input" id="idcolaborador" name="idcolaborador" style="color:black;" required>
                <option value="">Seleccione un colaborador</option>
              </select>
              <label for="idcolaborador">Receptor (Colaborador)</label>
            </div>
          </div>

          <!-- Forma de Pago -->
          <div class="col-md-6">
            <div class="form-floating">
              <select class="form-select input" id="idformapago" name="idformapago" style="color:black;" required>
                <option value="">Seleccione forma de entrega</option>
              </select>
              <label for="idformapago">Forma de entrega</label>
            </div>
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
              <input type="number" class="form-control input" id="monto" name="monto" step="0.01" min="0.01" placeholder="0.00" required>
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
        <button type="button" id="btnCancelar" class="btn btn-secondary">Cancelar</button>
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
      htmlCol += `<option value="${c.idcolaborador}">${c.nombre_completo}</option>`;
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

    // Cancelar → volver al listado
    document.getElementById('btnCancelar').addEventListener('click', () => {
      window.location.href = 'listar-egresos.php';
    });

    // Submit del form
    document.getElementById('form-egreso').addEventListener('submit', async e => {
      e.preventDefault();
      const form = e.target;
      const data = {
        action: 'register',
        idcolaborador: parseInt(form.idcolaborador.value, 10),
        idformapago: parseInt(form.idformapago.value, 10),
        concepto: form.concepto.value.trim(),
        monto: parseFloat(form.monto.value),
        numcomprobante: form.numcomprobante.value.trim()
      };

      // validaciones ligeras
      if (!data.idcolaborador || !data.idformapago || !data.concepto || data.monto <= 0) {
        return alert('Completa todos los campos obligatorios correctamente.');
      }

      // Envío al controller
      const resp = await fetch(API, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      }).then(r => r.json());

      if (resp.status === 'success') {
        alert('Egreso registrado correctamente');
        window.location.href = 'listar-egresos.php';
      } else {
        alert('Error: ' + (resp.message || 'No se pudo registrar.'));
      }
    });
  });
</script>