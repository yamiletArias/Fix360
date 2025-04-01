<?php

CONST NAMEVIEW = "Editar datos de cliente";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>
      <div class="container-main">
        <div class="card border">
          <div class="card-header">
            <h2>Nuevo registro de:</h2>
            <label>
              <input type="radio" name="tipo" value="persona" onclick="mostrarFormulario('persona')" checked>
              Persona
            </label>
            <label>
              <input type="radio" name="tipo" value="empresa" onclick="mostrarFormulario('empresa')">
              Empresa
            </label>
          </div>
          <div  class="card-body">
            <div id="formPersona">
              <div class="row">

                <div class="col-md-4">
                  <div class="form-floating">
                    <select class="form-select" id="floatingdoc" id="tipodoc" style="color: black;">
                      <option value="DNI">DNI</option>
                      <option value="Pasaporte" >Pasaporte</option>
                      <option value="cde"  >Carnet de extranjeria</option>
                    </select>
                    <label for="floatingdoc">Tipo de Documento:</label>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="floatingnumdoc" placeholder="parece que sin placeholder no tiene ese efecto" ><br><br>
                    <label for="floatingnumdoc">N° de Documento</label>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-floating">
                    <input type="email" id="correo" class="form-control" minlength="9" maxlength="9" placeholder="celular">
                    <label for="telprincipal">Tel. principal</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" id="nombres" class="form-control" placeholder="nombrealazar"><br><br>
                    <label for="nombres">Nombres</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" id="apellidos" class="form-control" placeholder="apellido">
                    <label for="apellidos">Apellidos</label>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-floating">
                    <input type="text" id="nombres" class="form-control" placeholder="mi casa"><br><br>
                    <label for="direccion">Direccion</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-floating">
                    <input type="text" id="correo" class="form-control" placeholder="thepunisher2000@gmail.com"><br><br>
                    <label for="correo">Correo</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-floating">
                    <input type="text" id="telarternativo" class="form-control" minlength="9" maxlength="9" placeholder="956633983"><br><br>
                    <label for="telalternativo">Tel. alternativo</label>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-floating">
                    <select class="form-select" style="color: black;" id="contactabilidad">
                      <option value="DNI">Redes Sociales</option>
                      <option value="Pasaporte">Folletos</option>
                      <option value="cde">Campaña Publicitaria</option>
                      <option value="cde">Recomendacion</option>
                    </select>
                    <label for="contactabilidad">Contatabilidad:</label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Formulario Empresa -->
            <div id="formEmpresa" class="hidden">
              <div class="row">

                <div class="col-md-4">
                  <div class="form-floating">
                    <input type="text" id="ruc" class="form-control" placeholder="rucdelaempresa"><br><br>
                    <label for="ruc">RUC</label>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-floating">
                    <input type="text" id="nomcomercial" class="form-control" placeholder="nomcomercial"><br><br>
                    <label for="nomcomercial">Nombre Comercial</label>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-floating">
                    <input type="text" id="razonsocial" class="form-control" placeholder="razonsocial"><br><br>
                    <label for="razonsocial">Razón Social</label>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-floating">
                    <input type="text" id="telempresa" class="form-control" placeholder="telempresa" minlength="9" maxlength="9">
                    <label for="telempresa">Teléfono</label>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-floating">
                    <input type="email" id="correoemp" class="form-control" placeholder="coreoemp">
                    <label for="correoEmp">Correo</label>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-floating">
                    <select class="form-select" id="contactabilidad" style="color: black;">
                      <option value="DNI">Redes Sociales</option>
                      <option value="Pasaporte">Folletos</option>
                      <option value="cde">Campaña Publicitaria</option>
                      <option value="cde">Recomendacion</option>
                    </select>
                    <label for="contactabilidad">Contatabilidad:</label>
                  </div>
                </div>

              </div>
            </div>
          </div>
          <div class="card-footer text-end">
            <button  onclick="window.location.href='listar-cliente.html'" class=" btn btn-danger">Cancelar</button>
            <button class="btn btn-success" onclick="window.location.href='listar-cliente.html'">Aceptar</button>
          </div>
        </div>

        <!-- Formulario Persona -->

      </div>
    </div>
  </div>
  <!--FIN VENTAS-->

<?php

  require_once "../../partials/_footer.php";
  
?>

  <!-- endinject -->
  <!-- Custom js for this page -->
  <!-- End custom js for this page -->
</body>

</html>

<script>
  function mostrarFormulario(tipo) {
    if (tipo === 'persona') {
      document.getElementById("formPersona").style.display = "block";
      document.getElementById("formEmpresa").style.display = "none";
    } else {
      document.getElementById("formPersona").style.display = "none";
      document.getElementById("formEmpresa").style.display = "block";
    }
  }
</script>