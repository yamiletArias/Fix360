<?php

if (isset($_SERVER['REQUEST_METHOD'])){
    header('Content-type: application/json; charset = utf-8');

    require_once "../models/Marca.php";
    $marca = new Marca();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if($_GET['task'] == 'getAllMarcaVehiculo'){
            echo json_encode($marca->getAllMarcaVehiculo());
            }
            if($_GET['task'] == 'getAllMarcaProducto'){
            echo json_encode($marca->getAllMarcaProducto());
            }
            break;
        
        case 'POST':
            
            break;
    }
}