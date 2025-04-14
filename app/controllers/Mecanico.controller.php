<?php

if(isset($_SERVER['REQUEST_METHOD'])){
  header('Content-type: application/json; charset = utf-8');

  require_once "../models/Mecanico.php";
  $mecanico = new Mecanico();

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      if($_GET['task'] == 'getAllMecanico'){
        echo json_encode($mecanico->getAllMecanico());
      }
      
      break;
    
    default:
      # code...
      break;
  }
}