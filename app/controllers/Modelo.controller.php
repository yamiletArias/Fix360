<?php

if (isset($_SERVER['REQUEST_METHOD'])) {

  header('Content-type: application/json; charset=utf-8');

  require_once "../models/Modelo.php";
  $modelo = new Modelo();

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      // Se esperan los parámetros idtipov e idmarca en la URL
      if (isset($_GET['idtipov']) && isset($_GET['idmarca'])) {
        $params = [
          "idtipov" =>  $_GET['idtipov'],
          "idmarca" =>  $_GET['idmarca']
        ];

        echo json_encode($modelo->GetAllModelosByTipoMarca($params));
      } else {
        echo json_encode([
          "status"  => false,
          "message" => "Faltan parámetros necesarios: idtipov e idmarca."
        ]);
      }
      break;

    default:

    break;
  }
}
