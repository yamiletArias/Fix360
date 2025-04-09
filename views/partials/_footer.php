         <!-- partial:partials/_footer.html -->
         <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2025 Fix360. All rights reserved. <a href="#"> Terms of use</a><a href="#">Privacy Policy</a></span>
              <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"></span>
            </div>
          </footer>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="<?= SERVERURL?>views/assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="<?= SERVERURL?>views/assets/vendors/chart.js/chart.umd.js"></script>
    <script src="<?= SERVERURL?>views/assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="<?= SERVERURL?>views/assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= SERVERURL?>views/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>

    <script src="<?= SERVERURL?>views/assets/vendors/moment/moment.min.js"></script>
    <script src="<?= SERVERURL?>views/assets/vendors/daterangepicker/daterangepicker.js"></script>
    <script src="<?= SERVERURL?>views/assets/vendors/chartist/chartist.min.js"></script>
    <script src="<?= SERVERURL?>views/assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="<?= SERVERURL?>views/assets/js/jquery.cookie.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="<?= SERVERURL?>views/assets/js/off-canvas.js"></script>
    <script src="<?= SERVERURL?>views/assets/js/hoverable-collapse.js"></script>
    <script src="<?= SERVERURL?>views/assets/js/misc.js"></script>
    <script src="<?= SERVERURL?>views/assets/js/settings.js"></script>
    <script src="<?= SERVERURL?>views/assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="<?= SERVERURL?>views/assets/js/dashboard.js"></script>
    
    <!-- js de carga moneda -->
     <script src="<?= SERVERURL?>views/assets/js/tipomoneda.js"></script>

    
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="<?= SERVERURL?>views/assets/js/off-canvas.js"></script>
  <script src="<?= SERVERURL?>views/assets/js/hoverable-collapse.js"></script>
  <script src="<?= SERVERURL?>views/assets/js/misc.js"></script>
  <script src="<?= SERVERURL?>views/assets/js/settings.js"></script>
  <script src="<?= SERVERURL?>views/assets/js/todolist.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

 <!-- DataTables JS -->
 <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

 <script>
    $(document).ready(function () {
      $('#miTabla').DataTable({
        language: {
          "lengthMenu": "Mostrar _MENU_ registros por página",
          "zeroRecords": "No se encontraron resultados",
          "info": "Mostrando página _PAGE_ de _PAGES_",
          "infoEmpty": "No hay registros disponibles",
          "infoFiltered": "(filtrado de _MAX_ registros totales)",
          "search": "Buscar:",
          "loadingRecords": "Cargando...",
          "processing": "Procesando...",
          "emptyTable": "No hay datos disponibles en la tabla"
        }
      });
    });

  </script>
    <!-- End custom js for this page -->