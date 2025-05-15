<?php
session_start();

require_once "./app/config/app.php";
require_once "./app/config/Server.php";

/*
if (isset($_SESSION['login']) && $_SESSION['login']['status'] == true){
  header("Location: " . SERVERURL . "views/page/home/welcome");
  exit();
}
*/


?>

<style>
      .input {
      font-size: 17px;
      color:black;
    }

    .content-wrapper {
    background: #01122c !important;
}
</style>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Fix360</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="./views/assets/css/vertical-light-layout/style.css">
  <link rel="stylesheet" href="./views/assets/images/favicon.png">
  <link rel="stylesheet" href="./views/assets/vendors/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="./views/assets/vendors/flag-icon-css/css/flag-icons.min.css">
  <link rel="stylesheet" href="./views/assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <!-- endinject -->
  <!-- Layout styles -->
  <!-- End layout styles -->
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth">
        <div class="row flex-grow">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left p-5">
              <h4>Bienvenido</h4>
              <h6 class="font-weight-light">Ingresa tu datos.</h6>
              <form class="pt-3" method="post" id="formLogin" autocomplete="off">
                <div class="form-group">
                  <input type="text" class="input form-control form-control-lg "
                    id="namuser" placeholder="Nombre de usuario" autofocus required>
                </div>
                <div class="form-group">
                  <input type="password" class="input form-control form-control-lg"
                    id="passuser" placeholder="contraseña" required>
                </div>
                <div class="mt-3">
                  <button class="btn d-grid btn-success btn-lg font-weight-medium auth-form-btn text-end"
                    type="submit">Iniciar sesión</button>

                </div>


              </form>
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
  <script src="./views/assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="./views/assets/js/off-canvas.js"></script>
  <script src="./views/assets/js/hoverable-collapse.js"></script>
  <script src="./views/assets/js/misc.js"></script>
  <script src="./views/assets/js/settings.js"></script>
  <script src="./views/assets/js/todolist.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const formLogin = document.querySelector("#formLogin");
  const namuser   = document.querySelector("#namuser");
  const passuser  = document.querySelector("#passuser");

  formLogin.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!namuser.value.trim() || !passuser.value.trim()) {
      return showToast("Ingrese usuario y contraseña", "WARNING");
    }

    // Preparamos el form data
    const fd = new FormData();
    fd.append("operation", "login");
    fd.append("namuser", namuser.value.trim());
    fd.append("passuser", passuser.value);

    try {
      const res = await fetch(`./app/controllers/Colaborador.controller.php`, {
        method: "POST",
        body: fd
      });
      const json = await res.json();

      if (json.status === true) {
        // Éxito: guardamos notificación y redirigimos
        showToast(json.message, "SUCCESS", 1500, "./views/page/movdiario/listar-movdiario.php");
      } else {
        // Falló login o contrato no vigente
        showToast(json.message || "Credenciales inválidas", "WARNING");
      }

    } catch (err) {
      console.error(err);
      showToast("Error al conectar con el servidor", "ERROR");
    }
  });

  /**
   * Muestra un alert (o tu toast) y opcionalmente redirige
   * @param {string} msg
   * @param {'SUCCESS'|'WARNING'|'ERROR'} type
   * @param {number} delay milisegundos antes de la redirección
   * @param {string|null} url a donde ir
   */
  function showToast(msg, type, delay = 2000, url = null) {
    // Aquí puedes reemplazar alert por tu sistema de notificaciones
    alert(`${type}: ${msg}`);
    if (url) {
      setTimeout(() => window.location.href = url, delay);
    }
  }
});
</script>


  <!-- endinject -->
</body>

</html>