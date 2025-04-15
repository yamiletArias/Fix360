<?php

const NAMEVIEW = "Registro de clientes";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

?>

<div class="container-main">
  <div class="card border">
    <div class="card-header">
      <h3>Tipo de cliente:</h3>
      <label>
        <input  class="form-check-input text-start" type="radio" name="tipo" value="persona" onclick="mostrarFormulario('persona')" checked>
        Persona
      </label>
      <label style="padding-left: 10px;">
        <input class="form-check-input text-start" type="radio" name="tipo" value="empresa" onclick="mostrarFormulario('empresa')">
        Empresa
      </label>
    </div>
    <div class="card-body">
      <!-- Formulario para registrar una persona -->
      <form action="" id="formPersona" style="display: block;">
        <div>
          <div class="row">
            <div class="col-md-2">
              <div class="form-floating">
                <select class="form-select" required name="tipodoc" id="tipodoc" style="color: black;">
                  <option value="DNI">DNI</option>
                  <option value="Pasaporte">Pasaporte</option>
                  <option value="cde">Carnet de extranjeria</option>
                </select>
                <label for="tipodoc">Tipo de Documento:</label>
              </div>
            </div>

            <div class="col-md-2 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control input"  name="numdoc" id="numdoc" minlength="8" maxlength="20" placeholder="numdoc" autofocus required>
                <label for="numdoc">N° de Documento</label>
              </div>
            </div>

            <div class="col-md-4 mb-3">
              <div class="form-floating">
                <input type="text" name="apellidos" class="form-control input" id="apellidos" minlength="2" maxlength="100" placeholder="apellidos" required>
                <label for="apellidos">Apellidos</label>
              </div>
            </div>

            <div class="col-md-4 mb-3">
              <div class="form-floating">
                <input type="text" id="nombres" class="form-control input" name="nombres" minlength="2" maxlength="100" required placeholder="nombrealazar">
                <label for="nombres">Nombres</label>
              </div>
            </div>

            <div class="col-md-8 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control input" name="direccion" id="direccion" minlength="5" maxlength="100" placeholder="mi casa">
                <label for="direccion">Direccion</label>
              </div>
            </div>

            <div class="col-md-2 mb-3">
              <div class="form-floating">
                <input type="text" id="telprincipal" class="form-control input" minlength="9" maxlength="9" name="telprincipal" placeholder="celular">
                <label for="telprincipal">Tel. principal</label>
              </div>
            </div>

            <div class="col-md-2 mb-3">
              <div class="form-floating">
                <input type="text" id="telalternativo" name="telalternativo" class="form-control input" minlength="9" maxlength="9" placeholder="956633983">
                <label for="telalternativo">Tel. alternativo</label>
              </div>
            </div>

            <div class="col-md-8 mb-3">
              <div class="form-floating">
                <input type="email" name="correo" class="form-control input" id="correo" minlength="10" maxlength="100" placeholder="thepunisher2000@gmail.com">
                <label for="correo">Correo</label>
              </div>
            </div>

            <div class="col-md-2 mb-3">
              <div class="form-floating">
                <input type="text" name="numruc" class="form-control input" id="numruc" minlength="11" maxlength="11" placeholder="N° RUC">
                <label for="numruc">N° RUC</label>
              </div>
            </div>
            
            <div class="col-md-2 mb-3">
              <div class="form-floating">
                <!-- Usamos id "cpersona" en el select para el formulario de persona -->
                <select class="form-select" id="cpersona" name="cpersona" style="color: black;" required>
                  <option value="">Seleccione una opción</option>
                </select>
                <label for="cpersona">Contactabilidad:</label>
              </div>
            </div>

          </div>
        </div>
      </form>
      <!-- Formulario Empresa -->
      <form action="" id="formEmpresa" style="display: none;">
        <div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <div class="form-floating">
                <input type="text" id="ruc" name="ruc" class="form-control input" placeholder="rucdelaempresa" minlength="11" maxlength="11" required>
                <label for="ruc">RUC</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" id="nomcomercial" name="nomcomercial" class="form-control input" placeholder="nomcomercial" minlength="5" maxlength="100" required>
                <label for="nomcomercial">Nombre Comercial</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" id="razonsocial" name="razonsocial" class="form-control input" placeholder="razonsocia0l" minlength="5" maxlength="100" required>
                <label for="razonsocial">Razón Social</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" id="telempresa" name="telempresa" class="form-control input" placeholder="telempresa" minlength="9" maxlength="9">
                <label for="telempresa">Teléfono</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="email" id="correoemp" name="correoemp" class="form-control input" placeholder="coreoemp" minlength="10" maxlength="100">
                <label for="correoEmp">Correo</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <!-- Usamos id "cempresa" para el formulario de empresa -->
                <select class="form-select" id="cempresa" name="cempresa" style="color: black;" required>
                  <option value="">Seleccione una opción</option>
                </select>
                <label for="cempresa">Contactabilidad:</label>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="card-footer text-end">
      <button type="button" class="btn btn-secondary" onclick="window.location.href='listar-cliente.php'" >Cancelar</button>
      <button type="submit" id="btnRegistrar" class="btn btn-success">Aceptar</button>
    </div>
  </div>
</div>

</div>
<!-- Formulario Persona -->
</div>
</div>
</div>

</body>

</html>

<script src="<?= SERVERURL?>views/page/clientes/js/registrar-cliente.js"></script>
<?php

require_once "../../partials/_footer.php";

?>