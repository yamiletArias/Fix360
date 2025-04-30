-- ALTERAR POR AHORA (IDORDEN, IDPROMOCION, IDCOLABORADOR, IDVEHICULO) EN VENTAS
ALTER TABLE detalleventa
MODIFY COLUMN idorden INT NULL,
MODIFY COLUMN idpromocion INT NULL;
ALTER TABLE ventas MODIFY idcolaborador INT NULL;
ALTER TABLE ventas MODIFY idvehiculo INT NULL;

-- 1) PROCEDIMIENTO DE REGISTRO DE VENTAS (cabecera)
DROP PROCEDURE IF EXISTS spuRegisterVenta;
DELIMITER $$
CREATE PROCEDURE spuRegisterVenta (
  IN _tipocom VARCHAR(50),
  IN _fechahora DATETIME, 
  IN _numserie VARCHAR(30),
  IN _numcom CHAR(20),
  IN _moneda CHAR(11),
  IN _idcliente INT,
  IN _idvehiculo INT,
  IN _kilometraje DECIMAL(10,2)
)
BEGIN
  INSERT INTO ventas (
    idcliente,
    idvehiculo,
    tipocom,
    fechahora,
    numserie,
    numcom,
    moneda,
    kilometraje
  )
  VALUES (
    _idcliente,
    _idvehiculo,
    _tipocom,
    _fechahora,
    _numserie,
    _numcom,
    _moneda,
    _kilometraje
  );
  SELECT LAST_INSERT_ID() AS idventa;
END $$

-- 2) PROCEDIMIENTO DE REGISTRO DETALLE DE VENTA OBTENIENDO EL IDVENTA
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

-- 3) PROCEDIMINETO PARA OBTENER MONEDAS (soles & dolares)
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

-- 4) PROCEDIMIENTO PARA BUSCAR CLIENTES
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

-- 5) PROCEDIMIENTO PARA BUSCAR PRODUCTO (producto, stock, precio)
DROP PROCEDURE IF EXISTS buscar_producto;
DELIMITER $$
CREATE PROCEDURE buscar_producto(
  IN termino_busqueda VARCHAR(255)
)
BEGIN
    SELECT 
        P.idproducto,
        CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
        P.precio,
        P.cantidad AS stock
    FROM productos P
    INNER JOIN subcategorias S 
      ON P.idsubcategoria = S.idsubcategoria
    WHERE 
        S.subcategoria LIKE CONCAT('%', termino_busqueda, '%') 
     OR P.descripcion LIKE CONCAT('%', termino_busqueda, '%')
    LIMIT 10;
END $$

-- ALTERAR ID COLABORADOR EN COMPRAS POR EL MOMENTO
ALTER TABLE compras MODIFY idcolaborador INT NULL;

-- 6) PROCEDIMIENTO PARA REGISTRAR COMPRAS
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

-- 7) PROCEDIMIENTO PARA REGISTRAR DETALLE DE COMPRA OBTENIENDO EL IDCOMPRA
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

-- 8) PROCEDIMIENTO PARA MOSTRAR EL PROVEEDOR
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

-- 9) PRODEDIMIENTO PARA LA JUSTIFICACION DE LA COMPRA ELIMINADA
-- pasa a estado = false
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

-- 2) PROCEDMIENTO PARA EL REGISTRO REAL DE CLIENTE EMPRESA (PARA QUE SE VEA EN PROVEEDORES)
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

-- POR EL MOMENTO ALTERAR ID COLABORADOR EN COTIZACIONES
ALTER TABLE cotizaciones MODIFY idcolaborador INT NULL;

-- 10) PROCEDIMIENTO PARA REGISTRAR COTIZACION
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

-- 11) PROCEDIMIENTO PARA REGISTRAR EL DETALLE COTIZACION OBTENIENDO EL IDCOTIZACION
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

-- PROBAR 
-- PRUEBA PARA OBTENER LAS MONEDAS
CALL spuGetMonedasVentas();
-- PRUEBA PARA BUSCAR PRODUCTO
CALL buscar_producto('moto');
-- PRUEBA PARA VER PROVEEDORE
CALL spuGetProveedores();
-- PRUEBA PARA VER LA JUSTIFICACION
SELECT idcompra, justificacion, estado
FROM compras
WHERE idcompra = 3;

-- PRUEBA DE VENTAS
SELECT * FROM detalleventa;
SELECT * FROM ventas;
-- PRUEBA DE COMPRAS
SELECT * FROM detallecompra;
SELECT * FROM compras;
-- PRUEBA DE COTIZACION
SELECT * FROM detallecotizacion;
SELECT * FROM cotizaciones;