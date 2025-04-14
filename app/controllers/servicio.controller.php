<?php
if (isset($_SERVER['REQUEST_METHOD'])) {
  header('Content-type: application/json; charset = utf-8');

  require_once "../models/Servicio.php";
  $servicio = new Servicio();

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      if($_GET['task'] == 'getServicioBySubcategoria'){
        echo json_encode($servicio->getServicioBySubcategoria($_GET['idsubcategoria']));
      }
      break;
    
    default:
      # code...
      break;
  }

}