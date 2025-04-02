<?php
if (isset($_SERVER['REQUEST_METHOD'])) {
    header('Content-type: application/json; charset=utf-8');

    require_once "../models/Propietario.php";
    require_once "../helpers/helper.php";

    $propietario = new Propietario();

    // Recoger y limpiar los parámetros
    $tipo    = Helper::limpiarCadena($_GET['tipo'] ?? 'persona'); // 'persona' o 'empresa'
    $metodo  = Helper::limpiarCadena($_GET['metodo'] ?? '');
    $valor   = Helper::limpiarCadena($_GET['valor'] ?? '');

    // Realizar la búsqueda
    $resultados = $propietario->buscarPropietario($tipo, $metodo, $valor);

    echo json_encode($resultados);
}
?>
