<?php
require_once "./app/config/app.php";
require_once './views/partials/header.php';

?>
<!-- partial - WRAPPER MAIN + FOOTER -->
<div class="main-panel">
  <!-- MAIN -->
  <div class="content-wrapper">
    <!-- Contenido main -->
    <div class="content-header">
      <div class='page-header'>
        <h3 class='page-title'> Bienvenido <?= $_SESSION["login"]["nombres"] ?> </h3>
        <nav aria-label='breadcrumb'>
          <ol class='breadcrumb'>
            <li class='breadcrumb-item'><a href='<?= SERVERURL ?>views/home/welcome'>Inicio</a></li>
            <li class='breadcrumb-item active' aria-current='page'>Página de inicio</li>
          </ol>
        </nav>
      </div>
    </div>

    <div class="content-main">

    </div>
  </div>
  <!-- content-wrapper ends -->
  <!-- partial:partials/_footer.html - FOOTER-->


  <?php
  require_once './views/partials/footer.php';
  ?>


  </body>

  </html>