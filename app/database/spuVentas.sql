-- PROCEDIMIENTO ALMACENADOS DE VENTAS REAL

-- ALTERAR POR AHORA (IDORDEN, IDPROMOCION, IDCOLABORADOR, IDVEHICULO) EN VENTAS
ALTER TABLE detalleventa
MODIFY COLUMN idorden INT NULL,
MODIFY COLUMN idpromocion INT NULL;
ALTER TABLE ventas MODIFY idcolaborador INT NULL;
ALTER TABLE ventas MODIFY idvehiculo INT NULL;
ALTER TABLE ventas MODIFY kilometraje DECIMAL(10,2) NULL;
-- ALTERAR ID COLABORADOR EN COMPRAS POR EL MOMENTO
ALTER TABLE compras MODIFY idcolaborador INT NULL;
-- POR EL MOMENTO ALTERAR ID COLABORADOR EN COTIZACIONES
ALTER TABLE cotizaciones MODIFY idcolaborador INT NULL;

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
    NULLIF(_idvehiculo, 0),
    _tipocom,
    _fechahora,
    _numserie,
    _numcom,
    _moneda,
    NULLIF(_kilometraje, 0)
  );
  SELECT LAST_INSERT_ID() AS idventa;
END$$

-- 2) PROCEDIMIENTO DE REGISTRO DETALLE DE VENTA OBTENIENDO EL IDVENTA
DROP PROCEDURE IF EXISTS spuInsertDetalleVenta;
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
DROP PROCEDURE IF EXISTS spuGetMonedasVentas;
DELIMITER $$
CREATE PROCEDURE spuGetMonedasVentas()
BEGIN
  SELECT 'Soles' AS moneda
  UNION
  SELECT 'Dólares' AS moneda
  UNION
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
DROP PROCEDURE IF EXISTS buscar_cliente;
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

-- 6) PROCEDIMIENTO PARA REGISTRAR COMPRAS
DROP PROCEDURE IF EXISTS spuRegisterCompra;
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
DROP PROCEDURE IF EXISTS spuInsertDetalleCompra;
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

-- 8) PROCEDIMIENTO PARA REGISTRAR PRODUCTO Y OBTENER EL ID COMO SALIDA
DROP PROCEDURE IF EXISTS spRegisterProducto;
DELIMITER $$
CREATE PROCEDURE spRegisterProducto(
  IN _idsubcategoria INT,
  IN _idmarca INT,
  IN _descripcion VARCHAR(50),
  IN _precio DECIMAL(7,2),
  IN _presentacion VARCHAR(40),
  IN _undmedida VARCHAR(40),
  IN _cantidad DECIMAL(10,2),
  IN _img VARCHAR(255),
  OUT _idproducto INT
)
BEGIN
  INSERT INTO productos (idsubcategoria, idmarca, descripcion, precio, presentacion, undmedida, cantidad, img) 
  VALUES (_idsubcategoria, _idmarca, _descripcion, _precio, _presentacion, _undmedida, _cantidad, _img);
  SET _idproducto = LAST_INSERT_ID();
END$$
DELIMITER $$

-- 9) PROCEDIMIENTO PARA MOSTRAR EL PROVEEDOR
DROP PROCEDURE IF EXISTS spuGetProveedores;
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

-- 2) PROCEDMIENTO PARA EL REGISTRO REAL DE CLIENTE EMPRESA (PARA QUE SE VEA EN PROVEEDORES AL REGISTRAR)
DROP PROCEDURE IF EXISTS spRegisterClienteEmpresa;
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

-- 10) PROCEDIMIENTO PARA REGISTRAR COTIZACION
DROP PROCEDURE IF EXISTS spuRegisterCotizaciones;
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
DROP PROCEDURE IF EXISTS spuInsertDetalleCotizacion;
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

-- 12) PRODEDIMIENTO PARA LA JUSTIFICACION DE LA COMPRA ELIMINADA = COMPRA ANULADA
DROP PROCEDURE IF EXISTS spuDeleteCompra;
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
    UPDATE compras
    SET estado = FALSE,
        justificacion = _justificacion
    WHERE idcompra = _idcompra;
  COMMIT;
END$$

-- 13) PRODEDIMIENTO PARA LA JUSTIFICACION DE LA VENTA ELIMINADA = VENTA ANULADA
DROP PROCEDURE IF EXISTS spuDeleteVenta;
DELIMITER $$
CREATE PROCEDURE spuDeleteVenta (
  IN _idventa      INT,
  IN _justificacion VARCHAR(255)
)
BEGIN
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;
  START TRANSACTION;
    UPDATE ventas
    SET estado = FALSE,
        justificacion = _justificacion
    WHERE idventa = _idventa;
  COMMIT;
END$$

-- 14) PROCEDIMIENTO PARA DATOS DE VENTA (DIA, SEMANA Y MES)
DROP PROCEDURE IF EXISTS spListVentasPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListVentasPorPeriodo(
  IN _modo   ENUM('semana','mes','dia'),
  IN _fecha  DATE
)
BEGIN
  DECLARE start_date DATE;
  DECLARE end_date   DATE;

  IF _modo = 'semana' THEN
    SET start_date = DATE_SUB(_fecha, INTERVAL WEEKDAY(_fecha) DAY);
    SET end_date   = DATE_ADD(start_date, INTERVAL 6 DAY);
  ELSEIF _modo = 'mes' THEN
    SET start_date = DATE_FORMAT(_fecha, '%Y-%m-01');
    SET end_date   = LAST_DAY(_fecha);
  ELSE
    SET start_date = _fecha;
    SET end_date   = _fecha;  
  END IF;

  SELECT
    v.idventa    AS id,
    COALESCE(CONCAT(p.apellidos,' ',p.nombres), e.nomcomercial) AS cliente,
    v.tipocom,
    v.numcom,
    vt.total_pendiente,
    CASE WHEN vt.total_pendiente = 0 THEN 'pagado' ELSE 'pendiente' END AS estado_pago
  FROM ventas v
    JOIN clientes c      ON v.idcliente  = c.idcliente
    LEFT JOIN personas p ON c.idpersona  = p.idpersona
    LEFT JOIN empresas e ON c.idempresa  = e.idempresa
    JOIN vista_saldos_por_venta vt ON v.idventa = vt.idventa
  WHERE DATE(v.fechahora) BETWEEN start_date AND end_date
    AND v.estado = TRUE
  ORDER BY v.fechahora;
END$$

-- 15) PROCEDIMIENTO PARA DATOS DE COMPRA (DIA, SEMANA Y MES)
DROP PROCEDURE IF EXISTS spListComprasPorPeriodo;
DELIMITER $$
CREATE PROCEDURE spListComprasPorPeriodo(
  IN _modo  ENUM('semana','mes','dia'),
  IN _fecha DATE
)
BEGIN
  DECLARE start_date DATE;
  DECLARE end_date   DATE;

  -- Calcular rango según modo
  IF _modo = 'semana' THEN
    SET start_date = DATE_SUB(_fecha, INTERVAL WEEKDAY(_fecha) DAY);
    SET end_date   = DATE_ADD(start_date, INTERVAL 6 DAY);
  ELSEIF _modo = 'mes' THEN
    SET start_date = DATE_FORMAT(_fecha, '%Y-%m-01');
    SET end_date   = LAST_DAY(_fecha);
  ELSE
    SET start_date = _fecha;
    SET end_date   = _fecha;
  END IF;

  -- Listar compras en el rango con info de pago
  SELECT 
    C.idcompra AS id,
    E.nomcomercial AS proveedor,
    C.tipocom, 
    C.numcom,
    vspc.total_pendiente,
    CASE 
      WHEN vspc.total_pendiente = 0 THEN 'pagado'
      ELSE 'pendiente'
    END AS estado_pago
  FROM compras C
  JOIN proveedores P ON C.idproveedor = P.idproveedor
  JOIN empresas E ON P.idempresa = E.idempresa
  LEFT JOIN vista_saldos_por_compra vspc ON C.idcompra = vspc.idcompra
  WHERE DATE(C.fechacompra) BETWEEN start_date AND end_date
    AND C.estado = TRUE
  ORDER BY C.fechacompra;
END$$

