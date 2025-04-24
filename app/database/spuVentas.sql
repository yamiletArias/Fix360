-- alterar por ahora
ALTER TABLE detalleventa
MODIFY COLUMN idorden INT NULL,
MODIFY COLUMN idpromocion INT NULL;
ALTER TABLE ventas MODIFY idcolaborador INT NULL;

-- 1) PROCEDIMIENTO DE VENTAS
-- registrar ventas
DELIMITER $$
CREATE PROCEDURE spuRegisterVenta (
  IN _tipocom VARCHAR(50),
  IN _fechahora VARCHAR(50), -- TIMESTAMP
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

-- MONEDA
DELIMITER $$
CREATE PROCEDURE spuGetMonedasVentas()
BEGIN
  -- Devolver "Soles" y "Dólares" incluso si no están registrados en la tabla
  SELECT 'Soles' AS moneda
  UNION
  SELECT 'Dólares' AS moneda
  UNION
  -- Ahora también verificamos las monedas registradas
  SELECT DISTINCT
    CASE
      WHEN UPPER(TRIM(moneda)) = 'SOLES' THEN 'Soles'
      WHEN UPPER(TRIM(moneda)) = 'DOLARES' THEN 'Dólares'
      ELSE NULL
    END AS moneda
  FROM ventas
  WHERE moneda IN ('SOLES', 'DOLARES');
END $$
DELIMITER ;
-- Fin Moneda
CALL spuGetMonedasVentas();

-- Procedimiento para buscar clientes

DELIMITER $$
CREATE PROCEDURE buscar_cliente(IN termino_busqueda VARCHAR(255))
BEGIN
    SELECT 
        C.idcliente,
        CASE
            WHEN C.idempresa IS NOT NULL AND E.nomcomercial IS NOT NULL THEN E.nomcomercial
            WHEN C.idpersona IS NOT NULL AND P.nombres IS NOT NULL THEN CONCAT(P.nombres, ' ', P.apellidos)
        END AS cliente,
        C.idempresa,
        C.idpersona
    FROM clientes C
    LEFT JOIN empresas E ON C.idempresa = E.idempresa
    LEFT JOIN personas P ON C.idpersona = P.idpersona
    WHERE 
        (E.nomcomercial LIKE CONCAT('%', termino_busqueda, '%') AND E.nomcomercial IS NOT NULL)
        OR 
        ((P.nombres LIKE CONCAT('%', termino_busqueda, '%') OR P.apellidos LIKE CONCAT('%', termino_busqueda, '%')) 
         AND P.nombres IS NOT NULL AND P.apellidos IS NOT NULL)
    LIMIT 10;
END$$
DELIMITER ;

-- Buscar producto
DELIMITER $$
CREATE PROCEDURE buscar_producto(IN termino_busqueda VARCHAR(255))
BEGIN
    SELECT 
        P.idproducto,
        CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
        P.precio
    FROM productos P
    INNER JOIN subcategorias S ON P.idsubcategoria = S.idsubcategoria
    LEFT JOIN detalleventa DV ON P.idproducto = DV.idproducto
    WHERE 
        S.subcategoria LIKE CONCAT('%', termino_busqueda, '%') 
        OR P.descripcion LIKE CONCAT('%', termino_busqueda, '%')
    LIMIT 10;
END $$
DELIMITER ;
-- Fin Buscar producto
CALL buscar_producto('moto');

-- ver Detalle ventas
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
-- FIN DEL PROCEDIMIENTO DE VENTAS

-- PROCEDIMIENTO DE COMPRAS
-- ALTERAR ID COLABORADOR POR EL MOMENTO
ALTER TABLE compras MODIFY idcolaborador INT NULL;

-- registrar compras
DELIMITER $$
CREATE PROCEDURE spuRegisterCompra (
  IN _fechacompra DATE,
  IN _tipocom VARCHAR(50),
  IN _numserie VARCHAR(10),
  IN _numcom VARCHAR(10),
  IN _moneda VARCHAR(20),
  IN _idproveedor INT
)
BEGIN
  INSERT INTO compras (
    idproveedor,
    fechacompra,
    tipocom,
    numserie,
    numcom,
    moneda
  )
  VALUES (
    _idproveedor,
    _fechacompra,
    _tipocom,
    _numserie,
    _numcom,
    _moneda
  );
  SELECT LAST_INSERT_ID() AS idcompra;
END $$
DELIMITER ;
-- fin registrar compras

-- registrar detalle compra
DELIMITER $$
CREATE PROCEDURE spuInsertDetalleCompra (
  IN _idcompra INT,
  IN _idproducto INT,
  IN _cantidad INT,
  IN _preciocompra DECIMAL(7,2),
  IN _descuento DECIMAL(5,2)
)
BEGIN
  INSERT INTO detallecompra (
    idproducto,
    idcompra,
    cantidad,
    preciocompra,
    descuento
  )
  VALUES (
    _idproducto,
    _idcompra,
    _cantidad,
    _preciocompra,
    _descuento
  );
END $$
DELIMITER ;
-- fin registrar detalle compra

-- MOSTRAR Proveedor
DELIMITER $$
CREATE PROCEDURE spuGetProveedores()
BEGIN
  SELECT DISTINCT 
    p.idproveedor,
    e.nomcomercial AS nombre_empresa
  FROM proveedores p
  INNER JOIN empresas e ON p.idempresa = e.idempresa
  LEFT JOIN compras c ON c.idproveedor = p.idproveedor;
END $$
DELIMITER ;
CALL spuGetProveedores();
-- FIN PROVEEDOR

-- PRODEDIMIENTO PARA LA JUSTIFICACION DE LA COMPRA ELIMINADA
-- pasa a estado = false:
DELIMITER $$
CREATE PROCEDURE spuDeleteCompra (
  IN _idcompra      INT,
  IN _justificacion VARCHAR(255)
)
BEGIN
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;
  START TRANSACTION;

    -- Eliminar detalles
    DELETE FROM detallecompra WHERE idcompra = _idcompra;

    -- Marcar como anulada + guardar justificación
    UPDATE compras
    SET estado = FALSE,
        justificacion = _justificacion
    WHERE idcompra = _idcompra;
  COMMIT;
END$$
DELIMITER ;

SELECT idcompra, justificacion, estado
FROM compras
WHERE idcompra = 3;
-- FIN DE JUSTIFICACION DE LA COMPRA ELIMINADA

-- registro real de cliente empresa (para que se vea en proveedores)
DELIMITER $$
CREATE PROCEDURE spRegisterClienteEmpresa (
  IN _ruc CHAR(11),
  IN _nomcomercial VARCHAR(80),
  IN _razonsocial VARCHAR(80),
  IN _telefono VARCHAR(20),
  IN _correo VARCHAR(100),
  IN _idcontactabilidad INT
)
BEGIN
  DECLARE _idempresa INT;
  -- Insertar en la tabla empresas
  INSERT INTO empresas (
    ruc,
    nomcomercial,
    razonsocial,
    telefono,
    correo
  )
  VALUES (
    _ruc,
    _nomcomercial,
    _razonsocial,
    _telefono,
    _correo
  );
  -- Obtener el ID de la empresa insertada
  SET _idempresa = LAST_INSERT_ID();
  -- Insertar en la tabla clientes vinculando la empresa
  INSERT INTO clientes (idempresa, idcontactabilidad)
  VALUES (_idempresa, _idcontactabilidad);
  -- Insertar en la tabla proveedores solo si no existe
  IF NOT EXISTS (
    SELECT 1 FROM proveedores WHERE idempresa = _idempresa
  ) THEN
    INSERT INTO proveedores (idempresa)
    VALUES (_idempresa);
  END IF;
END $$
DELIMITER ;
-- fin registro real de clientes empresa
-- FIN DEL PROCEDIMIENTO DE COMPRAS


-- PROCEDIMIENTO DE COTIZACIONES
-- POR EL MOMENTO ALTERAR ID COLABORADOR
ALTER TABLE cotizaciones MODIFY idcolaborador INT NULL;

-- registrar Cotizacion
DELIMITER $$
CREATE PROCEDURE spuRegisterCotizaciones (
  IN _fechahora TIMESTAMP,
  IN _vigenciadias INT,
  IN _moneda VARCHAR(20),
  IN _idcliente INT
)
BEGIN
  INSERT INTO cotizaciones (
    idcliente,
    fechahora,
    vigenciadias,
    moneda
  )
  VALUES (
    _idcliente,
    _fechahora,
    _vigenciadias,
    _moneda
  );
  SELECT LAST_INSERT_ID() AS idcotizacion;
END $$
DELIMITER ;
-- fin registrar Cotizacion

-- registrar detalle Cotizacion
DELIMITER $$
CREATE PROCEDURE spuInsertDetalleCotizacion (
  IN _idcotizacion INT,
  IN _idproducto INT,
  IN _cantidad INT,
  IN _precio DECIMAL(7,2),
  IN _descuento DECIMAL(5,2)
)
BEGIN
  INSERT INTO detallecotizacion (
    idproducto,
    idcotizacion,
    cantidad,
    precio,
    descuento
  )
  VALUES (
    _idproducto,
    _idcotizacion,
    _cantidad,
    _precio,
    _descuento
  );
END $$
DELIMITER ;
-- fin de registrar detalle cotizacion
-- FIN DEL PROCEDIMIENTO DE COTIZACIONES


-- PROBAR 
CALL spuRegisterCotizacion(fechahora, vigenciadias, idcliente, moneda);
CALL spuGetDetalleVenta(2);
CALL spuRegisterVenta('boleta', '2025-04-07 10:30:00', 'B076', 'B-0928971', 'Soles', 5);
CALL spuInsertDetalleVenta(18, 3, 1, 'B076', 120.50, 0);
SELECT * FROM detalleventa;
SELECT * FROM ventas;

CALL buscar_producto_compras('ace');
SELECT * FROM detallecompra;
SELECT * FROM compras;

SELECT * FROM detallecotizacion;
SELECT * FROM cotizaciones;