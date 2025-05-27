<?php
// header.php (o partials/header.php)

session_start();

// 1) Si no hay sesión activa, lo mando al login
if (
  !isset($_SESSION['login']) ||
  empty($_SESSION['login']['status']) ||
  $_SESSION['login']['status'] !== true
) {
  header("Location: " . SERVERURL);
  exit;
}



// 2) Si llegó aquí, ya está autenticado:
//    guardo el idcolaborador en una variable global
$idadmin = $_SESSION['login']['idcolaborador'];
require_once dirname(__DIR__, 2) . '/app/models/Colaborador.php';
$colModel = new Colaborador();
$usuario  = $colModel->getById($idadmin);

// 1. Ajustamos la zona horaria
date_default_timezone_set('America/Lima');
// 2. Sacamos la hora actual
$hora = (int) date('H');
// 3. Definimos el saludo según la hora
if ($hora >= 5 && $hora < 12) {
    $saludo = "Buenos días";
} elseif ($hora >= 12 && $hora < 18) {
    $saludo = "Buenas tardes";
} else {
    $saludo = "Buenas noches";
}


// ... luego requieres tus modelos helpers, etc.
require_once dirname(__DIR__, 2) . '/app/models/Agenda.php';
require_once dirname(__DIR__, 2) . '/app/helpers/helper.php';



$agendaModel = new Agenda();
$hoy = $agendaModel->getRecordatoriosHoy();
$hoy_count = count($hoy);
  $maxMostrar     = 4;
  $totalHoy       = $hoy_count;
  $hoyParaMostrar = array_slice($hoy, 0, $maxMostrar);
  $restantes      = $totalHoy - count($hoyParaMostrar);
  // Máximo a mostrar en el dropdown

?>


<!DOCTYPE html>
<html lang="en">

<style>

</style>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fix360</title>
  <!--Font awesome -->
  <!-- FonAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- plugins:css -->
  <link rel="stylesheet" href="<?= SERVERURL ?>views/assets/vendors/simple-line-icons/css/simple-line-icons.css" />
  <link rel="stylesheet" href="<?= SERVERURL ?>views/assets/vendors/flag-icon-css/css/flag-icons.min.css" />
  <link rel="stylesheet" href="<?= SERVERURL ?>views/assets/vendors/css/vendor.bundle.base.css" />
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../../assets/js/swalcustom.js"></script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-DQvkBjpPgn7RC31MCQoOeC9TI2kdqa4+BSgNMNj8v77fdC77Kj5zpWFTJaaAoMbC" crossorigin="anonymous">

  <link rel="stylesheet" href="<?= SERVERURL ?>views/assets/vendors/font-awesome/css/font-awesome.min.css" />

  <link rel="stylesheet" href="<?= SERVERURL ?>views/assets/css/vertical-light-layout/style.css" />

  <!--link rel="shortcut icon" href="<?= SERVERURL ?>views/assets/images/favicon.png" /-->
  <link rel="shortcut icon" href="<?= SERVERURL ?>images/minilogo.jpg" />

  <!-- Lightbox2 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">



  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" />

  <style>
    html,
    body {
      height: 100%;
      margin: 0;
      /*overflow: hidden; */
    }

    .swal2-modal .swal2-icon,
    .swal2-modal .swal2-success-ring {
      margin-top: 0;
      margin-bottom: 0px;
    }


    .container-main {
      background: transparent;
      padding: 0 15px;
      border-radius: 5px;
      box-shadow: none;
      margin: 50px;
      width: 80%;
    }

    #miTabla tbody td {
      vertical-align: middle;
    }

    #selectMetodo {
      background-color: white !important;
      cursor: pointer;
    }


    label {
      padding: 0px;
    }

    .form-group {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 20px;
      align-items: flex-end;

    }


    .input {
      font-size: 17px;
      color: black;
    }


    .form-field {
      display: flex;
      flex-direction: column;
      flex: 1;
      min-width: 150px;
    }

    .input-button-group {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    input,
    select,
    button {
      padding: 0px;
      border-radius: 0px;
      font-size: 100%;
    }



    .small-button {
      width: 40px;
      padding: 5px;
      text-align: center;
    }


    .border {
      border-radius: 4px;
    }

    .autocomplete {
      position: relative;
      display: inline-block;
      width: 100%;
      max-width: 600px;
    }

    .autocomplete-items {
      position: absolute;
      border: 1px solid #d4d4d4;
      border-top: none;
      z-index: 99;
      top: 100%;
      left: 0;
      right: 0;
      border-radius: 0 0 4px 4px;
      max-height: 200px;
      overflow-y: auto;
    }

    .autocomplete-items div {
      padding: 10px;
      cursor: pointer;
      background-color: #fff;
    }

    .autocomplete-items div:hover {
      background-color: #4e99e9;
      color: #ffffff;
    }

    .autocomplete-active {
      background-color: #4e99e9 !important;
      color: #ffffff;
    }

    .autocomplete-items .default-option {
      background-color: #4e99e9;
      color: #ffffff;
    }

    #numserie,
    #numcom {
      margin-right: 10px;
    }
  </style>
</head>

<body>
  <!-- VENTAS -->
  <div class="container-scroller">
    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo" href="../movdiario/listar-movdiario.php">
          <img src="../../../images/logofix360.png" alt="logo" style="width: 200px;" class="logo-dark" />
          <img src="../../../images/473424986_122094668432737167_5148454371714842654_n.jpg" alt="logo-light"
            class="logo-light" />
        </a>
        <a class="navbar-brand brand-logo-mini" href="../movdiario/listar-movdiario.php"><img
            src="../../../images/minilogo.jpg" alt="logo" /></a>
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center">
        <h2 class="mb-0 font-weight-medium d-none d-lg-flex"><?= NAMEVIEW ?></h2>
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle count-indicator message-dropdown" id="messageDropdown" href="#"
              data-bs-toggle="dropdown" title="Recordatorios de hoy" aria-expanded="false">
              <i class="icon-speech"></i>
              <span class="count"><?= $hoy_count ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-end navbar-dropdown preview-list p-0" aria-labelledby="messageDropdown" style="min-width:250px;">
              <div class="dropdown-header mb-0 px-3 py-2">
                <strong class="input"><?= $totalHoy ?> recordatorio<?= $totalHoy !== 1 ? 's' : '' ?></strong>
                <a class="btn btn-sm btn-primary float-end" href="<?= SERVERURL ?>views/page/agendas/listar-agendas.php" title="Ver todos los recordatorios">
                  <i class="fa fa-list-alt"></i>
                </a>
              </div>
              <div class="dropdown-divider"></div>

              <?php if ($totalHoy): ?>
                <?php foreach ($hoyParaMostrar as $r): ?>
                  <a class="dropdown-item preview-item" href="<?= SERVERURL ?>views/page/agendas/listar-agendas.php">
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1"><?= htmlspecialchars($r['nomcliente']) ?></p>
                      <p class="small-text text-muted mb-0"><?= htmlspecialchars($r['comentario']) ?></p>
                    </div>
                  </a>
                <?php endforeach; ?>

                <?php if ($restantes > 0): ?>
                  <a class="dropdown-item text-center small text-dark" href="<?= SERVERURL ?>views/page/agendas/listar-agendas.php">
                    y <?= $restantes ?> recordatorio<?= $restantes !== 1 ? 's' : '' ?> más
                  </a>
                <?php endif; ?>

              <?php else: ?>
                <div class="px-3 py-2 text-center text-muted">No hay recordatorios hoy</div>
              <?php endif; ?>
            </div>


          </li>
          <li class="nav-item dropdown d-none d-xl-inline-flex user-dropdown">
            <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-bs-toggle="dropdown"
              aria-expanded="false">
              <img class="img-xs rounded-circle ms-2"
                src="../../../images/473424986_122094668432737167_5148454371714842654_n.jpg" alt="Profile image" />
              <span class="font-weight-normal"><?= htmlspecialchars($usuario['nombreCompleto']) ?> </span></a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
              <div class="dropdown-header text-center">
                <img class="img-md rounded-circle"
                  src="../../../images/473424986_122094668432737167_5148454371714842654_n.jpg" alt="Profile image"
                  style="width:50px;" />
                <p class="mb-1 mt-3"><?= htmlspecialchars($usuario['nombreCompleto']) ?></p>
                <p class="font-weight-light text-muted mb-0">
                  <?= htmlspecialchars($usuario['namuser']) ?>
                </p>
              </div>
              <a class="dropdown-item"><i class="dropdown-item-icon icon-user text-primary"></i> My
                Profile
                <span class="badge badge-pill badge-danger">1</span></a>
              <a class="dropdown-item"><i class="dropdown-item-icon icon-speech text-primary"></i>
                Messages</a>
              <a class="dropdown-item"><i class="dropdown-item-icon icon-energy text-primary"></i>
                Activity</a>
              <a class="dropdown-item"><i class="dropdown-item-icon icon-question text-primary"></i>
                FAQ</a>
              <a class="dropdown-item" href="<?= SERVERURL ?>views/logout.php">
                <i class="dropdown-item-icon icon-power text-primary"></i> Cerrar Sesión
              </a>



            </div>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
          data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>
    <div class="container-fluid page-body-wrapper">
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item navbar-brand-mini-wrapper">
            <a class="nav-link navbar-brand brand-logo-mini" href="../movdiario/listar-movdiario.php"><img
                style="width: 50px;" src="../../../images/473424986_122094668432737167_5148454371714842654_n.jpg"
                alt="logo" /></a>
          </li>
          <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
              <div class="profile-image">
                <img class="img-xs rounded-circle"
                  src="../../../images/473424986_122094668432737167_5148454371714842654_n.jpg" alt="profile image" />
                <div class="dot-indicator bg-success"></div>
              </div>
              <div class="text-wrapper">
                <p class="profile-name"> <?= htmlspecialchars($usuario['nombreCompleto']) ?></p>
                <p class="designation"><?= htmlspecialchars($usuario['rol']) ?></p>
              </div>
            </a>
          </li>
          <li class="nav-item nav-category">
            <span class="nav-link">Inicio</span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL ?>views/page/movdiario/listar-movdiario.php">
              <span class="menu-title">Movimiento Diario </span>
              <i class="fa-solid fa-chart-line menu-icon"></i>
            </a>
          </li>
          <li class="nav-item nav-category">
            <span class="nav-link ">Inventario</span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL ?>views/page/ventas/listar-ventas.php">
              <span class="menu-title">Ventas</span>
              <i class="fa-solid fa-tags menu-icon"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL ?>views/page/compras/listar-compras.php">
              <span class="menu-title">Compras</span>
              <i class="fa-solid fa-cart-plus menu-icon"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL ?>views/page/productos/listar-producto.php">
              <span class="menu-title">Productos</span>
              <i class="fa-solid fa-store menu-icon"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL ?>views/page/kardex/listar-kardex.php">
              <span class="menu-title">Kardex</span>
              <i class="fa-solid fa-arrows-turn-to-dots menu-icon"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL ?>views/page/cotizaciones/listar-cotizacion.php">
              <span class="menu-title">Cotizaciones</span>
              <i class="fa-solid fa-list-ol menu-icon"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL ?>views/page/ordenservicios/listar-ordenes.php">
              <span class="menu-title">Órdenes de Servicio</span>
              <i class="fa-solid fa-car-tunnel menu-icon"></i>
            </a>
          </li>
          <!--- 
          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL ?>views/page/promociones/listar-promociones.php">
              <span class="menu-title">Promociones</span>
              <i class="fa-solid fa-percent menu-icon"></i>
            </a>
          </li>
          -->
          <li class="nav-item nav-category">
            <span class="nav-link">Administracion</span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL ?>views/page/clientes/listar-cliente.php">
              <span class="menu-title">Clientes</span>
              <i class="fa-solid fa-building-user menu-icon"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL ?>views/page/vehiculos/listar-vehiculos.php">
              <span class="menu-title">Vehiculos</span>
              <i class="fa-solid fa-car-side menu-icon"></i>
            </a>
          </li>
          <li class="nav-item nav-category">
            <span class="nav-link">Contactabilidad</span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL ?>views/page/contactabilidad/listar-graficos.php">
              <span class="menu-title">Graficos</span>
              <i class="fa-solid fa-chart-pie menu-icon"></i>
            </a>
          </li>
          <li class="nav-item nav-category">
            <span class="nav-link">Caja</span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL ?>views/page/egresos/listar-egresos.php">
              <span class="menu-title">Egresos</span>
              <i class="fa-solid fa-money-bill-transfer menu-icon"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL ?>views/page/arqueocaja/listar-arqueo-caja.php">
              <span class="menu-title">Arqueo de caja</span>
              <i class="fa-solid fa-table menu-icon"></i>
            </a>
          </li>
        </ul>
      </nav>

      <script>
        document.getElementById('logoutBtn').addEventListener('click', async e => {
          e.preventDefault();
          try {
            const resp = await fetch('<?= SERVERURL ?>app/controllers/colaborador.controller.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
              body: new URLSearchParams({
                operation: 'logout'
              })
            });
            const data = await resp.json();
            if (data.status) {
              // Una vez que el back cierre la sesión, redirige al login
              window.location.href = '<?= SERVERURL ?>';
            } else {
              alert('No se pudo cerrar sesión: ' + data.message);
            }
          } catch (err) {
            console.error(err);
            alert('Error de red al cerrar sesión');
          }
        });
      </script>