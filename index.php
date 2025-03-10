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

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>FIX360</title>
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
              <form class="pt-3" method="post" id="formLogin" autocomplete="off" >
                <div class="form-group">
                  <input type="text" class="form-control form-control-lg" 
                  id="namuser" placeholder="User Name" autofocus required>
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg" 
                  id="passuser" placeholder="Password" required>
                </div>
                <div class="mt-3">
                  <button class="btn d-grid btn-primary btn-lg font-weight-medium auth-form-btn" 
                  type="submit">Iniciar sesión</button>

                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input" id="remember"> 
                      Recordar contraseña 
                    </label>
                  </div>
                  <a href="#" class="auth-link text-black">Olvidaste tu contraseña?</a>
                </div>

                <div class="text-center mt-4 font-weight-light"> No tienes cuenta? <a href="register.html" class="text-primary">Create</a>
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
  const NamUser = document.querySelector("#namuser");
  const PassUser = document.querySelector("#passuser");

  formLogin.addEventListener("submit", async (event) => {
    event.preventDefault();

    // Validar que los campos no estén vacíos
    if (NamUser.value.trim() === "" || PassUser.value.trim() === "") {
      showToast("Ingrese usuario y contraseña", "WARNING");
      return;
    }

    // Datos a enviar
    const parametros = new FormData();
    parametros.append("operation", "login");
    parametros.append("namuser", NamUser.value.trim());
    parametros.append("passuser", PassUser.value.trim());

    try {
      const response = await fetch(`./app/controllers/Colaborador.controller.php`, {
        method: "POST",
        body: parametros,
      });

      const responseText = await response.text(); // Captura la respuesta en texto
      console.log("Respuesta del servidor:", responseText); // Muestra en la consola

      // Intentar parsear la respuesta a JSON
      let data;
      try {
        data = JSON.parse(responseText);
      } catch (error) {
        console.error("Error al convertir JSON:", responseText);
        showToast("Respuesta inválida del servidor", "ERROR");
        return;
      }

      // Validar respuesta
      if (data.esCorrecto) {
        showToast(data.mensaje, "SUCCESS", 2000, "./views/page/home/welcome");
      } else {
        showToast(data.mensaje, "WARNING");
      }
    } catch (error) {
      console.error("Error en la petición:", error);
      showToast("Error al conectar con el servidor", "ERROR");
    }
  });

  function showToast(mensaje, tipo, delay = 2000, redirect = null) {
    alert(`${tipo}: ${mensaje}`);
    if (redirect) {
      setTimeout(() => {
        window.location.href = redirect;
      }, delay);
    }
  }
});
</script>

  <!-- endinject -->
</body>
</html>