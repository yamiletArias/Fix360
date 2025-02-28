<?php
session_start();

require_once "./app/config/Server.php";

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
                    id="namuser" placeholder="USERNAME" autofocus required>
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control form-control-lg" 
                    id="passuser" placeholder="Password" require>
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
                    <a href="#" class="auth-link text-black">Forgot password?</a>
                  </div>

                  <div class="text-center mt-4 font-weight-light"> Don't have an account? <a href="register.html" class="text-primary">Create</a>
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

      formLogin.addEventListener("submit", async(event) => {
        event.preventDefault();

        //datos a enviar
        const parametros = new FormData();
        parametros.append("operation", "login");
        parametros.append("namuser", NamUser.value);
        parametros.append("passuser", PassUser.value);


        const response = await fetch(`./app/controllers/Colaborador.controller.php`, {
          method: 'POST',
          body: parametros
        });

        const data = await response.json();
      
        if (!data.esCorrecto){
          showToast(data.mensaje, 'WARNING');
        }else{
          showToast(data.mensaje, 'SECCESS', 200, './views');
        }

      });
    });
  </script>
    <!-- endinject -->
</body>
</html>