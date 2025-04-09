-- alterar por ahora
ALTER TABLE detalleventa
MODIFY COLUMN idorden INT NULL,
MODIFY COLUMN idpromocion INT NULL;
ALTER TABLE ventas MODIFY idcolaborador INT NULL;

-- registrar ventas
DELIMITER $$
CREATE PROCEDURE spuRegisterVenta (
  IN _tipocom VARCHAR(50),
  IN _fechahora VARCHAR(50),
  IN _numserie VARCHAR(30),
  IN _numcom CHAR(20),
  IN _moneda CHAR(11),
  IN _idcliente INT
)
BEGIN
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
  
  SELECT LAST_INSERT_ID() AS idventa;
END $$
DELIMITER ;

-- registrar detalle ventas con idventa
DELIMITER $$
CREATE PROCEDURE spuInsertDetalleVenta (
  IN _idventa INT,
  IN _idproducto INT,
  IN _cantidad INT,
  IN _numserie_detalle VARCHAR(50),
  IN _precioventa DECIMAL(7,2),
  IN _descuento DECIMAL(5,2)
)
BEGIN
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
	CASE 
	  WHEN _numserie_detalle IS NULL THEN JSON_ARRAY() 
	  ELSE JSON_ARRAY(_numserie_detalle) 
	END,
	_precioventa,
	_descuento
	);
END $$
DELIMITER ;
-- Fin de registrar detalle ventas con idventa

-- Moneda
DELIMITER $$
CREATE PROCEDURE spuGetMonedasVentas()
BEGIN
  SELECT DISTINCT moneda FROM ventas;
END $$
DELIMITER ;
-- Fin Moneda

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

-- Detalle ventas
DELIMITER $$

CREATE PROCEDURE spuGetDetalleVenta (
    IN _idventa INT
)
BEGIN
    SELECT 
        d.iddetventa,
        d.idproducto,
        d.cantidad,
        d.numserie,
        d.precioventa,
        d.descuento,
        CONCAT(s.subcategoria, ' ', p.descripcion) AS producto,
        
        -- Datos del cliente
        c.idcliente,
        CASE
            WHEN c.idpersona IS NOT NULL THEN 'Persona'
            WHEN c.idempresa IS NOT NULL THEN 'Empresa'
            ELSE 'Desconocido'
        END AS tipo_cliente,
        COALESCE(pe.nombres, em.nomcomercial) AS nombre_cliente

    FROM detalleventa d
    INNER JOIN ventas v ON d.idventa = v.idventa
    INNER JOIN clientes c ON v.idcliente = c.idcliente
    LEFT JOIN personas pe ON c.idpersona = pe.idpersona
    LEFT JOIN empresas em ON c.idempresa = em.idempresa
    LEFT JOIN productos p ON d.idproducto = p.idproducto
    LEFT JOIN subcategorias s ON p.idsubcategoria = s.idsubcategoria
    WHERE d.idventa = _idventa;
END $$

DELIMITER ;
-- Detalle ventas

CALL spuGetDetalleVenta(2);

CALL spuRegisterVenta('boleta', '2025-04-07 10:30:00', 'B076', 'B-0928971', 'Soles', 5);
CALL spuInsertDetalleVenta(18, 3, 1, 'B076', 120.50, 0);

SELECT * FROM detalleventa;
SELECT * FROM ventas;
