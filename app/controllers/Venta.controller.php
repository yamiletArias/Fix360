<?php
//require_once '../models/Conexion.php';
//require_once '../models/Venta.php';

if (isset($_SERVER['REQUEST_METHOD'])) {
    
    header('Content-Type: application/json; charset=utf-8');

    require_once '../models/Venta.php';
    $venta = new Venta();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            echo json_encode($venta->getAll());
            break;

            case 'POST':
                $input = file_get_contents('php://input');
                $dataJSON = json_decode($input, true);
    
                // Datos de la venta
                $ventaData = [
                    'idcliente'    => htmlspecialchars($dataJSON['idcliente']),
                    'tipocom'      => htmlspecialchars($dataJSON['tipocom']),
                    'numserie'     => htmlspecialchars($dataJSON['numserie']),
                    'numcom'       => htmlspecialchars($dataJSON['numcom']),
                    'fechahora'    => htmlspecialchars($dataJSON['fechahora']),
                    'moneda'       => htmlspecialchars($dataJSON['moneda']),
                ];
    
                try {
                    // Registrar la venta en la tabla `ventas`
                    $idVenta = $venta->add($ventaData); // Esto devuelve el ID de la venta
    
                    if ($idVenta) {
                        // Registrar los productos en la tabla `detalleventa`
                        $detalleVentaData = $dataJSON['productos']; // Se espera que sea un array de productos
                        $productosRegistrados = 0;
    
                        foreach ($detalleVentaData as $producto) {
                            $productoData = [
                                'idventa'    => $idVenta,   // ID de la venta registrada
                                'idproducto' => htmlspecialchars($producto['idproducto']),
                                'precioventa'=> htmlspecialchars($producto['precioventa']),
                                'cantidad'   => htmlspecialchars($producto['cantidad']),
                                'descuento'  => htmlspecialchars($producto['descuento']),
                            ];
    
                            $resultado = $venta->addDetalleVenta($productoData); // Registrar cada producto en detalleventa
                            if ($resultado > 0) {
                                $productosRegistrados++;
                            }
                        }
    
                        // Verificar si al menos un producto fue registrado correctamente
                        if ($productosRegistrados > 0) {
                            echo json_encode(["status" => "success", "idventa" => $idVenta, "message" => "Venta y productos registrados con éxito."]);
                        } else {
                            echo json_encode(["status" => "error", "message" => "No se pudo registrar ningún producto."]);
                        }
                    } else {
                        echo json_encode(["status" => "error", "message" => "No se pudo registrar la venta."]);
                    }
    
                } catch (Exception $e) {
                    // Si ocurre algún error en cualquier parte, se captura y muestra el mensaje.
                    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                }
                break;
    }
}
?>
