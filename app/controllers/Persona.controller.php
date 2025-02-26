<?php

require_once "../models/Persona.php";

//GET = Consultas, búsquedas, filtros (LECTURA)
if (isset($_GET['operacion'])){

  $persona = new Persona();

  switch ($_GET['operacion']){
    case 'getAll':
      echo json_encode($persona->getAll()); //JSON
      break;
  }

}