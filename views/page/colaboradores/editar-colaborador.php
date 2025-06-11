<?php

const NAMEVIEW = "Colaborador | Editar";

require_once "../../../app/helpers/helper.php";
require_once "../../../app/config/app.php";
require_once "../../partials/header.php";

$id = isset($_GET['idcolaborador']) ? (int) $_GET['idcolaborador'] : 0;
if ($id <= 0) {
    echo "<div class='container-main'>
    <div class='alert alert-danger'>ID de colaborador inválido.
    </div>
    </div>
    </div>
    </div>";
    require_once "../../partials/_footer.php";
    exit;
}

?>

<div class="container-main">
    <div class="card border">
        <div class="card-body">
            <form id="formEditarColaborador" autocomplete="off">
                <input type="hidden" id="idcolaborador" name="idcolaborador" value="<?= $id ?>">
                <div class="row g-3">
                    <div class="col-md-2">
                        <div class="form-floating">
                            <select class="form-select input" id="tipodoc" name="tipodoc" style="color: black;" ReadOnly disabled>
                                <option value="DNI">DNI</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="Carnet">Carnet de extranjería</option>
                            </select>
                            <label for="tipodoc"><strong>Tipo Documento</strong></label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="text" class="form-control input" id="numdoc" name="numdoc" minlength="6" maxlength="20" pattern="[0-9A-Za-z]+" placeholder="Documento" autocomplete="off" disabled> 
                            <label for="numdoc"><strong>N° Documento</strong></label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control input" id="apellidos" name="apellidos" minlength="2" maxlength="50" placeholder="Apellidos" autocomplete="off" ReadOnly>
                            <label for="apellidos"><strong>Apellidos</strong></label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control input" id="nombres" name="nombres" minlength="2" maxlength="50" placeholder="Nombres" autocomplete="off" ReadOnly>
                            <label for="nombres"><strong>Nombres</strong></label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control input" id="direccion" name="direccion" minlength="5" maxlength="100" placeholder="Dirección" autocomplete="off" required>
                            <label for="direccion">Dirección</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="email" class="form-control input" id="correo" name="correo" minlength="5" maxlength="100" placeholder="Correo" autocomplete="off" required>
                            <label for="correo">Correo</label>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="text" class="form-control input" id="telprincipal" name="telprincipal" minlength="9" maxlength="9" pattern="9\d{8}" placeholder="Tel. principal" autocomplete="off" required>
                            <label for="telprincipal"><strong>Tel. Principal</strong></label>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-floating">
                            <select class="form-select input" id="idrol" name="idrol" style="color: black;" required ReadOnly>
                                <option value="">Cargando roles...</option>
                            </select>
                            <label for="idrol"><strong>Rol</strong></label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" class="form-control input" name="namuser" id="namuser" minlength="3" maxlength="50" placeholder="Usuario" autocomplete="off" >
                            <label for="namuser"><strong>Username</strong></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="password" class="form-control input" name="passuser" id="passuser" minlength="6" maxlength="100" placeholder="Nueva contraseña (dejar en blanco para mantener)" autocomplete="off">
                            <label for="passuser">Nueva contraseña</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="date" class="form-control input" name="fechainicio" id="fechainicio" disabled>
                            <label for="fechainicio"><strong>Fecha Inicio</strong></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="date" class="form-control input" name="fechafin" id="fechafin">
                            <label for="fechafin">Fecha Fin (opcional)</label>
                        </div>
                    </div>

                </div>

            </form>
        </div>
        <div class="card-footer text-end">
            <a href="listar-colaborador.php" class="btn btn-secondary">Cancelar</a>
            <button id="btnActualizar" class="btn btn-success" disabled>Guardar</button>
        </div>
    </div>
</div>
</div>
</div>

<?php require_once "../../partials/_footer.php"; ?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const id = <?= $id ?>;
        const tipodocEl = document.getElementById('tipodoc');
        const numdocEl = document.getElementById('numdoc');
        const nombresEl = document.getElementById('nombres');
        const apellidosEl = document.getElementById('apellidos');
        const direccionEl = document.getElementById('direccion');
        const correoEl = document.getElementById('correo');
        const telEl = document.getElementById('telprincipal');
        const rolSelect = document.getElementById('idrol');
        const namuserEl = document.getElementById('namuser');
        const passuserEl = document.getElementById('passuser');
        const btnActualizar = document.getElementById('btnActualizar');
        const form = document.getElementById('formEditarColaborador');

        let originalUsername = ''; // para comparar después

        // 1) Carga roles y luego datos del colaborador
        fetch("<?= SERVERURL ?>app/controllers/Rol.controller.php")
            .then(res => res.json())
            .then(data => {
                rolSelect.innerHTML = '<option value="">Seleccione un rol</option>';
                data.forEach(r => {
                    rolSelect.innerHTML += `<option value="${r.idrol}">${r.rol}</option>`;
                });
                return fetch(`<?= SERVERURL ?>app/controllers/colaborador.controller.php?action=get&id=${id}`);
            })
            .then(res => res.json())
            .then(resp => {
                if (!resp.status) {
                    Swal.fire('Error', 'No se pudo cargar datos.', 'error');
                    return;
                }
                const d = resp.data;
                tipodocEl.value = d.tipodoc;
                numdocEl.value = d.numdoc;
                nombresEl.value = d.nombres;
                apellidosEl.value = d.apellidos;
                direccionEl.value = d.direccion;
                correoEl.value = d.correo;
                telEl.value = d.telprincipal;
                document.getElementById('fechainicio').value = d.fechainicio;
                document.getElementById('fechafin').value = d.fechafin || '';
                namuserEl.value = d.username || '';
                originalUsername = d.username || '';
                rolSelect.value = d.idrol;
                updateBtnState();
            })
            .catch(err => console.error(err));

        // Validaciones
        function validarCorreo() {
            const c = correoEl.value.trim();
            return !c || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(c);
        }

        function validarTelefono() {
            return /^[9]\d{8}$/.test(telEl.value.trim());
        }

        function camposBasicosValidos() {
            if (!telEl.value.trim() || !validarTelefono()) return false;
            if (!rolSelect.value) return false;
            return true;
        }

        // Regla de credenciales ajustada
        function credencialesValidas() {
            const u = namuserEl.value.trim();
            const p = passuserEl.value.trim();

            // Caso 1: no cambió usuario y no puso contraseña → OK
            if (u === originalUsername && !p) return true;

            // Caso 2: cambió usuario (u ≠ original) → exige password
            if (u !== originalUsername && (!p || p.length < 6)) return false;

            // Caso 3: quiere cambiar sólo pass (u === original) → exige password
            if (u === originalUsername && p && p.length < 6) return false;

            // Resto: user no puede quedar vacío
            if (!u || u.length < 3) return false;

            return true;
        }

        // Habilita o deshabilita el botón
        function updateBtnState() {
            btnActualizar.disabled = !(camposBasicosValidos() && credencialesValidas());
        }

        // Listeners en cada campo relevante
        [
            direccionEl, correoEl, telEl,
            rolSelect, namuserEl, passuserEl
        ].forEach(el => el.addEventListener('input', updateBtnState));

        // Al hacer click en Guardar
        btnActualizar.addEventListener('click', async e => {
            e.preventDefault();

            if (!camposBasicosValidos()) {
                return showToast('Completa correctamente los campos obligatorios.', 'ERROR', 1500);
            }
            if (!credencialesValidas()) {
                return showToast(
                    'Si cambias el usuario o la contraseña, asegúrate de que:\n' +
                    '- Usuario tenga al menos 3 caracteres.\n' +
                    '- Contraseña (si se ingresa) tenga al menos 6 caracteres.',
                    'ERROR', 2500
                );
            }

            const confirmar = await ask("¿Confirma la actualización del colaborador?", "Colaboradores");
            if (!confirmar) return;

            const fd = new FormData(form);
            fd.append('action', 'update');

            try {
                const resp = await fetch("<?= SERVERURL ?>app/controllers/colaborador.controller.php", {
                    method: 'POST',
                    body: fd
                });
                const result = await resp.json();
                if (result.status) {
                    showToast('Colaborador actualizado.', 'SUCCESS', 1000);
                    setTimeout(() => window.location.href = 'listar-colaborador.php', 1500);
                } else {
                    showToast(result.message || 'Error inesperado.', 'ERROR', 2000);
                }
            } catch (err) {
                console.error(err);
                showToast('Error de servidor.', 'ERROR', 2000);
            }
        });
    });
</script>