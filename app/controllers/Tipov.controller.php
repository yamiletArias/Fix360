<?php

if (isset($_SERVER['REQUEST_METHOD'])){

  header('Content-type: application/json; charset = utf-8');

  require_once "../models/Tipov.php";
  $tipov = new Tipov();

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      echo json_encode($tipov->GetAll());
      break;
    
    case 'POST':
      
      break;
  }


}