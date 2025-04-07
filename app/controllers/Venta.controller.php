<?php


if (isset($_SERVER['REQUEST_METHOD'])) {

    header('Content-Type: application/json; charset=utf-8');

    require_once '../models/Venta.php';
    require_once "../helpers/helper.php";
    $venta = new Venta();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['type']) && $_GET['type'] == 'moneda') {
                echo json_encode($venta->getMonedasVentas());
            } else if (isset($_GET['q']) && !empty($_GET['q'])) {
                if (isset($_GET['type']) && $_GET['type'] == 'producto') {
                    // Buscar productos
                    $termino = $_GET['q'];
                    echo json_encode($venta->buscarProducto($termino));
                } else {
                    // Buscar clientes
                    $termino = $_GET['q'];
                    echo json_encode($venta->buscarCliente($termino));
                }
            } else {
                echo json_encode($venta->getAll());
            }
            break;
        case 'POST':
            $input = file_get_contents('php://input');
            $dataJSON = json_decode($input, true);

            // Limpiar las cadenas entrantes para evitar inyecciones o caracteres no deseados
            $tipocom = Helper::limpiarCadena($dataJSON['tipocom'] ?? "");
            $fechahora = Helper::limpiarCadena($dataJSON['fechahora'] ?? "");
            $numserie = Helper::limpiarCadena($dataJSON['numserie'] ?? "");
            $numcom = Helper::limpiarCadena($dataJSON['numcom'] ?? "");
            $moneda = Helper::limpiarCadena($dataJSON['moneda'] ?? "");
            $idcliente = $dataJSON['idcliente'] ?? 0;
            $idproducto = $dataJSON['idproducto'] ?? 0;
            $cantidad = $dataJSON['cantidad'] ?? 0;
            $numserie_detalle = json_encode($dataJSON['numserie_detalle'] ?? []);
            $precioventa = $dataJSON['precioventa'] ?? 0.00;
            $descuento = $dataJSON['descuento'] ?? 0.00;

            // Verificar que los datos requeridos están presentes
            if (empty($tipocom) || empty($fechahora) || empty($numserie) || empty($numcom) || empty($moneda) || $idcliente == 0 || $idproducto == 0 || $cantidad == 0) {
                echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios."]);
                exit;
            }

            // Preparar los parámetros para registrar la venta
            $params = [
                "tipocom" => $tipocom,
                "fechahora" => $fechahora,
                "numserie" => $numserie,
                "numcom" => $numcom,
                "moneda" => $moneda,
                "idcliente" => $idcliente,
                "idproducto" => $idproducto,
                "cantidad" => $cantidad,
                "numserie_detalle" => $numserie_detalle, // Guardarlo como JSON
                "precioventa" => $precioventa,
                "descuento" => $descuento
            ];

            // Llamar al método para registrar la venta
            $n = $venta->registerVentas($params);

            // Responder con el resultado de la operación
            if ($n > 0) {
                echo json_encode(["status" => "success", "message" => "Venta registrada exitosamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "No se pudo registrar la venta."]);
            }
            break;


    }
}
?>