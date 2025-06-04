<?php
const SERVERURL = "http://localhost/Fix360/";
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Stellar Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="<?= SERVERURL ?>views/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?= SERVERURL ?>views/assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="<?= SERVERURL ?>views/assets/vendors/flag-icon-css/css/flag-icons.min.css">
    <link rel="stylesheet" href="<?= SERVERURL ?>views/assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End Plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="<?= SERVERURL ?>views/assets/css/vertical-light-layout/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="<?= SERVERURL ?>views/assets/images/favicon.png" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center text-center error-page " style="background-color: #01122c;">
          <div class="row flex-grow">
            <div class="col-lg-7 mx-auto text-white">
              <div class="row align-items-center d-flex flex-row">
                <div class="col-lg-6 text-right pr-lg-4">
                  <h1 class="display-1 mb-0">403</h1>
                </div>
                <div class="col-lg-6 error-page-divider text-lg-left pl-lg-4 text-center" >
                  <h2>¡No tiene permiso para acceder a esta vista!</h2>
                 <!-- <h3 class="font-weight-light">Internal server error!</h3> -->
                </div>
              </div>
              <div class="row mt-5">
                <div class="col-12 text-center mt-xl-2">
                  <a class="text-white font-weight-medium" href="<?= SERVERURL ?>views/page/movdiario/listar-movdiario.php">Volver al inicio</a>
                </div>
              </div>
              <div class="row mt-5">
                <div class="col-12 mt-xl-2">
                   <!-- <p class="text-white font-weight-medium text-center">Copyright &copy; 2021 All rights reserved.</p> -->
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="<?= SERVERURL ?>views/assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="<?= SERVERURL ?>views/assets/js/off-canvas.js"></script>
    <script src="<?= SERVERURL ?>views/assets/js/hoverable-collapse.js"></script>
    <script src="<?= SERVERURL ?>views/assets/js/misc.js"></script>
    <script src="<?= SERVERURL ?>views/assets/js/settings.js"></script>
    <script src="<?= SERVERURL ?>views/assets/js/todolist.js"></script>
    <!-- endinject -->
  </body>
</html>