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
    

    case 'POST':
      $input = file_get_contents('php://input');

      $dataJSON = json_decode($input, true);

      if ($dataJSON === null) {
        echo json_encode(["error" => "JSON invalido"]);
        error_log("JSON Recibido: " . $input);
        exit;
      }

      $registro =[
        "idcomponente" => Helper::limpiarCadena($dataJSON["idcomponente"]),
        "idorden"      => Helper::limpiarCadena($dataJSON["idorden"]),
        "estado"       => Helper::limpiarCadena($dataJSON["estado"]),
        "foto"         => Helper::limpiarCadena($dataJSON["foto"]),
      ];
      $n = $observacion->add($registro);
      if ($n === 0) {
        echo json_encode(["error" => "No se pudo registrar la observacion"]);
        error_log("JSON Recibido: " . $input);
      } else {
        echo json_encode(["success" => "Observacion registrado", "rows" => $n]);
    }
    break;
    default:
      
      break;
  }
}