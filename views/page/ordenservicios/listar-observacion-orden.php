<?php

const NAMEVIEW = "Observaciones de la Orden de Registro";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
require_once "../../../app/models/Observacion.php";
$obsModel  = new Observacion();
$idorden   = intval($_GET['idorden'] ?? 0);
$observes  = $obsModel->getObservacionByOrden($idorden);
?>
<style>
  img {
    width: 1500px;
    height: 150px;
  }

  .form-check-input {
    transform: scale(1.5);
  }
</style>

<div class="container-main">
  <form action="" id="formObservacion" method="POST">
    <div class="card">
      <div class="card-header"><strong>
          <h3>Registrar Observacion</h3>
        </strong></div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-floating">
              <select class="form-select" id="componente" name="componente" style="color: black;" required>
                <option>Seleccione un componente</option>
              </select>
              <label for="categoria">Componente:</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="file" class="btn btn-outline-dark border input-img" name="img" id="img" accept="image/png, image/jpeg" placeholder="img">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-check form-switch">
              <label class="form-check-label ms-2" style="margin-left:10px;"><strong>Estado:</strong></label>
              <input class="form-check-input" type="checkbox" style="margin-left:10px;">
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer text-end mb-4">
        <button class="btn btn-secondary">Cancelar</button>
        <button class="btn btn-success">Registrar</button>
      </div>
    </div>
  </form>
  <!-- textarea + botón en un flex container -->
  <div class="mb-4 d-flex align-items-start gap-2">
    <div class="flex-grow-1">
      <label for="obs-general" class="form-label"><strong>Observación de la orden:</strong></label>
      <textarea id="obs-general"
        class="form-control"
        rows="2"
        disabled><?= htmlspecialchars($observes[0]['observacion_orden'] ?? '') ?></textarea>
    </div>
    <!--a class="btn btn-success align-self-end" href="registrar-observacion-ordenes.php">
      Registrar
    </!--a-->
  </div>

  <div id="obs-container" class="d-flex flex-wrap gap-4">
  </div>
  <div>
    <a href="listar-ordenes.php" class="btn btn-secondary">Volver</a>
  </div>
</div>


</div>
</div>

<?php

require_once "../../partials/_footer.php";

?>
<!-- 
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const cont = document.getElementById('obs-container');
    const params = new URLSearchParams(location.search);
    const idorden = params.get('idorden');
    if (!idorden) {
      cont.innerHTML = '<p class="text-danger">No se indicó ningún ID de orden.</p>';
      return;
    }

    fetch(`http://localhost/fix360/app/controllers/observacion.controller.php?task=getObservacionByOrden&idorden=${idorden}`)
      .then(r => {
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        return r.json();
      })
      .then(data => {
        cont.innerHTML = '';
        if (data.length === 0) {
          cont.innerHTML = `
        <div class="alert alert-warning" role="alert">
        No hay observaciones por mostrar
        </div>`;
          return;
        }
        data.forEach(o => {
          const card = document.createElement('div');
          card.className = 'card';
          card.style = 'width:18rem;position:relative;margin:10px';
          card.innerHTML = `
          <div class="card-header"><strong>${o.componente}</strong></div>
          <img src="<?= SERVERURL ?>${o.foto || 'ruta/por/defecto.jpg'}"  alt="Foto no proporcionada" class="card-img-top" />
          <div class="card-footer" style="margin:0px;">
            <div class="d-flex justify-content-between align-items-center">
            <div class="form-check form-switch  d-flex align-items-center m-0 ">
          <label class="form-check-label ms-2" style="margin-left:10px;" ><strong>Estado:</strong></label>
          <input class="form-check-input" type="checkbox" ${o.estado ? 'checked' : ''} disabled style="transform: scale(1.4);margin-left:10px;">
          </div>
          <div>
          <button class="btn btn-warning btn-sm me-1" onclick="location.href='editar-observacion-ordenes.php?idobs=${o.idobservacion}&idorden=${idorden}'">
          <i class="fa-solid fa-pen-to-square"></i>
          </button>
          <button class="btn btn-danger btn-sm" onclick="if(confirm('¿Borrar esta observación?')) location.href='eliminar-observacion.php?idobs=${o.idobservacion}&idorden=${idorden}'">
        <i class="fa-solid fa-trash"></i>
      </button>
    </div>
  </div>
</div>
          </div>
          `;
          cont.appendChild(card);
        });
      })
      .catch(err => {
        console.error('Error cargando observaciones:', err);
        cont.innerHTML = '<p class="text-danger">Error al cargar las observaciones.</p>';
      });
  });
</script>

-->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const cont = document.getElementById('obs-container');
    const selectComp = document.getElementById('componente');
    const params = new URLSearchParams(location.search);
    const idorden = params.get('idorden');

    // 1) Fetch para llenar el select de componentes
    fetch('http://localhost/fix360/app/controllers/componente.controller.php?task=getAll')
      .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
      })
      .then(lista => {
        // quitamos la opción placeholder
        selectComp.innerHTML = '<option value="">Seleccione un componente</option>';
        lista.forEach(c => {
          const opt = document.createElement('option');
          opt.value = c.idcomponente;       // o el nombre del campo PK que venga en tu vwComponentes
          opt.textContent = c.componente;   // o el campo que almacena el nombre
          selectComp.appendChild(opt);
        });
      })
      .catch(err => {
        console.error('Error cargando componentes:', err);
        selectComp.innerHTML = '<option value="">Error cargando componentes</option>';
      });

    // 2) Tu fetch existente para las observaciones
    if (!idorden) {
      cont.innerHTML = '<p class="text-danger">No se indicó ningún ID de orden.</p>';
      return;
    }
    fetch(`http://localhost/fix360/app/controllers/observacion.controller.php?task=getObservacionByOrden&idorden=${idorden}`)
      .then(r => {
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        return r.json();
      })
      .then(data => {
        cont.innerHTML = '';
        if (data.length === 0) {
          cont.innerHTML = `
            <div class="alert alert-warning" role="alert">
              No hay observaciones por mostrar
            </div>`;
          return;
        }
        data.forEach(o => {
          const card = document.createElement('div');
          card.className = 'card';
          card.style = 'width:18rem;position:relative;margin:10px';
          card.innerHTML = `
            <div class="card-header"><strong>${o.componente}</strong></div>
            <img src="<?= SERVERURL ?>${o.foto || 'ruta/por/defecto.jpg'}"  
                 alt="Foto no proporcionada" 
                 class="card-img-top" />
            <div class="card-footer" style="margin:0px;">
              <div class="d-flex justify-content-between align-items-center">
                <div class="form-check form-switch d-flex align-items-center m-0">
                  <label class="form-check-label m-0 me-2"><strong>Estado:</strong></label>
                  <input class="form-check-input" 
                         type="checkbox" 
                         ${o.estado ? 'checked' : ''} 
                         disabled 
                         style="transform: scale(1.4);margin-left:10px;">
                </div>
                <div>
                  <button class="btn btn-warning btn-sm me-1" 
                          onclick="location.href='editar-observacion-ordenes.php?idobs=${o.idobservacion}&idorden=${idorden}'">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <button class="btn btn-danger btn-sm" 
                          onclick="if(confirm('¿Borrar esta observación?')) location.href='eliminar-observacion.php?idobs=${o.idobservacion}&idorden=${idorden}'">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>`;
          cont.appendChild(card);
        });
      })
      .catch(err => {
        console.error('Error cargando observaciones:', err);
        cont.innerHTML = '<p class="text-danger">Error al cargar las observaciones.</p>';
      });
  });
</script>




</body>

</html>