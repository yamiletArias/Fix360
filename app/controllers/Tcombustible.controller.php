<?php

if(isset($_SERVER['REQUEST_METHOD'])){
    header('Content-type: application/json; charset = utf-8');

    require_once "../models/Tcombustible.php";
    $tcombustible = new Tcombustible();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            echo json_encode($tcombustible->getAll());
            break;
        
        default:
            # code...
            break;
    }


}
