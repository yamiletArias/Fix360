<?php
if(isset($_SERVER['REQUEST_METHOD'])){
    header('Content-type: application/json; charset = utf-8');

    require_once "../models/Subcategoria.php";
    $subcategoria = new Subcategoria();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if(isset($_GET['idcategoria'])){ 
                $params = [
                    "idcategoria" => $_GET['idcategoria']
                ];
                echo json_encode($subcategoria->getSubcategoriaByCategoria($params));
            }else {
                echo json_encode([
                  "status"  => false,
                  "message" => "Faltan parámetros necesarios: idcategoria"
                ]);
              }
            break;
        
        default:
            # code...
            break;
    }


}
?>