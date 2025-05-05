<?php

const NAMEVIEW = "Lista de observaciones";
require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";
require_once "../../../app/models/Observacion.php";
$obsModel  = new Observacion();
$idorden   = intval($_GET['idorden'] ?? 0);
$observes  = $obsModel->getObservacionByOrden($idorden);
?>
<style>
  img{
    width:  1500px;
    height: 150px;
  }
</style>

<div class="container-main">
  <!-- textarea + botón en un flex container -->
  <div class="mb-4 d-flex align-items-start gap-2">
    <div class="flex-grow-1">
      <label for="obs-general" class="form-label"><strong>Observación de la orden:</strong></label>
      <textarea id="obs-general"
                class="form-control"
                rows="2"
                disabled><?= htmlspecialchars($observes[0]['observacion_orden'] ?? '') ?></textarea>
    </div>
    <a class="btn btn-success align-self-end" href="registrar-observacion-ordenes.php">
      Registrar
    </a>
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

          
            <div class="form-check form-switch" style=";position:absolute;bottom:10px;right:10px;">
              <input class="form-check-input" type="checkbox" ${o.estado?'checked':''} disabled>
              <label class="form-check-label">Estado</label>
            
          </div>
          <div class="card-footer d-flex justify-content-between">
            <button class="btn btn-warning btn-sm"
                    onclick="location.href='editar-observacion-ordenes.php?idobs=${o.idobservacion}&idorden=${idorden}'">
              <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <button class="btn btn-danger btn-sm"
                    onclick="if(confirm('¿Borrar esta observación?')) location.href='eliminar-observacion.php?idobs=${o.idobservacion}&idorden=${idorden}'">
              <i class="fa-solid fa-trash"></i>
            </button>
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