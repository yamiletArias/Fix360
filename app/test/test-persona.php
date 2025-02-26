<?php

require_once "../models/Persona.php";
$persona = new Persona();

//Registrar una nueva persona, necesitamos guardar sus datos en un ARREGLO
$nuevaPersona = [
  "apellidos"   => "Fajardo López",
  "nombres"     => "Sofia",
  "dni"         => "77770001",
  "fechanac"    => "1998-03-03",
  "direccion"   => "Calle Lima 123",
  "telefono"    => "956123123"
];

$mensaje = $persona->add($nuevaPersona);
var_dump($mensaje);

//var_dump() - RADIOGRAFÍA
//var_dump($persona->getAll());

//json_encode() - Convertir un objeto PHP a una cadena de tipo JSON (Javascript object Notation)
//JSON = "forma como podemos intercambiar información"
//echo json_encode($persona->getAll());
