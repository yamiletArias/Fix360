-- prueba
DELIMITER $$
CREATE PROCEDURE spuRegisterVentas (
  IN _tipocom VARCHAR(50),
  IN _fechahora VARCHAR(50),
  IN _numserie VARCHAR(30),
  IN _numcom CHAR(20),
  IN _moneda CHAR(11),
  IN _idcliente INT,
  IN _idproducto INT,
  IN _cantidad INT,
  IN _numserie_detalle VARCHAR(50),
  IN _precioventa DECIMAL(7,2),
  IN _descuento DECIMAL(5,2)
)
BEGIN
  DECLARE _idventa INT;
  
  -- Insertar en la tabla ventas
  INSERT INTO ventas (
    idcliente,
    tipocom,
    fechahora,
    numserie,
    numcom,
    moneda
  )
  VALUES (
    _idcliente,
    _tipocom,
    _fechahora,
    _numserie,
    _numcom,
    _moneda
  );

  -- Obtener el ID de la venta insertada
  SET _idventa = LAST_INSERT_ID();

  -- Insertar en la tabla detalleventa
  INSERT INTO detalleventa (
    idproducto,
    idventa,
    cantidad,
    numserie,
    precioventa,
    descuento
  )
  VALUES (
    _idproducto,
    _idventa,
    _cantidad,
    _numserie_detalle,  
    _precioventa,
    _descuento
  );

  -- Retornar el ID de la venta (o un indicador de éxito)
  SELECT _idventa AS idventa;
END $$
DELIMITER ;
CALL spuRegisterVentas(
  'boleta',
  '2025-04-07 10:30:00',
  'B010',
  'B-0401500',
  'Soles',
  1,
  3,
  2,
  '["B010"]', -- Aquí se pasa un valor JSON válido (arreglo JSON).
  120.50,
  10.00
);
SELECT * FROM detalleventa;
SELECT * FROM ventas;

-- alterar por ahora
ALTER TABLE detalleventa
MODIFY COLUMN idorden INT NULL,
MODIFY COLUMN idpromocion INT NULL;
ALTER TABLE ventas MODIFY idcolaborador INT NULL;

SHOW CREATE TABLE detalleventa;
SELECT LAST_INSERT_ID();
SELECT * FROM detalleventa WHERE numserie = 'B024';
SELECT * FROM detalleventa WHERE numserie = 'ABC12333';
SELECT * FROM ventas ORDER BY idventa DESC LIMIT 3;
SELECT * FROM detalleventa ORDER BY iddetventa DESC LIMIT 3;
-- fin registro

-- tipomoneda
DELIMITER $$
CREATE PROCEDURE spuGetMonedasVentas()
BEGIN
  SELECT DISTINCT moneda FROM ventas;
END $$
DELIMITER ;
-- fintipomoneda

-- Procedimiento para buscar clientes
DELIMITER $$
CREATE PROCEDURE buscar_cliente(IN termino_busqueda VARCHAR(255))
BEGIN
    SELECT 
        C.idcliente,
        CASE
            WHEN C.idempresa IS NOT NULL AND E.nomcomercial IS NOT NULL THEN E.nomcomercial
            WHEN C.idpersona IS NOT NULL AND P.nombres IS NOT NULL THEN P.nombres
        END AS cliente,
        C.idempresa,
        C.idpersona
    FROM clientes C
    LEFT JOIN empresas E ON C.idempresa = E.idempresa
    LEFT JOIN personas P ON C.idpersona = P.idpersona
    WHERE 
        (E.nomcomercial LIKE CONCAT('%', termino_busqueda, '%') AND E.nomcomercial IS NOT NULL)
        OR 
        (P.nombres LIKE CONCAT('%', termino_busqueda, '%') AND P.nombres IS NOT NULL)
    LIMIT 10;
END$$
DELIMITER ;
-- fin busqueda cliente

-- Buscar producto
DELIMITER $$
CREATE PROCEDURE buscar_producto(IN termino_busqueda VARCHAR(255))
BEGIN
    SELECT 
        P.idproducto,
        CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
        DV.precioventa
    FROM productos P
    INNER JOIN subcategorias S ON P.idsubcategoria = S.idsubcategoria
    LEFT JOIN detalleventa DV ON P.idproducto = DV.idproducto
    WHERE 
        (S.subcategoria LIKE CONCAT('%', termino_busqueda, '%') OR P.descripcion LIKE CONCAT('%', termino_busqueda, '%'))
    LIMIT 10;
END $$
DELIMITER ;
-- Fin Buscar producto

-- producto con precioventa prueba
DELIMITER $$
CREATE PROCEDURE buscar_producto(IN termino_busqueda VARCHAR(255))
BEGIN
    SELECT 
        subcategoria_producto,
        precioventa
    FROM vs_registro_venta
    WHERE subcategoria_producto LIKE CONCAT('%', termino_busqueda, '%')
    LIMIT 10;
END $$
DELIMITER ;
-- fin de producto con precioventa prueba

-- Buscar producto prueba2
DELIMITER $$
CREATE PROCEDURE buscar_producto(IN termino_busqueda VARCHAR(255))
BEGIN
    SELECT 
        P.idproducto,
        CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
        DV.precioventa
    FROM productos P
    INNER JOIN subcategorias S ON P.idsubcategoria = S.idsubcategoria
    INNER JOIN detalleventa DV ON P.iddetventa= DV.ididdetventa
    WHERE 
        (S.subcategoria LIKE CONCAT('%', termino_busqueda, '%') OR P.descripcion LIKE CONCAT('%', termino_busqueda, '%'))
    LIMIT 10;
END $$
DELIMITER ;
-- fin busqueda producto


-- listar
DELIMITER $$
CREATE PROCEDURE spListarVentas(
    IN _numserie CHAR(12)  -- Puedes filtrar por número de serie, si es necesario
)
BEGIN
    -- Variables para obtener los detalles de la venta
    DECLARE _idventa INT;
    DECLARE _idcliente INT;
    DECLARE _nombre_cliente VARCHAR(40);
    DECLARE _tipocom VARCHAR(10);
    DECLARE _fecha DATETIME;
    DECLARE _numcom VARCHAR(8);
    DECLARE _total DECIMAL(10, 2);

    IF _numserie IS NOT NULL THEN
        SELECT v.idventa, v.idcliente, p.nombres, p.apellidos, v.tipocom, v.numserie, v.numcom, v.fechahora
        INTO _idventa, _idcliente, _nombre_cliente, _tipocom, _numserie, _numcom, _fecha
        FROM ventas v
        JOIN clientes c ON v.idcliente = c.idcliente
        JOIN personas p ON c.idpersona = p.idpersona  -- Relacionamos con la tabla personas
        WHERE v.numserie = _numserie;
    ELSE
        SELECT v.idventa, v.idcliente, p.nombres, p.apellidos, v.tipocom, v.numserie, v.numcom, v.fechahora
        INTO _idventa, _idcliente, _nombre_cliente, _tipocom, _numserie, _numcom, _fecha
        FROM ventas v
        JOIN clientes c ON v.idcliente = c.idcliente
        JOIN personas p ON c.idpersona = p.idpersona; 
    END IF;

    SELECT 
        dv.iddetventa, 
        p.descripcion AS producto, 
        dv.cantidad, 
        dv.precioventa, 
        dv.descuento, 
        (dv.cantidad * dv.precioventa * (1 - dv.descuento / 100)) AS total_producto
    FROM detalleventa dv
    JOIN productos p ON dv.idproducto = p.idproducto
    WHERE dv.idventa = _idventa;

    SELECT SUM(dv.cantidad * dv.precioventa * (1 - dv.descuento / 100)) AS total_venta
    INTO _total
    FROM detalleventa dv
    WHERE dv.idventa = _idventa;

    SELECT _total AS total_venta;
END $$
DELIMITER ;

SHOW PROCEDURE STATUS WHERE Name = 'spListarVentas';
CALL spListarVentas('B001');
CALL spListarVentas(NULL);
SELECT * FROM ventas WHERE numserie = 'B001';   
SELECT * FROM detalleventa WHERE idventa = 2;  -- Asumiendo que la venta con 'B001' tiene idventa = 1.

