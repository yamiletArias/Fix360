<?php

require_once "../models/Conexion.php";
$conexion = new Conexion();

$nombreSucio = "<script>alert('Luis')</script>";
$nombreLimpio = $conexion->limpiarCadena($nombreSucio);

echo $nombreLimpio;