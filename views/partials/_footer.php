<!-- partial:partials/_footer.html -->
<footer class="footer">
  <div class="d-sm-flex justify-content-center justify-content-sm-between">
    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
      Copyright © 2025 Fix360. All rights reserved.
      <a href="#"> Terms of use</a>
      <a href="#"> Privacy Policy</a>
    </span>
    <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"></span>
  </div>
</footer>
<!-- partial -->
</div> <!-- main-panel ends -->
</div> <!-- page-body-wrapper ends -->
</div> <!-- container-scroller -->

<!-- 1) jQuery (único) -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- 2) DataTables -->
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

<!-- 3) jQuery UI (solo si lo usas) -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- Lightbox2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>


<!-- 5) Plugins/Vendor JS -->
<script src="<?= SERVERURL ?>views/assets/vendors/chart.js/chart.umd.js"></script>
<script src="<?= SERVERURL ?>views/assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
<script src="<?= SERVERURL ?>views/assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= SERVERURL ?>views/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="<?= SERVERURL ?>views/assets/vendors/moment/moment.min.js"></script>
<script src="<?= SERVERURL ?>views/assets/vendors/daterangepicker/daterangepicker.js"></script>
<script src="<?= SERVERURL ?>views/assets/vendors/chartist/chartist.min.js"></script>
<script src="<?= SERVERURL ?>views/assets/vendors/progressbar.js/progressbar.min.js"></script>
<script src="<?= SERVERURL ?>views/assets/js/colores.js"></script>
<script src="<?= SERVERURL ?>views/assets/js/dashboard.js"></script>

<!-- 6) Tus scripts de UI y lógica -->
<script src="<?= SERVERURL ?>views/assets/js/off-canvas.js"></script>
<script src="<?= SERVERURL ?>views/assets/js/hoverable-collapse.js"></script>
<script src="<?= SERVERURL ?>views/assets/js/misc.js"></script>
<script src="<?= SERVERURL ?>views/assets/js/settings.js"></script>
<script src="<?= SERVERURL ?>views/assets/js/todolist.js"></script>
<script src="<?= SERVERURL ?>views/assets/js/dashboard.js"></script>

<!-- 7) Inicialización de Lightbox2 y DataTable -->
<script>
  // Definir URL base de servidor en JS
  const SERVERURL = "<?= SERVERURL ?>";

  // Opciones de Lightbox2
  lightbox.option({
    resizeDuration: 200,
    wrapAround: true
  });

  // Cargar tu tabla de productos/cuando el DOM esté listo
  $(document).ready(function() {
    if (typeof cargarTablaProductos === 'function') {
      cargarTablaProductos();
    }
  });
</script>
