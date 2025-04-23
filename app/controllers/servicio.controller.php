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

    case 'POST':
      $input = file_get_contents('php://input');

      $dataJSON = json_decode($input,true);

      if($dataJSON === null){
        echo json_encode(["error" => "JSON invalido"]);
        error_log("JSON Recibido: " . $input);
        exit;
      }

      $registro = [
        "idsubcategoria" => Helper::limpiarCadena($dataJSON["idsubcategoria"] ?? "" ),
        "servicio"       => Helper::limpiarCadena($dataJSON["servicio"] ?? "")
      ];
    
    default:
      # code...
      break;
  }

}