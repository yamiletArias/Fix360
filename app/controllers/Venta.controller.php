<?php
//require_once '../models/Conexion.php';
//require_once '../models/Venta.php';

if (isset($_SERVER['REQUEST_METHOD'])){
    header('Content-Type: application/json; charset=utf-8');

    require_once '../models/Venta.php';
    $venta = new Venta();

    switch ($_SERVER['REQUEST_METHOD']){
        case 'GET':
            echo json_encode($venta->getAll());
            break;
        case 'POST':
            $input = file_get_contents('php//input');
            $dataJSON = json_decode($input, true);

            $registro = [
                'idcliente'         => htmlspecialchars($dataJSON['idcliente']),
                'idproducto'         => htmlspecialchars($dataJSON['idproducto']),
                'tipocom'           => htmlspecialchars($dataJSON['tipocom']),
                'numserie'          => htmlspecialchars($dataJSON['numserie']),
                'numcom'            => htmlspecialchars($dataJSON['numcom']),
                'fechahora'         => htmlspecialchars($dataJSON['fechahora']),
                'cantidad'         => htmlspecialchars($dataJSON['cantidad']),
                'precioventa'         => htmlspecialchars($dataJSON['precioventa']),
                'descuento'         => htmlspecialchars($dataJSON['descuento'])
            ];

            $nventa = $venta->add($registro);
            echo json_encode(["rows" => $nventa]);
            break;
    }
}

?>
