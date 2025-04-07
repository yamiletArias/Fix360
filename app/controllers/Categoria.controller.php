<?php
if(isset($_SERVER['REQUEST_METHOD'])){
    header('Content-type: application/json; charset = utf-8');

    require_once "../models/Categoria.php";
    $categoria = new Categoria();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if($_GET['task'] == 'getAll'){ echo json_encode($categoria->getAll());}
            break;
        
        default:
            # code...
            break;
    }
}
?>