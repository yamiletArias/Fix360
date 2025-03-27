DELIMITER $$

CREATE PROCEDURE spRegistroVentas(
    IN _tipo               VARCHAR(10),
    IN _numserie           CHAR(12),
    IN _numcomprobante     CHAR(8),
    IN _nomcliente        VARCHAR(40),
    IN _fecha              DATE,
    IN _tipomoneda         VARCHAR(10),
    IN _producto          JSON,
    IN _precio            JSON,
    IN _cantidad         JSON,
    IN _descuento         JSON
)
BEGIN
    DECLARE _idcliente INT;
    DECLARE _idcolaborador INT;
    DECLARE _idventa INT;
    DECLARE _idproducto INT;
    DECLARE i INT DEFAULT 0;
    DECLARE num_productos INT;
    DECLARE _new_numserie CHAR(12);

    -- Generación automática del número de serie si no se envía uno
    IF _numserie IS NULL THEN
        SET _new_numserie = (SELECT CONCAT('V', LPAD(COALESCE(MAX(CAST(SUBSTRING(numserie, 2) AS UNSIGNED)), 0) + 1, 5, '0'))
                             FROM ventas);
    ELSE
        SET _new_numserie = _numserie;
    END IF;

    -- Obtener el idcliente según el proveedor
    SELECT idcliente INTO _idcliente FROM clientes WHERE nomprovedor = _nomprovedor LIMIT 1;

    -- Asignar un idcolaborador (esto debe ser dinámico según el usuario autenticado)
    SET _idcolaborador = 1;  -- Asumiendo que el colaborador es el ID 1 por defecto.

    -- Insertar en la tabla ventas
    INSERT INTO ventas (idcliente, idcolaborador, tipocom, numserie, numcom, fechahora)
    VALUES (_idcliente, _idcolaborador, _tipo, _new_numserie, _numcomprobante, _fecha);

    -- Obtener el id de la venta insertada
    SET _idventa = LAST_INSERT_ID();

    -- Calcular el número de productos en el JSON
    SET num_productos = JSON_LENGTH(_productos);

    -- Insertar los productos
    WHILE i < num_productos DO
        -- Obtener el id del producto desde el JSON
        SET _idproducto = JSON_UNQUOTE(JSON_EXTRACT(_productos, CONCAT('$[', i, ']')));

        -- Obtener el precio, cantidad y descuento desde el JSON
        SET _precio = JSON_UNQUOTE(JSON_EXTRACT(_precios, CONCAT('$[', i, ']')));
        SET _cantidad = JSON_UNQUOTE(JSON_EXTRACT(_cantidades, CONCAT('$[', i, ']')));
        SET _descuento = JSON_UNQUOTE(JSON_EXTRACT(_descuentos, CONCAT('$[', i, ']')));

        -- Insertar en la tabla detallecotizacion
        INSERT INTO detallecotizacion (idcotizacion, idproducto, cantidad, precio, descuento)
        VALUES (_idventa, _idproducto, _cantidad, _precio, _descuento);

        -- Incrementar el índice
        SET i = i + 1;
    END WHILE;

END $$

DELIMITER ;
