<?php
if(isset($_SERVER['REQUEST_METHOD'])){
  header('Content-type: application/json; charset = utf-8');
  require_once "../models/Observacion.php";
  $observacion = new Observacion();

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      if ($_GET['task'] == 'getObservacionByOrden') {
        echo json_encode($observacion->getObservacionByOrden($_GET['idorden']));
      }
      break;
    
    default:
      
      break;
  }
}