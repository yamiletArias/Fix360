<?php

//__DIR__ nos da la ruta en donde esta el archivo en donde se esta ejecutando

require_once __DIR__ . "/../models/Conexion.php";
require_once __DIR__ ."/../models/Contactabilidad.php";

$contactabilidad = new Contactabilidad();
$mensaje = $contactabilidad->getContactabilidad();
var_dump($mensaje);

