<?php
if(isset($_SERVER['REQUEST_METHOD'])){
    header('Content-type: application/json; charset = utf-8');

    require_once "../models/Subcategoria.php";
    $subcategoria = new Subcategoria();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if($_GET['task'] == 'getSubcategoriaByCategoria'){ echo json_encode($subcategoria->getSubcategoriaByCategoria());}
            break;
        
        default:
            # code...
            break;
    }


}
?>